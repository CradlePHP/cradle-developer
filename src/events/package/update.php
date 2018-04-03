<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

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

    // get developer package
    $developer = $this->package('cradlephp/cradle-developer');

    // empty package name?
    if (!$name) {
        $developer->packageLog(
            'error',
            'Not enough arguments. Usage: `cradle package update vendor/package`'
        );
    }

    // reset log file
    $developer->packageLog(null, [], $name);

    // valid version?
    if ($version && !preg_match('/^[0-9\.]+$/i', $version)) {
        $developer->packageLog(
            'error',
            'Version format is not valid.',
            $name,
            'update-error'
        );
    }

    // does the package installed already?
    if (!$this->package('global')->config('packages', $name)) {
        // let them update instead
        $developer->packageLog(
            'error',
            sprintf(
                'Package is not yet installed. Run `cradle package install %s` instead.',
                $name
            ),
            $name,
            'update-error'
        );
    }

    // get the package
    $package = $this->package($name);

    // get the packaage type
    $type = $package->getPackageType();

    // if it's a pseudo package
    if ($type === Package::TYPE_PSEUDO) {
        $developer->packageLog(
            'error',
            sprintf(
                'Can\'t update pseudo package %s.',
                $name
            ),
            $name,
            'update-error'
        );
    }

    // if it's a root package
    if ($type === Package::TYPE_ROOT) {
        $developer->packageLog(
            'info', 
            sprintf('Updating root package %s.', $name), 
            $name,
            'update-pending'
        );

        // directory doesn't exists?
        if (!is_dir($package->getPackagePath())) {
            $developer->packageLog(
                'error', 
                'Package does not exists.', 
                $name, 
                'update-error'
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
                'warning', 
                'Bootstrap file .cradle.php does not exists.', 
                $name
            );
        }

        // just let the package process the given version
        $request->setStage('version', $version);
    }

    // if it's a vendor package
    if ($type === Package::TYPE_VENDOR && is_dir($package->getPackagePath())) {
        $developer->packageLog(
            'info',
            sprintf('Updating vendor package %s.', $name),
            $name,
            'update-pending'
        );

        // we need to check from packagist
        $results = (new Packagist())->get($name);

        // if results is empty
        if (!isset($results['packages'][$name])) {
            $developer->packageLog(
                'error',
                'Package does not exists.',
                $name,
                'update-error'
            );
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
            $developer->packageLog(
                'error',
                sprintf(
                    'Specified version must be greater than installed version. %s (target) <= %s (current)',
                    $version,
                    $installed
                ),
                $name,
                'update-error'
            );
        }

        // the version should exists in packagists
        if ($version && !isset($results['packages'][$name][$version])) {
            $developer->packageLog(
                'error',
                'Specified version does not exists.',
                $name,
                'update-error'
            );
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
                $developer->packageLog(
                    'error',
                    'Couldn\'t find a valid version.',
                    $name,
                    'update-error'
                );
            }

            //sort versions, and get the latest one
            usort($versions, 'version_compare');
            $version = array_pop($versions);
        }

        // if the version is the same as the installed version
        if (version_compare($version, $installed, '==')) {
            $developer->packageLog(
                'error', 
                'Package is already on it\'s latest version.',
                $name,
                'update-error'
            );
        } else {
            // let them know we're installing via composer
            $developer->packageLog(
                'info',
                sprintf(
                    'Updating the package from %s to %s via composer.',
                    $installed,
                    $version
                ),
                $name,
                'update-pending'
            );

            //increase memory limit
            ini_set('memory_limit', -1);

            // composer needs to know where to place cache files
            $composer = $this->package('global')->path('root') . '/vendor/bin/composer';

            // run composer update command
            (new Command($composer))
                // set our custom output handler
                ->setOutputHandler(function($message, $newline) use ($developer, $name) {
                    // log composer output
                    $developer->packageLog('info', $message, $name);
                })
                // require the package
                ->require(sprintf('%s:%s', $name, $version));

            // let them install the package manually
            $developer->packageLog('info', 'Package has been updated.', $name);
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
        $developer->packageLog('error', $response->getMessage(), $name, 'update-error');
        return;
    }

    // if version
    if ($response->hasResults('version')) {
        // this means that the package itself updates it's version
        $version = $response->getResults('version');
        
        $developer->packageLog(
            'success',
            sprintf('%s was updated to %s', $name, $version),
            $name,
            'update-success'
        );

        return;
    }

    $developer->packageLog(
        'success',
        sprintf('%s was updated', $name),
        $name,
        'update-success'
    );
};
