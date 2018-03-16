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
use Predis\Connection\ConnectionException;

/**
 * $ cradle redis flush
 * $ cradle redis flush package=foo/bar
 *
 * @param Request $request
 * @param Response $response
 */
return function ($request, $response) {
    $service = $this->package('global')->service('redis-main');

    if (!$service) {
        CommandLine::error('Redis is not enabled. Check config/services.php');
        return;
    }

    CommandLine::system('Flushing Redis...');

    // keys
    $keys = $service->keys('*');

    //whether to ask questions
    $force = $request->hasStage('f') || $request->hasStage('force');

    $continue = true;
    if (!empty($keys) && !$force) {
        $answer = CommandLine::input('This will flush all the keys on your redis server. Are you sure?(y)', 'y');
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
        $event = sprintf('%s-%s-flush-redis', $author, $package);
        $this->trigger($event, $request, $response);

        if($this->getEventHandler()->getMeta() === EventHandler::STATUS_NOT_FOUND) {
            CommandLine::warning(sprintf('%s does not have a flush Redis handler. Skipping.', $name));
        }

        return;
    }

    // on each keys
    foreach($keys as $key) {
        CommandLine::info(sprintf('Flushing %s', $key));

        try {
            // delete key
            $service->del($key);
        } catch(ConnectionException $e) {
            //because there is no reason to continue
            CommandLine::warning('No cache server found. Aborting...');
        }
    }
};
