<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/config.php';
spl_autoload_register(function (string $class): void {
    $file = __DIR__ . "/src/$class.php";
    if (is_file($file))
        require $file;
});
require __DIR__ . '/src/helpers.php';
