<?php //-->
/**
 * This file is part of a Custom Package.
 */

/**
 * Renders a create form
 *
 * @param Request $request
 * @param Response $response
 */
$this->get('/[[name]]/create', function ($request, $response) {
    $this->routeTo('GET', '/system/object/');
});

/**
 * Renders a create form
 *
 * @param Request $request
 * @param Response $response
 */
$this->get('/[[name]]/detail/:[[primary]]', function ($request, $response) {});

/**
 * Removes a [[name]]
 *
 * @param Request $request
 * @param Response $response
 */
$this->get('/[[name]]/remove/:[[primary]]', function ($request, $response) {});

/**
 * Restores a [[name]]
 *
 * @param Request $request
 * @param Response $response
 */
$this->get('/[[name]]/restore/:[[primary]]', function ($request, $response) {});

/**
 * Renders a search page
 *
 * @param Request $request
 * @param Response $response
 */
$this->get('/[[name]]/search', function ($request, $response) {});

/**
 * Renders an update form
 *
 * @param Request $request
 * @param Response $response
 */
$this->get('/[[name]]/update/:[[primary]]', function ($request, $response) {});

/**
 * Processes a create form
 *
 * @param Request $request
 * @param Response $response
 */
$this->post('/[[name]]/create', function ($request, $response) {});

/**
 * Processes an update form
 *
 * @param Request $request
 * @param Response $response
 */
$this->post('/[[name]]/update/:[[primary]]', function ($request, $response) {});
