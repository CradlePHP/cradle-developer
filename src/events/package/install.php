<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Framework\CommandLine;
use Cradle\Framework\Package;
use Cradle\Event\EventHandler;
use Cradle\Composer\Command;
use Cradle\Composer\Packagist;

/**
 * $ cradle package install foo/bar
 *
 * @param Request $request
 * @param Response $response
 */
return function($request, $response) {
    // get the pacakge name
    $name = $request->getStage(0);
    // get the package version
    $version = $request->getStage(1);

    // empty package name?
    if (!$name) {
        CommandLine::error(
            'Not enough arguments. Usage: `cradle package install vendor/package`'
        );
    }

    // valid version?
    if ($version && !preg_match('/^[0-9\.]+$/i', $version)) {
        CommandLine::error(
            'Unable to install package. Version is not valid version format should be 0.0.*.'
        );
    }

    // does the package installed already?
    if ($this->package('global')->config('packages', $name)) {
        // let them update instead
        CommandLine::error(sprintf(
            'Package is already installed. Run `cradle package update %s` instead',
            $name
        ));
    }

    // get active packages
    $packages = $this->getPackages();
    
    // if package is not yet loaded
    if (!isset($packages[$name])) {
        // register temporary package space
        $package = $this->register($name)->package($name);
    } else {
        // just load the registered package
        $package = $this->package($name);
    }

    // get the packaage type
    $type = $package->getPackageType();

    // if it's a pseudo package
    if ($type === Package::TYPE_PSEUDO) {
        CommandLine::error(sprintf(
            'Unable to install pseudo package %s.',
            $name
        ));
    }

    // if it's a root package
    if ($type === Package::TYPE_ROOT) {
        // directory doesn't exists?
        if (!is_dir($package->getPackagePath())) {
            CommandLine::error(sprintf(
                'Unable to install package. Root package %s does not exists.',
                $name
            ));
        }

        // bootstrap file exists?
        if (!file_exists(
            sprintf(
                '%s/%s',
                $package->getPackagePath(),
                '.cradle.php'
            )
        )) {
            CommandLine::error(sprintf(
                'Unable to install root package %s. Bootstrap file .cradle.php does not exists.',
                $name
            ));
        }

        // just let the package process the given version
        $request->setStage('version', $version);
    }

    // if it's a vendor package
    if ($type === Package::TYPE_VENDOR && !is_dir($package->getPackagePath())) {
        // we need to check from packagist
        $results = (new Packagist())->get($name);

        // if results is empty
        if (!isset($results['packages'][$name])) {
            CommandLine::error(sprintf(
                'Unable to install vendor package %s. Package does not exists.',
                $name
            ));
        }

        // if version is not set
        if (!$version) {
            // look for valid versions e.g 0.0.1
            $versions = [];
            foreach($results['packages'][$name] as $version => $info) {
                if (preg_match('/^[0-9\.]+$/i', $version)) {
                    $versions[] = $version;
                }
            }

            // if no valid version
            if (empty($versions)) {
                CommandLine::error(sprintf(
                    'Unable to install vendor package %s. Could not find a valid version.',
                    $name
                ));
            }

            //sort versions, and get the latest one
            usort($versions, 'version_compare');
            $version = array_pop($versions);
        } else {
            // if version does not exists
            if (!isset($results['packages'][$name][$version])) {
                CommandLine::error(sprintf(
                    'Unable to install vendor package %s. Could not find the provided version %s.',
                    $name,
                    $version
                ));
            }
        }

        // let them know we're installing via composer
        CommandLine::info(sprintf(
            'Installing the vendor package %s@%s via composer.',
            $name,
            $version
        ));

        // and that it requires additional step to complete the installation
        CommandLine::info('This will require additional steps to complete the installation.');

        //increase memory limit
        ini_set('memory_limit', -1);

        // composer needs to know where to place cache files
        $composer = $this->package('global')->path('root') . '/vendor/bin/composer';

        // run composer require command
        (new Command($composer))->require(sprintf('%s:%s', $name, $version));

        // let them install the package manually
        CommandLine::info('Package was added to your vendor packages.');

        // let them know what they need to do next
        CommandLine::info(sprintf(
            'Run `cradle %s install` to complete the installation.',
            $name
        ));

        return;
    }

    // if it's a vendor package
    if ($type === Package::TYPE_VENDOR) {
        list($vendor, $package) = explode('/', $name, 2);
    } else {
        //it's a root package
        list($vendor, $package) = explode('/', substr($name, 1), 2);
    }

    // trigger event
    $event = sprintf('%s-%s-%s', $vendor, $package, 'install');
    $this->trigger($event, $request, $response);

    // if no event was triggered
    $status = $this->getEventHandler()->getMeta();
    if($status === EventHandler::STATUS_NOT_FOUND) {
        return;
    }

    // if error
    if ($response->isError()) {
        CommandLine::error($response->getMessage(), false);
        return;
    }

    // if version
    if ($response->hasResults('version')) {
        // this means that the package itself updates it's version
        $version = $response->getResults('version');
        CommandLine::success(sprintf('%s was installed to %s', $name, $version));
        return;
    }

    CommandLine::success(sprintf('%s was installed', $name));
};
