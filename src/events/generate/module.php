<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Http\Request;
use Cradle\Http\Response;
use Cradle\Framework\CommandLine;
use Cradle\Package\System\Schema;

/**
 * $ cradle generate module foobar
 *
 * @param Request $request
 * @param Response $response
 */
return function (Request $request, Response $repsonse) {
    $name = $request->getStage(0);

    //no name?
    if (!$name) {
        return CommandLine::error('No name provided. Try `$ cradle generate module foobar`');
    }

    $cwd = $request->getServer('PWD');
    $rootPath = $cwd . '/module/' . $name;
    if (!is_dir($rootPath)) {
        return CommandLine::error(sprintf('%s was not found', $rootPath));
    }

    $schemaPath = $rootPath . '/schema.php';
    if (!file_exists($schemaPath)) {
        $schemaPath = $rootPath . '/schema';
        if (!is_dir($schemaPath)) {
            return CommandLine::error(sprintf('%s or %s was not found', $schemaPath, $schemaPath . '.php'));
        }
    }

    $schemas = [];
    //if it's a file
    if (file_exists($schemaPath)) {
        $schemas = [include $schemaPath];
    //it's a directory
    } else {
        $paths = scandir($schemaPath, 0);

        foreach ($paths as $path) {
            if($path === '.' || $path === '..' || substr($path, -4) !== '.php') {
                continue;
            }

            $schema = $schemaPath . '/' . pathinfo($path, PATHINFO_FILENAME);

            $schemas[] = include $schema;
        }
    }

    CommandLine::system('Generating module...');
    $handlebars = $this->package('global')->handlebars()->setBars('[]');

    $sourcePath = __DIR__ . '/../../template/module';

    foreach ($schemas as $data) {
        $schema = Schema::i($data);
        //get all the files
        $directory = new RecursiveDirectoryIterator($sourcePath);
        $iterator = new RecursiveIteratorIterator($directory);

        foreach ($iterator as $source) {
            //is it a folder ?
            if($source->isDir()) {
                continue;
            }

            //it's a file, determine the destination
            // if /template/module/src/foo/bar/events.php, then /src/foo/bar/events.php
            // if /template/module/.cradle.php, then /.cradle.php
            $path = substr($source->getPathname(), strlen($sourcePath));
            $destination = $rootPath . $path;

            //TODO: Case for multi schema

            //if the destination exists
            if (file_exists($destination)) {
                //ask questions
                $overwrite = CommandLine::input($destination .' exists. Overwrite?(n)', 'n');
                if($overwrite === 'n') {
                    CommandLine::warning('Skipping...');
                    continue;
                }
            }

            CommandLine::info('Making ' . $destination);

            //does it not exist?
            if (!is_dir(dirname($destination))) {
                //then make it
                mkdir(dirname($destination), 0777, true);
            }

            $contents = file_get_contents($source->getPathname());
            $template = $handlebars->compile($contents);
            $contents = $template($data);

            //file_put_contents($destination, $contents);
        }
    }
};
