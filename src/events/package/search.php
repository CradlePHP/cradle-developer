<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Framework\CommandLine;
use Cradle\Composer\Packagist;

/**
 * $ cradle package search foo/bar
 *
 * @param Request $request
 * @param Response $response
 */
return function($request, $response) {
    // get developer package
    $developer = $this->package('cradlephp/cradle-developer');

    $developer->packageLog('info', 'Searching for packages...');

    // initialize packagist
    $packagist = new Packagist();

    // query set?
    if ($request->hasStage(0)) {
        // set query
        $packagist->setQuery($request->getStage(0));
    } else {
        // set default query
        $packagist->setQuery('');
    }

    // type set?
    if ($request->hasStage('type')) {
        // set type
        $packagist->setType($request->getStage('type'));
    } else {
        // set default type
        $packagist->setType('cradle-package');
    }

    // page set?
    if ($request->hasStage('page')) {
        // set page
        $packagist->setPage($request->getStage('page'));
    } else {
        // set default page
        $packagist->setPage(1);
    }

    // range set?
    if ($request->hasStage('range')) {
        // set range
        $packagist->setPerPage($request->getStage('range'));
    } else {
        // set default range
        $packagist->setPerPage(10);
    }

    // get the packages
    $packages = $packagist->search();

    // if we have packages
    if (isset($packages['results'])
    && !empty($packages['results'])) {
        $developer->packageLog('info', sprintf('Found %s package(s).', $packages['total']));

        if ($request->hasStage('data')) {
            // set response
            $response
                ->setError(false)
                ->set('json', $packages);
        }
  
        // print each package
        foreach($packages['results'] as $package) {
            $developer->packageLog('success', sprintf(
                '* %s - %s',
                $package['name'],
                $package['description']
            ));
        }

        $developer->packageLog('warning', 'Run: `cradle package install vendor/package` to install a package.');
        return;
    }

    $developer->packageLog('error', 'No package(s) found');
};
