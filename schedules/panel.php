<?php

require_once('../controller/scheduleController.php');
require_once('../utils.php');

date_default_timezone_set("America/Sao_Paulo");


$scheduleController = new ScheduleController($MySQLi);

$statusList = ['Agendado','Aguardando', 'Em operação', 'Fim de operação', 'Liberado'];

$startDate = date("d/m/Y") . ' 00:00:00';
$endDate = date("d/m/Y") . ' 23:59:59';

$scheduled = 0;
$waiting = 0;
$inOperation = 0;
$operationDone = 0;
$done = 0;
$scaneadas = 0;
$scaneadas = 0;
$inlocal = 0;

if(isset($_GET['startDate']) && $_GET['startDate'] != null){

    $startDate = $_GET['startDate'];
    $endDate = $_GET['endDate'];
}


if(isset($_POST['startDate']) && $_POST['startDate'] != null){

    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
}

// if(isset($_POST['order-action']) && $_POST['order-action'] != null){

//     if(isset($_POST['column']) && count($_POST['column']) > 0){
        
//         $result = $scheduleController->savePreferences($columns, $_POST);

//         switch ($result) {
//             case 'SAVED':
//                 successAlert('Preferências salvas com sucesso!');
//                 break;
            
//             case 'UPDATED':
//                 successAlert('Preferências atualizadas com sucesso!');
//                 break;
            
//             case 'SAVE_ERROR':
//                 errorAlert('Ocorreu um erro ao salvar a preferência. Tente novamente ou entre em contato com o administrador.');
//                 break;
//         }
//     }
// }

$schedules = $scheduleController->findByClientStatusStartDateAndEndDate($_SESSION['customerName'], 'Todos', $startDate, $endDate);

$scheduled = 0;
$waiting = 0;
$inOperation = 0;
$operationDone = 0;
$done = 0;
$scaneadas = 0;

// Initialize data arrays
$statusData = [
    'Agendado' => ['count' => 0, 'carga' => 0, 'docas' => [], 'nfs' => [], 'transportadoras' => [], 'shipments' => []],
    'Aguardando' => ['count' => 0, 'carga' => 0, 'docas' => [], 'nfs' => [], 'transportadoras' => [], 'shipments' => []],
    'Em operação' => ['count' => 0, 'carga' => 0, 'docas' => [], 'nfs' => [], 'transportadoras' => [], 'shipments' => []],
    'Fim de operação' => ['count' => 0, 'carga' => 0, 'docas' => [], 'nfs' => [], 'transportadoras' => [], 'shipments' => []],
    'Liberado' => ['count' => 0, 'carga' => 0, 'docas' => [], 'nfs' => [], 'transportadoras' => [], 'shipments' => []],
    'Cargas Scaneadas' => ['count' => 0, 'carga' => 0, 'docas' => [], 'nfs' => [], 'transportadoras' => [], 'shipments' => []]
];

foreach ($schedules as $schedule) {
    $status = $schedule['getStatus'] ?? 'Agendado';
    if (!isset($statusData[$status])) {
        $status = 'Agendado';
    }
    
    // Collect individual shipment details
            $shipmentData = [
                'id' => $schedule['getId'] ?? '',
                'shipment_id' => $schedule['getShipmentId'] ?? '',
                'operacao' => $schedule['getOperacao'] ?? '',
                'data_agendamento' => $schedule['getDataAgendamento'] ?? '',
                'att_invoice_status' => $schedule['getAttInvoiceStatus'] ?? 'open',
                'att_picking_status' => $schedule['getAttPickingStatus'] ?? 'open',
                'att_certificate_status' => $schedule['getAttCertificateStatus'] ?? 'open',
                'att_boarding_status' => $schedule['getAttBoardingStatus'] ?? 'open',
                'att_other_status' => $schedule['getAttOtherStatus'] ?? 'open',
                'scaneado' => $schedule['getScaneado'] ?? 'Não',
                'carga_em_qualidade' => $schedule['getCargaEmQualidade'] ?? 'Não'
            ];
    
    // Check if carga is scaneada - se for, adiciona apenas ao card "Cargas Scaneadas"
    $scaneado = $schedule['getScaneado'] ?? 'Não';
    if ($scaneado === 'Sim' && $status === 'Agendado') {
        $scaneadas++;
        $statusData['Cargas Scaneadas']['count']++;
        $statusData['Cargas Scaneadas']['shipments'][] = $shipmentData;
        
        // Add carga to Cargas Scaneadas
        $cargaScaneada = $schedule['getCargaQtde'] ?? 0;
        $statusData['Cargas Scaneadas']['carga'] += floatval($cargaScaneada);
        
        // Add doca to Cargas Scaneadas
        $docaScaneada = $schedule['getDoca'] ?? '';
        if ($docaScaneada) {
            $statusData['Cargas Scaneadas']['docas'][$docaScaneada] = true;
        }
        
        // Add NF to Cargas Scaneadas
        $nfScaneada = $schedule['getNf'] ?? '';
        if ($nfScaneada) {
            $statusData['Cargas Scaneadas']['nfs'][$nfScaneada] = true;
        }
        
        // Add transportadora to Cargas Scaneadas
        $transportadoraScaneada = $schedule['getTransportadora'] ?? '';
        if ($transportadoraScaneada) {
            $statusData['Cargas Scaneadas']['transportadoras'][$transportadoraScaneada] = true;
        }
        
        // Pula para não adicionar ao card "Agendado"
        continue;
    }
    
    // Se não for scaneada, adiciona ao card do status normal
    $statusData[$status]['count']++;
    $statusData[$status]['shipments'][] = $shipmentData;
    
    // Add carga
    $carga = $schedule['getCargaQtde'] ?? 0;
    $statusData[$status]['carga'] += floatval($carga);
    
    // Add doca
    $doca = $schedule['getDoca'] ?? '';
    if ($doca) {
        $statusData[$status]['docas'][$doca] = true;
    }
    
    // Add NF
    $nf = $schedule['getNf'] ?? '';
    if ($nf) {
        $statusData[$status]['nfs'][$nf] = true;
    }
    
    // Add transportadora
    $transportadora = $schedule['getTransportadora'] ?? '';
    if ($transportadora) {
        $statusData[$status]['transportadoras'][$transportadora] = true;
    }

    switch ($status) {
        case 'Agendado':
            $scheduled++;
            break;

        case 'Aguardando':
            $waiting++;
            $inlocal++;
            break;

        case 'Em operação':
            $inOperation++;
            $inlocal++;
            break;

        case 'Fim de operação':
            $operationDone++;
            $inlocal++;
            break;

        case 'Liberado':
            $done++;
            break;
        
        default:
            $scheduled++;
            break;
    }
    
}

?>

<body onload="progressTimer()">
    <div class="row">
        <div class="col-lg-12">
            <div class="panel-title">
                <p>Painel</p>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="automatedTimeSwitch" onchange="HandleChangeAutomatedTimeSwitch()" checked>
                    <label class="form-check-label" for="automatedTimeSwitch">
                        Ativar atualização automática
                    </label>
                </div>
            </div>
            <div class="functions-group">
                <form method="post" id="panel-form" action="index.php?conteudo=panel.php&startDate=<?=$startDate?>&endDate=<?=$endDate?>">
                    <div class="row-element-group">
                        <div class="form-group">
                            <label>Data inicial</label>
                            <div class='input-group date' id='datetimepicker1'>
                                <input name="startDate" id="startDate" type='text' data-date-format="DD/MM/YYYY HH:mm:ss" class="form-control" value="<?=$startDate ?>" onblur="dateTimeHandleBlur(this)" required  minlength="19" maxlength="19" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Data final</label>
                            <div class='input-group date' id='datetimepicker1'>
                                <input name="endDate" id="endDate" type='text' data-date-format="DD/MM/YYYY HH:mm:ss" class="form-control" onblur="dateTimeHandleBlur(this)" value="<?=$endDate ?>" minlength="19" maxlength="19"  required/>
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Buscar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel-title">
                <h1 class="display-2">Agendamentos</h1>
            </div>
            <div class="label-terminal-panel">
                <div>
                    <span>Total de <span class="label-big-number"><?=$inlocal ?></span> veículos no terminal</span>
                    
                </div>
            </div>
            <div class="panel-home">
                <div class="card-wrapper">
                    <div class="schedule-box-status box-gray" onclick="navigateToSearch('Agendado')">
                        <div class="box-home-header">
                            <p>Agendados</p>
                            <img src="../images/home-icons/schedule-truck.png"></img>
                            <p class="home-box-text"><?=$scheduled ?></p>
                        </div>
                    </div>
                    <div class="card-details-grid card-shipments-grid">
                        <?php foreach ($statusData['Agendado']['shipments'] as $shipment): 
                            // Verifica status da NF
                            $nfBadgeClass = ($shipment['att_invoice_status'] === 'closed') ? 'badge-green' : 'badge-red';
                            $nfBadgeText = ($shipment['att_invoice_status'] === 'closed') ? 'NF OK' : 'NF Pendente';
                            
                            // Verifica status dos outros documentos
                            $allDocsOk = (
                                $shipment['att_picking_status'] === 'closed' &&
                                $shipment['att_certificate_status'] === 'closed' &&
                                $shipment['att_boarding_status'] === 'closed' &&
                                $shipment['att_other_status'] === 'closed'
                            );
                            $docsBadgeClass = $allDocsOk ? 'badge-green' : 'badge-yellow';
                            $docsBadgeText = $allDocsOk ? 'Docs OK' : 'Docs Pendentes';
                        ?>
                            <div class="detail-item">
                                <span class="detail-label">Shipment</span>
                                <span class="detail-value"><?=htmlspecialchars($shipment['shipment_id'] ?: '-') ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Operação</span>
                                <span class="detail-value"><?=htmlspecialchars($shipment['operacao'] ?: '-') ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Status</span>
                                <div style="margin-top: 4px;">
                                    <span class="status-badge <?=htmlspecialchars($nfBadgeClass) ?>"><?=htmlspecialchars($nfBadgeText) ?></span>
                                    <span class="status-badge <?=htmlspecialchars($docsBadgeClass) ?>" style="margin-left: 4px;"><?=htmlspecialchars($docsBadgeText) ?></span>
                                </div>
                            </div>
                            <div class="detail-item">
                                <button class="btn btn-xs btn-success mark-scaneado-btn" data-id="<?=htmlspecialchars($shipment['id']) ?>" style="margin-top: 5px; cursor: pointer;">
                                    Scan
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="card-wrapper">
                    <div class="schedule-box-status box-purple" onclick="navigateToSearch('Agendado')">
                        <div class="box-home-header">
                            <p>Cargas Scaneadas</p>
                            <img src="../images/home-icons/scaneada.png"></img>
                            <p class="home-box-text"><?=$scaneadas ?></p>
                        </div>
                    </div>
                    <div class="card-details-grid card-shipments-grid">
                        <?php foreach ($statusData['Cargas Scaneadas']['shipments'] as $shipment): 
                            $nfBadgeClass = ($shipment['att_invoice_status'] === 'closed') ? 'badge-green' : 'badge-red';
                            $nfBadgeText = ($shipment['att_invoice_status'] === 'closed') ? 'NF OK' : 'NF Pendente';
                            $allDocsOk = (
                                $shipment['att_picking_status'] === 'closed' &&
                                $shipment['att_certificate_status'] === 'closed' &&
                                $shipment['att_boarding_status'] === 'closed' &&
                                $shipment['att_other_status'] === 'closed'
                            );
                            $docsBadgeClass = $allDocsOk ? 'badge-green' : 'badge-yellow';
                            $docsBadgeText = $allDocsOk ? 'Docs OK' : 'Docs Pendentes';
                        ?>
                            <div class="detail-item">
                                <span class="detail-label">Shipment</span>
                                <span class="detail-value"><?=htmlspecialchars($shipment['shipment_id'] ?: '-') ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Operação</span>
                                <span class="detail-value"><?=htmlspecialchars($shipment['operacao'] ?: '-') ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Status</span>
                                <div style="margin-top: 4px;">
                                    <span class="status-badge <?=htmlspecialchars($nfBadgeClass) ?>"><?=htmlspecialchars($nfBadgeText) ?></span>
                                    <span class="status-badge <?=htmlspecialchars($docsBadgeClass) ?>" style="margin-left: 4px;"><?=htmlspecialchars($docsBadgeText) ?></span>
                                </div>
                            </div>
                            <div class="detail-item">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="card-wrapper">
                    <div class="schedule-box-status box-orange" onclick="navigateToSearch('Aguardando')">
                        <div class="box-home-header">
                            <p>Aguardando</p>
                            <img src="../images/home-icons/empty-truck.png"></img>
                            <p class="home-box-text"><?=$waiting ?></p>
                        </div>
                    </div>
                    <div class="card-details-grid card-shipments-grid">
                        <?php foreach ($statusData['Aguardando']['shipments'] as $shipment): 
                            $nfBadgeClass = ($shipment['att_invoice_status'] === 'closed') ? 'badge-green' : 'badge-red';
                            $nfBadgeText = ($shipment['att_invoice_status'] === 'closed') ? 'NF OK' : 'NF Pendente';
                            $allDocsOk = (
                                $shipment['att_picking_status'] === 'closed' &&
                                $shipment['att_certificate_status'] === 'closed' &&
                                $shipment['att_boarding_status'] === 'closed' &&
                                $shipment['att_other_status'] === 'closed'
                            );
                            $docsBadgeClass = $allDocsOk ? 'badge-green' : 'badge-yellow';
                            $docsBadgeText = $allDocsOk ? 'Docs OK' : 'Docs Pendentes';
                        ?>
                            <div class="detail-item">
                                <span class="detail-label">Shipment</span>
                                <span class="detail-value"><?=htmlspecialchars($shipment['shipment_id'] ?: '-') ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Operação</span>
                                <span class="detail-value"><?=htmlspecialchars($shipment['operacao'] ?: '-') ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Status</span>
                                <div style="margin-top: 4px;">
                                    <span class="status-badge <?=htmlspecialchars($nfBadgeClass) ?>"><?=htmlspecialchars($nfBadgeText) ?></span>
                                    <span class="status-badge <?=htmlspecialchars($docsBadgeClass) ?>" style="margin-left: 4px;"><?=htmlspecialchars($docsBadgeText) ?></span>
                                </div>
                            </div>
                            <div class="detail-item">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="card-wrapper">
                    <div class="schedule-box-status box-blue" onclick="navigateToSearch('Em operação')">
                        <div class="box-home-header">
                            <p>Em operação</p>
                            <img src="../images/home-icons/operation-truck.png"></img>
                            <p class="home-box-text"><?=$inOperation ?></p>
                        </div>
                    </div>
                    <div class="card-details-grid card-shipments-grid">
                        <?php foreach ($statusData['Em operação']['shipments'] as $shipment): 
                            $nfBadgeClass = ($shipment['att_invoice_status'] === 'closed') ? 'badge-green' : 'badge-red';
                            $nfBadgeText = ($shipment['att_invoice_status'] === 'closed') ? 'NF OK' : 'NF Pendente';
                            $allDocsOk = (
                                $shipment['att_picking_status'] === 'closed' &&
                                $shipment['att_certificate_status'] === 'closed' &&
                                $shipment['att_boarding_status'] === 'closed' &&
                                $shipment['att_other_status'] === 'closed'
                            );
                            $docsBadgeClass = $allDocsOk ? 'badge-green' : 'badge-yellow';
                            $docsBadgeText = $allDocsOk ? 'Docs OK' : 'Docs Pendentes';
                        ?>
                            <div class="detail-item">
                                <span class="detail-label">Shipment</span>
                                <span class="detail-value"><?=htmlspecialchars($shipment['shipment_id'] ?: '-') ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Operação</span>
                                <span class="detail-value"><?=htmlspecialchars($shipment['operacao'] ?: '-') ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Status</span>
                                <div style="margin-top: 4px;">
                                    <span class="status-badge <?=htmlspecialchars($nfBadgeClass) ?>"><?=htmlspecialchars($nfBadgeText) ?></span>
                                    <span class="status-badge <?=htmlspecialchars($docsBadgeClass) ?>" style="margin-left: 4px;"><?=htmlspecialchars($docsBadgeText) ?></span>
                                </div>
                            </div>
                            <div class="detail-item">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="card-wrapper">
                    <div class="schedule-box-status box-yellow" onclick="navigateToSearch('Fim de operação')">
                        <div class="box-home-header">
                            <p>Fim de operação</p>
                            <img src="../images/home-icons/full-truck.png"></img>
                            <p class="home-box-text"><?=$operationDone ?></p>
                        </div>
                    </div>
                    <div class="card-details-grid card-shipments-grid">
                        <?php foreach ($statusData['Fim de operação']['shipments'] as $shipment): 
                            $nfBadgeClass = ($shipment['att_invoice_status'] === 'closed') ? 'badge-green' : 'badge-red';
                            $nfBadgeText = ($shipment['att_invoice_status'] === 'closed') ? 'NF OK' : 'NF Pendente';
                            $allDocsOk = (
                                $shipment['att_picking_status'] === 'closed' &&
                                $shipment['att_certificate_status'] === 'closed' &&
                                $shipment['att_boarding_status'] === 'closed' &&
                                $shipment['att_other_status'] === 'closed'
                            );
                            $docsBadgeClass = $allDocsOk ? 'badge-green' : 'badge-yellow';
                            $docsBadgeText = $allDocsOk ? 'Docs OK' : 'Docs Pendentes';
                        ?>
                            <div class="detail-item">
                                <span class="detail-label">Shipment</span>
                                <span class="detail-value"><?=htmlspecialchars($shipment['shipment_id'] ?: '-') ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Operação</span>
                                <span class="detail-value"><?=htmlspecialchars($shipment['operacao'] ?: '-') ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Status</span>
                                <div style="margin-top: 4px;">
                                    <span class="status-badge <?=htmlspecialchars($nfBadgeClass) ?>"><?=htmlspecialchars($nfBadgeText) ?></span>
                                    <span class="status-badge <?=htmlspecialchars($docsBadgeClass) ?>" style="margin-left: 4px;"><?=htmlspecialchars($docsBadgeText) ?></span>
                                </div>
                            </div>
                            <div class="detail-item">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="card-wrapper">
                    <div class="schedule-box-status box-green" onclick="navigateToSearch('Liberado')">
                        <div class="box-home-header">
                            <p>Liberados</p>
                            <img src="../images/home-icons/done-truck.png"></img>
                            <p class="home-box-text"><?=$done ?></p>
                        </div>
                    </div>
                    <div class="card-details-grid card-shipments-grid">
                        <?php foreach ($statusData['Liberado']['shipments'] as $shipment): 
                            $nfBadgeClass = ($shipment['att_invoice_status'] === 'closed') ? 'badge-green' : 'badge-red';
                            $nfBadgeText = ($shipment['att_invoice_status'] === 'closed') ? 'NF OK' : 'NF Pendente';
                            $allDocsOk = (
                                $shipment['att_picking_status'] === 'closed' &&
                                $shipment['att_certificate_status'] === 'closed' &&
                                $shipment['att_boarding_status'] === 'closed' &&
                                $shipment['att_other_status'] === 'closed'
                            );
                            $docsBadgeClass = $allDocsOk ? 'badge-green' : 'badge-yellow';
                            $docsBadgeText = $allDocsOk ? 'Docs OK' : 'Docs Pendentes';
                        ?>
                            <div class="detail-item">
                                <span class="detail-label">Shipment</span>
                                <span class="detail-value"><?=htmlspecialchars($shipment['shipment_id'] ?: '-') ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Operação</span>
                                <span class="detail-value"><?=htmlspecialchars($shipment['operacao'] ?: '-') ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Status</span>
                                <div style="margin-top: 4px;">
                                    <span class="status-badge <?=htmlspecialchars($nfBadgeClass) ?>"><?=htmlspecialchars($nfBadgeText) ?></span>
                                    <span class="status-badge <?=htmlspecialchars($docsBadgeClass) ?>" style="margin-left: 4px;"><?=htmlspecialchars($docsBadgeText) ?></span>
                                </div>
                            </div>
                            <div class="detail-item">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="panel-progress">
                <progress id="panel-progress" value="60000" max="60000"></progress>
            </div>
        </div>
    </div>
<script>
$(document).ready(function() {
    console.log('Document ready');
    
    $('.mark-scaneado-btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Evita que o clique no botão acione o clique no card
        
        var id = $(this).data('id');
        console.log('Clicou no botão para ID:', id);
        
        $.ajax({
            url: 'update-status.php',
            method: 'POST',
            data: {
                id: id,
                campo: 'scaneado',
                valor: 'Sim'
            },
            success: function(response) {
                console.log('Resposta do servidor:', response);
                // Recarrega a página para atualizar os contadores dos cards
                location.reload();
            },
            error: function(xhr, status, error) {
                console.error('Erro na requisição:', error);
                alert('Erro ao marcar como scaneado!');
            }
        });
    });
});
</script>
</body>