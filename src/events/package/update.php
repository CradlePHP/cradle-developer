<?php //-->

use Cradle\Framework\CommandLine;

/**
 * $ cradle package update foo/bar
 *
 * @param Request $request
 * @param Response $response
 */
return function($request, $response) {
    $request->setStage('package', $request->getStage(0));
    //$this->trigger('system-package-update', $request, $response);

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

    //this is the package name
    $name = $request->getStage('package');

    //these are all the active packages
    $active = $this->getPackages();

    //these are all the installed packages
    $installed = $this->package('global')->config('packages', 'installed');

    //current version
    if (!isset($installed[$name])) {
        //CTA to call install instead
        CommandLine::error(sprintf(
            'Package is not installed. run `cradle package install %s` instead',
            $name
        ));
    }

    $version = $installed[$name];

    //is it a module or vendor?
    $type = 'vendor';
    if (strpos($name, '/') === 0) {
        $type = 'module';
    }

    $available = '0.0.1';
    if ($type === 'module') {
        //these are all the module packages
        $folder = $this->package('global')->path('module');
        $modules = scandir($folder);
        foreach ($modules as $package) {
            //package name
            $name = '/module/' . $package;

            if ($package === '.'
                || $package === '..'
                || is_file($folder . '/' . $package)
                || $name !== ('/module/' . $package)
            )
            {
                continue;
            }


            //available version
            $available = $next($folder . '/' . $package . '/install');
            break;
        }
    } else {
        //these are vendor packages
        $folder = $this->package('global')->path('vendor');
        $file = $this->package('global')->path('root') . '/composer.lock';
        $composer = file_get_contents($file);
        $composer = json_decode($composer, true);
        foreach ($composer['packages'] as $package) {
            if ($package['type'] !== 'cradle-package'
                || $name !== $package['name']
            )
            {
                continue;
            }

            //available version
            $available = $next($folder . '/' . $name . '/install');
            break;
        }
    }

    //package data
    $package = [
        'name' => $name,
        'type' => $type,
        'version' => $version,
        'available' => $available,
        'active' => isset($active[$name])
    ];

    CommandLine::info($package['name'] . '(' . $package['version'] . ') -> ' . $package['available']);

    //$installed[$package['name']] = Install::install($package['path'], $package['version'], function($name, $version) {
    //    CommandLine::info(sprintf(
    //        'Updating %s to %s',
    //        $name,
    //        $version,
    //    ));
    //});

    $file = $this->package('global')->path('config') . '/packages/installed.php';
    $content = "<?php //-->\nreturn ".var_export($installed, true);
    //file_put_contents($file, $content);
};
