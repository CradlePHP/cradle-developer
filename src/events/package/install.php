<?php //-->

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

    //these are all the installed packages
    $installed = $this->package('global')->config('packages', 'installed');

    //it's already installed
    if (isset($installed[$name])) {
        //CTA to call update instead
        CommandLine::error(sprintf(
            'Package is already installed. run `cradle package update %s` instead',
            $name
        ));
    }

    // if package is not yet installed
    if (!$this->isPackage($name)) {
        // temporarily register the package
        $package = $this->register($name)->package($name);
    } else {
        // load the package space
        $package = $this->package($name);    
    }

    $available = $package->getPackageVersion();
    $active = isset($active[$name]);

    //if available is 0.0.0
    if ($available === '0.0.0') {
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
            if (preg_match('/^[0-9.]+$/i', $version)) {
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

        // get the composer home, composer needs to
        // know where to place their cache files...
        $home = dirname(__DIR__) . '/../../../../bin/composer';

        // run composer install command
        (new Command($home))->require(sprintf('%s:%s', $name, $available));
    }

    CommandLine::info('Installing ' . $name . ' -> ' . $available);

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
    $event = sprintf('%s-%s-package-install', $author, $package);
    // trigger event handler
    $this->trigger($event, $request, $response);

    // handler does not exists?
    if ($this->getEventHandler()->getMeta() === EventHandler::STATUS_NOT_FOUND) {
        CommandLine::warning(sprintf('%s does not have a package install handler. Skipping.', $name));
    }

    // install package
    $installed[$name] = Package::install($name, $available);

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
