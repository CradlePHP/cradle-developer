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
use Cradle\Storm\SqlFactory;

/**
 * $ cradle sql flush
 * $ cradle sql flush package=foo/bar
 *
 * @param Request $request
 * @param Response $response
 */
return function ($request, $response) {
    CommandLine::system('Flushing SQL...');

    $service = $this->package('global')->service('sql-main');
    $database = SqlFactory::load($service);

    //truncate all tables
    $tables = $database->getTables();

    //whether to ask questions
    $force = $request->hasStage('f') || $request->hasStage('force');

    $continue = true;
    if (!empty($tables) && !$force) {
        $answer = CommandLine::input('This will truncate tables in your existing database. Are you sure?(y)', 'y');
        if ($answer !== 'y') {
            $continue = false;
        }
    }

    if (!$continue) {
        CommandLine::warning('Aborting...');
        return;
    }

    //we only want to consider active packages
    $packages = $this->getPackages();

    //if we just want to flush one package
    if($request->hasStage('package')) {
        $name = $request->getStage('package');

        //if it is not an installed package
        if (!isset($packages[$name])) {
            CommandLine::error(sprintf(
                '%s is not installed. Try `$ cradle %s install`',
                $name,
                $name
            ));
        }

        $type = $packages[$name]->getPackageType();
        //skip pseudo packages
        if ($type === 'pseudo') {
            CommandLine::warning(sprintf('Skipping %s', $name));
            return;
        }

        //path is name
        $path = $name;
        if ($type === 'root') {
            $path = substr($path, 1);
        }

        CommandLine::info(sprintf('Flushing %s', $name));
        list($author, $package) = explode('/', $path, 2);
        $event = sprintf('%s-%s-sql-flush', $author, $package);
        $this->trigger($event, $request, $response);

        if($this->getEventHandler()->getMeta() === EventHandler::STATUS_NOT_FOUND) {
            CommandLine::warning(sprintf('%s does not have a flush SQL handler. Skipping.', $name));
        }

        return;
    }

    // iterate on each tables
    foreach ($tables as $table) {
        CommandLine::info(sprintf('Flushing %s', $table));
        $database->query('TRUNCATE TABLE `' . $table . '`;');
    }
};
