<?php

require_once('../controller/attachmentLogController.php');

date_default_timezone_set("America/Sao_Paulo");

$startDate = date("d/m/Y") . ' 00:00:00';
$endDate = date("d/m/Y") . ' 23:59:59';
$shipmentId = '';

$attachmentLogController = new AttachmentLogController($MySQLi);

if((isset($_POST['startDate']) && $_POST['startDate'] != null) && (isset($_POST['endDate']) && $_POST['endDate'] != null) && $_POST['shipmentId'] == null){

    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];

    $result = $attachmentLogController->findByClientStartDateAndEndDate($_SESSION['customerName'], $startDate, $endDate);

    if(empty($result)){
        warningAlert('Não foram encontrados registros.');
    }
}

if(isset($_POST['shipmentId']) && $_POST['shipmentId'] != null){
    $shipmentId = $_POST['shipmentId'];
    $result = $attachmentLogController->findByShipmentId($_POST['shipmentId']);

    if(empty($result)){
        warningAlert('Não foram encontrados registros.');
    }
}

?>

<body onload="checkToDisableFileds('shipmentId','startDate', 'endDate')">
    <div class="row">
        <div class="col-lg-12">
            <h3>Anexos</h3>
            <div class="functions-group">
                <form method="post" action="#">
                    <div class="row-element-group">
                        <div class="form-group">
                            <label>ShipmentId</label>
                            <input name="shipmentId" id="shipmentId" type="text" class="form-control" value="<?=$shipmentId ?>" onkeyup="handleKeyupDisableFields(this, 'startDate', 'endDate')" />
                        </div>
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
                                <input name="endDate" type='text' id="endDate" data-date-format="DD/MM/YYYY HH:mm:ss" class="form-control" onblur="dateTimeHandleBlur(this)" value="<?=$endDate ?>" minlength="19" maxlength="19"  required/>
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
                    <form onsubmit="return handleExport()" method="post" action="export/att-tracking-export.php">
                        <input name="tableString" id="tableString" type="hidden" value="" />
                        <button type="submit" class="btn btn-secondary" onclick="handleExport()"><i class="fa fa-file-excel-o"></i> Exportar</button>
                    </form>
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
                                    <th scope="column" class="td-05">#</th>
                                    <th scope="column" class="td-70">ShipmentId</th>
                                    <th scope="column" class="td-70">Nome do arquivo</th>
                                    <th scope="column" class="td-30">Tipo</th>
                                    <th scope="column" class="td-30">Ação</th>
                                    <th scope="column" class="td-70">Responsável</th>
                                    <th scope="column" class="td-30">Data/hora</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    foreach ($result as $key => $data) {

                                        $path = $data->getPath();
                                        $fileName = substr($path, strrpos("/$path", '/'));
                                        
                                        //definir o tipo do arquivo
                                        $type;
                                        switch ($data->getType()) {
                                            case 'picking':
                                                $type = 'Picking';
                                                break;
                                            
                                            case 'invoice':
                                                $type = 'Nota Fiscal';
                                                break;

                                            case 'certificate':
                                                $type = 'Certificado';
                                                break;

                                            case 'boarding':
                                                $type = 'Lista de Embarque';
                                                break;

                                            case 'other':
                                                $type = 'Outros';
                                                break;
                                            
                                            default:
                                                $type = 'Outros';
                                                break;
                                        }

                                        $actionName = $data->getAction() == 'delete' ? 'Exclusão' : 'Inclusão'; 
                                        echo '<tr class="odd gradeX">';
                                        echo '<td>'.$key + 1 .'</td>';
                                        echo '<td>'.$data->getShipmentId().'</td>';
                                        echo '<td>'.$fileName.'</td>';
                                        echo '<td>'.$type.'</td>';
                                        echo '<td>'.$actionName.'</td>';
                                        echo '<td>'.$data->getUserAction().'</td>';
                                        echo '<td>'.$data->getDateTimeAction().'</td>';
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
    <table id="table-export" hidden>
        <thead>
            <tr>
                <th scope="column" class="td-70">ShipmentId</th>
                <th scope="column" class="td-70">Nome do arquivo</th>
                <th scope="column" class="td-30">Tipo</th>
                <th scope="column" class="td-30">Ação</th>
                <th scope="column" class="td-70">Responsável</th>
                <th scope="column" class="td-30">Data/hora</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach ($result as $key => $data) {

                    $path = $data->getPath();
                    $fileName = substr($path, strrpos("/$path", '/'));
                    
                    //definir o tipo do arquivo
                    $type;
                    switch ($data->getType()) {
                        case 'picking':
                            $type = 'Picking';
                            break;
                        
                        case 'invoice':
                            $type = 'Nota Fiscal';
                            break;

                        case 'certificate':
                            $type = 'Certificado';
                            break;

                        case 'boarding':
                            $type = 'Lista de Embarque';
                            break;

                        case 'other':
                            $type = 'Outros';
                            break;
                        
                        default:
                            $type = 'Outros';
                            break;
                    }

                    $actionName = $data->getAction() == 'delete' ? 'Exclusão' : 'Inclusão'; 
                    echo '<tr class="odd gradeX">';
                    echo '<td>'.$data->getShipmentId().'</td>';
                    echo '<td>'.$fileName.'</td>';
                    echo '<td>'.$type.'</td>';
                    echo '<td>'.$actionName.'</td>';
                    echo '<td>'.$data->getUserAction().'</td>';
                    echo '<td>'.$data->getDateTimeAction().'</td>';
                    echo '</tr>';
                }
            ?>
        </tbody>
    </table>
</body>