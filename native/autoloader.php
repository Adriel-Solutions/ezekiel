<?php

    function autoloader($classname) {
        $root = __DIR__ . DIRECTORY_SEPARATOR . '..';

        // Prevent conflict with other autoloaders
        $namespace = current(explode("\\", $classname));
        if(!in_array($namespace, ['native', 'app'])) return;

        $path = strtolower(str_replace("\\", DIRECTORY_SEPARATOR, $classname)) . '.php';
        $filepath = join(DIRECTORY_SEPARATOR, [ $root , $path ]);

        // Prevent exceptions
        if(!file_exists($filepath)) return;

        require $filepath;
    }
    
    spl_autoload_register('autoloader');
