<?php
    require('class.php');
    require('../../conn.php');
    require('../functions.php');

    $dataInicial = date('Y-m-d', strtotime(date('Y').'/'.date('m').'/'.date('01')));
    $dataFinal = date('Y-m-d');

    if(isset($_POST['dataInicial']) && $_POST['dataInicial'] != null){
        if($_POST['dataInicial'] <= $_POST['dataFinal']){
            $dataInicial = $_POST['dataInicial'];
            $dataFinal = $_POST['dataFinal']; 
        }else{
            echo '<div class="alert alert-danger alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>ops! </strong>A data inicial NÃO pode ser maior que a data final!
            </div>';
        }
         
    }
    $janelas = new Janela();
    $janelas = janelaPorPeriodo($MySQLi, $dataInicial, $dataFinal);

?>
   
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Relatório de agendamentos</h1>
                </div>
            </div>
            <div class="row col-lg-12">
                    <div>
                        <form class="form-row" method="post" action="index.php?conteudo=relatorio.php">
                            <div class="form-group col-md-3">
                                <label>Data inicial</label>
                                <input name="dataInicial" type="date" value="<?php echo date('Y-m-d', strtotime($dataInicial)) ?>" name="data" class="form-control">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Data Final</label>
                                <input name="dataFinal" type="date" value="<?php echo date('Y-m-d', strtotime($dataFinal)) ?>" name="data" class="form-control"><br>
                            </div>
                            <div class="form-group col-md-3 button-gerar">
                                <button type="submit" class="btn btn-primary">Buscar</button>
                            </div>
                        </form><br>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <strong><a href="excel.php?dataInicial=<?=$dataInicial?>&dataFinal=<?=$dataFinal ?>&tipo=periodo"><i class="fa fa-file-excel-o"></i> Exportar</a></strong>
                    </div>
                </div><br>
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                        <div class="panel-body">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>ID</th>
                                        <th>Data</th>
                                        <th>Horário</th>
                                        <th>Transportadora</th>
                                        <th>Veículo</th>
                                        <th>Placa</th>
                                        <th>Operação</th>
                                        <th>NF'e</th>
                                        <th>Oferta</th>
                                        <th>Peso</th>
                                        <th>Destino</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                <?php
                                            $count = 0;
                                            foreach($janelas as $janela) {
                                                $class = "odd gradeX ";
                                                if($janela->getFimOperacao()==null){
                                                    $statusOperacao = '<td>Em andamento</td>';
                                                }
                                                else{
                                                    $class = "danger";
                                                    $statusOperacao ='<td>Finalizado</td>'; 
                                                }
                                                echo '<tr class="'.$class.'">';
                                                echo $statusOperacao;
                                                echo '<td>'.$janela->getId().'</td>';
                                                echo '<td>'.Date('d/m/Y',strtotime($janela->getData())).'</td>';
                                                echo '<td>'.$janela->getIdhorario().'</td>';
                                                echo '<td>'.$janela->getTransportadora().'</td>';
                                                echo '<td>'.$janela->getTipoveiculo().'</td>';
                                                echo '<td>'.$janela->getPlacaCavalo().'</td>';
                                                echo '<td>'.$janela->getOperacao().'</td>';
                                                echo '<td>'.$janela->getNF().'</td>';
                                                echo '<td>'.$janela->getOferta().'</td>';
                                                echo '<td>'.$janela->getPeso().'</td>';
                                                echo '<td>'.$janela->getDestino().'</td>';
                                                echo '</tr>';
                                                
                                            
                                                $count = $count + 1;
                                            }
                                        ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
<style>
.button-gerar{
    margin-top:2.5%;
}
</style>