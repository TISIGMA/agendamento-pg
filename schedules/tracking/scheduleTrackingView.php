<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once('../controller/scheduleLogController.php');

$shipmentId = '';
$scheduleLogController = new ScheduleLogController($MySQLi);

if(isset($_POST['shipmentId']) && $_POST['shipmentId'] != null){
    $shipmentId = $_POST['shipmentId'];
    $result = $scheduleLogController->findByClientAndShipmentId($_SESSION['customerName'], $_POST['shipmentId']);

    if(empty($result)){
        warningAlert('Não foram encontrados registros.');
    }
}

?>

<body>
    <div class="row">
        <div class="col-lg-12">
            <h3>Agendamentos</h3>
            <div class="functions-group">
                <form method="post" action="#">
                    <div class="row-element-group">
                        <div class="form-group">
                            <label>ShipmentId</label>
                            <input name="shipmentId" id="shipmentId" type="text" class="form-control" value="<?=$shipmentId ?>" required />
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Buscar</button>
                        </div>
                    </div>
                </form>
                <div class="btn-functions-group">
                    <form onsubmit="return handleExport()" method="post" action="export/schedule-tracking-export.php">
                        <input name="tableString" id="tableString" type="hidden" value="" />
                        <button type="submit" class="btn btn-secondary"><i class="fa fa-file-excel-o"></i> Exportar</button>
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
                                    <th scope="column" class="td-70">Ação</th>
                                    <th scope="column" class="td-150">Responsável Ação</th>
                                    <th scope="column" class="td-120">Data/hora Ação</th>
                                    <th scope="column" class="td-70">Data criação</th>
                                    <th scope="column" class="td-70">Criado por</th>
                                    <th scope="column" class="td-70">Status</th>
                                    <th scope="column" class="td-70">Hora Chegada</th>
                                    <th scope="column" class="td-120">Início Operação</th>
                                    <th scope="column" class="td-120">Fim Operação</th>
                                    <th scope="column" class="td-70">Saída</th>
                                    <th scope="column" class="td-70">Operação</th>
                                    <th scope="column" class="td-70">Cidade</th>
                                    <th scope="column" class="td-120">Transportadora</th>
                                    <th scope="column" class="td-70">Tipo Veículo</th>
                                    <th scope="column" class="td-100">Placa Cavalo</th>
                                    <th scope="column" class="td-100">Placa Carreta</th>
                                    <th scope="column" class="td-120">Placa Carreta 2</th>
                                    <th scope="column" class="td-70">NF</th>
                                    <th scope="column" class="td-70">Peso</th>
                                    <th scope="column" class="td-70">Doca</th>
                                    <th scope="column" class="td-100">Separação</th>
                                    <th scope="column" class="td-70">DO's</th>
                                    <th scope="column" class="td-100">Qtde. Carga</th>
                                    <th scope="column" class="td-120">Motorista</th>
                                    <th scope="column" class="td-170">Documento Motorista</th>
                                    <th scope="column" class="td-100">Observação</th>
                                    <th scope="column" class="td-100">Dados Gerais</th>
                                    <th scope="column" class="td-70">Cliente</th>
                                    <th scope="column" class="td-100">Operador</th>
                                    <th scope="column" class="td-100">Conferente</th>
                                    <th scope="column" class="td-120">Status Picking</th>
                                    <th scope="column" class="td-150">Status Certificado</th>
                                    <th scope="column" class="td-170">Status Lista Embarque</th>
                                    <th scope="column" class="td-100">Status NF</th>
                                    <th scope="column" class="td-120">Status Outros</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    foreach ($result as $key => $data) {

                                        $action;
                                        switch ($data->getAction()) {
                                            case 'save':
                                                $action = 'Inclusão';
                                                break;

                                            case 'update':
                                                $action = 'Edição';
                                                break;

                                            case 'delete':
                                                $action = 'Exclusão';
                                                break;
                                        }

                                        $statusPicking = ($data->getAttPickingStatus() == 'open') ? 'Aberto' : 'Fechado';
                                        $statusCertificate = ($data->getAttCertificateStatus() == 'open') ? 'Aberto' : 'Fechado';
                                        $statusBoarding = ($data->getAttBoardingStatus() == 'open') ? 'Aberto' : 'Fechado';
                                        $statusinvoice = ($data->getAttInvoiceStatus() == 'open') ? 'Aberto' : 'Fechado';
                                        $statusOther = ($data->getAttOtherStatus() == 'open') ? 'Aberto' : 'Fechado';

                                        echo '<tr class="odd gradeX">';
                                        echo '<td>'.$key + 1 .'</td>';
                                        echo '<td>'.$data->getShipmentId().'</td>';
                                        echo '<td>'.$action.'</td>';
                                        echo '<td>'.$data->getUserAction().'</td>';
                                        echo '<td>'.$data->getDateTimeAction().'</td>';
                                        echo '<td>'.$data->getDataAgendamento().'</td>';
                                        echo '<td>'.$data->getNomeUsuario().'</td>';
                                        echo '<td>'.$data->getStatus().'</td>';
                                        echo '<td>'.$data->getHoraChegada().'</td>';
                                        echo '<td>'.$data->getInicioOperacao().'</td>';
                                        echo '<td>'.$data->getFimOperacao().'</td>';
                                        echo '<td>'.$data->getSaida().'</td>';
                                        echo '<td>'.$data->getOperacao().'</td>';
                                        echo '<td>'.$data->getCidade().'</td>';
                                        echo '<td>'.$data->getTransportadora().'</td>';
                                        echo '<td>'.$data->getTipoVeiculo().'</td>';
                                        echo '<td>'.$data->getPlacaCavalo().'</td>';
                                        echo '<td>'.$data->getPlacaCarreta().'</td>';
                                        echo '<td>'.$data->getPlacaCarreta2().'</td>';
                                        echo '<td>'.$data->getNf().'</td>';
                                        echo '<td>'.$data->getPeso().'</td>';
                                        echo '<td>'.$data->getDoca().'</td>';
                                        echo '<td>'.$data->getSeparacao().'</td>';
                                        echo '<td>'.$data->getDo_s().'</td>';
                                        echo '<td>'.$data->getCargaQtde().'</td>';
                                        echo '<td>'.$data->getNomeMotorista().'</td>';
                                        echo '<td>'.$data->getDocumentoMotorista().'</td>';
                                        echo '<td>'.$data->getObservacao().'</td>';
                                        echo '<td>'.$data->getDadosGerais().'</td>';
                                        echo '<td>'.$data->getCliente().'</td>';
                                        echo '<td>'.$data->getOperator().'</td>';
                                        echo '<td>'.$data->getChecker().'</td>';
                                        echo '<td>'.$statusPicking.'</td>';
                                        echo '<td>'.$statusCertificate.'</td>';
                                        echo '<td>'.$statusBoarding.'</td>';
                                        echo '<td>'.$statusinvoice.'</td>';
                                        echo '<td>'.$statusOther.'</td>';
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
                <th scope="column" class="td-05">#</th>
                <th scope="column" class="td-70">ShipmentId</th>
                <th scope="column" class="td-70">Ação</th>
                <th scope="column" class="td-150">Responsável Ação</th>
                <th scope="column" class="td-120">Data/hora Ação</th>
                <th scope="column" class="td-70">Data criação</th>
                <th scope="column" class="td-70">Criado por</th>
                <th scope="column" class="td-70">Status</th>
                <th scope="column" class="td-70">Hora Chegada</th>
                <th scope="column" class="td-120">Início Operação</th>
                <th scope="column" class="td-120">Fim Operação</th>
                <th scope="column" class="td-70">Saída</th>
                <th scope="column" class="td-70">Operação</th>
                <th scope="column" class="td-70">Cidade</th>
                <th scope="column" class="td-120">Transportadora</th>
                <th scope="column" class="td-70">Tipo Veículo</th>
                <th scope="column" class="td-100">Placa Cavalo</th>
                <th scope="column" class="td-100">Placa Carreta</th>
                <th scope="column" class="td-120">Placa Carreta 2</th>
                <th scope="column" class="td-70">NF</th>
                <th scope="column" class="td-70">Peso</th>
                <th scope="column" class="td-70">Doca</th>
                <th scope="column" class="td-100">Separação</th>
                <th scope="column" class="td-70">DO's</th>
                <th scope="column" class="td-100">Qtde. Carga</th>
                <th scope="column" class="td-120">Motorista</th>
                <th scope="column" class="td-170">Documento Motorista</th>
                <th scope="column" class="td-100">Observação</th>
                <th scope="column" class="td-100">Dados Gerais</th>
                <th scope="column" class="td-70">Cliente</th>
                <th scope="column" class="td-100">Operador</th>
                <th scope="column" class="td-100">Conferente</th>
                <th scope="column" class="td-120">Status Picking</th>
                <th scope="column" class="td-150">Status Certificado</th>
                <th scope="column" class="td-170">Status Lista Embarque</th>
                <th scope="column" class="td-100">Status NF</th>
                <th scope="column" class="td-120">Status Outros</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach ($result as $key => $data) {

                    $action;
                    switch ($data->getAction()) {
                        case 'save':
                            $action = 'Inclusão';
                            break;

                        case 'update':
                            $action = 'Edição';
                            break;

                        case 'delete':
                            $action = 'Exclusão';
                            break;
                    }

                    $statusPicking = ($data->getAttPickingStatus() == 'open') ? 'Aberto' : 'Fechado';
                    $statusCertificate = ($data->getAttCertificateStatus() == 'open') ? 'Aberto' : 'Fechado';
                    $statusBoarding = ($data->getAttBoardingStatus() == 'open') ? 'Aberto' : 'Fechado';
                    $statusinvoice = ($data->getAttInvoiceStatus() == 'open') ? 'Aberto' : 'Fechado';
                    $statusOther = ($data->getAttOtherStatus() == 'open') ? 'Aberto' : 'Fechado';

                    echo '<tr class="odd gradeX">';
                    echo '<td>'.$key + 1 .'</td>';
                    echo '<td>'.$data->getShipmentId().'</td>';
                    echo '<td>'.$action.'</td>';
                    echo '<td>'.$data->getUserAction().'</td>';
                    echo '<td>'.$data->getDateTimeAction().'</td>';
                    echo '<td>'.$data->getDataAgendamento().'</td>';
                    echo '<td>'.$data->getNomeUsuario().'</td>';
                    echo '<td>'.$data->getStatus().'</td>';
                    echo '<td>'.$data->getHoraChegada().'</td>';
                    echo '<td>'.$data->getInicioOperacao().'</td>';
                    echo '<td>'.$data->getFimOperacao().'</td>';
                    echo '<td>'.$data->getSaida().'</td>';
                    echo '<td>'.$data->getOperacao().'</td>';
                    echo '<td>'.$data->getCidade().'</td>';
                    echo '<td>'.$data->getTransportadora().'</td>';
                    echo '<td>'.$data->getTipoVeiculo().'</td>';
                    echo '<td>'.$data->getPlacaCavalo().'</td>';
                    echo '<td>'.$data->getPlacaCarreta().'</td>';
                    echo '<td>'.$data->getPlacaCarreta2().'</td>';
                    echo '<td>'.$data->getNf().'</td>';
                    echo '<td>'.$data->getPeso().'</td>';
                    echo '<td>'.$data->getDoca().'</td>';
                    echo '<td>'.$data->getSeparacao().'</td>';
                    echo '<td>'.$data->getDo_s().'</td>';
                    echo '<td>'.$data->getCargaQtde().'</td>';
                    echo '<td>'.$data->getNomeMotorista().'</td>';
                    echo '<td>'.$data->getDocumentoMotorista().'</td>';
                    echo '<td>'.$data->getObservacao().'</td>';
                    echo '<td>'.$data->getDadosGerais().'</td>';
                    echo '<td>'.$data->getCliente().'</td>';
                    echo '<td>'.$data->getOperator().'</td>';
                    echo '<td>'.$data->getChecker().'</td>';
                    echo '<td>'.$statusPicking.'</td>';
                    echo '<td>'.$statusCertificate.'</td>';
                    echo '<td>'.$statusBoarding.'</td>';
                    echo '<td>'.$statusinvoice.'</td>';
                    echo '<td>'.$statusOther.'</td>';
                    echo '</tr>';
                }
            ?>
        </tbody>
    </table>
</body>