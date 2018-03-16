<?php //-->

use Cradle\Framework\CommandLine;
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

    $package = Package::getMeta($name);
    $package['available'] = Package::getAvailableVersion($name);
    $package['active'] = isset($active[$name]);

    //if available is 0.0.0
    if ($package['available'] === '0.0.0') {
        //it means it doesn't exists
        //and we should packagist search
        $results = Rest::i('https://packagist.org/p')->get($name . '.json');

        //no package?
        if (!isset($results['packages'][$name])) {
            CommandLine::error(sprintf(
                '%s was not found in your project or on packagist.org',
                $name
            ));
        }

        $versions = [];
        foreach($results['packages'][$name] as $version => $info) {
            if (preg_match('/^[0-9\.]$/', $version)) {
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
        $package['available'] = array_pop($versions);

        //run composer install
    }

    CommandLine::info('Installing ' . $package['name'] . ' -> ' . $package['available']);

    //$installed[$package['name']] = Package::install($package['name'], $package['available']);

    $file = $this->package('global')->path('config') . '/packages/installed.php';
    $content = "<?php //-->\nreturn ".var_export($installed, true);
    //file_put_contents($file, $content);
};
