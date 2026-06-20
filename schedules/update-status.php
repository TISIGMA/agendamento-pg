<?php
// Habilitar exibição de erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../conn.php');
require_once('../session.php');
require_once('../repository/scheduleRepository.php');
require_once('../utils.php');

// Log de debug
error_log('update-status.php chamado');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log('Método não permitido: ' . $_SERVER['REQUEST_METHOD']);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$campo = isset($_POST['campo']) ? $_POST['campo'] : '';
$valor = isset($_POST['valor']) ? $_POST['valor'] : '';

error_log('Dados recebidos: id=' . $id . ', campo=' . $campo . ', valor=' . $valor);

if (!$id || !in_array($campo, ['scaneado', 'carga_em_qualidade']) || !in_array($valor, ['Sim', 'Não'])) {
    error_log('Dados inválidos');
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

try {
    $scheduleRepository = new ScheduleRepository($MySQLi);
    error_log('ScheduleRepository inicializado');
    
    // Usamos o método existente para atualizar campo individual
    $result = $scheduleRepository->updateAttAction($id, $campo, $valor);
    error_log('Resultado do update: ' . var_export($result, true));
    
    if (!$result) {
        throw new Exception('Erro ao atualizar');
    }
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log('Exceção: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
