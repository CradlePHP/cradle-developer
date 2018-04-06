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
 * CLI Deploy
 *
 * @param Request $request
 * @param Response $response
 */
return function ($request, $response) {
    $cwd = getcwd();
    $deploy = $this->package('global')->config('deploy');

    if (empty($deploy)) {
        CommandLine::warning('Deploy is not setup. Check config/deploy.php. Aborting.');
        return;
    }

    $deployable = [];
    $deployConfig = [];
    foreach ($deploy['servers'] as $name => $server) {
        if (isset($server['deploy'])) {
            if (!$server['deploy']) {
                continue;
            }

            unset($server['deploy']);
        }

        $command = 'ssh -i %s %s@%s -o "StrictHostKeyChecking no" exit';
        exec(sprintf($command, $deploy['key'], $server['user'], $server['host']));

        $deployConfig[] = '[' . $name . ']';
        $deployConfig[] = 'key ' . $deploy['key'];
        foreach ($server as $key => $value) {
            $deployConfig[] = $key . ' ' . $value;
        }

        //make it readable
        $deployConfig[] = '';

        $deployable[] = $name;
    }

    if(empty($deployConfig)) {
        CommandLine::error('Nothing to Deploy. Aborting.');
        return;
    }

    //write to tmp
    file_put_contents('/tmp/deploy.conf', implode("\n", $deployConfig));

    //deploy
    foreach($deployable as $name) {
        $command = sprintf(
            '%s/deploy -C %s -c /tmp/deploy.conf %s',
            __DIR__,
            $cwd,
            $name
        );

        CommandLine::system($command);
        system($command);
    }
};
