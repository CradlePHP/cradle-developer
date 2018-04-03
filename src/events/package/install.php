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

    // get developer package
    $developer = $this->package('cradlephp/cradle-developer');

    // empty package name?
    if (!$name) {
        $developer->packageLog(
            'error',
            'Not enough arguments. Usage: `cradle package install vendor/package`'
        );
    }

    // reset log file
    $developer->packageLog(null, [], $name);

    // valid version?
    if ($version && !preg_match('/^[0-9\.]+$/i', $version)) {
        $developer->packageLog(
            'error',
            'Unable to install package. Version is not valid version format should be 0.0.*.',
            $name,
            'install-error'
        );
    }

    // does the package installed already?
    if ($this->package('global')->config('packages', $name)) {
        // let them update instead
        $developer->packageLog(
            'error',
            sprintf(
                'Package is already installed. Run `cradle package update %s` instead',
                $name
            ),
            $name,
            'install-error'
        );
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
        $developer->packageLog(
            'error',
            sprintf('Can\'t install pseudo package %s.', $name),
            $name,
            'install-error'
        );
    }

    // if it's a root package
    if ($type === Package::TYPE_ROOT) {
        $developer->packageLog(
            'info',
            sprintf('Installing root package %s.', $name),
            $name,
            'install-pending'
        );

        // directory doesn't exists?
        if (!is_dir($package->getPackagePath())) {
            $developer->packageLog(
                'error',
                'Package does not exists.',
                $name,
                'install-error'
            );
        }

        // bootstrap file exists?
        if (!file_exists(
            sprintf(
                '%s/%s',
                $package->getPackagePath(),
                '.cradle.php'
            )
        )) {
            $developer->packageLog(
                'error',
                'Bootstrap file .cradle.php does not exists.',
                $name,
                'install-error'
            );
        }

        // just let the package process the given version
        $request->setStage('version', $version);
    }

    // if it's a vendor package
    if ($type === Package::TYPE_VENDOR) {
        $developer->packageLog(
            'info',
            sprintf('Installing vendor package %s.', $name),
            $name,
            'install-pending'
        );

        // we need to check from packagist
        $results = (new Packagist())->get($name);

        // if results is empty
        if (!isset($results['packages'][$name])) {
            $developer->packageLog(
                'error',
                'Package does not exists from packagists.org.',
                $name,
                'install-error'
            );
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
                $developer->packageLog(
                    'error', 
                    'Couldn\'t find a valid version.',
                    $name,
                    'install-error'
                );
            }

            //sort versions, and get the latest one
            usort($versions, 'version_compare');
            $version = array_pop($versions);
        } else {
            // if version does not exists
            if (!isset($results['packages'][$name][$version])) {
                $developer->packageLog(
                    'error',
                    'Couldn\'t find the provided version.',
                    $name,
                    'install-error'
                );
            }
        }

        // let them know we're installing via composer
        $developer->packageLog(
            'info',
            sprintf(
                'Installing package version %s via composer.',
                $version
            ),
            $name,
            'install-pending'
        );

        //increase memory limit
        ini_set('memory_limit', -1);

        // composer needs to know where to place cache files
        $composer = $this->package('global')->path('root') . '/vendor/bin/composer';

        // run composer require command
        (new Command($composer))
            // set our custom output handler
            ->setOutputHandler(function($message, $newline) use ($developer, $name) {
                // log composer output
                $developer->packageLog('info', $message, $name);
            })
            // require the package
            ->require(sprintf('%s:%s', $name, $version));

        // let them install the package manually
        $developer->packageLog('info', 'Package has been installed.', $name);

        // register the package again
        $package = $this->register($name)->package($name);
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
    // $this->trigger($event, $request, $response);

    // if no event was triggered
    $status = $this->getEventHandler()->getMeta();
    if($status === EventHandler::STATUS_NOT_FOUND) {
        return;
    }

    // if error
    if ($response->isError()) {
        $developer->packageLog(
            'error',
            $response->getMessage(), 
            $name,
            'install-error'
        );
        
        return;
    }

    // if version
    if ($response->hasResults('version')) {
        // this means that the package itself updates it's version
        $version = $response->getResults('version');
        $developer->packageLog(
            'success', 
            sprintf('%s was installed to %s', $name, $version), 
            $name,
            'install-success'
        );

        return;
    }

    $developer->packageLog(
        'success',
        sprintf('%s was installed', $name),
        $name,
        'install-success'
    );
};
