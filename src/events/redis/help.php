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
 * $ cradle redis help
 *
 * @param Request $request
 * @param Response $response
 *
 * @return string
 */
return function ($request, $response) {
    CommandLine::warning('Redis Commands:');
    CommandLine::output(PHP_EOL);

    CommandLine::success('bin/cradle redis flush');
    CommandLine::info(' Clears the Redis cache');
    CommandLine::info(' Example: bin/cradle redis flush');
    CommandLine::info(' Example: bin/cradle redis flush package=foo/bar');
    CommandLine::output(PHP_EOL);
};
