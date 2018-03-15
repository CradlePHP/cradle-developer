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

    CommandLine::success('bin/cradle faucet install');
    CommandLine::info(' Installs Project');
    CommandLine::info(' - Example: bin/cradle faucet install');
    CommandLine::info(' - Example: bin/cradle faucet install --force --populate-sql');
    CommandLine::info(' - Example: bin/cradle faucet install testing_db -h 127.0.0.1 -u root -p root --force');
    echo PHP_EOL;

    CommandLine::success('bin/cradle faucet update');
    CommandLine::info(' Updates Project with versioning install scripts');
    CommandLine::info(' - Example: bin/cradle faucet update');
    CommandLine::info(' - Example: bin/cradle faucet update --module post');
    echo PHP_EOL;

    CommandLine::success('bin/cradle faucet server');
    CommandLine::info(' Starts up the PHP server (dev mode)');
    CommandLine::info(' - Example: bin/cradle faucet server');
    CommandLine::info(' - Example: bin/cradle faucet server -h 127.0.0.1 -p 8888');
    echo PHP_EOL;

    CommandLine::warning('Generator Commands:');
    echo PHP_EOL;

    CommandLine::success('bin/cradle faucet generate-app');
    CommandLine::info(' Generates a new app folder');
    echo PHP_EOL;

    CommandLine::success('bin/cradle faucet generate-module');
    CommandLine::info(' Generates a new module given schema');
    CommandLine::info(' - Example: bin/cradle faucet generate-module');
    CommandLine::info(' - Example: bin/cradle faucet generate-module --schema post');
    echo PHP_EOL;

    CommandLine::success('bin/cradle faucet generate-admin');
    CommandLine::info(' Generates a new admin controller given schema');
    CommandLine::info(' - Example: bin/cradle faucet generate-admin');
    CommandLine::info(' - Example: bin/cradle faucet generate-admin --schema post');
    echo PHP_EOL;

    CommandLine::success('bin/cradle faucet generate-rest');
    CommandLine::info(' Generates a new REST controller given schema');
    CommandLine::info(' - Example: bin/cradle faucet generate-rest');
    CommandLine::info(' - Example: bin/cradle faucet generate-rest --schema post');
    echo PHP_EOL;

    CommandLine::success('bin/cradle faucet generate-sql');
    CommandLine::info(' Generates SQL given schema');
    CommandLine::info(' - Example: bin/cradle faucet generate-sql');
    CommandLine::info(' - Example: bin/cradle faucet generate-sql --schema post');
    echo PHP_EOL;

    CommandLine::success('bin/cradle faucet generate-elastic');
    CommandLine::info(' Generates ElasticSearch map given schema');
    CommandLine::info(' - Example: bin/cradle faucet generate-elastic');
    CommandLine::info(' - Example: bin/cradle faucet generate-elastic --schema post');
    echo PHP_EOL;

    CommandLine::warning('SQL Commands:');
    echo PHP_EOL;

    CommandLine::success('bin/cradle faucet flush-sql');
    CommandLine::info(' Clears SQL database');
    CommandLine::info(' - Example: bin/cradle faucet flush-sql');
    CommandLine::info(' - Example: bin/cradle faucet flush-sql --table post');
    CommandLine::info(' - Example: bin/cradle faucet flush-sql --tableset post');
    echo PHP_EOL;

    CommandLine::success('bin/cradle faucet build-sql');
    CommandLine::info(' Builds SQL schema on database');
    echo PHP_EOL;

    CommandLine::success('bin/cradle faucet populate-sql');
    CommandLine::info(' Populates SQL database');
    CommandLine::info(' - Example: bin/cradle faucet populate-sql');
    CommandLine::info(' - Example: bin/cradle faucet populate-sql --module post');
    echo PHP_EOL;

    CommandLine::warning('ElasticSearch Commands:');
    echo PHP_EOL;

    CommandLine::success('bin/cradle faucet flush-elastic');
    CommandLine::info(' Clears the ElasticSearch index');
    echo PHP_EOL;

    CommandLine::success('bin/cradle faucet map-elastic');
    CommandLine::info(' Builds an ElasticSearch schema map');
    echo PHP_EOL;

    CommandLine::success('bin/cradle faucet populate-elastic');
    CommandLine::info(' Populates ElasticSearch index');
    echo PHP_EOL;

    CommandLine::warning('Redis Commands:');
    echo PHP_EOL;

    CommandLine::success('bin/cradle faucet flush-redis');
    CommandLine::info(' Clears the Redis cache');
    echo PHP_EOL;

    CommandLine::warning('RabbitMQ Commands:');
    echo PHP_EOL;

    CommandLine::success('bin/cradle faucet queue [event] [data]');
    CommandLine::info(' Queues any event');
    CommandLine::info(' - Example: bin/cradle faucet queue auth-verify-mail auth_id=1 host=127.0.0.1');
    echo PHP_EOL;

    CommandLine::success('bin/cradle faucet work');
    CommandLine::info(' Starts a worker');
    echo PHP_EOL;

    CommandLine::warning('Deployment Related Commands:');
    echo PHP_EOL;

    CommandLine::success('bin/cradle faucet connect-to');
    CommandLine::info(' Gives the command to connect to a production server');
    CommandLine::info('   You need ask the faucet owner for the private key');
    CommandLine::info('   You need to setup config/deploy.php');
    CommandLine::info('   see: https://gist.github.com/cblanquera/3ff60b4c9afc92be1ac0a9d57afceb17#file-instructions-md');
    echo PHP_EOL;

    CommandLine::success('bin/cradle faucet deploy-production');
    CommandLine::info(' Deploys code to production servers');
    CommandLine::info('   You need to setup config/deploy.php');
    CommandLine::warning('   Use with caution.');
    echo PHP_EOL;

    CommandLine::success('bin/cradle faucet deploy-s3');
    CommandLine::info(' Deploys public assets to AWS S3');
    CommandLine::info('   You need to setup config/services.php');
    CommandLine::warning('   Use with caution.');
    echo PHP_EOL;
};
