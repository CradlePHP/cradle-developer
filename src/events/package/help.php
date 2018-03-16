<?php //-->
/**
 * This file is part of the Cradle PHP Kitchen Sink Faucet Project.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Framework\CommandLine;

/**
 * $ cradle package help
 *
 * @param Request $request
 * @param Response $response
 *
 * @return string
 */
return function ($request, $response) {
    CommandLine::warning('Package Related Commands:');
    CommandLine::output(PHP_EOL);

    CommandLine::success('bin/cradle package install');
    CommandLine::info(' Install a local or packagist.org based package');
    CommandLine::info(' Example: bin/cradle package install cradlephp/address');
    CommandLine::info(' Example: bin/cradle package install /module/address');
    CommandLine::output(PHP_EOL);

    CommandLine::success('bin/cradle package update');
    CommandLine::info(' Update a local or packagist.org based package');
    CommandLine::info(' Example: bin/cradle package update cradlephp/address');
    CommandLine::info(' Example: bin/cradle package update /module/address');
    CommandLine::output(PHP_EOL);

    CommandLine::success('bin/cradle package remove');
    CommandLine::info(' Remove a local or packagist.org based package');
    CommandLine::info(' Example: bin/cradle package remove cradlephp/address');
    CommandLine::info(' Example: bin/cradle package remove /module/address');
    CommandLine::output(PHP_EOL);

    CommandLine::success('bin/cradle package search');
    CommandLine::info(' Search a package from packgist.org');
    CommandLine::info(' Example: bin/cradle package search [query]');
    CommandLine::info(' Example: bin/cradle package search [query] page=1 range=10');
    CommandLine::output(PHP_EOL);

    CommandLine::success('bin/cradle package list');
    CommandLine::info(' List all the active packages of your project');
    CommandLine::info(' Example: bin/cradle package list');
    CommandLine::output(PHP_EOL);
};
