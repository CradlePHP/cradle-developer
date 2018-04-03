<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Framework\Package;
use Cradle\Framework\CommandLine;
use Cradle\Event\EventHandler;
use Cradle\Composer\Command;

/**
 * $ cradle package remove foo/bar
 *
 * @param Request $request
 * @param Response $response
 */
return function($request, $response) {
    // get the package name
    $name = $request->getStage(0);

    // get developer package
    $developer = $this->package('cradlephp/cradle-developer');

    // empty package name?
    if (!$name) {
        $developer->packageLog(
            'error',
            'Not enough arguments. Usage: `cradle package remove vendor/package`'
        );
    }

    // reset log file
    $developer->packageLog(null, [], $name);

    // if package is not installed
    if (!$this->package('global')->config('packages', $name)) {
        // let them update instead
        $developer->packageLog(
            'error',
            sprintf(
                'Package is not yet installed. Run `cradle package install %s` instead.',
                $name
            ),
            $name,
            'remove-error'
        );
    }

    // get active packages
    $packages = $this->getPackages();

    // get pacakge
    $package = $this->package($name);
    
    // get the packaage type
    $type = $package->getPackageType();

    // if it's a pseudo package
    if ($type === Package::TYPE_PSEUDO) {
        $developer->packageLog(
            'error',
            sprintf(
                'Can\'t remove pseudo package %s.',
                $name
            ),
            $name,
            'remove-error'
        );
    }

    // if it's a root package
    if ($type === Package::TYPE_ROOT) {
        $developer->packageLog(
            'info',
            sprintf('Removing root package %s.', $name),
            $name,
            'remove-pending'
        );

        // directory doesn't exists?
        if (!is_dir($package->getPackagePath())) {
            $developer->packageLog(
                'error',
                'Package does not exists.',
                $name,
                'remove-error'
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
                'remove-error'
            );
        }
    }

    // NOTE: If the package is a vendor package
    // we need to trigger the package remove events
    // first before proceeding to the actual vendor
    // removal.

    // get original config
    $config = $this->package('global')->config('packages', $name);

    // if it's a vendor package
    if ($type === Package::TYPE_VENDOR) {
        list($vendor, $namespace) = explode('/', $name, 2);
    } else {
        //it's a root package
        list($vendor, $namespace) = explode('/', substr($name, 1), 2);
    }

    // trigger event
    $event = sprintf('%s-%s-%s', $vendor, $namespace, 'remove');
    $this->trigger($event, $request, $response);

    // if no event was triggered
    $status = $this->getEventHandler()->getMeta();
    if($status === EventHandler::STATUS_NOT_FOUND) {
        // let them know that no event was triggered and we should proceed
        $developer->packageLog(
            'warning',
            sprintf(
                'Package %s has no package remove handler. Skipping.',
                $name
            ),
            $name
        );
    }

    // if error
    if ($response->isError()) {
        // bring back the config
        $this->package('global')->config('packages', $name, $config);

        $developer->packageLog(
            'error', 
            $response->getMessage(), 
            $name,
            'remove-error'
        );
        
        return;
    }

    // just ignore package related errors and proceed to package removal
    if ($type === Package::TYPE_VENDOR && is_dir($package->getPackagePath())) {
        $developer->packageLog(
            'info',
            sprintf('Removing vendor package %s.', $name),
            $name,
            'remove-pending'
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
            // remove the package
            ->remove(sprintf('%s', $name));

        $developer->packageLog(
            'success',
            'Package has been removed',
            $name,
            'remove-success'
        );

        return;
    }

    $developer->packageLog(
        'success',
        'Package has been removed',
        $name,
        'remove-success'
    );
};
