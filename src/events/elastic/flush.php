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
use Elasticsearch\Common\Exceptions\NoNodesAvailableException;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;

/**
 * $ cradle elastic flush
 * $ cradle elastic flush package=foo/bar
 *
 * @param Request $request
 * @param Response $response
 */
return function ($request, $response) {
    $service = $this->package('global')->service('elastic-main');

    if (!$service) {
        CommandLine::error('ElasticSearch is not enabled. Check config/services.php');
        return;
    }

    CommandLine::system('Flushing Elastic...');

    // indices
    $indices = $service->indices()->get(['index' => '*']);

    //whether to ask questions
    $force = $request->hasStage('f') || $request->hasStage('force');

    $continue = true;
    if (!empty($indices) && !$force) {
        $answer = CommandLine::input('This will truncate all the data in your existing indices. Are you sure?(y)', 'y');
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
        $event = sprintf('%s-%s-flush-elastic', $author, $package);
        $this->trigger($event, $request, $response);

        if($this->getEventHandler()->getMeta() === EventHandler::STATUS_NOT_FOUND) {
            CommandLine::warning(sprintf('%s does not have a flush Elastic handler. Skipping.', $name));
        }

        return;
    }

    // get indices names
    $indices = array_keys($indices);

    // on each indices
    foreach($indices as $index) {
        CommandLine::info(sprintf('Flushing %s', $index));

        try {
            // delete all documents by query
            $service->deleteByQuery([
                'index' => $index,
                'body' => [
                    'query' => [
                        'match_all' => (object) []
                    ]
                ]
            ]);
        } catch(Missing404Exception $e) {
        } catch(NoNodesAvailableException $e) {
            //because there is no reason to continue
            CommandLine::warning('No index server found. Aborting...');
        }
    }
};
