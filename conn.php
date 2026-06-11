<?php
header('Content-Type: text/html; charset=utf-8');

$isProduction = false;

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

$MySQLi = new MySQLi($MySQL['servidor'], $MySQL['usuario'], $MySQL['senha'], $MySQL['banco']);
/*$MySQLi->set_charset("utf8");*/

?>
