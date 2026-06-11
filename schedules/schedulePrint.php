<?php

require_once('../conn.php');
require_once('../session.php');

if(isset($_GET['id']) && $_GET['id'] != null){

    $id = $_GET['id'];

    
    $sql = "SELECT id,data_agendamento,transportadora,status,tipoVeiculo,placa_cavalo,operacao,nf,horaChegada,inicio_operacao,fim_operacao,usuario,dataInclusao,peso,saida,separacao,shipment_id,do_s,cidade,carga_qtde,observacao,dados_gerais,cliente,doca, nome_motorista, placa_carreta2, documento_motorista, placa_carreta
            FROM janela
            WHERE id = '".$id."'";  

    $result = $MySQLi->query($sql);

    $schedule = array();

    while ($data = $result->fetch_assoc()){ 


        $schedule['getId'] = $data['id'];
        $schedule['getTransportadora'] = $data['transportadora'];
        $schedule['getTipoVeiculo'] = $data['tipoVeiculo'];
        $schedule['getPlacaCavalo'] = $data['placa_cavalo'];
        $schedule['getOperacao'] = $data['operacao'];
        $schedule['getNf'] = $data['nf'];
        $schedule['getHoraChegada'] = date("d/m/Y H:i:s", strtotime($data['horaChegada']));
        if(empty($data['horaChegada'])) $schedule['getHoraChegada'] = '';

        $schedule['getInicioOperacao'] = date("d/m/Y H:i:s", strtotime($data['inicio_operacao']));
        if(empty($data['inicio_operacao'])) $schedule['getInicioOperacao'] = '';

        $schedule['getFimOperacao'] = date("d/m/Y H:i:s", strtotime($data['fim_operacao']));
        if(empty($data['fim_operacao'])) $schedule['getFimOperacao'] = '';

        $schedule['getNomeUsuario'] = $data['usuario'];
        $schedule['getDataInclusao'] = date("d/m/Y H:i:s", strtotime($data['dataInclusao']));
        $schedule['getPeso'] = $data['peso'];
        $schedule['getDataAgendamento'] = date("d/m/Y H:i:s", strtotime($data['data_agendamento']));
        $schedule['getSaida'] = date("d/m/Y H:i:s", strtotime($data['saida']));
        if(empty($data['saida'])) $schedule['getSaida'] = '';

        $schedule['getSeparacao'] = $data['separacao'];
        $schedule['getShipmentId'] = $data['shipment_id'];
        $schedule['getDoca'] = $data['doca'];
        $schedule['getDo_s'] = $data['do_s'];
        $schedule['getCidade'] = $data['cidade'];
        $schedule['getCargaQtde'] = $data['carga_qtde'];
        $schedule['getObservacao'] = $data['observacao'];
        $schedule['getDadosGerais'] = $data['dados_gerais'];
        $schedule['getCliente'] = $data['cliente'];
        $schedule['getStatus'] = $data['status'];
        $schedule['getNomeMotorista'] = $data['nome_motorista']; 
        $schedule['getPlacaCarreta2'] = $data['placa_carreta2'];
        $schedule['getDocumentoMotorista'] = $data['documento_motorista'];
        $schedule['getPlacaCarreta'] = $data['placa_carreta'];
    }
}

?>
<head>
    <link href="../vendor/morrisjs/morris.css" rel="stylesheet">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
    <link href="../vendor/datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">
    <link href="../vendor/datatables-responsive/dataTables.responsive.css" rel="stylesheet">
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">
    <link href="../custom-style.css" rel="stylesheet">
    <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/3.1.3/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
</head>
<body onload='printValues()'>
<div class="row">
    <div class="col-lg-12">
         <div class="panel panel-default">
            <div class="panel-body print-body">
                <div class="row">
                    <div class="col-lg-6">
                        <p>Detalhes agendamento <?=$_SESSION['customerDescription']  ?></p>
                        <table class="table-print">
                            <tr style="margin-top: 10px">
                                <td style="width:250px"><span>Status</span></td>
                                <td><span><?=$schedule['getStatus'] ?></span></td>
                            </tr>
                            <tr>
                                <td><span>Horário Carregamento</span></td>
                                <td><span><?=$schedule['getDataAgendamento'] ?></span></td>
                            </tr>
                            <tr>
                                <td><span>Chegada</span></td>
                                <td><span><?=$schedule['getHoraChegada'] ?></span></td>
                            </tr> 
                            
                            <tr>
                                <td><span>Início</span></td>
                                <td><span><?=$schedule['getInicioOperacao'] ?></span></td>
                            </tr>
    
                            <tr>
                                <td><span>Fim</span></td>
                                <td><span><?=$schedule['getFimOperacao'] ?></span></td>
                            </tr>
                            
                            <tr>
                                <td><span>Saída</span></td>
                                <td><span><?=$schedule['getSaida'] ?></span></td>
                            </tr>
                            
                            <tr>
                                <td><span>Nome Motorista</span></td>
                                <td><span><?=$schedule['getNomeMotorista'] ?></span></td>
                            </tr>
                            
                            <tr>
                                <td><span>CPF Motorista</span></td>
                                <td><span ><?=$schedule['getDocumentoMotorista'] ?></span></td>
                            </tr>
                            
                            <tr>
                                <td><span>Tipo de Operação</span></td>
                                <td><span><?=$schedule['getOperacao'] ?></span></td>
                            </tr>
                            
                            <tr>
                                <td><span>Transportadora</span></td>
                                <td><span><?=$schedule['getTransportadora'] ?></span></td>
                            </tr>
                            
                            <tr>
                                <td><span>Cidade</span></td>
                                <td><span><?=$schedule['getCidade'] ?></span></td>
                            </tr>
                            
                            <tr>
                                <td><span>Separação/Bin</span></td>
                                <td><span><?=$schedule['getSeparacao'] ?></span></td>
                            </tr>
                            
                            <tr>
                                <td><span>Shipment ID </span></td>
                                <td><span><?=$schedule['getShipmentId'] ?></span></td>
                            </tr>
                            
                            <tr>
                                <td><span>Doca </span></td>
                                <td><span><?=$schedule['getDoca'] ?></span></td>
                            </tr>
                            
                            <tr>
                                <td><span>Tipo de Veículo</span></td>
                                <td><span><?=$schedule['getTipoVeiculo'] ?></span></td>
                            </tr>
                            
                            <tr>
                                <td><span>Placa do cavalo</span></td>
                                <td><span><?=$schedule['getPlacaCavalo'] ?></span></td>
                            </tr>
                            
                            <tr>
                                <td><span>Placa Carreta 1</span></td>
                                <td><span><?=$schedule['getPlacaCarreta'] ?></span></td>
                            </tr>
    
                            <tr>
                                <td><span>Placa Carreta 2</span></td>
                                <td><span><?=$schedule['getPlacaCarreta2'] ?></span></td>
                            </tr>
                            
                            <tr>
                                <td><span>DO's</span></td>
                                <td><span><?=$schedule['getDo_s'] ?></span></td>
                            </tr>
    
                            <tr>
                                <td><span>Nota Fiscal</span></td>
                                <td><span><?=$schedule['getNf'] ?></span></td>
                            </tr>
                            
                            <tr>
                                <td><span>Peso Bruto</span></td>
                                <td><span><?=$schedule['getPeso'] ?></span></td>
                            </tr>
    
                            <tr>
                                <td><span>Paletes</span></td>
                                <td><span><?=$schedule['getCargaQtde'] ?></span></td>
                            </tr>
                            
                            <tr>
                                <td><span>Material</span></td>
                                <td><span><?=$schedule['getDadosGerais'] ?></span></td>
                            </tr>
    
                            <tr>
                                <td><span>Observação</span></td>
                                <td><span><?=$schedule['getObservacao'] ?></span></td>
                            </tr>
                        </table>    
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

<script>
    function printValues(){
        window.print();
        window.onafterprint = function() {
            window.location='index.php?conteudo=searchSchedule.php';
        };
    }
</script>

