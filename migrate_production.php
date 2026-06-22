<?php
/**
 * Script para aplicar migrações no banco de dados de produção
 * USO: Edite as configurações abaixo e execute via linha de comando ou navegador
 */

// Configurações do banco de dados de produção
$dbHost = '186.250.92.46';
$dbUser = 'app_user';
$dbPass = '6VSh6Q3Y9SNZ';
$dbName = 'labsoft_novo';
$dbPort = 3306;

// Conectar ao banco de dados
$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
if ($mysqli->connect_error) {
    die('Erro de conexão: ' . $mysqli->connect_error);
}
$mysqli->set_charset("utf8mb4");

echo "Conectado ao banco de dados com sucesso!\n\n";

function checkAndAddColumns($mysqli, $table, $columns) {
    foreach ($columns as $column => $definition) {
        $result = $mysqli->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
        if ($result->num_rows == 0) {
            echo "Adicionando coluna $column...\n";
            $mysqli->query("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
            if ($mysqli->error) {
                echo "ERRO ao adicionar $column: " . $mysqli->error . "\n";
            } else {
                echo "✓ Coluna $column adicionada com sucesso!\n";
            }
        } else {
            echo "✓ Coluna $column já existe\n";
        }
    }
}

$columnsToCheck = [
    'attatchment_picking_status' => 'VARCHAR(255) NULL',
    'attatchment_invoice_status' => 'VARCHAR(255) NULL',
    'attatchment_certificate_status' => 'VARCHAR(255) NULL',
    'attatchment_boarding_status' => 'VARCHAR(255) NULL',
    'attatchment_other_status' => 'VARCHAR(255) NULL',
    'scaneado' => "VARCHAR(50) DEFAULT 'Não'",
    'carga_em_qualidade' => "VARCHAR(50) DEFAULT 'Não'",
    'carregando_ou_rejeitado' => 'VARCHAR(50) NULL',
    'documentos' => "VARCHAR(50) DEFAULT 'aguardando'",
    'created_date' => 'DATETIME NULL',
    'last_modified_by' => 'VARCHAR(255) NULL',
    'last_modified_date' => 'DATETIME NULL',
];

echo "Verificando tabela 'janela'...\n";
checkAndAddColumns($mysqli, 'janela', $columnsToCheck);

echo "\n✅ Migrações aplicadas com sucesso!\n";
$mysqli->close();
?>