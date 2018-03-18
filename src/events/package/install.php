<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Framework\CommandLine;
use Cradle\Event\EventHandler;
use Cradle\Composer\Command;
use Cradle\Composer\Packagist;
use Cradle\Package\System\Package;
use Cradle\Curl\Rest;

/**
 * $ cradle package install foo/bar
 *
 * @param Request $request
 * @param Response $response
 */
return function($request, $response) {
    //this is the package name
    $name = $request->getStage(0);

    //these are all the active packages
    $active = $this->getPackages();

    //it's already installed
    if ($this->package('global')->config('versions', $name)) {
        //CTA to call update instead
        CommandLine::error(sprintf(
            'Package is already installed. run `cradle package update %s` instead',
            $name
        ));
    }

    // if package is not yet installed
    if (!isset($active[$name])) {
        // temporarily register the package
        $package = $this->register($name)->package($name);
    } else {
        // load the package space
        $package = $this->package($name);
    }

    // get current version
    $current = $package->getPackageVersion();
    // get available version
    $available = $current;

    //if available is 0.0.0
    if ($current === '0.0.0') {
        CommandLine::info(sprintf(
            '%s was not found in your project. Trying to search from packagist.org...',
            $name
        ));

        //it means it doesn't exists
        //and we should packagist search
        $results = (new Packagist())->get($name);

        //no package?
        if (!isset($results['packages'][$name])) {
            CommandLine::error(sprintf(
                '%s was not found in your project or on packagist.org',
                $name
            ));
        }

        $versions = [];
        foreach($results['packages'][$name] as $version => $info) {
            if (preg_match('/^[0-9\.]+$/i', $version)) {
                $versions[] = $version;
            }
        }

        //no versions?
        if (empty($versions)) {
            CommandLine::error(sprintf(
                'Could not find a valid version for %s',
                $name
            ));
        }

        //sort versions
        usort($versions, 'version_compare');
        $available = array_pop($versions);

        CommandLine::info(sprintf(
            '%s@%s was found. Installing package via composer...',
            $name,
            $available
        ));

        //increase memory limit
        ini_set('memory_limit', -1);

        // get the composer home, composer needs to
        // know where to place their cache files...
        $home = dirname(__DIR__) . '/../../../../bin/composer';

        // run composer require command
        (new Command($home))->require(sprintf('%s:%s', $name, $available));

        // re-register the package to load the package install events
        $package = $this->register($name)->package($name);
    }

    // path is name
    $path = $name;

    // local package?
    if (strpos($path, '/') === 0) {
        // remove trailing /
        $path = substr($path, 1);
    }

    // get author and package
    list($author, $package) = explode('/', $path, 2);
    // formulate event handler
    $event = sprintf('%s-%s-install', $author, $package);
    // trigger event handler
    $this->trigger($event, $request, $response);

    //event result cases
    switch($this->getEventHandler()->getMeta()) {
        case EventHandler::STATUS_INCOMPLETE:
            return;
        case EventHandler::STATUS_OK:
        case EventHandler::STATUS_NOT_FOUND:
        default:
            break;
    }

    // install package
    $version = Package::install($name, $current);

    // update the config
    $this->package('global')->config('versions', $name, $version);
    CommandLine::info($name . ' was updated to ' . $available);
};
