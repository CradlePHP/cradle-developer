<?php //-->

/**
 * $ cradle package remove foo/bar
 *
 * @param Request $request
 * @param Response $response
 */
return function($request, $response) {
    $request->setStage('package', $request->getStage(0));
    $this->trigger('system-package-remove', $request, $response);
};
