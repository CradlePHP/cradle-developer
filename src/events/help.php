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
    echo PHP_EOL;
    CommandLine::warning('Project Maintenance Commands:');
    echo PHP_EOL;

    CommandLine::success('cradle install');
    CommandLine::info(' Installs Project');
    CommandLine::info(' - Example: cradle install');
    CommandLine::info(' - Example: cradle install --force');
    CommandLine::info(' - Example: cradle install testing_db -h 127.0.0.1 -u root -p root --force');
    CommandLine::info(' - Flags:');
    CommandLine::info('   --force -f - Skips asking questions');
    CommandLine::info('   --skip-configs - Skips config file setup');
    CommandLine::info('   --skip-mkdir - Skips folder creation');
    CommandLine::info('   --skip-chmod - Skips chmodding');
    CommandLine::info('   --skip-sql - Skips SQL installation');
    CommandLine::info('   --skip-versioning - Skips version updates');
    echo PHP_EOL;

    CommandLine::success('cradle update');
    CommandLine::info(' Updates Project with versioning install scripts');
    CommandLine::info(' - Example: cradle update');
    echo PHP_EOL;

    CommandLine::success('cradle server');
    CommandLine::info(' Starts up the PHP server (dev mode)');
    CommandLine::info(' - Example: cradle server');
    CommandLine::info(' - Example: cradle server -h 127.0.0.1 -p 8888');
    echo PHP_EOL;

    CommandLine::warning('SQL Commands:');
    echo PHP_EOL;

    CommandLine::success('cradle sql-flush');
    CommandLine::info(' Clears SQL database');
    CommandLine::info(' - Example: cradle sql-flush');
    CommandLine::info(' - Example: cradle cradlephp/cradle-history sql-flush');
    echo PHP_EOL;

    CommandLine::success('cradle sql-build');
    CommandLine::info(' Builds SQL schema on database');
    CommandLine::info(' - Example: cradle sql-build');
    CommandLine::info(' - Example: cradle cradlephp/cradle-history sql-build');
    echo PHP_EOL;

    CommandLine::success('cradle sql-populate');
    CommandLine::info(' Populates SQL database');
    CommandLine::info(' - Example: cradle sql-populate');
    CommandLine::info(' - Example: cradle cradlephp/cradle-history sql-populate');
    echo PHP_EOL;

    CommandLine::warning('ElasticSearch Commands:');
    echo PHP_EOL;

    CommandLine::success('cradle elastic-flush');
    CommandLine::info(' Clears the ElasticSearch index');
    CommandLine::info(' - Example: cradle elastic-flush');
    CommandLine::info(' - Example: cradle cradlephp/cradle-history elastic-flush');
    echo PHP_EOL;

    CommandLine::success('cradle elastic-map');
    CommandLine::info(' Builds an ElasticSearch schema map');
    CommandLine::info(' - Example: cradle elastic-map');
    CommandLine::info(' - Example: cradle cradlephp/cradle-history elastic-map');
    echo PHP_EOL;

    CommandLine::success('cradle elastic-populate');
    CommandLine::info(' Populates ElasticSearch index');
    CommandLine::info(' - Example: cradle elastic-populate');
    CommandLine::info(' - Example: cradle cradlephp/cradle-history elastic-populate');
    echo PHP_EOL;

    CommandLine::warning('Redis Commands:');
    echo PHP_EOL;

    CommandLine::success('cradle redis-flush');
    CommandLine::info(' Clears the Redis cache');
    CommandLine::info(' - Example: cradle redis-flush');
    CommandLine::info(' - Example: cradle cradlephp/cradle-history redis-flush');
    echo PHP_EOL;

    CommandLine::warning('RabbitMQ Commands:');
    echo PHP_EOL;

    CommandLine::success('cradle queue [event] [data]');
    CommandLine::info(' Queues any event');
    CommandLine::info(' - Example: cradle queue auth-verify-mail auth_id=1 host=127.0.0.1');
    echo PHP_EOL;

    CommandLine::success('cradle work');
    CommandLine::info(' Starts a worker');
    CommandLine::info(' - Example: cradle work');
    CommandLine::info(' - Example: cradle work name=[queue name]');
    echo PHP_EOL;

    CommandLine::warning('Deployment Related Commands:');
    echo PHP_EOL;

    CommandLine::success('cradle connect-to');
    CommandLine::info(' Gives the command to connect to a production server');
    CommandLine::info('   You need ask the faucet owner for the private key');
    CommandLine::info('   You need to setup config/deploy.php');
    CommandLine::info('   see: https://gist.github.com/cblanquera/3ff60b4c9afc92be1ac0a9d57afceb17#file-instructions-md');
    echo PHP_EOL;

    CommandLine::success('cradle deploy-production');
    CommandLine::info(' Deploys code to production servers');
    CommandLine::info('   You need to setup config/deploy.php');
    CommandLine::warning('   Use with caution.');
    echo PHP_EOL;

    CommandLine::success('cradle deploy-s3');
    CommandLine::info(' Deploys public assets to AWS S3');
    CommandLine::info('   You need to setup config/services.php');
    CommandLine::warning('   Use with caution.');
    CommandLine::info(' - Example: cradle deploy-s3');
    CommandLine::info(' - Example: cradle deploy-s3 --include-yarn --include-upload');
    echo PHP_EOL;
};
