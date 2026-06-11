<?php

require_once('conn.php');
require_once('session.php');

$username =  $_POST['username'];
$password =  $_POST['password'];


if (login($username, $password, $MySQLi) == true){  
	checkNotification($MySQLi);      			   
	echo "<script>window.location='home.php'</script>";	
}else	{
	echo '<script type="text/javascript">alert("ERRO: SENHA OU USUARIO INCORRETOS!'. $req_senha .'");</script>';
	echo "<script>window.location='index.php'</script>";
}
?>
