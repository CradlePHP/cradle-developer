<?php //-->
/**
 * This file is part of the Cradle PHP Kitchen Sink Faucet Project.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Framework\CommandLine;
use Cradle\Event\EventHandler;
use Cradle\Storm\SqlFactory;

/**
 * CLI clear index
 *
 * @param Request $request
 * @param Response $response
 */
return function ($request, $response) {
    CommandLine::system('Flushing SQL...');

    //we only want to consider active packages
    $packages = $this->getPackages();

    //if we just want to flush one package
    if($request->hasStage('package')) {
        $package = $request->getStage('package');
        //if it is not an installed package
        if (!isset($packages[$package])) {
            CommandLine::error(sprintf(
                '%s is not installed. Try `$ cradle %s install`',
                $package,
                $package
            ));
        }

        $type = $package->getPackageType();
        //skip pseudo packages
        if ($type === 'pseudo') {
            CommandLine::warning(sprintf('Skipping %s', $package));
            return;
        }

        //path is name
        $path = $name;
        if ($type === 'root') {
            $path = substr($path, 1);
        }

        CommandLine::info(sprintf('Flushing %s', $name));
        list($author, $package) = explode('/', $path, 2);
        $event = sprintf('%s-%s-flush-sql', $author, $package);
        $this->trigger($event, $request, $response);

        if($this->getEventHandler()->getMeta() === EventHandler::STATUS_NOT_FOUND) {
            CommandLine::warning(sprintf('%s does not have a flush SQL handler. Skipping.', $name));
        }

        return;
    }

    $service = $this->package('global')->service('sql-main');
    $database = SqlFactory::load($service);

    //truncate all tables
    $tables = $database->getTables();
    foreach ($tables as $table) {
        CommandLine::info(sprintf('Flushing %s', $table));
        $database->query('TRUNCATE TABLE `' . $table . '`;');
    }
};
