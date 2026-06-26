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
    <style>
        /* Preserva os estilos em tela cheia - GARANTE SCROLL! */
        .fullscreen-preserve-styles:fullscreen, 
        .fullscreen-preserve-styles:-webkit-full-screen, 
        .fullscreen-preserve-styles:-moz-full-screen, 
        .fullscreen-preserve-styles:-ms-fullscreen {
            background: white !important;
            background-color: white !important;
            padding: 40px !important;
            overflow-y: auto !important; /* GARANTE SCROLL VERTICAL */
            overflow-x: auto !important; /* GARANTE SCROLL HORIZONTAL */
            width: 100vw !important;
            height: 100vh !important;
            box-sizing: border-box !important;
            zoom: 1 !important; /* EVITA ZOOM NA TELA CHEIA */
            -moz-transform: none !important; /* EVITA SCALING NO FIREFOX */
            -webkit-transform: none !important; /* EVITA SCALING NO CHROME/SAFARI */
            -ms-transform: none !important; /* EVITA SCALING NO IE/EDGE */
            transform: none !important;
        }
        
        /* Força o fundo branco para o documento todo em tela cheia - ABSOLUTAMENTE BRANCO */
        :fullscreen, :-webkit-full-screen, :-moz-full-screen, :-ms-fullscreen {
            background-color: white !important;
            background: white !important;
            zoom: 1 !important;
            -moz-transform: none !important;
            -webkit-transform: none !important;
            -ms-transform: none !important;
            transform: none !important;
        }
        
        /* CORRIGE O FUNDO PRETO - MUDA O PSEUDO-ELEMENTO BACKDROP DO NAVEGADOR */
        ::backdrop, ::-ms-backdrop {
            background-color: white !important;
        }
        
        /* GARANTE QUE TUDO DENTRO DO ELEMENTO TENHA FUNDO BRANCO (exceto as células coloridas) */
        .fullscreen-preserve-styles:fullscreen, 
        .fullscreen-preserve-styles:-webkit-full-screen, 
        .fullscreen-preserve-styles:-moz-full-screen, 
        .fullscreen-preserve-styles:-ms-fullscreen {
            background-color: white !important;
        }
        

        
        /* PRESERVAÇÃO ABSOLUTA DAS CORES DAS CÉLULAS - NÃO SOBRESCREVE NADA */
        /* Esta é a regra MAIS ESPECÍFICA, para garantir que as cores sejam preservadas */
        .fullscreen-preserve-styles:fullscreen td[style], 
        .fullscreen-preserve-styles:-webkit-full-screen td[style],
        .fullscreen-preserve-styles:-moz-full-screen td[style], 
        .fullscreen-preserve-styles:-ms-full-screen td[style],
        .fullscreen-preserve-styles:fullscreen th[style], 
        .fullscreen-preserve-styles:-webkit-full-screen th[style],
        .fullscreen-preserve-styles:-moz-full-screen th[style], 
        .fullscreen-preserve-styles:-ms-full-screen th[style] {
            /* NÃO DEIXA NENHUM ESTILO SOBRESCREVER OS ESTILOS INLINE */
            background-image: none !important;
            background-clip: border-box !important;
        }
        
        /* Para células sem estilo inline, preserva as listras */
        .fullscreen-preserve-styles:fullscreen .table-striped>tbody>tr:nth-of-type(odd)>td:not([style]),
        .fullscreen-preserve-styles:-webkit-full-screen .table-striped>tbody>tr:nth-of-type(odd)>td:not([style]),
        .fullscreen-preserve-styles:-moz-full-screen .table-striped>tbody>tr:nth-of-type(odd)>td:not([style]),
        .fullscreen-preserve-styles:-ms-full-screen .table-striped>tbody>tr:nth-of-type(odd)>td:not([style]) {
            background-color: #f9f9f9 !important;
        }
        
        /* Preserva as listras da tabela para LINHAS SEM ESTILO INLINE */
        .fullscreen-preserve-styles:fullscreen .table-striped tbody tr:nth-child(odd):not([style]), 
        .fullscreen-preserve-styles:-webkit-full-screen .table-striped tbody tr:nth-child(odd):not([style]),
        .fullscreen-preserve-styles:-moz-full-screen .table-striped tbody tr:nth-child(odd):not([style]), 
        .fullscreen-preserve-styles:-ms-fullscreen .table-striped tbody tr:nth-child(odd):not([style]) {
            background-color: #f9f9f9 !important;
        }
        
        /* Preserva todos os estilos do Bootstrap - NÃO INTERFERE NAS CÉLULAS */
        .fullscreen-preserve-styles:fullscreen .table, 
        .fullscreen-preserve-styles:-webkit-full-screen .table,
        .fullscreen-preserve-styles:-moz-full-screen .table, 
        .fullscreen-preserve-styles:-ms-full-screen .table {
            width: 100% !important;
            max-width: 100% !important;
            margin-bottom: 20px !important;
            background-color: transparent !important;
            border-spacing: 0 !important;
            border-collapse: collapse !important;
        }
        
        .fullscreen-preserve-styles:fullscreen .table-bordered, 
        .fullscreen-preserve-styles:-webkit-full-screen .table-bordered,
        .fullscreen-preserve-styles:-moz-full-screen .table-bordered, 
        .fullscreen-preserve-styles:-ms-full-screen .table-bordered {
            border: 1px solid #ddd !important;
        }
        
        .fullscreen-preserve-styles:fullscreen .table-bordered th, 
        .fullscreen-preserve-styles:-webkit-full-screen .table-bordered th,
        .fullscreen-preserve-styles:-moz-full-screen .table-bordered th, 
        .fullscreen-preserve-styles:-ms-full-screen .table-bordered th {
            border: 1px solid #ddd !important;
            padding: 8px !important;
            vertical-align: top !important;
        }
        
        /* Apenas estilos base do Bootstrap - NÃO TOCA NAS CORES DAS CÉLULAS */
        .fullscreen-preserve-styles:fullscreen .table-bordered td, 
        .fullscreen-preserve-styles:-webkit-full-screen .table-bordered td,
        .fullscreen-preserve-styles:-moz-full-screen .table-bordered td, 
        .fullscreen-preserve-styles:-ms-full-screen .table-bordered td {
            border: 1px solid #ddd !important;
            padding: 8px !important;
            vertical-align: top !important;
        }
        
        .fullscreen-preserve-styles:fullscreen .panel, 
        .fullscreen-preserve-styles:-webkit-full-screen .panel,
        .fullscreen-preserve-styles:-moz-full-screen .panel, 
        .fullscreen-preserve-styles:-ms-full-screen .panel {
            margin-bottom: 20px !important;
            background-color: #fff !important;
            border: 1px solid transparent !important;
            border-radius: 4px !important;
            box-shadow: 0 1px 1px rgba(0,0,0,.05) !important;
        }
        
        .fullscreen-preserve-styles:fullscreen .panel-default, 
        .fullscreen-preserve-styles:-webkit-full-screen .panel-default,
        .fullscreen-preserve-styles:-moz-full-screen .panel-default, 
        .fullscreen-preserve-styles:-ms-full-screen .panel-default {
            border-color: #ddd !important;
        }
        
        .fullscreen-preserve-styles:fullscreen .panel-heading, 
        .fullscreen-preserve-styles:-webkit-full-screen .panel-heading,
        .fullscreen-preserve-styles:-moz-full-screen .panel-heading, 
        .fullscreen-preserve-styles:-ms-full-screen .panel-heading {
            padding: 10px 15px !important;
            border-bottom: 1px solid transparent !important;
            border-top-left-radius: 3px !important;
            border-top-right-radius: 3px !important;
            color: #333 !important;
            background-color: #f5f5f5 !important;
            border-color: #ddd !important;
        }
        
        .fullscreen-preserve-styles:fullscreen .panel-title, 
        .fullscreen-preserve-styles:-webkit-full-screen .panel-title,
        .fullscreen-preserve-styles:-moz-full-screen .panel-title, 
        .fullscreen-preserve-styles:-ms-full-screen .panel-title {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
            font-size: 16px !important;
            color: inherit !important;
            font-weight: 500 !important;
            line-height: 1.1 !important;
        }
        
        .fullscreen-preserve-styles:fullscreen .panel-body, 
        .fullscreen-preserve-styles:-webkit-full-screen .panel-body,
        .fullscreen-preserve-styles:-moz-full-screen .panel-body, 
        .fullscreen-preserve-styles:-ms-full-screen .panel-body {
            padding: 15px !important;
        }
        
        .fullscreen-preserve-styles:fullscreen .table-responsive, 
        .fullscreen-preserve-styles:-webkit-full-screen .table-responsive,
        .fullscreen-preserve-styles:-moz-full-screen .table-responsive, 
        .fullscreen-preserve-styles:-ms-full-screen .table-responsive {
            min-height: .01% !important;
            overflow-x: auto !important;
        }

        /* Modo ampliado persistente do painel */
        body.panel-expanded-active {
            overflow: hidden;
            background: #ffffff;
        }

        body.panel-expanded-active #fullscreenBtn {
            position: fixed !important;
            top: 10px !important;
            right: 15px !important;
            z-index: 10001 !important;
        }

        #main-content.panel-expanded-mode {
            position: fixed;
            inset: 0;
            z-index: 10000;
            width: 100vw;
            height: 100vh;
            padding: 24px 32px 32px;
            overflow-y: auto;
            overflow-x: auto;
            box-sizing: border-box;
            background: #ffffff;
        }

        #main-content.panel-expanded-mode .table-responsive {
            overflow-x: auto;
        }

        #main-content.panel-expanded-mode .btn-panel-collapse {
            display: inline-block !important;
        }
        
        @keyframes blink-red {
            0%, 100% { background-color: transparent; }
            50% { background-color: #ffcccc; }
        }
        
        .tempo-espera-alerta {
            animation: blink-red 1s infinite;
            color: #ff0000;
            font-weight: bold;
        }
    </style>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel-title" style="display: flex; justify-content: space-between; align-items: center;">
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
                    <div class="form-group" style="display: flex; gap: 10px; align-items: flex-end;">
                        <div style="text-align: center;">
                            <label>&nbsp;</label>
                            <div>
                                <button id="toggle-btn" class="btn btn-primary" onclick="toggleView()">QUADRO</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12" style="position: relative;">
            <!-- Botão no canto direito extremo -->
            <div style="position: absolute; right: 15px; top: 10px; z-index: 1000;">
                <button class="btn btn-default" id="fullscreenBtn" onclick="toggleFullScreen()" title="Alternar tela cheia" style="padding: 8px 12px;">
                    <span class="glyphicon glyphicon-fullscreen"></span>
                </button>
            </div>
            
            <div id="main-content" class="fullscreen-preserve-styles" style="overflow-y: auto; overflow-x: auto;">
                <div class="panel-title" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h1 class="display-2">Agendamentos</h1>
                    <button class="btn btn-default btn-sm btn-panel-collapse" onclick="toggleFullScreen()" style="display: none;">
                        <span class="glyphicon glyphicon-resize-small"></span> Sair do Modo Ampliado
                    </button>
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
            
            <div id="quadro-view" class="fullscreen-preserve-styles" style="display: none;">
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
                                        <th>Chegada do veículo (faturado?)</th>
                                        <th>Material liberado?</th>
                                        <th>Carregando ou rejeitado?</th>
                                        <th>Documentos ok ou aguardando?</th>
                                        <th>Tempo de espera</th>
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
                                        <tr style="cursor: pointer;" onclick="window.location.href='index.php?customer=<?=$_SESSION['customerName']?>&conteudo=newSchedule.php&search=<?=$schedule['getId']?>'">
                                            <td><?php echo strtoupper($schedule['getShipmentId']); ?></td>
                                            <td><?php echo strtoupper($schedule['getCidade']); ?></td>
                                            <td><?php echo strtoupper($schedule['getDataAgendamento']); ?></td>
                                            <td style="background-color: <?php echo $schedule['has_picking'] ? '#4CAF50' : '#F44336'; ?> !important; color: white !important; text-align: center;">
                                                <?php echo $schedule['has_picking'] ? 'SIM' : 'NÃO'; ?>
                                            </td>
                                            <td style="background-color: <?php echo $schedule['getScaneado'] == 'Sim' ? '#4CAF50' : ($schedule['getScaneado'] == 'Não' ? '#F44336' : ''); ?> !important; color: white !important; text-align: center;">
                                                <?php echo strtoupper($schedule['getScaneado']); ?>
                                            </td>
                                            <td style="background-color: <?php echo $schedule['has_invoice'] ? '#4CAF50' : '#FFC107'; ?> !important; color: <?php echo $schedule['has_invoice'] ? 'white' : 'black'; ?> !important; text-align: center;">
                                                <?php echo $schedule['has_invoice'] ? 'SIM' : 'FATURAR'; ?>
                                            </td>
                                            <td style="background-color: <?php echo $schedule['getCargaEmQualidade'] == 'Sim' ? '#4CAF50' : ($schedule['getCargaEmQualidade'] == 'Não' ? '#F44336' : ''); ?> !important; color: white !important; text-align: center;">
                                                <?php echo strtoupper($schedule['getCargaEmQualidade']); ?>
                                            </td>
                                            <td style="background-color: <?php echo strtoupper($schedule['getCarregandoOuRejeitado']) == 'CARREGANDO' ? '#4CAF50' : (strtoupper($schedule['getCarregandoOuRejeitado']) == 'REJEITADO' ? '#F44336' : ''); ?> !important; color: white !important; text-align: center;">
                                                <?php echo strtoupper($schedule['getCarregandoOuRejeitado']); ?>
                                            </td>
                                            <td style="background-color: <?php echo strtoupper($schedule['getDocumentos']) == 'OK' ? '#4CAF50' : '#337ab7'; ?> !important; color: white !important; text-align: center;">
                                                <?php echo strtoupper($schedule['getDocumentos']) == 'OK' ? 'OK' : 'AGUARDANDO'; ?>
                                            </td>
                                            <td class="<?php
                                                $saida = $schedule['getSaida'];
                                                $carregandoOuRejeitado = $schedule['getCarregandoOuRejeitado'];
                                                $horaChegada = $schedule['getHoraChegada'];
                                                
                                                // Verifica se tem hora de saída OU está REJEITADO
                                                $temSaida = $saida != '';
                                                $estaRejeitado = strtoupper($carregandoOuRejeitado) == 'REJEITADO';
                                                $classeAlerta = '';
                                                $exibirTempo = '';
                                                
                                                if(!$temSaida && !$estaRejeitado && $horaChegada) {
                                                    // Usa DateTime para garantir o formato correto (dd/mm/aaaa HH:MM:SS)
                                                    $chegadaDateTime = DateTime::createFromFormat('d/m/Y H:i:s', $horaChegada);
                                                    if($chegadaDateTime) {
                                                        $agora = new DateTime();
                                                        $diff = $agora->getTimestamp() - $chegadaDateTime->getTimestamp();
                                                        
                                                        if($diff > 0) {
                                                            $horas = floor($diff / 3600);
                                                            $minutos = floor(($diff % 3600) / 60);
                                                            $exibirTempo = strtoupper("$horas h $minutos min");
                                                            
                                                            // Verifica se passou de 1h40 (6000 segundos)
                                                            if($diff > 6000) {
                                                                $classeAlerta = 'tempo-espera-alerta';
                                                            }
                                                        }
                                                    }
                                                }
                                                
                                                echo $classeAlerta;
                                            ?>" data-tempo-espera="<?php
                                                if(isset($chegadaDateTime)) {
                                                    $agora = new DateTime();
                                                    $diff = $agora->getTimestamp() - $chegadaDateTime->getTimestamp();
                                                    echo $diff;
                                                }
                                            ?>">
                                                <?php echo $exibirTempo; ?>
                                            </td>
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
            </div> <!-- fecha main-content -->
        </div>
    </div>
<script>
var PANEL_EXPANDED_STORAGE_KEY = 'panelExpandedMode';

function isPanelExpanded() {
    return localStorage.getItem(PANEL_EXPANDED_STORAGE_KEY) === 'true' ||
        localStorage.getItem('fullscreenEnabled') === 'true';
}

function updateFullscreenState() {
    var btn = document.getElementById('fullscreenBtn');
    if (!btn) {
        return;
    }

    var icon = btn.querySelector('.glyphicon');
    var expanded = document.body.classList.contains('panel-expanded-active');

    if (expanded) {
        icon.classList.remove('glyphicon-fullscreen');
        icon.classList.add('glyphicon-resize-small');
    } else {
        icon.classList.remove('glyphicon-resize-small');
        icon.classList.add('glyphicon-fullscreen');
    }
}

function setExpandedMode(enabled) {
    var mainContent = document.getElementById('main-content');
    if (!mainContent) {
        return;
    }

    document.body.classList.toggle('panel-expanded-active', enabled);
    mainContent.classList.toggle('panel-expanded-mode', enabled);

    localStorage.setItem(PANEL_EXPANDED_STORAGE_KEY, enabled ? 'true' : 'false');
    localStorage.setItem('fullscreenEnabled', enabled ? 'true' : 'false');

    updateFullscreenState();
}

function toggleFullScreen() {
    setExpandedMode(!document.body.classList.contains('panel-expanded-active'));
}

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
    progressTimer();

    var savedView = localStorage.getItem('agendamentoView');
    var cardsView = document.getElementById('cards-view');
    var quadroView = document.getElementById('quadro-view');
    var btn = document.getElementById('toggle-btn');

    if (savedView === 'quadro') {
        quadroView.style.display = 'block';
        cardsView.style.display = 'none';
        btn.textContent = 'CARDS';
    } else {
        quadroView.style.display = 'none';
        cardsView.style.display = 'block';
        btn.textContent = 'QUADRO';
    }

    setExpandedMode(isPanelExpanded());
});
</script>
