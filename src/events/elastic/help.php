<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Framework\CommandLine;

/**
 * $ cradle elastic help
 *
 * @param Request $request
 * @param Response $response
 *
 * @return string
 */
return function ($request, $response) {
    CommandLine::warning('ElasticSearch Commands:');
    CommandLine::output(PHP_EOL);

    CommandLine::success('bin/cradle elastic flush');
    CommandLine::info(' Clears ElasticSearch index');
    CommandLine::info(' Example: bin/cradle elastic flush');
    CommandLine::info(' Example: bin/cradle elastic flush package=foo/bar');
    CommandLine::output(PHP_EOL);

    CommandLine::success('bin/cradle elastic build');
    CommandLine::info(' Builds an ElasticSearch schema map');
    CommandLine::info(' Example: bin/cradle elastic build');
    CommandLine::info(' Example: bin/cradle elastic build package=foo/bar');
    CommandLine::output(PHP_EOL);

    CommandLine::success('bin/cradle elastic populate');
    CommandLine::info(' Populates ElasticSearch index');
    CommandLine::info(' Example: bin/cradle elastic populate');
    CommandLine::info(' Example: bin/cradle elastic populate package=foo/bar');
    CommandLine::output(PHP_EOL);
};
