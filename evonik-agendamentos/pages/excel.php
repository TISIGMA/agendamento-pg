<?php
require('class.php');
require('../../conn.php');
require('../functions.php');
if(isset($_GET['tipo']) && $_GET['tipo'] != null){
    if($_GET['tipo'] == 'dia'){
        $data = date('Y-m-d', strtotime($_GET['data']));
        $titulo = $_GET['titulo'];
        $armazem = $_GET['armazem'];

        $janelas = listarJanelasOcupadasDados($MySQLi, $data, $armazem);

    }else{
        $dataInicial = $_GET['dataInicial'];
        $dataFinal = $_GET['dataFinal'];
        $titulo = 'por período';
        $janelas = janelaPorPeriodo($MySQLi, $dataInicial, $dataFinal);
    }
}


$tabela = '';

$tabela .= '<table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
                                        <th>'.utf8_decode("Armazém").'</th>
                                        <th>Status</th>
                                        <th>Data</th>
                                        <th>'.utf8_decode("Horário").'</th>
                                        <th>Transportadora</th>
                                        <th>'.utf8_decode("Veículo").'</th>
                                        <th>Placa</th>
                                        <th>'.utf8_decode("Operação").'</th>
                                        <th>NFe</th>
                                        <th>Oferta</th>
                                        <th>Doca</th>
                                        <th>Peso</th>
                                        <th>Destino</th>
                                        <th>'.utf8_decode("Data Início operação").'</th>
                                        <th>'.utf8_decode("Hora Início operação").'</th>
                                        <th>'.utf8_decode("Data Fim operação").'</th>
                                        <th>'.utf8_decode("Hora Fim operação").'</th>

                                    </tr>
                                </thead>
                                <tbody>';
                                    
                                            
                                                foreach($janelas as $janela) {
                                                
                                                if($janela->getFimOperacao()==null){
                                                    $statusOperacao = '<td>Em andamento</td>';
                                                }
                                                else{
                                                    $class = "danger";
                                                    $statusOperacao ='<td>Finalizado</td>'; 
                                                }
                                                $tabela .= '<tr class="odd gradeX ">';
                                                $tabela .= '<td>'.$janela->getArmazem().'</td>';
                                                $tabela .= $statusOperacao;
                                                $tabela .= '<td>'.Date('d/m/Y',strtotime($janela->getData())).'</td>';
                                                $tabela .= '<td>'.$janela->getIdhorario().'</td>';
                                                $tabela .= '<td>'.utf8_decode($janela->getTransportadora()).'</td>';
                                                $tabela .= '<td>'.utf8_decode($janela->getTipoveiculo()).'</td>';
                                                $tabela .= '<td>'.$janela->getPlacaCavalo().'</td>';
                                                $tabela .= '<td>'.$janela->getOperacao().'</td>';
                                                $tabela .= '<td>'.$janela->getNF().'</td>';
                                                $tabela .= '<td>'.$janela->getOferta().'</td>';
                                                $tabela .= '<td>'.$janela->getDoca().'</td>';
                                                $tabela .= '<td>'.$janela->getPeso().'</td>';
                                                $tabela .= '<td>'.$janela->getDestino().'</td>';
                                                if($janela->getInicioOperacao() != null){
                                                    $tabela .= '<td>'.date('d/m/Y', strtotime($janela->getInicioOperacao())).'</td>';
                                                }else{
                                                    $tabela .= '<td></td>';
                                                }
                                                if($janela->getInicioOperacao() != null){
                                                    $tabela .= '<td>'.date('H:i:s', strtotime($janela->getInicioOperacao())).'</td>';
                                                }else{
                                                    $tabela .= '<td></td>';
                                                }
                                                if($janela->getFimOperacao() != null){
                                                    $tabela .= '<td>'.date('d/m/Y', strtotime($janela->getFimOperacao())).'</td>';
                                                }else{
                                                    $tabela .= '<td></td>';
                                                }
                                                if($janela->getFimOperacao() != null){
                                                    $tabela .= '<td>'.date('H:i:s', strtotime($janela->getFimOperacao())).'</td>';
                                                }else{
                                                    $tabela .= '<td></td>';
                                                }
                                                $tabela .= '</tr>';
                                            }
                                        
                                $tabela .= '</tbody>';
                                $tabela .= '</table>';
                            
   
    
    // Configurações header para forçar o download
    header ("Expires: Mon, 29 Out 2015 15:00:00 GMT");
    header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
    header ("Cache-Control: no-cache, must-revalidate");
    header ("Pragma: no-cache");
    header ("Content-type: application/x-msexcel"); 
    header ("Content-Disposition: attachment; filename=janelas $titulo.xls" );
    header ("Content-Description: PHP Generated Data" );
    
    // Envia o conteúdo do arquivo
    echo $tabela;
?>