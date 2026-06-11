<?php
    require('class.php');
    require('../../conn.php');
    require('../functions.php');

    if(isset($_GET['data']) && $_GET['data'] != null){
        $data = date('Y-m-d', strtotime($_GET['data']));
        $armazem = $_GET['armazem'];
    }
    if(isset($_GET['remove']) && $_GET['remove'] != null){
        if(deletarAgendamento($_GET['remove'], $MySQLi)){
            messageSuccess("Agendamento excluído com sucesso!");
        }else{
            messageErro("Fallha ao excluir agendamento.<br>Tente mais tarde!");
        }
    }

    $janelas = listarJanelasOcupadasDados($MySQLi, $data, $armazem);

   
?>
<style>
    @media print {
        #ocult{
            visibility: hidden;
        }
    }
</style>
   
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Horários Ocupados <br><small>Armazém <?= $armazem?></small></h1>
                    
                    <strong><a href="excel.php?data=<?=$data ?>&titulo=ocupadas&armazem=<?=$armazem ?>&tipo=dia"><i class="fa fa-file-excel-o"></i> Exportar</a></strong>
                </div>
            </div><br>
                <div class="col-lg-12">
                    <div id="print" class="panel panel-default">
                        <div class="panel-heading">
                        <div class="panel-body">
                            <table style="text-align: center;" width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
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
                                        <th>Imprimir</th>
                                        <th id="ocult">Editar</th>
                                        <th id="ocult">Excluir</th>
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
                                                echo '<td><button class="btn btn-link" onclick="print('.$janela->getId().')"><span class="glyphicon glyphicon-print"></span></button></td>';
                                                echo '<td id="ocult"><a href="index.php?conteudo=edit-agendamento.php&id='.$janela->getId().'" class="fa fa-edit" title="Editar"></a></td>';
                                                echo '<td id="ocult"><a  class="fa fa-remove" title="Excluir" data-toggle="modal" data-target="#exampleModal'.$count.'">';
                                                echo '</tr>';
                                                echo '
                                                <div class="modal fade" id="exampleModal'.$count.'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Confirmar</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Tem certeza que deseja excluir o agendamento?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                        <a href="index.php?conteudo=janelaOcupada.php&remove='.$janela->getId().'&data='.$data.'&armazem='.$janela->getArmazem().'">
                                                        <button type="button" class="btn btn-danger">Excluir</button></a>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                                ';
                                                $count = $count + 1;
                                            }
                                        ?>
                                        
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <script>
function imprimir(){
   var conteudo = document.getElementById('print').innerHTML;
   tela_impressao = window.open('Horários Ocupados');
   tela_impressao.document.write(conteudo);
   tela_impressao.window.print();
   tela_impressao.window.close();
}
function print(id){
    var myForm = document.createElement("form");
    myForm.action = "print.php";
    myForm.method = "post";
    var input = document.createElement("input");
    input.name = "id";
    input.value = id;
    
	input.type = "number"; 
	myForm.appendChild(input);	
    document.body.appendChild(myForm);
    myForm.submit();
}
</script>
