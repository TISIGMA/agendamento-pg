<?php

use Illuminate\Database\Capsule\Manager as Capsule;

if (!class_exists(Capsule::class)) {
    return;
}

if (isset($GLOBALS['eloquent_bootstrapped']) && $GLOBALS['eloquent_bootstrapped'] === true) {
    return;
}

$capsule = new Capsule();
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $MySQL['servidor'],
    'port' => $MySQL['porta'],
    'database' => $MySQL['banco'],
    'username' => $MySQL['usuario'],
    'password' => $MySQL['senha'],
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

$GLOBALS['eloquent_bootstrapped'] = true;
$GLOBALS['eloquent_capsule'] = $capsule;
