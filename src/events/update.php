<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Framework\CommandLine;
use Cradle\Storm\SqlFactory;

use Cradle\Event\EventHandler;

/**
 * CLI faucet installation
 *
 * @param Request $request
 * @param Response $response
 */
return function ($request, $response) {
    //schema should be writeable
    $folder = $this->package('global')->path('config') . '/schema';
    if (!is_dir($folder) || !is_writable($folder)) {
        CommandLine::error(sprintf(
            '%s is not writable Try `chmod -R 777 %s` first',
            $folder,
            $folder
        ));
    }

    //listen for install and update
    $schemas = [];
    $events = ['system-schema-create', 'system-schema-update'];
    $this->on($events, function($request, $response) use (&$schemas) {
        $schemas[] = $request->getStage('name');
    });

    //these are all the active packages
    $active = $this->getPackages();
    //these are the installed packages
    $installed = $this->package('global')->config('packages');

    $hasErrors = false;
    foreach ($active as $name => $package) {
        $type = $package->getPackageType();
        //skip pseudo packages
        if ($type === 'pseudo') {
            continue;
        }

        //determine author/package
        //if a vendor package
        if ($type === 'vendor') {
            list($vendor, $package) = explode('/', $name, 2);
        } else {
            //it's a root package
            list($vendor, $package) = explode('/', substr($name, 1), 2);
        }

        //determine action
        $action = 'install';
        //if it's installed
        if (isset($installed[$name]['version'])) {
            $action = 'update';
        }

        if ($action === 'install') {
            CommandLine::system(sprintf('Installing %s', $name));
        } else {
            CommandLine::system(sprintf('Updating %s', $name));
        }

        //trigger event
        $event = sprintf('%s-%s-%s', $vendor, $package, $action);
        $this->trigger($event, $request, $response);

        //if no event was triggered
        $status = $this->getEventHandler()->getMeta();
        if($status === EventHandler::STATUS_NOT_FOUND) {
            CommandLine::warning(sprintf('No actions needed on %s', $name));
            continue;
        }

        $logs = $response->getResults('logs');
        if (!empty($logs)) {
            foreach ($logs as $package => $group) {
                foreach ($group as $version => $messages) {
                    foreach ($messages as $log) {
                        CommandLine::$brand = '[' . $package . ' ' . $version . ']';
                        if (!isset($log['message'])) {
                            continue;
                        }

                        if (!isset($log['type'])) {
                            $log['type'] = 'info';
                        }

                        switch ($log['type']) {
                            case 'warning':
                                CommandLine::warning($log['message']);
                                break;
                            case 'error':
                                CommandLine::error($log['message'], false);
                                break;
                            case 'system':
                            case 'info':
                            default:
                                CommandLine::system($log['message']);
                                break;
                        }
                    }
                }
            }

            CommandLine::$brand = '[cradle]';
            $response->removeResults('logs');
        }

        //if error
        if ($response->isError()) {
            $message = sprintf('%s did not correctly install.', $name);
            CommandLine::error($message, false);
            $response->remove('json');
            $hasErrors = true;
            continue;
        }

        //if it's install
        if ($action === 'install') {
            $message = sprintf('Installed %s', $name);
            if ($response->hasResults('version')) {
                $message = sprintf(
                    'Installed %s to %s',
                    $name,
                    $response->getResults('version')
                );
            }

            CommandLine::success($message, false);
            continue;
        }

        //it's update
        $message = sprintf('Updated %s', $name);
        if ($response->hasResults('version')) {
            $message = sprintf(
                'Updated %s to %s',
                $name,
                $response->getResults('version')
            );
        }

        CommandLine::success($message, false);
    }

    //deal with schemas without packages
    $this->trigger('system-schema-search', $request, $response);
    foreach ($response->getResults('rows') as $schema) {
        //if this schema has already been installed/updated
        if (in_array($schema['name'], $schemas)) {
            //skip it
            continue;
        }

        //run an update
        $payload = $this->makePayload();
        $payload['request']->setStage($schema);
        $this->trigger(
            'system-schema-update',
            $payload['request'],
            $payload['response']
        );

        //if error
        if ($payload['response']->isError()) {
            CommandLine::error($payload['response']->getMessage(), false);
            continue;
        }

        //it's updated
        $message = sprintf('Updated schema %s', $schema['name']);
        CommandLine::success($message, false);
    }

    if ($hasErrors) {
        $message = 'There were some errors in the packages being installed/updated.';
        CommandLine::error($message, false);
        $response->setError(true, $message);
    }

    $response->setResults($schemas);
};
