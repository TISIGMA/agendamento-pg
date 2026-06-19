<?php
require_once('../session.php');
require_once('../repository/scheduleRepository.php');
require_once('../utils.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$campo = isset($_POST['campo']) ? $_POST['campo'] : '';
$valor = isset($_POST['valor']) ? $_POST['valor'] : '';

if (!$id || !in_array($campo, ['scaneado', 'carga_em_qualidade']) || !in_array($valor, ['Sim', 'Não'])) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

try {
    $scheduleRepository = new ScheduleRepository($MySQLi);
    
    // Usamos o método existente para atualizar campo individual
    $result = $scheduleRepository->updateAttAction($id, $campo, $valor);
    
    if (!$result) {
        throw new Exception('Erro ao atualizar');
    }
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
