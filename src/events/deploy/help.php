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
 * CLI help menu
 *
 * @param Request $request
 * @param Response $response
 *
 * @return string
 */
return function ($request, $response) {
    CommandLine::warning('Deployment Related Commands:');
    CommandLine::output(PHP_EOL);

    CommandLine::success('bin/cradle deploy production');
    CommandLine::info(' Deploys code to production servers');
    CommandLine::info('   You need to setup config/deploy.php');
    CommandLine::warning('   Use with caution.');
    CommandLine::output(PHP_EOL);

    CommandLine::success('bin/cradle deploy s3');
    CommandLine::info(' Deploys public assets to AWS S3');
    CommandLine::info('   You need to setup config/services.php');
    CommandLine::warning('   Use with caution.');
    CommandLine::info(' - Example: bin/cradle deploy s3');
    CommandLine::info(' - Example: bin/cradle deploy s3 --include-upload');
    CommandLine::info(' - Example: bin/cradle deploy s3 --include-yarn');
    CommandLine::output(PHP_EOL);
};
