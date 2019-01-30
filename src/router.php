<?php //-->

return (function() {
    //are we in php server?
    if (php_sapi_name() !== 'cli-server') {
        return false;
    }

    $root = $_SERVER['DOCUMENT_ROOT'];
    $path = $_SERVER['REQUEST_URI'];

    if (file_exists($root . $path) && !is_dir($root . $path)) {
        return false;
    }

    return include $root . '/index.php';
})();
