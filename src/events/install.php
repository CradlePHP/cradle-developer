<?php //-->
/**
 * This file is part of the Cradle PHP Kitchen Sink Faucet Project.
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
    $cwd = $request->getServer('PWD');
    if(!$request->hasStage('skip-configs')) {
        CommandLine::system('Setting up config files...');


        $paths = scandir(__DIR__ . '/config', 0);
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
        $file = cradle('global')->path('config') . '/version.php';
        //if there's a version file
        if(file_exists($file)) {
            //this is an install process so reset the versions
            file_put_contents($file, '<?php return [];');
        }

        //now run the update
        $this->trigger('developer-update', $request, $response);
    }

    CommandLine::info('Recommended actions:');
    CommandLine::info(' - yarn build');
    CommandLine::info(' - bin/cradle faucet populate-sql');
    CommandLine::info(' - bin/cradle faucet flush-elastic');
    CommandLine::info(' - bin/cradle faucet map-elastic');
    CommandLine::info(' - bin/cradle faucet populate-elastic');
};
