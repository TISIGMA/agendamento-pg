<?php
require_once 'conn.php';

try {
    // Check if column exists
    $checkColumn = $MySQLi->query("SHOW COLUMNS FROM janela LIKE 'carregando_ou_rejeitado'");
    
    if ($checkColumn && $checkColumn->num_rows == 0) {
        // Column doesn't exist, add it
        $sql = "ALTER TABLE janela ADD COLUMN carregando_ou_rejeitado VARCHAR(50) DEFAULT NULL";
        $result = $MySQLi->query($sql);
        
        if ($result) {
            echo "Coluna 'carregando_ou_rejeitado' adicionada com sucesso!";
        } else {
            echo "Erro ao adicionar coluna: " . $MySQLi->error;
        }
    } else {
        echo "Coluna 'carregando_ou_rejeitado' já existe!";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
