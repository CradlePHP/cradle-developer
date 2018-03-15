<?php //-->
/**
 * This file is part of the Cradle PHP Kitchen Sink Faucet Project.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Framework\CommandLine;
use Cradle\Module\Utility\ServiceFactory;

/**
 * CLI clear index
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

    CommandLine::system('Building ElasticSearch...');

    $objects = array_keys(ServiceFactory::get('elastic'));

    foreach ($objects as $object) {
        CommandLine::info('Indexing ' . $object . '...');

        $sql = ServiceFactory::get($object, 'sql');
        $elastic = ServiceFactory::get($object, 'elastic');

        $i = 0;
        $working = false;
        do {
            CommandLine::info('  - Indexing ' . $object . ': ' . $i . '-' . ($i + 100));

            $results = $sql->search([
                'start' => $i,
                'range' => 100
            ]);

            $rows = $results['rows'];
            $total = $results['total'];

            foreach ($rows as $row) {
                $primary = $object . '_id';

                if ($elastic->create($row[$primary]) === false) {
                    if(!$working) {
                        //because there is no reason to continue;
                        CommandLine::warning('No index server found. Aborting...');
                        return;
                    }

                    CommandLine::warning($row[$primary] . ' failed to insert. Skipping...');
                    continue;
                }

                $working = true;
            }

            $i += 100;
        } while ($i < $total);
    }
};
