<?php
$servidor = '186.250.92.46';
$usuario = 'app_user';
$senha = '6VSh6Q3Y9SNZ';
$bancoFonte = 'labsoft_novo';
$bancoDestino = 'labsoft_teste';
$porta = 3306;

try {
    $pdoFonte = new PDO("mysql:host=$servidor;port=$porta;dbname=$bancoFonte;charset=utf8mb4", $usuario, $senha);
    $pdoFonte->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdoDestino = new PDO("mysql:host=$servidor;port=$porta;dbname=$bancoDestino;charset=utf8mb4", $usuario, $senha);
    $pdoDestino->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=========================================\n";
    echo "Comparando bancos de dados:\n";
    echo "Fonte: $bancoFonte\n";
    echo "Destino: $bancoDestino\n";
    echo "=========================================\n\n";

    $tabelasFonte = getTabelas($pdoFonte);
    $tabelasDestino = getTabelas($pdoDestino);

    $sqlComandos = [];

    foreach ($tabelasFonte as $tabela) {
        if (!in_array($tabela, $tabelasDestino)) {
            echo "[+] Tabela nova encontrada: $tabela\n";
            $createSql = getCreateTable($pdoFonte, $tabela);
            $sqlComandos[] = $createSql;
        } else {
            $colunasFonte = getColunas($pdoFonte, $tabela);
            $colunasDestino = getColunas($pdoDestino, $tabela);
            
            foreach ($colunasFonte as $coluna) {
                if (!colunaExiste($colunasDestino, $coluna['nome'])) {
                    echo "[+] Coluna nova na tabela $tabela: {$coluna['nome']}\n";
                    $alterSql = "ALTER TABLE `$tabela` ADD COLUMN {$coluna['definicao']};";
                    $sqlComandos[] = $alterSql;
                }
            }
        }
    }

    echo "\n=========================================\n";
    echo "Comandos SQL para sincronizar:\n";
    echo "=========================================\n\n";
    echo implode("\n\n", $sqlComandos) . "\n\n";

    echo "=========================================\n";
    echo "Deseja executar os comandos? (s/n): ";
    $resposta = trim(fgets(STDIN));

    if (strtolower($resposta) === 's') {
        echo "\nExecutando comandos...\n";
        foreach ($sqlComandos as $sql) {
            try {
                $pdoDestino->exec($sql);
                echo "✓ " . substr($sql, 0, 80) . "...\n";
            } catch (PDOException $e) {
                echo "✗ Erro: " . $e->getMessage() . "\n";
                echo "  SQL: $sql\n";
            }
        }
        echo "\nProcesso concluído!\n";
    }

} catch (PDOException $e) {
    die("Erro: " . $e->getMessage() . "\n");
}

function getTabelas($pdo) {
    $stmt = $pdo->query("SHOW TABLES");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function getColunas($pdo, $tabela) {
    $stmt = $pdo->query("SHOW FULL COLUMNS FROM `$tabela`");
    $colunas = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $definicao = "`{$row['Field']}` {$row['Type']}";
        if ($row['Null'] === 'NO') {
            $definicao .= " NOT NULL";
        }
        if ($row['Default'] !== null) {
            if (strtoupper($row['Default']) === 'CURRENT_TIMESTAMP') {
                $definicao .= " DEFAULT CURRENT_TIMESTAMP";
            } else {
                $definicao .= " DEFAULT '" . addslashes($row['Default']) . "'";
            }
        }
        if ($row['Extra']) {
            $definicao .= " {$row['Extra']}";
        }
        if ($row['Comment']) {
            $definicao .= " COMMENT '" . addslashes($row['Comment']) . "'";
        }
        $colunas[] = [
            'nome' => $row['Field'],
            'definicao' => $definicao
        ];
    }
    return $colunas;
}

function colunaExiste($colunasDestino, $nomeColuna) {
    foreach ($colunasDestino as $coluna) {
        if ($coluna['nome'] === $nomeColuna) {
            return true;
        }
    }
    return false;
}

function getCreateTable($pdo, $tabela) {
    $stmt = $pdo->query("SHOW CREATE TABLE `$tabela`");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['Create Table'] . ";";
}
?>