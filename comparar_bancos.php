<?php
header('Content-Type: text/html; charset=utf-8');

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

    echo "<h2>Comparando bancos de dados:</h2>";
    echo "<p><strong>Fonte:</strong> $bancoFonte</p>";
    echo "<p><strong>Destino:</strong> $bancoDestino</p>";
    echo "<hr>";

    $tabelasFonte = getTabelas($pdoFonte);
    $tabelasDestino = getTabelas($pdoDestino);

    $sqlComandos = [];

    foreach ($tabelasFonte as $tabela) {
        if (!in_array($tabela, $tabelasDestino)) {
            echo "<h3>Tabela nova encontrada: $tabela</h3>";
            $createSql = getCreateTable($pdoFonte, $tabela);
            $sqlComandos[] = $createSql;
            echo "<pre>$createSql</pre>";
        } else {
            $colunasFonte = getColunas($pdoFonte, $tabela);
            $colunasDestino = getColunas($pdoDestino, $tabela);
            
            foreach ($colunasFonte as $coluna) {
                if (!colunaExiste($colunasDestino, $coluna['nome'])) {
                    echo "<h4>Coluna nova na tabela $tabela: {$coluna['nome']}</h4>";
                    $alterSql = "ALTER TABLE `$tabela` ADD COLUMN {$coluna['definicao']};";
                    $sqlComandos[] = $alterSql;
                    echo "<pre>$alterSql</pre>";
                }
            }
        }
    }

    echo "<hr>";
    echo "<h2>Comandos SQL para sincronizar:</h2>";
    echo "<pre>";
    echo implode("\n\n", $sqlComandos);
    echo "</pre>";

    echo "<hr>";
    echo "<h2>Executar comandos automaticamente?</h2>";
    echo '<form method="post">';
    echo '<input type="submit" name="executar" value="Executar Comandos" style="padding:10px 20px; font-size:16px; cursor:pointer;">';
    echo '</form>';

    if (isset($_POST['executar'])) {
        echo "<hr>";
        echo "<h2>Executando comandos...</h2>";
        foreach ($sqlComandos as $sql) {
            try {
                $pdoDestino->exec($sql);
                echo "<p style='color:green;'>✓ Executado com sucesso: " . substr($sql, 0, 100) . "...</p>";
            } catch (PDOException $e) {
                echo "<p style='color:red;'>✗ Erro: " . $e->getMessage() . "</p>";
                echo "<pre>$sql</pre>";
            }
        }
        echo "<h3 style='color:green;'>Processo concluído!</h3>";
    }

} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
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