<?php //-->
/**
 * This file is part of the Cradle PHP Kitchen Sink Faucet Project.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Framework\CommandLine;
use Predis\Connection\ConnectionException;

/**
 * CLI clear cache 
 *
 * @param Request $request
 * @param Response $response
 */
return function ($request, $response) {
    $service = $this->package('global')->service('redis-main');

    if (!$service) {
        CommandLine::error('Cache is not enabled. Check config/services.php');
        return;
    }

    CommandLine::system('Flushing Redis...');

    try {
        $service->flushAll();
    } catch (ConnectionException $e) {
        //because there is no reason to continue
        CommandLine::warning('No cache server found. Aborting...');
    }
};
