<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Framework\CommandLine;
use Cradle\Event\EventHandler;

/**
 * $ cradle sql populate
 * $ cradle sql populate package=foo/bar
 *
 * @param Request $request
 * @param Response $response
 */
return function ($request, $response) {
    CommandLine::system('Populating SQL...');

    //we only want to consider active packages
    $packages = $this->getPackages();

    //if we just want to populate one package
    if($request->hasStage('package')) {
        $package = $request->getStage('package');
        //if it is not an installed package
        if (!isset($packages[$package])) {
            CommandLine::error(sprintf(
                '%s is not installed. Try `$ cradle %s install`',
                $package,
                $package
            ));
        }

        $packages = [ $package => $packages[$package] ];
    }

    //loop through the packages
    foreach ($packages as $name => $package) {
        $type = $package->getPackageType();
        //skip pseudo packages
        if ($type === 'pseudo') {
            CommandLine::warning(sprintf('Skipping %s', $name));
            continue;
        }

        //path is name
        $path = $name;
        if ($type === 'root') {
            $path = substr($path, 1);
        }

        CommandLine::info(sprintf('Populating %s', $name));
        list($author, $package) = explode('/', $path, 2);
        $event = sprintf('%s-%s-sql-populate', $author, $package);
        $this->trigger($event, $request, $response);

        switch ($this->getEventHandler()->getMeta()) {
            case EventHandler::STATUS_NOT_FOUND:
                CommandLine::warning(sprintf('%s does not have a populate SQL handler. Skipping.', $name));
                break;
            case EventHandler::STATUS_OK:
            case EventHandler::STATUS_INCOMPLETE:
            default:
                CommandLine::success(sprintf('%s SQL was populated.', $name));
                break;
        }
    }
};
