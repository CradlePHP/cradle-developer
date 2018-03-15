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
 * $ cradle sql help
 *
 * @param Request $request
 * @param Response $response
 *
 * @return string
 */
return function ($request, $response) {
    CommandLine::warning('SQL Commands:');
    CommandLine::output(PHP_EOL);

    CommandLine::success('bin/cradle sql flush');
    CommandLine::info(' Clears SQL database');
    CommandLine::info(' Example: bin/cradle sql flush');
    CommandLine::info(' Example: bin/cradle sql flush package=foo/bar');
    CommandLine::output(PHP_EOL);

    CommandLine::success('bin/cradle sql build');
    CommandLine::info(' Builds SQL schema on database');
    CommandLine::info(' Example: bin/cradle sql build');
    CommandLine::info(' Example: bin/cradle sql build package=foo/bar');
    CommandLine::output(PHP_EOL);

    CommandLine::success('bin/cradle sql populate');
    CommandLine::info(' Populates SQL database');
    CommandLine::info(' Example: bin/cradle sql populate');
    CommandLine::info(' Example: bin/cradle sql populate package=foo/bar');
    CommandLine::output(PHP_EOL);
};
