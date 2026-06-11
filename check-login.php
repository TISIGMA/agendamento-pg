<?php

require_once('conn.php');
require_once('session.php');

$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

if (login($username, $password, $MySQLi) == true){  
	checkNotification($MySQLi);
    header('Location: home.php');
    exit;
} else {
    header('Location: index.php?erro=login');
    exit;
}
?>
