<?php
// Base URL of the app - adjust if your project folder name differs
define('BASE_URL', '/pharmacare/public');

// Session must be started before anything else touches $_SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoload core classes, models, controllers without a Composer dependency
spl_autoload_register(function ($class) {
    $dirs = ['core', 'models', 'controllers'];
    foreach ($dirs as $dir) {
        $path = __DIR__ . '/../' . $dir . '/' . $class . '.php';
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

require_once __DIR__ . '/database.php';