<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

/**
 * Developer Events
 */
return function ($request, $response) {
    /**
     * $ cradle connect app-1
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('connect', include __DIR__ . '/events/connect.php');

    /**
     * $ cradle help
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('help', include __DIR__ . '/events/help.php');

    /**
     * $ cradle install
     * $ cradle install -f | --force
     * $ cradle install --skip-sql
     * $ cradle install --skip-versioning
     * $ cradle install -h 127.0.0.1 -u root -p 123
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('install', include __DIR__ . '/events/install.php');

    /**
     * $ cradle deploy
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('deploy', include __DIR__ . '/events/deploy.php');

    /**
     * $ cradle deploy help
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('deploy-help', include __DIR__ . '/events/deploy/help.php');

    /**
     * $ cradle deploy production
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('deploy-production', include __DIR__ . '/events/deploy/production.php');

    /**
     * $ cradle deploy s3
     * $ cradle deploy s3 --include-yarn
     * $ cradle deploy s3 --include-upload
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('deploy-s3', include __DIR__ . '/events/deploy/s3.php');

    /**
     * $ cradle elastic
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('elastic', include __DIR__ . '/events/elastic.php');

    /**
     * $ cradle elastic help
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('elastic-help', include __DIR__ . '/events/elastic/help.php');

    /**
     * $ cradle elastic flush
     * $ cradle elastic flush foo/bar
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('elastic-flush', include __DIR__ . '/events/elastic/flush.php');

    /**
     * $ cradle elastic map
     * $ cradle elastic map foo/bar
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('elastic-map', include __DIR__ . '/events/elastic/map.php');

    /**
     * $ cradle elastic populate
     * $ cradle elastic populate foo/bar
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('elastic-populate', include __DIR__ . '/events/elastic/populate.php');

    /**
     * $ cradle generate
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('generate', include __DIR__ . '/events/generate.php');

    /**
     * $ cradle generate help
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('generate-help', include __DIR__ . '/events/generate/help.php');

    /**
     * $ cradle generate module foobar
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('generate-module', include __DIR__ . '/events/generate/module.php');

    /**
     * $ cradle generate app foobar
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('generate-app', include __DIR__ . '/events/generate/admin.php');

    /**
     * $ cradle package
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('package', include __DIR__ . '/events/package.php');

    /**
     * $ cradle package help
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('package-help', include __DIR__ . '/events/package/help.php');

    /**
     * $ cradle package install foo/bar
     * $ cradle package install foo/bar 1.0.0
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('package-install', include __DIR__ . '/events/package/install.php');

    /**
     * $ cradle package list
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('package-list', include __DIR__ . '/events/package/list.php');

    /**
     * $ cradle package remove foo/bar
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('package-remove', include __DIR__ . '/events/package/remove.php');

    /**
     * $ cradle package search foobar
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('package-search', include __DIR__ . '/events/package/search.php');

    /**
     * $ cradle package update foo/bar
     * $ cradle package update foo/bar 1.0.0
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('package-update', include __DIR__ . '/events/package/update.php');

    /**
     * $ cradle redis
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('redis', include __DIR__ . '/events/redis.php');

    /**
     * $ cradle redis help
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('redis-help', include __DIR__ . '/events/redis/help.php');

    /**
     * $ cradle redis flush
     * $ cradle redis flush foo/bar
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('redis-flush', include __DIR__ . '/events/redis/flush.php');

    /**
     * $ cradle sql
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('sql', include __DIR__ . '/events/sql.php');

    /**
     * $ cradle sql help
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('sql-help', include __DIR__ . '/events/sql/help.php');

    /**
     * $ cradle sql build
     * $ cradle sql build foo/bar
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('sql-build', include __DIR__ . '/events/sql/build.php');

    /**
     * $ cradle sql flush
     * $ cradle sql flush foo/bar
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('sql-flush', include __DIR__ . '/events/sql/flush.php');

    /**
     * $ cradle sql populate
     * $ cradle sql populate foo/bar
     *
     * @param Request $request
     * @param Response $response
     */
    $this->on('sql-populate', include __DIR__ . '/events/sql/populate.php');
};
