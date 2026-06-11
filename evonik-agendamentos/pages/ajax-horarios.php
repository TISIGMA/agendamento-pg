<?php

require('class.php');
require('../../conn.php');
require('../functions.php');
$result = editHorarioStatus($MySQLi, $_GET['id'], $_GET['status']);
echo $$result;
?>