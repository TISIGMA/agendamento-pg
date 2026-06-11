<?php

require('class.php');
require('../../conn.php');
require('../functions.php');
$data = date("Y-m-d");
$result = editHorarioStatus($MySQLi, $_GET['id'], $_GET['status'], $data);
echo $$result;
?>