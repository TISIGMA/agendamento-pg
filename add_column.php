<?php
require_once 'conn.php';

try {
    // Use Eloquent to check and add the column
    $capsule = $GLOBALS['eloquent_capsule'];
    
    // Check if column carregando_ou_rejeitado exists
    $columnExists1 = $capsule::select("SHOW COLUMNS FROM janela LIKE 'carregando_ou_rejeitado'");
    
    if (empty($columnExists1)) {
        // Column doesn't exist, add it
        $capsule::statement("ALTER TABLE janela ADD COLUMN carregando_ou_rejeitado VARCHAR(50) DEFAULT NULL");
        echo "Coluna 'carregando_ou_rejeitado' adicionada com sucesso!<br>";
    } else {
        echo "Coluna 'carregando_ou_rejeitado' já existe!<br>";
    }
    
    // Check if column documentos exists
    $columnExists2 = $capsule::select("SHOW COLUMNS FROM janela LIKE 'documentos'");
    
    if (empty($columnExists2)) {
        // Column doesn't exist, add it with default 'aguardando'
        $capsule::statement("ALTER TABLE janela ADD COLUMN documentos VARCHAR(50) DEFAULT 'aguardando'");
        echo "Coluna 'documentos' adicionada com sucesso!";
    } else {
        echo "Coluna 'documentos' já existe!";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
