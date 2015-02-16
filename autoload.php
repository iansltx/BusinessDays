<?php // PSR-4 autoloader for internal classes

spl_autoload_register(function ($class) {
    $map = [
        'iansltx\\BusinessDays\\Test\\' => __DIR__ . DIRECTORY_SEPARATOR . 'tests',
        'iansltx\\BusinessDays\\' => __DIR__ . DIRECTORY_SEPARATOR . 'src'
    ];

    foreach ($map as $prefix => $baseDir) {
        // does the class use the namespace prefix?
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            // no, move to the next registered autoloader
            continue;
        }

        // get the relative class name
        $relative_class = substr($class, $len);

        // replace the namespace prefix with the base directory, replace namespace
        // separators with directory separators in the relative class name, append
        // with .php
        $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relative_class) . '.php';

        // if the file exists, require it
        if (file_exists($file)) {
            require $file;
        }
    }
});
