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

/**
 * CLI populates database with dummy data
 *
 * @param Request $request
 * @param Response $response
 */
return function ($request, $response) {
    CommandLine::system('Building SQL...');

    //whether to ask questions
    $force = $request->hasStage('f') || $request->hasStage('force');

    $continue = true;
    if (!empty($tables) && !$force) {
        $answer = CommandLine::input('This will override tables in your existing database. Are you sure?(y)', 'y');
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

    //if we just want to populate one package
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

        $packages = [ $package => $packages[$package] ];
    }

    //loop through the packages
    foreach ($packages as $name => $package) {
        $type = $package->getPackageType();
        //skip pseudo packages
        if ($type === 'pseudo') {
            CommandLine::warning(sprintf('Skipping %s', $name));
            continue;
        }

        //path is name
        $path = $name;
        if ($type === 'root') {
            $path = substr($path, 1);
        }

        CommandLine::info(sprintf('Building %s', $name));
        list($author, $package) = explode('/', $path, 2);
        $event = sprintf('%s-%s-build-sql', $author, $package);
        $this->trigger($event, $request, $response);

        switch ($this->getEventHandler()->getMeta()) {
            case EventHandler::STATUS_NOT_FOUND:
                CommandLine::warning(sprintf('%s does not have a build SQL handler. Skipping.', $name));
                break;
            case EventHandler::STATUS_OK:
            case EventHandler::STATUS_INCOMPLETE:
            default:
                CommandLine::success(sprintf('%s SQL was built.', $name));
                break;
        }
    }
};
