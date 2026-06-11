<?php

$MySQL = array(
    'servidor' => 'localhost',  
     'usuario' => 'root',   
     'senha' => 'getnis2018',       
     'banco' => 'sigmaScheduling'    
 );
 
 $MySQLi = new MySQLi($MySQL['servidor'], $MySQL['usuario'], $MySQL['senha'], $MySQL['banco']);

$sql = "SELECT nome FROM usuario";
/*$result = $MySQLi->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "id: " . $row["nome"]. "<br>";
    }
} else {
    echo "0 results";
}*/

echo $sql;

?>