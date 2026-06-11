<?php

require_once('../../session.php');
require_once('../../conn.php');

$startDate;
$endDate;
$status;

if((isset($_GET['startDate']) && $_GET['startDate'] != null) && (isset($_GET['endDate']) && $_GET['endDate'] != null)){

    $startDate = $_GET['startDate'];
    $endDate = $_GET['endDate'];
    $status = $_GET['status'];

    $startDate = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $startDate )));
    $endDate = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $endDate )));

    if($status == 'Todos'){
        $sql = "SELECT id,data_agendamento,transportadora,status,tipoVeiculo,placa_cavalo,operacao,nf,horaChegada,inicio_operacao,fim_operacao,usuario,dataInclusao,peso,saida,separacao,shipment_id,do_s,cidade,carga_qtde,observacao,dados_gerais,cliente,doca, nome_motorista, placa_carreta2, documento_motorista, placa_carreta,operator,checker
                FROM janela
                WHERE cliente = '".$_SESSION['customerName']."'
                AND data_agendamento >= '".$startDate."'
                AND data_agendamento <= '".$endDate."'
                ORDER BY data_agendamento";  
    }else {
        $sql = "SELECT id,data_agendamento,transportadora,status,tipoVeiculo,placa_cavalo,operacao,nf,horaChegada,inicio_operacao,fim_operacao,usuario,dataInclusao,peso,saida,separacao,shipment_id,do_s,cidade,carga_qtde,observacao,dados_gerais,cliente,doca, nome_motorista, placa_carreta2, documento_motorista, placa_carreta,operator,checker
                FROM janela
                WHERE cliente = '".$_SESSION['customerName']."'
                AND status = '".$status."' 
                AND data_agendamento >= '".$startDate."'
                AND data_agendamento <= '".$endDate."'
                ORDER BY data_agendamento";  
    }

    $schedules = $MySQLi->query($sql);
}

$file = '';
$file .= '<table><thead><tr>';        
    
$file .= '<th>Status</th>';
$file .= '<th>Agendamento</th>';
$file .= '<th>Chegada</th>';
$file .= '<th>'.utf8_decode("Início").'</th>';
$file .= '<th>Fim</th>';
$file .= '<th>'.utf8_decode("Saída").'</th>';
$file .= '<th>'.utf8_decode("Operação").'</th>';
$file .= '<th>Transportadora</th>';
$file .= '<th>Cidade</th>';
$file .= '<th>CPF</th>';
$file .= '<th>Nome Motorista</th>';
$file .= '<th>Placa Cavalo</th>';
$file .= '<th>Placa carreta</th>';
$file .= '<th>Placa Carreta 2</th>';
$file .= '<th>'.utf8_decode("Separação BIN").'</th>';
$file .= '<th>Shipment ID</th>';
$file .= '<th>Doca</th>';
$file .= '<th>'.utf8_decode("Tipo Veículo").'</th>';
$file .= '<th>DOs</th>';
$file .= '<th>NF</th>';
$file .= '<th>Peso Final</th>';
$file .= '<th>Paletes</th>';
$file .= '<th>Dados Gerais</th>';
$file .= '<th>'.utf8_decode("Observação").'</th>';

if($_SESSION['tipo'] != 'client'){
    $file .= '<th>Operador</th>';
    $file .= '<th>Conferente</th>';
}

while ($data = $schedules->fetch_assoc()){ 
    $file .= '<tr>';
    $file .= '<td>'.utf8_decode($data['status']).'</td>';
    $file .= '<td>'.utf8_decode(date("d/m/Y H:i:s", strtotime($data['data_agendamento']))).'</td>';
    
    $arrive = (empty($data['horaChegada'])) ? '' : date("d/m/Y H:i:s", strtotime($data['horaChegada']));
    $file .= '<td>'.utf8_decode($arrive).'</td>';
    
    $operationStart = (empty($data['inicio_operacao'])) ? '' : date("d/m/Y H:i:s", strtotime($data['inicio_operacao']));
    $file .= '<td>'.utf8_decode($operationStart).'</td>';
    
    $operationEnd = (empty($data['fim_operacao'])) ? '' : date("d/m/Y H:i:s", strtotime($data['fim_operacao'])); 
    $file .= '<td>'.utf8_decode( $operationEnd ).'</td>';
    
    $exit = (empty($data['saida'])) ? '' : date("d/m/Y H:i:s", strtotime($data['saida']));
    $file .= '<td>'.utf8_decode( $exit ).'</td>';
    
    $file .= '<td>'.utf8_decode( $data['operacao'] ).'</td>';
    $file .= '<td>'.utf8_decode( $data['transportadora'] ).'</td>';
    $file .= '<td>'.utf8_decode( $data['cidade'] ).'</td>';
    $file .= '<td>'.utf8_decode( $data['documento_motorista'] ).'</td>';
    $file .= '<td>'.utf8_decode( $data['nome_motorista'] ).'</td>';
    $file .= '<td>'.utf8_decode( $data['placa_cavalo'] ).'</td>';
    $file .= '<td>'.utf8_decode( $data['placa_carreta'] ).'</td>';
    $file .= '<td>'.utf8_decode( $data['placa_carreta2'] ).'</td>';
    $file .= '<td>'.utf8_decode( $data['separacao'] ).'</td>';
    $file .= '<td>'.utf8_decode( $data['shipment_id'] ).'</td>';
    $file .= '<td>'.utf8_decode( $data['doca'] ).'</td>';
    $file .= '<td>'.utf8_decode( $data['tipoVeiculo'] ).'</td>';
    $file .= '<td>'.utf8_decode( $data['do_s'] ).'</td>';
    $file .= '<td>'.utf8_decode( $data['nf'] ).'</td>';
    $file .= '<td>'.utf8_decode( $data['peso'] ).'</td>';
    $file .= '<td>'.utf8_decode( $data['carga_qtde'] ).'</td>';
    $file .= '<td>'.utf8_decode( $data['dados_gerais'] ).'</td>';
    $file .= '<td>'.utf8_decode( $data['observacao'] ).'</td>';

    if($_SESSION['tipo'] != 'client'){
        $file .= '<td>'.utf8_decode( $data['operator'] ).'</td>';
        $file .= '<td>'.utf8_decode( $data['checker'] ).'</td>';
    }
    $file .= '</tr>';
}

$file .= '</tbody></table>';

header ("Expires: Mon, 29 Out 2015 15:00:00 GMT");
header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
header ("Content-type: application/x-msexcel"); 
header ("Content-Disposition: attachment; filename=agendamentos.xls" );
header ("Content-Description: PHP Generated Data" );

echo $file;

?>