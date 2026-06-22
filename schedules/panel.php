<?php

require_once('../controller/scheduleController.php');
require_once('../utils.php');

date_default_timezone_set("America/Sao_Paulo");
$fieldsAccess = ($_SESSION['tipo'] != 'client') ? true : false;

$columns = [
    'status'               => ['name' => 'status',                'label'=> 'Status',         'order' => 0,  'value' => 'getStatus',             'columnSize'=> 'td-100', 'show' => true],
    'operationScheduleTime'=> ['name' => 'operationScheduleTime', 'label'=> 'Agendamento',    'order' => 1,  'value' => 'getDataAgendamento',    'columnSize'=> 'td-150', 'show' => true],
    'arrival'              => ['name' => 'arrival',               'label'=> 'Chegada',        'order' => 2,  'value' => 'getHoraChegada',        'columnSize'=> 'td-150', 'show' => true],
    'operationStart'       => ['name' => 'operationStart',        'label'=> 'Início',         'order' => 3,  'value' => 'getInicioOperacao',     'columnSize'=> 'td-150', 'show' => true],
    'operationDone'        => ['name' => 'operationDone',         'label'=> 'Fim',            'order' => 4,  'value' => 'getFimOperacao',        'columnSize'=> 'td-150', 'show' => true],
    'operationExit'        => ['name' => 'operationExit',         'label'=> 'Saída',          'order' => 5,  'value' => 'getSaida',              'columnSize'=> 'td-150', 'show' => true],
    'operationType'        => ['name' => 'operationType',         'label'=> 'Operação',       'order' => 6,  'value' => 'getOperacao',           'columnSize'=> 'td-100', 'show' => true],
    'shippingCompany'      => ['name' => 'shippingCompany',       'label'=> 'Transportadora', 'order' => 7,  'value' => 'getTransportadora',     'columnSize'=> 'td-150', 'show' => true],
    'city'                 => ['name' => 'city',                  'label'=> 'Cidade',         'order' => 8,  'value' => 'getCidade',             'columnSize'=> 'td-100', 'show' => true],
    'driverName'           => ['name' => 'driverName',            'label'=> 'Nome Motorista', 'order' => 10, 'value' => 'getNomeMotorista',      'columnSize'=> 'td-150', 'show' => true],
    'licenceTruck'         => ['name' => 'licenceTruck',          'label'=> 'Placa Cavalo',   'order' => 11, 'value' => 'getPlacaCavalo',        'columnSize'=> 'td-120', 'show' => true],
    'dock'                 => ['name' => 'dock',                  'label'=> 'Doca',           'order' => 16, 'value' => 'getDoca',               'columnSize'=> 'td-70',  'show' => true],
    'invoice'              => ['name' => 'invoice',               'label'=> 'NF',             'order' => 19, 'value' => 'getNf',                 'columnSize'=> 'td-70',  'show' => true],
    'pallets'              => ['name' => 'pallets',               'label'=> 'Paletes',        'order' => 21, 'value' => 'getCargaQtde',          'columnSize'=> 'td-70',  'show' => true],
];

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
                'cidade' => $schedule['getCidade'] ?? '',
                'data_agendamento' => $schedule['getDataAgendamento'] ?? '',
                'att_invoice_status' => $schedule['getAttInvoiceStatus'] ?? 'open',
                'att_picking_status' => $schedule['getAttPickingStatus'] ?? 'open',
                'att_certificate_status' => $schedule['getAttCertificateStatus'] ?? 'open',
                'att_boarding_status' => $schedule['getAttBoardingStatus'] ?? 'open',
                'att_other_status' => $schedule['getAttOtherStatus'] ?? 'open',
                'scaneado' => $schedule['getScaneado'] ?? 'Não',
                'carga_em_qualidade' => $schedule['getCargaEmQualidade'] ?? 'Não',
                'has_invoice' => $schedule['has_invoice'] ?? false,
                'has_picking' => $schedule['has_picking'] ?? false,
                'has_certificate' => $schedule['has_certificate'] ?? false,
                'has_boarding' => $schedule['has_boarding'] ?? false,
                'has_other' => $schedule['has_other'] ?? false
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
                <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                    <form method="post" id="panel-form" action="index.php?conteudo=panel.php&startDate=<?=$startDate?>&endDate=<?=$endDate?>" style="margin: 0;">
                        <div class="row-element-group" style="display: flex; gap: 15px;">
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
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="form-group" style="margin-left: auto;">
                        <label>&nbsp;</label>
                        <div style="text-align: center;">
                            <button id="toggle-btn" class="btn btn-primary" onclick="toggleView()">QUADRO</button>
                        </div>
                    </div>
                </div>
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
            
            <div id="cards-view">
                <div class="panel-home">
                    <div class="card-wrapper">
                        <div class="schedule-box-status box-gray" onclick="navigateToSearch('Agendado')">
                            <div class="box-home-header">
                                <p>Agendados</p>
                                <img src="../images/home-icons/schedule-truck.png"></img>
                                <p class="home-box-text"><?=$scheduled ?></p>
                            </div>
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
                    </div>
                    <div class="card-wrapper">
                        <div class="schedule-box-status box-orange" onclick="navigateToSearch('Aguardando')">
                            <div class="box-home-header">
                                <p>Aguardando</p>
                                <img src="../images/home-icons/empty-truck.png"></img>
                                <p class="home-box-text"><?=$waiting ?></p>
                            </div>
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
                    </div>
                    <div class="card-wrapper">
                        <div class="schedule-box-status box-yellow" onclick="navigateToSearch('Fim de operação')">
                            <div class="box-home-header">
                                <p>Fim de operação</p>
                                <img src="../images/home-icons/full-truck.png"></img>
                                <p class="home-box-text"><?=$operationDone ?></p>
                            </div>
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
                    </div>
                </div>
                <div class="panel-progress">
                    <progress id="panel-progress-cards" value="60000" max="60000"></progress>
                </div>
            </div>
            
            <div id="quadro-view" style="display: none;">
                <div class="panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Quadro de Agendamentos</h3>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Carregamento BS</th>
                                        <th>Destino</th>
                                        <th>Janela</th>
                                        <th>Lançamento de carga?</th>
                                        <th>Carga escaneada?</th>
                                        <th>Chegada de veículo (Faturar)</th>
                                        <th>NF no site?</th>
                                        <th>Carregando ou rejeitado?</th>
                                        <th>Documentos ok ou aguardando?</th>
                                        <th>Tempo de espera</th>
                                        <th>Saída</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($schedules as $schedule): ?>
                                        <?php 
                                        // Filtra para não mostrar itens com status "Fim de operação" ou "Liberado" no quadro
                                        $status = $schedule['getStatus'];
                                        if ($status == 'Fim de operação' || $status == 'Liberado') {
                                            continue;
                                        }
                                        ?>
                                        <tr>
                                            <td><?php echo strtoupper($schedule['getShipmentId']); ?></td>
                                            <td><?php echo strtoupper($schedule['getCidade']); ?></td>
                                            <td><?php echo strtoupper($schedule['getDataAgendamento']); ?></td>
                                            <td style="background-color: <?php echo $schedule['has_picking'] ? '#4CAF50' : '#F44336'; ?>; color: white; text-align: center;">
                                                <?php echo $schedule['has_picking'] ? 'SIM' : 'NÃO'; ?>
                                            </td>
                                            <td style="background-color: <?php echo $schedule['getScaneado'] == 'Sim' ? '#4CAF50' : ($schedule['getScaneado'] == 'Não' ? '#F44336' : ''); ?>; color: white; text-align: center;">
                                                <?php echo strtoupper($schedule['getScaneado']); ?>
                                            </td>
                                            <td style="background-color: <?php echo $schedule['has_invoice'] ? '#4CAF50' : '#FFC107'; ?>; color: <?php echo $schedule['has_invoice'] ? 'white' : 'black'; ?>; text-align: center;">
                                                <?php echo $schedule['has_invoice'] ? 'SIM' : 'FATURAR'; ?>
                                            </td>
                                            <td style="background-color: <?php echo $schedule['has_invoice'] ? '#4CAF50' : '#F44336'; ?>; color: white; text-align: center;">
                                                <?php echo $schedule['has_invoice'] ? 'SIM' : 'NÃO'; ?>
                                            </td>
                                            <td style="background-color: <?php echo strtoupper($schedule['getCarregandoOuRejeitado']) == 'CARREGANDO' ? '#4CAF50' : (strtoupper($schedule['getCarregandoOuRejeitado']) == 'REJEITADO' ? '#F44336' : ''); ?>; color: white; text-align: center;">
                                                <?php echo strtoupper($schedule['getCarregandoOuRejeitado']); ?>
                                            </td>
                                            <td style="background-color: <?php echo strtoupper($schedule['getDocumentos']) == 'OK' ? '#4CAF50' : '#337ab7'; ?>; color: white; text-align: center;">
                                                <?php echo strtoupper($schedule['getDocumentos']) == 'OK' ? 'OK' : 'AGUARDANDO'; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $saida = $schedule['getSaida'];
                                                $carregandoOuRejeitado = $schedule['getCarregandoOuRejeitado'];
                                                $horaChegada = $schedule['getHoraChegada'];
                                                
                                                // Verifica se tem hora de saída OU está REJEITADO
                                                $temSaida = $saida != '';
                                                $estaRejeitado = strtoupper($carregandoOuRejeitado) == 'REJEITADO';
                                                
                                                if (!$temSaida && !$estaRejeitado && $horaChegada) {
                                                    // Usa DateTime para garantir o formato correto (dd/mm/aaaa HH:MM:SS)
                                                    $chegadaDateTime = DateTime::createFromFormat('d/m/Y H:i:s', $horaChegada);
                                                    if ($chegadaDateTime) {
                                                        $agora = new DateTime();
                                                        $diff = $agora->getTimestamp() - $chegadaDateTime->getTimestamp();
                                                        
                                                        if ($diff > 0) {
                                                            $horas = floor($diff / 3600);
                                                            $minutos = floor(($diff % 3600) / 60);
                                                            echo strtoupper("$horas h $minutos min");
                                                        }
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo strtoupper($schedule['getSaida']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="panel-progress">
                            <progress id="panel-progress" value="60000" max="60000"></progress>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
function toggleView() {
    var cardsView = document.getElementById('cards-view');
    var quadroView = document.getElementById('quadro-view');
    var btn = document.getElementById('toggle-btn');
    
    if (quadroView.style.display === 'none') {
        quadroView.style.display = 'block';
        cardsView.style.display = 'none';
        btn.textContent = 'CARDS';
        localStorage.setItem('agendamentoView', 'quadro');
    } else {
        quadroView.style.display = 'none';
        cardsView.style.display = 'block';
        btn.textContent = 'QUADRO';
        localStorage.setItem('agendamentoView', 'cards');
    }
}

$(document).ready(function() {
    console.log('Document ready');
    progressTimer(); // Inicia o timer quando o documento estiver pronto
    
    // Restaura a visualização salva
    var savedView = localStorage.getItem('agendamentoView');
    var cardsView = document.getElementById('cards-view');
    var quadroView = document.getElementById('quadro-view');
    var btn = document.getElementById('toggle-btn');
    
    if (savedView === 'quadro') {
        quadroView.style.display = 'block';
        cardsView.style.display = 'none';
        btn.textContent = 'CARDS';
    } else {
        // Padrão é cards
        quadroView.style.display = 'none';
        cardsView.style.display = 'block';
        btn.textContent = 'QUADRO';
    }
});
</script>
