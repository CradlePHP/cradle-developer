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
            'Version format is not valid.'
        );
    }

    // does the package installed already?
    if (!$this->package('global')->config('packages', $name)) {
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
            'Can\'t update pseudo package %s.',
            $name
        ));
    }

    // if it's a root package
    if ($type === Package::TYPE_ROOT) {
        CommandLine::info(sprintf('Updating root package %s.', $name));

        // directory doesn't exists?
        if (!is_dir($package->getPackagePath())) {
            CommandLine::error('Package does not exists.');
        }

        // bootstrap file exists?
        if (!file_exists(
            sprintf(
                '%s/%s',
                $package->getPackagePath(),
                '.cradle.php'
            )
        )) {
            CommandLine::error('Bootstrap file .cradle.php does not exists.');
        }

        // just let the package process the given version
        $request->setStage('version', $version);
    }

    // if it's a vendor package
    if ($type === Package::TYPE_VENDOR && is_dir($package->getPackagePath())) {
        CommandLine::info(sprintf('Updating vendor package %s.', $name));

        // we need to check from packagist
        $results = (new Packagist())->get($name);

        // if results is empty
        if (!isset($results['packages'][$name])) {
            CommandLine::error('Package does not exists.');
        }

        // load composer file
        $composer = json_decode(
            file_get_contents(
                $this->package('global')->path('root') . '/composer.json'
            ),
            true
        );

        // get the installed version
        $installed = $composer['require'][$name];

        // less than installed version?
        if ($version && version_compare($version, $installed, '<=')) {
            CommandLine::error(sprintf(
                'Specified version must be greater than installed version. %s (target) <= %s (current)',
                $version,
                $installed
            ));
        }

        // the version should exists in packagists
        if ($version && !isset($results['packages'][$name][$version])) {
            CommandLine::error('Specified version does not exists.');
        }

        // if they didn't provide a version
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
                CommandLine::error('Couldn\'t find a valid version.');
            }

            //sort versions, and get the latest one
            usort($versions, 'version_compare');
            $version = array_pop($versions);
        }

        // if the version is the same as the installed version
        if (version_compare($version, $installed, '==')) {
            CommandLine::info('Package is already on it\'s latest version.');
        } else {
            // let them know we're installing via composer
            CommandLine::info(sprintf(
                'Updating the package from %s to %s via composer.',
                $installed,
                $version
            ));

            //increase memory limit
            ini_set('memory_limit', -1);

            // composer needs to know where to place cache files
            $composer = $this->package('global')->path('root') . '/vendor/bin/composer';

            // run composer update command
            (new Command($composer))->require(sprintf('%s:%s', $name, $version));

            // let them install the package manually
            CommandLine::info('Package has been updated.');

            // register the package again
            $package = $this->register($name)->package($name);
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
