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
    //this is the package name
    $name = $request->getStage(0);

    //these are all the active packages
    $active = $this->getPackages();

    //these are all the installed packages
    $installed = $this->package('global')->config('packages/installed');

    // if installed is empty
    if (!is_array($installed)) {
        $installed = [];
    }

    //it's not installed
    if (!isset($installed[$name])) {
        //CTA to call update instead
        CommandLine::error(sprintf(
            'Package is not installed run `cradle package install %s` to install the package',
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

    // NOTE: Need to trigger the package remove events
    // first before we proceed to the actual composer
    // package uninstallation...
    // get author and package
    CommandLine::info(sprintf('Removing package %s', $name));

    // path is name
    $path = $name;

    // local package?
    if (strpos($path, '/') === 0) {
        // remove trailing /
        $path = substr($path, 1);
    }

    list($author, $packageName) = explode('/', $path, 2);
    // formulate event handler
    $event = sprintf('%s-%s-package-remove', $author, $packageName);
    // trigger event handler
    $this->trigger($event, $request, $response);

    // handler does not exists?
    if ($this->getEventHandler()->getMeta() === EventHandler::STATUS_NOT_FOUND) {
        CommandLine::warning(sprintf('%s does not have a package remove handler. Skipping.', $name));
    }

    // vendor package?
    if ($package->getPackageType() == Package::TYPE_VENDOR) {
        CommandLine::info('Removing the package from composer...');

        // get the composer home, composer needs to
        // know where to place their cache files...
        $home = dirname(__DIR__) . '/../../../../bin/composer';

        // run composer require command
        (new Command($home))->remove(sprintf('%s', $name));
    }

    // if current is installed
    if (isset($installed[$name])) {
        // remove from installed config
        unset($installed[$name]);
    }

    // get packages config path
    $file = $this->package('global')->path('config') . '/packages';

    // directory not exits?
    if (!is_dir($file)) {
        // make it ..
        mkdir($file, 0777);
    }

    // set installed file config
    $file .= '/installed.php';

    // update the config
    $content = "<?php //-->\nreturn ".var_export($installed, true).';';
    file_put_contents($file, $content);
};
