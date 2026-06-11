<?php
require_once __DIR__ . '/../conn.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$names = [];

$names = Capsule::table('usuario')->orderBy('nome')->pluck('nome')->toArray();

header('Content-Type: text/plain; charset=utf-8');
echo implode(PHP_EOL, $names);

?>
