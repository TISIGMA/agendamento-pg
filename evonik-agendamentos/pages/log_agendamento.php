<?php
    require('class.php');
    require('../../conn.php');
    require('../functions.php');

    date_default_timezone_set('America/Sao_Paulo');
    $data = date("Y-m-d");
    $arrayLogAgendamento = listarLogAgendamento($MySQLi);
?>
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">LOG agendamento</h1>
                </div>                
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Visualize aqui todos as edições e exclusões realizadas nos agendamentos.
                        </div>
                        <div class="panel-body">
                            <table style="text-align: center;" width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
                                        <th>Data do evento</th>
                                        <th>Evento</th>
                                        <th>Usuário evento</th>
                                        <th>Status</th>
                                        <th>Data agendamento</th>
                                        <th>Horário</th>
                                        <th>Armazém</th>
                                        <th>Transportadora</th>
                                        <th>Tipo de veículo</th>
                                        <th>Placa cavalo</th>
                                        <th>NF</th>
                                        <th>Operação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    <?php
                                        foreach($arrayLogAgendamento as $logAgendamento) { 
                                            echo '<tr class="odd gradeX">';
                                            echo '<td>'.date('d/m/y',strtotime($logAgendamento->getDataOperacaoTabela())).'</td>';
                                            if($logAgendamento->getOperacaoTabela() == 'delete'){
                                                echo '<td>Exclusão</td>';
                                            }else{
                                                echo '<td>Edição</td>';
                                            }
                                            echo '<td>'.$logAgendamento->getUsuarioOperacaoTabela().'</td>';
                                            echo '<td>'.$logAgendamento->getStatus().'</td>';
                                            echo '<td>'.date('d/m/Y', strtotime($logAgendamento->getData())).'</td>';
                                            $horarioDesc = buscarHorario($logAgendamento->getIdhorario(), $MySQLi);
                                            echo '<td>'.$horarioDesc->getHorario().'</td>';
                                            echo '<td>'.$logAgendamento->getArmazem().'</td>';
                                            echo '<td>'.$logAgendamento->getTransportadora().'</td>';
                                            echo '<td>'.$logAgendamento->getTipoVeiculo().'</td>';
                                            echo '<td>'.$logAgendamento->getPlacaCavalo().'</td>';
                                            echo '<td>'.$logAgendamento->getNF().'</td>';
                                            echo '<td>'.$logAgendamento->getOperacao().'</td>';
                                            echo '</tr>';
                                        }   
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
