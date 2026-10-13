<?php

// Vercel executes this file as the PHP function entry point. Keep Laravel's
// existing public front controller as the single request handler.
// Function deployments are read-only apart from /tmp, so put Laravel's
// generated views, sessions, caches, and local files in the writable area.
$storagePath = $_ENV['LARAVEL_STORAGE_PATH'] ?? $_SERVER['LARAVEL_STORAGE_PATH'] ?? '/tmp/laravel-storage';
$_ENV['LARAVEL_STORAGE_PATH'] = $storagePath;
$_SERVER['LARAVEL_STORAGE_PATH'] = $storagePath;

foreach ([
    'app/private',
    'app/public',
    'framework/cache/data',
    'framework/sessions',
    'framework/views',
    'logs',
] as $directory) {
    $path = $storagePath.'/'.$directory;

    if (! is_dir($path)) {
        mkdir($path, 0775, true);
    }
}

require __DIR__.'/../public/index.php';
