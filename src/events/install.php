<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Framework\CommandLine;
use Cradle\Storm\SqlFactory;

/**
 * CLI faucet installation
 *
 * @param Request $request
 * @param Response $response
 */
return function ($request, $response) {
    //schema should be writeable
    $folder = $this->package('global')->path('config') . '/schema';
    if (!is_dir($folder) || !is_writable($folder)) {
        CommandLine::error(sprintf(
            '%s is not writable Try `chmod -R 777 %s` first',
            $folder,
            $folder
        ));
    }

    //whether to ask questions
    $force = $request->hasStage('f') || $request->hasStage('force');

    //name
    $name = false;
    if ($request->hasStage(0)) {
        $name = $request->getStage(0);
    }

    if (!$name) {
        if (!$force) {
            $name = CommandLine::input('What is the name of the SQL database to install?(testing_db)', 'testing_db');
        } else {
            $name = 'testing_db';
        }
    }

    //host
    $host = false;
    if ($request->hasStage('h')) {
        $host = $request->getStage('h');
    } else if ($request->hasStage('host')) {
        $host = $request->getStage('host');
    }

    if (!$host) {
        if (!$force) {
            $host = CommandLine::input('What is the SQL server address?(127.0.0.1)', '127.0.0.1');
        } else {
            $host = '127.0.0.1';
        }
    }

    //user
    $user = false;
    if ($request->hasStage('u')) {
        $user = $request->getStage('u');
    } else if ($request->hasStage('user')) {
        $user = $request->getStage('user');
    }

    if (!$user) {
        if (!$force) {
            $user = CommandLine::input('What is the SQL server user name?(root)', 'root');
        } else {
            $user = 'root';
        }
    }

    //pass
    $pass = false;
    if ($request->hasStage('p')) {
        $pass = $request->getStage('p');
    } else if ($request->hasStage('password')) {
        $pass = $request->getStage('password');
    }

    if (!$pass) {
        if (!$force) {
            $pass = CommandLine::input('What is the SQL server password?(enter for none)', '');
        } else {
            $pass = '';
        }
    }

    //setup the configs
    $cwd = getcwd();
    if(!$request->hasStage('skip-configs')) {
        CommandLine::system('Setting up config files...');

        $paths = scandir(__DIR__ . '/../template/config');
        foreach($paths as $path) {
            if($path === '.' || $path === '..' || substr($path, -4) !== '.php') {
                continue;
            }

            $source = __DIR__ . '/../template/config/' . $path;
            $destination = $cwd . '/config/' . $path;

            if (file_exists($destination) && !$force) {
                $answer = CommandLine::input('Overwrite config/' . $path . '?(y)', 'y');
                if ($answer !== 'y') {
                    CommandLine::system('Skipping...');
                    continue;
                }
            }

            $contents = file_get_contents($source);
            $contents = str_replace('<DATABASE HOST>', $host, $contents);
            $contents = str_replace('<DATABASE NAME>', $name, $contents);
            $contents = str_replace('<DATABASE USER>', $user, $contents);
            $contents = str_replace('<DATABASE PASS>', $pass, $contents);

            file_put_contents($destination, $contents);
        }
    }

    //create compiled, log, public/upload, config/schema
    if(!$request->hasStage('skip-mkdir')) {
        if (!is_dir($cwd . '/compiled')) {
            CommandLine::system('Making ' . $cwd . '/compiled');
            mkdir($cwd . '/compiled', 0777);
        }

        if (!is_dir($cwd . '/log')) {
            CommandLine::system('Making ' . $cwd . '/log');
            mkdir($cwd . '/log', 0777);
        }

        if (!is_dir($cwd . '/public/upload')) {
            CommandLine::system('Making ' . $cwd . '/public/upload');
            mkdir($cwd . '/public/upload', 0777);
        }

        if (!is_dir($cwd . '/config/schema')) {
            CommandLine::system('Making ' . $cwd . '/config/schema');
            mkdir($cwd . '/config/schema', 0777);
        }

        if (!is_dir($cwd . '/config/packages')) {
            CommandLine::system('Making ' . $cwd . '/config/packages');
            mkdir($cwd . '/config/packages', 0777);
        }
    }

    //chmod compiled, log, config, public/upload
    if(!$request->hasStage('skip-chmod')) {
        // special case for config folder
        $configDirectories = glob($cwd . '/config/*', GLOB_ONLYDIR);

        // map each directories
        foreach($configDirectories as $directory) {
            CommandLine::system('chmoding ' . $directory);
            chmod($directory, 0777);
        }

        if (is_dir($cwd . '/compiled')) {
            CommandLine::system('chmoding ' . $cwd . '/compiled');
            chmod($cwd . '/compiled', 0777);
        }

        if (is_dir($cwd . '/log')) {
            CommandLine::system('chmoding ' . $cwd . '/log');
            chmod($cwd . '/log', 0777);
        }

        if (is_dir($cwd . '/public/upload')) {
            CommandLine::system('chmoding ' . $cwd . '/public/upload');
            chmod($cwd . '/public/upload', 0777);
        }
    }

    if(!$request->hasStage('skip-sql')) {
        //SQL
        CommandLine::system('Setting up SQL...');

        //connection
        $build = SqlFactory::load(new PDO('mysql:host=' . $host, $user, $pass));
        $exists = $build->query("SHOW DATABASES LIKE '" . $name . "';");

        $continue = false;
        if (!empty($exists) && !$force) {
            $answer = CommandLine::input('This will override your existing database. Are you sure?(y)', 'y');
            if ($answer === 'y') {
                $continue = true;
            }
        }

        if (empty($exists) || $continue || $force) {
            CommandLine::system('Installing Database...');

            $build->query('CREATE DATABASE IF NOT EXISTS `' . $name . '`;');

            $database = SqlFactory::load(new PDO('mysql:host=' . $host . ';dbname=' . $name, $user, $pass));

            //drop all tables
            $tables = $database->getTables();
            foreach ($tables as $table) {
                $database->query('DROP TABLE `' . $table . '`;');
            }
        }
    }

    if(!$request->hasStage('skip-versioning')) {
        // copy the default packages if it doesn't exists
        if (!$this->package('global')->config('packages')) {
            // get sample package config
            $sample = $this->package('global')->config('packages.sample');

            // save the default packages from sample
            $this->package('global')->config('packages', $sample);

        // reset the version from all the packages
        } else {
            // get the packages
            $packages = $this->package('global')->config('packages');

            // on each packages
            foreach($packages as $package => $config) {
                // reset the version so we can re-install again
                if (isset($config['version'])) {
                    unset($packages[$package]['version']);
                }
            }

            // update the config
            $this->package('global')->config('packages', $packages);
        }

        //now run the update
        $this->trigger('update', $request, $response);
    }

    if (!$response->isError()) {
        CommandLine::info('Recommended actions:');
        CommandLine::info(' - bin/cradle sql populate');
        CommandLine::info(' - yarn build');
    }
};
