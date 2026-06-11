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
    'documentDriver'       => ['name' => 'documentDriver',        'label'=> 'CPF',            'order' => 9,  'value' => 'getDocumentoMotorista', 'columnSize'=> 'td-100', 'show' => true],
    'driverName'           => ['name' => 'driverName',            'label'=> 'Nome Motorista', 'order' => 10, 'value' => 'getNomeMotorista',      'columnSize'=> 'td-150', 'show' => true],
    'licenceTruck'         => ['name' => 'licenceTruck',          'label'=> 'Placa Cavalo',   'order' => 11, 'value' => 'getPlacaCavalo',        'columnSize'=> 'td-120', 'show' => true],
    'licenceTrailer2'      => ['name' => 'licenceTrailer2',       'label'=> 'Placa carreta',  'order' => 12, 'value' => 'getPlacaCarreta',       'columnSize'=> 'td-150', 'show' => true],
    'licenceTrailer'       => ['name' => 'licenceTrailer',        'label'=> 'Placa Carreta 2','order' => 13, 'value' => 'getPlacaCarreta2',      'columnSize'=> 'td-150', 'show' => true],
    'binSeparation'        => ['name' => 'binSeparation',         'label'=> 'Separação BIN',  'order' => 14, 'value' => 'getSeparacao',          'columnSize'=> 'td-150', 'show' => true],
    'shipmentId'           => ['name' => 'shipmentId',            'label'=> 'Shipment ID',    'order' => 15, 'value' => 'getShipmentId',         'columnSize'=> 'td-150', 'show' => true],
    'dock'                 => ['name' => 'dock',                  'label'=> 'Doca',           'order' => 16, 'value' => 'getDoca',               'columnSize'=> 'td-70',  'show' => true],
    'truckType'            => ['name' => 'truckType',             'label'=> 'Tipo Veículo',   'order' => 17, 'value' => 'getTipoVeiculo',        'columnSize'=> 'td-120', 'show' => true],
    'dos'                  => ['name' => 'dos',                   'label'=> 'DOs',            'order' => 18, 'value' => 'getDo_s',               'columnSize'=> 'td-70',  'show' => true],
    'invoice'              => ['name' => 'invoice',               'label'=> 'NF',             'order' => 19, 'value' => 'getNf',                 'columnSize'=> 'td-70',  'show' => true],
    'grossWeight'          => ['name' => 'grossWeight',           'label'=> 'Peso Final',     'order' => 20, 'value' => 'getPeso',               'columnSize'=> 'td-100', 'show' => true],
    'pallets'              => ['name' => 'pallets',               'label'=> 'Paletes',        'order' => 21, 'value' => 'getCargaQtde',          'columnSize'=> 'td-70',  'show' => true],
    'material'             => ['name' => 'material',              'label'=> 'Material',       'order' => 22, 'value' => 'getDadosGerais',        'columnSize'=> 'td-150', 'show' => true],
    'observation'          => ['name' => 'observation',           'label'=> 'Observação',     'order' => 23, 'value' => 'getObservacao',         'columnSize'=> 'td-150', 'show' => true],
    'operator'             => ['name' => 'operator',              'label'=> 'Operador',       'order' => 24, 'value' => 'getOperator',           'columnSize'=> 'td-150', 'show' => $fieldsAccess],
    'checker'              => ['name' => 'checker',               'label'=> 'Conferente',     'order' => 25, 'value' => 'getChecker',            'columnSize'=> 'td-150', 'show' => $fieldsAccess],
];

$scheduleController = new ScheduleController($MySQLi);

$statusList = ['Todos','Agendado','Aguardando', 'Em operação', 'Fim de operação', 'Liberado'];

$status = 'Todos';
$startDate = date("d/m/Y") . ' 00:00:00';
$endDate = date("d/m/Y") . ' 23:59:59';

if(isset($_POST['order-action']) && $_POST['order-action'] != null){

    if(isset($_POST['column']) && count($_POST['column']) > 0){
        
        $result = $scheduleController->savePreferences($columns, $_POST);

        switch ($result) {
            case 'SAVED':
                successAlert('Preferências salvas com sucesso!');
                break;
            
            case 'UPDATED':
                successAlert('Preferências atualizadas com sucesso!');
                break;
            
            case 'SAVE_ERROR':
                errorAlert('Ocorreu um erro ao salvar a preferência. Tente novamente ou entre em contato com o administrador.');
                break;
        }
    }
}


if(isset($_POST['status']) && $_POST['status'] != null){

    $status = $_POST['status'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
}

if(isset($_GET['status']) && $_GET['status'] != null){

    $status = $_GET['status'];
    $startDate = $_GET['startDate'];
    $endDate = $_GET['endDate'];
}

$preference = $scheduleController->findPreferenceByUser();

if($preference->getId() != null){
    $columns = array();

    $columns = $scheduleController->sortArray(json_decode($preference->getPreference(), true, JSON_UNESCAPED_UNICODE));
}

// #region debug-point C:search-filters
$__dbg=@parse_ini_file(__DIR__ . '/../.dbg/schedule-empty-data.env'); @file_get_contents(($__dbg['DEBUG_SERVER_URL'] ?? 'http://127.0.0.1:7777/event'), false, stream_context_create(['http'=>['method'=>'POST','header'=>"Content-Type: application/json\r\n",'content'=>json_encode(['sessionId'=>($__dbg['DEBUG_SESSION_ID'] ?? 'schedule-empty-data'),'runId'=>'pre-fix','hypothesisId'=>'C','location'=>'schedules/searchSchedule.php','msg'=>'[DEBUG] search filters before query','data'=>['session_customer'=>(isset($_SESSION['customerName']) ? $_SESSION['customerName'] : null),'status'=>$status,'startDate'=>$startDate,'endDate'=>$endDate,'session_tipo'=>(isset($_SESSION['tipo']) ? $_SESSION['tipo'] : null),'session_user_id'=>(isset($_SESSION['id']) ? $_SESSION['id'] : null)],'ts'=>round(microtime(true)*1000)])]]));
// #endregion

$schedules = $scheduleController->findByClientStatusStartDateAndEndDate($_SESSION['customerName'], $status, $startDate, $endDate);

?>

<body>
    <div class="row">
        <div class="col-lg-12">
            <h3>Filtro</h3>
            <div class="functions-group">
                <form method="post" action="index.php?conteudo=searchSchedule.php">
                    <div class="row-element-group">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" aria-label="Default select example">
                                <?php
        
                                foreach ($statusList as $statusValue) {
                                    
                                    $selected = '';
        
                                    if($statusValue == $status) $selected = 'selected';
                                    echo '<option value="'.$statusValue.'" '.$selected.'>'.$statusValue.'</option>';
                                }
                                ?>
                                
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Data inicial</label>
                            <div class='input-group date' id='datetimepicker1'>
                                <input name="startDate" type='text' data-date-format="DD/MM/YYYY HH:mm:ss" class="form-control" value="<?=$startDate ?>" onblur="dateTimeHandleBlur(this)" required  minlength="19" maxlength="19" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Data final</label>
                            <div class='input-group date' id='datetimepicker1'>
                                <input name="endDate" type='text' data-date-format="DD/MM/YYYY HH:mm:ss" class="form-control" onblur="dateTimeHandleBlur(this)" value="<?=$endDate ?>" minlength="19" maxlength="19"  required/>
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Buscar</button>
                        </div>
                    </div>
                </form>
                <div class="btn-functions-group">
                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#columnOrder" onclick="readColumns()"><i class="glyphicon glyphicon-sort-by-attributes"></i> Ordenar Colunas</button>
                    <a href="export/schedule-export.php?startDate=<?=$startDate?>&endDate=<?=$endDate?>&status=<?=$status?>"><button type="button" class="btn btn-secondary"><i class="fa fa-file-excel-o"></i> Exportar</button></a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-body">
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th scope="column" class="td-70">Detalhes</th>
                                    <th scope="column" class="td-70">Editar</th>
                                    <th scope="column" class="td-70">Imprimir</th>

                                    <?php
                                    foreach ($columns as $key => $value) {
                                        if(!$value['show']) continue;
                                        echo '<th class="'.$value["columnSize"].'">'.$value["label"].'</th>';
                                    }
                                    ?>
            
                                </tr>
                            </thead>
                            <tbody>
                                <?php
    
                                    foreach ($schedules as $schedule) {
                                        echo '<tr>';
                                        echo '<td class="text-center"><a href="index.php?customer='.$_SESSION['customerName'].'&conteudo=newSchedule.php&search='.$schedule["getId"].'"><span class="fa fa-search text-primary"></span></a></td>';
                                        echo '<td class="text-center"><a href="index.php?customer='.$_SESSION['customerName'].'&conteudo=newSchedule.php&edit='.$schedule["getId"].'"><span class="fa fa-edit text-primary"></span></a></td>';
                                        echo '<td class="text-center"><a href="schedulePrint.php?id='.$schedule["getId"].'"><span class="glyphicon glyphicon-print text-primary"></span></a></td>';
                                        foreach ($columns as $key => $value) {

                                            if(!$value['show']) continue;

                                            echo '<td>'.$schedule[$value['value']].'</td>';
                                        }
                                        
                                        echo '</tr>';
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<div class="modal fade" id="columnOrder" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered large-modal" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLongTitle">Ordenar colunas</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="restoreColumns()">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body row-center-box">
                <div class="column-group" id="column-group-active">
                    <h3>Colunas Ativas</h3>

                    <div class="row-space-between" id="active-box">
                        <form action="#" method="post" id="order-form">
                            <input name="order-action" value="save-order" type="hidden" />
                            <input name="preferenceId" value="<?=$preference->getId() ?>" type="hidden" />
                            <div id="active-columns" name="active-columns" class="column-space-between active-columns-order" slot="active">
                                <?php
                                foreach ($columns as $key => $value) {

                                    if(!$value['show']) continue;
                                    
                                    echo '<div class="column-box-order" id="div-'.$value['order'].'" name="active-column-name">';
                                    echo    '<div class="form-check">';
                                    echo        '<input class="form-check-input active-column" type="checkbox" id="order-'.$value['order'].'" name="column[]" value="'.$key.'" onchange="handleSelect(this)" >';
                                    echo        '<label class="form-check-label">'.$value['label'].'</label>';
                                    echo    '</div>';
                                    echo '</div>';
                                }
                                ?> 
                            </div>
                        </form> 
                        <div class="column-group-inner-action">
                            <button class="btn btn-secondary" onclick="moveColumn('up')"><i class="glyphicon glyphicon-chevron-up"></i></button>
                            <button class="btn btn-secondary" onclick="moveColumn('down')"><i class="glyphicon glyphicon-chevron-down"></i></button>
                        </div>
                    </div>


                </div>
                <div class="column-group-action">
                    <button class="btn btn-primary" onclick="inactiveColumn()"><i class="glyphicon glyphicon-chevron-right"></i></button>
                    <button class="btn btn-primary" onclick="activeColumn()"><i class="glyphicon glyphicon-chevron-left"></i></button>
                </div>
                <div class="column-group" id="column-group-inactive">
                    <h3>Colunas Inativas</h3>
                    <div class="row-space-between" id="inactive-box">
                        <div id="inactive-columns" name="inactive-columns" class="column-space-between" slot="inactive">
                            
                            <?php
                            foreach ($columns as $key => $value) {
    
                                if($value['show']) continue;
                                
                                echo '<div class="column-box-order" id="div-'.$value['order'].'" name="inactive-column-name">';
                                echo    '<div class="form-check">';
                                echo        '<input class="form-check-input inactive-column" type="checkbox" id="order-'.$value['order'].'" name="column[]" value="'.$key.'" onchange="handleSelect(this)">';
                                echo        '<label class="form-check-label">'.$value['label'].'</label>';
                                echo    '</div>';
                                echo '</div>';
                            }
                            ?> 
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="restoreColumns()">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="saveOrder()">Salvar</button>
            </div>
        </div>
    </div>
</div>
