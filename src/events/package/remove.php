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

    // empty package name?
    if (!$name) {
        CommandLine::error(
            'Not enough arguments. Usage: `cradle package remove vendor/package`'
        );
    }

    // if package is not installed
    if (!$this->package('global')->config('packages', $name)) {
        // let them update instead
        CommandLine::error(sprintf(
            'Unable to remove package %s. Package is not installed.',
            $name
        ));
    }

    // get active packages
    $packages = $this->getPackages();

    // get pacakge
    $package = $this->package($name);
    
    // get the packaage type
    $type = $package->getPackageType();

    // if it's a pseudo package
    if ($type === Package::TYPE_PSEUDO) {
        CommandLine::error(sprintf(
            'Unable to remove pseudo package %s.',
            $name
        ));
    }

    // if it's a root package
    if ($type === Package::TYPE_ROOT) {
        // directory doesn't exists?
        if (!is_dir($package->getPackagePath())) {
            CommandLine::error(sprintf(
                'Unable to remove package. Root package %s does not exists.',
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
                'Unable to remove root package %s. Bootstrap file .cradle.php does not exists.',
                $name
            ));
        }
    }

    // NOTE: If the package is a vendor package
    // we need to trigger the package remove events
    // first before proceeding to the actual vendor
    // removal.

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
        CommandLine::warning(sprintf(
            'Package %s has no package remove handler. Skipping.',
            $name
        ));
    }

    // if error
    if ($response->isError()) {
        CommandLine::error($response->getMessage(), false);
        return;
    }

    // just ignore package related errors and proceed to package removal
    if ($type === Package::TYPE_VENDOR && is_dir($package->getPackagePath())) {
        //increase memory limit
        ini_set('memory_limit', -1);

        // composer needs to know where to place cache files
        $composer = $this->package('global')->path('root') . '/vendor/bin/composer';

        // run composer require command
        (new Command($composer))->remove(sprintf('%s', $name));

        CommandLine::success(sprintf(
            'Package %s has been removed from your vendor packages.',
            $name
        ));
    }
};
