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
 * $ cradle package update foo/bar
 *
 * @param Request $request
 * @param Response $response
 */
return function($request, $response) {
    // get the package name
    $name = $request->getStage(0);
    // get the package version
    $version = $request->getStage(1);

    // empty package name?
    if (!$name) {
        CommandLine::error(
            'Not enough arguments. Usage: `cradle package update vendor/package`'
        );
    }

    // valid version?
    if ($version && !preg_match('/^[0-9\.]+$/i', $version)) {
        CommandLine::error(
            'Unable to update package. Version is not valid version format should be 0.0.*.'
        );
    }

    // does the package installed already?
    if (!$this->package('global')->config('version', $name)) {
        // let them update instead
        CommandLine::error(sprintf(
            'Package is not yet installed. Run `cradle package install %s` instead.',
            $name
        ));
    }

    // get the package
    $package = $this->package($name);

    // get the packaage type
    $type = $package->getPackageType();

    // if it's a pseudo package
    if ($type === Package::TYPE_PSEUDO) {
        CommandLine::error(sprintf(
            'Unable to update pseudo package %s.',
            $name
        ));
    }

    // if it's a root package
    if ($type === Package::TYPE_ROOT) {
        // directory doesn't exists?
        if (!is_dir($package->getPackagePath())) {
            CommandLine::error(sprintf(
                'Unable to update package. Root package %s does not exists.',
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
                'Unable to update root package %s. Bootstrap file .cradle.php does not exists.',
                $name
            ));
        }

        // just let the package process the given version
        $request->setStage('version', $version);
    }

    // if it's a vendor package
    if ($type === Package::TYPE_VENDOR && is_dir($package->getPackagePath())) {
        // we need to check from packagist
        $results = (new Packagist())->get($name);

        // if results is empty
        if (!isset($results['packages'][$name])) {
            CommandLine::error(sprintf(
                'Unable to update vendor package %s. Package does not exists.',
                $name
            ));
        }

        // load the composer json
        $config = json_decode(file_get_contents(
            $this->package('global')->path('root') . '/composer.json'
        ), true);

        // check installed version
        $installed = $config['require'][$name];

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
                    'Unable to update vendor package %s. Could not find a valid version.',
                    $name
                ));
            }

            //sort versions, and get the latest one
            usort($versions, 'version_compare');
            $version = array_pop($versions);
        } else {
            // if the given version is less than our installed version
            if (version_compare($version, $installed, '<')) {
                // let them know that we can't install < version
                CommandLine::error(sprintf(
                    'Unable to update vendor package %s from %s to %s.',
                    $name,
                    $installed,
                    $version
                ), false);

                // let them know that it's > the installed version
                CommandLine::error('Specified version must be greater than the installed version.');
            }

            // if version does not exists
            if (!isset($results['packages'][$name][$version])) {
                CommandLine::error(sprintf(
                    'Unable to update vendor package %s. Could not find the provided version %s.',
                    $name,
                    $version
                ));
            }
        }

        // if the installed version is > the target version
        if (version_compare($version, $installed, '>')) {
            // let them know we're updating via composer
            CommandLine::info(sprintf(
                'Updating the vendor package %s to from %s to %s via composer.',
                $name,
                $installed,
                $version
            ));

            // and that it requires additional step to complete the update
            CommandLine::info('This will require additional steps to complete the package update.');

            //increase memory limit
            ini_set('memory_limit', -1);

            // composer needs to know where to place cache files
            $composer = $this->package('global')->path('root') . '/vendor/bin/composer';

            // run composer require command
            (new Command($composer))->require(sprintf('%s:%s', $name, $version));

            // let them install the package manually
            CommandLine::info('Package was updated');

            // let them know what they need to do next
            CommandLine::info(sprintf(
                'Run `cradle %s update` to complete the package update.',
                $name
            ));

            return;
        } else {
            // let them know that the package is already installed
            CommandLine::warning(sprintf(
                'Package %s@%s is already installed. Skipping.',
                $name,
                $version
            ));
        }
    }

    // if it's a vendor package
    if ($type === Package::TYPE_VENDOR) {
        list($vendor, $package) = explode('/', $name, 2);
    } else {
        //it's a root package
        list($vendor, $package) = explode('/', substr($name, 1), 2);
    }

    // trigger event
    $event = sprintf('%s-%s-%s', $vendor, $package, 'update');
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
        CommandLine::success(sprintf('%s was updated to %s', $name, $version));
        return;
    }

    CommandLine::success(sprintf('%s was updated', $name));
};
