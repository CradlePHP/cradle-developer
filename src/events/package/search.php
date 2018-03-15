<?php //-->

/**
 * $ cradle package search foo/bar
 *
 * @param Request $request
 * @param Response $response
 */
return function($request, $response) {
    $request->setStage('q', $request->getStage(0));
    $this->trigger('system-package-search', $request, $response);
};
