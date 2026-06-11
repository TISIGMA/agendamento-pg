<?php
header('Content-Type: text/html; charset=utf-8');

$autoloadPath = __DIR__ . '/app_vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    http_response_code(500);
    exit('ORM bootstrap error: execute composer install e publique o diretório app_vendor/.');
}
require_once $autoloadPath;

$isProduction = true;

$productionConn = array(
    'servidor' => '10.15.1.120',
    'usuario'  => 'root',
    'senha'    => 'iRoE0IGyNBd0HKEEEKe3f34tgT2',
    'banco'    => 'labsoftt_sigma',
    'porta'    => 3306
);

$homolConn = array(
    'servidor' => '186.250.92.46',
    'usuario'  => 'app_user',
    'senha'    => '6VSh6Q3Y9SNZ',
    'banco'    => 'labsoft_teste',
    'porta'    => 3306
);

$MySQL = ($isProduction) ? $productionConn : $homolConn;

$MySQLi = null;

require_once __DIR__ . '/bootstrap/eloquent.php';

if (!class_exists('Illuminate\\Database\\Capsule\\Manager') || !isset($GLOBALS['eloquent_capsule'])) {
    http_response_code(500);
    exit('ORM bootstrap error: Eloquent nao foi inicializado.');
}
