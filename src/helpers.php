<?php // -->

return function ($request, $response) {
    $this->package('cradlephp/cradle-developer')
    
    /**
     * Custom CLI / Package Installer Logger
     * 
     * @param $type
     * @param $message
     * @param $status
     * @param $package
     */
    ->addMethod('packageLog', function(
        $type = null,
        $message = null, 
        $package = null, 
        $status = null
    ) use (&$request, &$response) {
        $data = $request->hasStage('data');

        // if type and message is set
        if (!$data && $type && $message) {
            // call out command line
            \Cradle\Framework\CommandLine::$type($message, false);
        }
    
        // skip if package is not set
        if (!$package) {
            // regular error?
            if (!$data && $type && $type == 'error') {
                exit;
            }

            return;
        }

        // get packages folder
        $folder = cradle('global')->path('config') . '/packages';

        // normalize package name
        if (strpos($package, '/') === 0) {
            $package = str_replace('/', '.', substr($package, 1));
        } else {
            $package = str_replace('/', '.', $package);
        }

        // package log file
        $file = sprintf(
            cradle('global')->path('config') . '/packages/%s.install.php',
            $package
        );

        // try to create the folder
        if (!is_dir($folder)) {
            // make a folder
            mkdir($folder, 0777, true);
        }
        
        // try to create the file
        if (!file_exists($file)) {
            // make the file
            touch($file);
            chmod($file, 0777);
        }

        // load the file
        $log = include($file);

        // set default content
        if (!is_array($log)) {
            $log = [];
        }

        // if status is set
        if ($status) {
            // set the status
            $log['status'] = $status;
        }

        // if message is set
        if (!is_null($message)) {
            // set default logs
            if (!isset($log['logs'])) {
                $log['logs'] = [];
            }

            // set default logs
            if (!is_array($log['logs'])) {
                $log['logs'] = [];
            }

            // if message is array
            if (is_array($message)) {
                // overwrite logs
                $log['logs'] = $message;
            } else {
                // just push through
                $log['logs'][] = $message;
            }
        }

        // set timestamp
        $log['timestamp'] = time();

        // update the log file
        $content = "<?php //-->\nreturn ".var_export($log, true).';';
        file_put_contents($file, $content);

        // should we exit?
        if (!$data && $type && $type == 'error') {
            exit;
        }
    });
};