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
 * $ cradle package update foo/bar
 *
 * @param Request $request
 * @param Response $response
 */
return function($request, $response) {
    //this is the package name
    $name = $request->getStage(0);

    //current version
    if (!$this->package('global')->config('version', $name)) {
        //CTA to call install instead
        CommandLine::error(sprintf(
            'Package is not installed. run `cradle package install %s` instead',
            $name
        ));
    }

    // path is name
    $path = $name;

    // local package?
    if (strpos($path, '/') === 0) {
        // remove trailing /
        $path = substr($path, 1);
    }

    // get author and package
    list($author, $package) = explode('/', $path, 2);
    // formulate event handler
    $event = sprintf('%s-%s-update', $author, $package);
    // trigger event handler
    $this->trigger($event, $request, $response);

    if ($response->hasResults('version')) {
        $version = $response->getResults('version');
        CommandLine::success(sprintf('%s was updated to %s', $name, $version));
        return;
    }

    CommandLine::success(sprintf('%s was updated', $name));
};
