<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Framework\CommandLine;

/**
 * $ cradle package list
 *
 * @param Request $request
 * @param Response $response
 */
return function($request, $response) {
    // load developer package
    $developer = $this->package('cradlephp/cradle-developer');

    $developer->packageLog('info', 'List of available packages...');

    //tmp
    $next = function($path) {
        //if there is no install
        if(!is_dir($path)) {
            return '0.0.1';
        }

        //collect and organize all the versions
        $versions = [];
        $files = scandir($path, 0);
        foreach ($files as $file) {
            if ($file === '.'
                || $file === '..'
                || is_dir($path . '/' . $file)
            )
            {
                continue;
            }

            //get extension
            $extension = pathinfo($file, PATHINFO_EXTENSION);

            if ($extension !== 'php'
                && $extension !== 'sh'
                && $extension !== 'sql'
            ) {
                continue;
            }

            //get base as version
            $version = pathinfo($file, PATHINFO_FILENAME);

            //validate version
            if (!(version_compare($version, '0.0.1', '>=') >= 0)) {
                continue;
            }

            $versions[] = $version;
        }

        if(empty($versions)) {
            return '0.0.1';
        }

        //sort versions
        usort($versions, 'version_compare');

        $current = array_pop($versions);
        $revisions = explode('.', $current);
        $revisions = array_reverse($revisions);

        $found = false;
        foreach($revisions as $i => $revision) {
            if(!is_numeric($revision)) {
                continue;
            }

            $revisions[$i]++;
            $found = true;
            break;
        }

        if(!$found) {
            return $current . '.1';
        }

        $revisions = array_reverse($revisions);
        return implode('.', $revisions);
    };

    $packages = [];

    //these are all the active packages
    $active = $this->getPackages();

    //these are all the installed packages
    $installed = $this->package('global')->config('packages');

    //these are all the module packages
    $folder = $this->package('global')->path('module');
    $modules = scandir($folder);
    foreach ($modules as $package) {
        if ($package === '.'
            || $package === '..'
            || is_file($folder . '/' . $package)
        )
        {
            continue;
        }

        //package name
        $name = '/module/' . $package;
        //available version
        $available = $next($folder . '/' . $package . '/install');
        //current version
        $version = '0.0.0';
        if (isset($installed[$name]) && isset($installed[$name]['version'])) {
            $version = $installed[$name]['version'];
        }

        $packages[$name] = [
            'name' => $name,
            'type' => 'module',
            'version' => $version,
            'available' => $available,
            'active' => isset($active[$name])
        ];
    }

    //these are vendor packages
    $folder = $this->package('global')->path('vendor');
    $file = $this->package('global')->path('root') . '/composer.lock';
    $composer = file_get_contents($file);
    $composer = json_decode($composer, true);
    foreach ($composer['packages'] as $package) {
        if ($package['type'] !== 'cradle-package') {
            continue;
        }

        //package name
        $name = $package['name'];
        //available version
        $available = $next($folder . '/' . $name . '/install');
        //current version
        $version = '0.0.0';
        if (isset($installed[$name]) && isset($installed[$name]['version'])) {
            $version = $installed[$name]['version'];
        }

        $packages[$name] = [
            'name' => $name,
            'type' => 'vendor',
            'version' => $version,
            'available' => $available,
            'active' => isset($active[$name])
        ];
    }

    foreach ($packages as $package) {
        $developer->packageLog(
            'success', 
            '* ' . $package['name'] . '(' . $package['version'] . ') -> ' . $package['available']
        );
    }

    if ($request->hasStage('data')) {
        $response
            ->set('json', 'results', $packages);
    }
};
