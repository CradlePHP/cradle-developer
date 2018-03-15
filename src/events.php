<?php //-->
/**
 * This file is part of the Cradle PHP Kitchen Sink Faucet Project.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */


/**
 * CLI developer starting point
 *
 * @param Request $request
 * @param Response $response
 *
 * @return string
 */
$cradle->on('package', include __DIR__ . '/events/package.php');
$cradle->on('package-list', include __DIR__ . '/events/package/list.php');
$cradle->on('package-install', include __DIR__ . '/events/package/install.php');

/**
 * CLI developer starting point
 *
 * @param Request $request
 * @param Response $response
 *
 * @return string
 */
$cradle->on('dev', include __DIR__ . '/events/developer.php');

/**
 * CLI help menu
 *
 * @param Request $request
 * @param Response $response
 *
 * @return string
 */
$cradle->on('dev-help', include __DIR__ . '/events/help.php');

/**
 * CLI Deploy
 *
 * @param Request $request
 * @param Response $response
 */
//$cradle->on('developer-deploy-production', include __DIR__ . '/events/deploy/production.php');

/**
 * CLI Deploy
 *
 * @param Request $request
 * @param Response $response
 */
//$cradle->on('developer-deploy-s3', include __DIR__ . '/events/deploy/s3.php');

/**
 * CLI production connect
 *
 * @param Request $request
 * @param Response $response
 */
//$cradle->on('developer-connect-to', include __DIR__ . '/events/deploy/connect.php');

/**
 * CLI clear cache
 *
 * @param Request $request
 * @param Response $response
 */
//$cradle->on('developer-flush-redis', include __DIR__ . '/events/redis/flush.php');

/**
 * CLI clear index
 *
 * @param Request $request
 * @param Response $response
 */
//$cradle->on('developer-flush-elastic', include __DIR__ . '/events/elastic/flush.php');

/**
 * CLI map index
 *
 * @param Request $request
 * @param Response $response
 */
//$cradle->on('developer-map-elastic', include __DIR__ . '/events/elastic/map.php');

/**
 * CLI clear index
 *
 * @param Request $request
 * @param Response $response
 */
//$cradle->on('developer-populate-elastic', include __DIR__ . '/events/elastic/populate.php');

/**
 * CLI clear index
 *
 * @param Request $request
 * @param Response $response
 */
//$cradle->on('developer-flush-sql', include __DIR__ . '/events/sql/flush.php');

/**
 * CLI populates database with dummy data
 *
 * @param Request $request
 * @param Response $response
 */
//$cradle->on('developer-build-sql', include __DIR__ . '/events/sql/build.php');

/**
 * CLI populates database with dummy data
 *
 * @param Request $request
 * @param Response $response
 */
//$cradle->on('developer-populate-sql', include __DIR__ . '/events/sql/populate.php');

/**
 * CLI developer installation
 *
 * @param Request $request
 * @param Response $response
 */
//$cradle->on('developer-install', include __DIR__ . '/events/install.php');

/**
 * CLI developer update
 *
 * @param Request $request
 * @param Response $response
 */
//$cradle->on('developer-update', include __DIR__ . '/events/update.php');

/**
 * CLI developer server
 *
 * @param Request $request
 * @param Response $response
 */
//$cradle->on('developer-server', include __DIR__ . '/events/server.php');

/**
 * CLI app generate
 *
 * @param Request $request
 * @param Response $response
 */
//$cradle->on('developer-generate-app', include __DIR__ . '/events/generate/app.php');

/**
 * CLI module generate
 *
 * @param Request $request
 * @param Response $response
 */
//$cradle->on('developer-generate-module', include __DIR__ . '/events/generate/module.php');

/**
 * CLI admin generate
 *
 * @param Request $request
 * @param Response $response
 */
//$cradle->on('developer-generate-admin', include __DIR__ . '/events/generate/admin.php');

/**
 * CLI REST generate
 *
 * @param Request $request
 * @param Response $response
 */
//$cradle->on('developer-generate-rest', include __DIR__ . '/events/generate/rest.php');

/**
 * CLI SQL generate
 *
 * @param Request $request
 * @param Response $response
 */
//$cradle->on('developer-generate-sql', include __DIR__ . '/events/generate/sql.php');

/**
 * CLI Elastic generate
 *
 * @param Request $request
 * @param Response $response
 */
//$cradle->on('developer-generate-elastic', include __DIR__ . '/events/generate/elastic.php');
