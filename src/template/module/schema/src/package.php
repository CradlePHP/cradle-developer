<?php //-->
/**
 * This file is part of a Custom Package.
 */

/**
 * $ cradle package install /module/[[name]]
 * $ cradle package install /module/[[name]] 1.0.0
 * $ cradle /module/[[name]] install
 * $ cradle /module/[[name]] install 1.0.0
 *
 * @param Request $request
 * @param Response $response
 */
$this->on('module-[[name]]-install', function ($request, $response) {});

/**
 * $ cradle package update /module/[[name]]
 * $ cradle package update /module/[[name]] 1.0.0
 * $ cradle /module/[[name]] update
 * $ cradle /module/[[name]] update 1.0.0
 *
 * @param Request $request
 * @param Response $response
 */
$this->on('module-[[name]]-update', function ($request, $response) {});

/**
 * $ cradle package remove /module/[[name]]
 * $ cradle /module/[[name]] remove
 *
 * @param Request $request
 * @param Response $response
 */
$this->on('module-[[name]]-remove', function ($request, $response) {});

/**
 * $ cradle elastic flush /module/[[name]]
 * $ cradle /module/[[name]] elastic-flush
 *
 * @param Request $request
 * @param Response $response
 */
$this->on('module-[[name]]-elastic-flush', function ($request, $response) {});

/**
 * $ cradle elastic map /module/[[name]]
 * $ cradle /module/[[name]] elastic-map
 *
 * @param Request $request
 * @param Response $response
 */
$this->on('module-[[name]]-elastic-map', function ($request, $response) {});

/**
 * $ cradle elastic populate /module/[[name]]
 * $ cradle /module/[[name]] elastic-populate
 *
 * @param Request $request
 * @param Response $response
 */
$this->on('module-[[name]]-elastic-populate', function ($request, $response) {});

/**
 * $ cradle redis flush /module/[[name]]
 * $ cradle /module/[[name]] redis-flush
 *
 * @param Request $request
 * @param Response $response
 */
$this->on('module-[[name]]-redis-flush', function ($request, $response) {});

/**
 * $ cradle redis populate /module/[[name]]
 * $ cradle /module/[[name]] redis-populate
 *
 * @param Request $request
 * @param Response $response
 */
$this->on('module-[[name]]-redis-populate', function ($request, $response) {});

/**
 * $ cradle sql build /module/[[name]]
 * $ cradle /module/[[name]] sql-build
 *
 * @param Request $request
 * @param Response $response
 */
$this->on('module-[[name]]-sql-build', function ($request, $response) {});

/**
 * $ cradle sql flush /module/[[name]]
 * $ cradle /module/[[name]] sql-flush
 *
 * @param Request $request
 * @param Response $response
 */
$this->on('module-[[name]]-sql-flush', function ($request, $response) {});

/**
 * $ cradle sql populate /module/[[name]]
 * $ cradle /module/[[name]] sql-populate
 *
 * @param Request $request
 * @param Response $response
 */
$this->on('module-[[name]]-sql-populate', function ($request, $response) {});
