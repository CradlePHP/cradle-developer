<?php //-->
/**
 * This file is part of a Custom Package.
 */

$admin = $this->handler($this->config('settings', 'admin'));

/**
 * Renders a create form
 *
 * @param Request $request
 * @param Response $response
 */
$admin->get('/[[name]]/create', function ($request, $response) {});

/**
 * Renders a create form
 *
 * @param Request $request
 * @param Response $response
 */
$admin->get('/[[name]]/detail/:[[primary]]', function ($request, $response) {});

/**
 * Removes a [[name]]
 *
 * @param Request $request
 * @param Response $response
 */
$admin->get('/[[name]]/remove/:[[primary]]', function ($request, $response) {});

/**
 * Restores a [[name]]
 *
 * @param Request $request
 * @param Response $response
 */
$admin->get('/[[name]]/restore/:[[primary]]', function ($request, $response) {});

/**
 * Renders a search page
 *
 * @param Request $request
 * @param Response $response
 */
$admin->get('/[[name]]/search', function ($request, $response) {});

/**
 * Renders an update form
 *
 * @param Request $request
 * @param Response $response
 */
$admin->get('/[[name]]/update/:[[primary]]', function ($request, $response) {});

/**
 * Processes a create form
 *
 * @param Request $request
 * @param Response $response
 */
$admin->post('/[[name]]/create', function ($request, $response) {});

/**
 * Processes an update form
 *
 * @param Request $request
 * @param Response $response
 */
$admin->post('/[[name]]/update/:[[primary]]', function ($request, $response) {});
