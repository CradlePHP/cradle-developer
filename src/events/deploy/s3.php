<?php //-->
/**
 * This file is part of the Cradle PHP Kitchen Sink Faucet Project.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Framework\CommandLine;
use Cradle\Module\Utility\File;

use Aws\S3\S3Client;

/**
 * CLI Deploy
 *
 * @param Request $request
 * @param Response $response
 */
return function ($request, $response) {
    $cdn = $this->package('global')->service('s3-main');

    if (!$cdn) {
        CommandLine::warning('CDN is not setup. Check config/services.php. Aborting.');
        return;
    }

    // load s3
    $s3 = S3Client::factory([
        'version' => 'latest',
        'region'  => $cdn['region'], //example ap-southeast-1
        'credentials' => [
            'key'    => $cdn['token'],
            'secret' => $cdn['secret'],
        ]
    ]);

    //get the public path
    $public = $this->package('global')->path('public');
    $pattern = '(\.htaccess)|(\.php)|(DS_Store)';

    $root = null;
    if (isset($cdn['root']) && strpos($cdn['root'], '<') !== 0) {
        $root = $cdn['root'];
        if (strpos($root, '/') === 0) {
            $root = substr($root, 1);
        }

        if (substr($root, -1) !== '/') {
            $root .= '/';
        }

        if ($root === '/') {
            $root = null;
        }
    }

    if(!$request->hasStage('include-yarn')) {
        $pattern .= '|(\/components)';
    }

    if(!$request->hasStage('include-upload')) {
        $pattern .= '|(\/upload)';
    }

    //get all the files
    $paths = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($public));

    foreach ($paths as $path) {
        //if it's a directory
        if ($path->isDir()) {
            continue;
        }

        //get the file string
        $file = $path->getPathname();

        //there's no point pushing these things
        if (preg_match('/' . $pattern . '/', $file)) {
            continue;
        }

        // if /foo/bar/repo/public/path/to/file, then /path/to/file
        $path = substr($file, strlen($public) + 1);

        $path = $root . $path;

        //there's no better way to get a mime
        $mime = File::getMimeFromLink($file);

        //open a pipe
        $pipe = fopen($file, 'r');

        print sprintf("\033[36m%s\033[0m", '[cradle] * pushing '.$path);
        print PHP_EOL;

        $s3->putObject(array(
            'Bucket'        => $cdn['bucket'],
            'ACL'           => 'public-read',
            'ContentType'   => $mime,
            'Key'           => $path,
            'Body'          => $pipe,
            'CacheControl'  => 'max-age=43200'
        ));

        if (is_resource($pipe)) {
            fclose($pipe);
        }
    }
};
