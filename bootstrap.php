<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/classes/Database.php';

spl_autoload_register(function (string $class) {
    $file = __DIR__ . '/classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

session_start();
