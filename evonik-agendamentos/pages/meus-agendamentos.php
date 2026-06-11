<?php
    require('class.php');
    require('../../conn.php');
    require('../functions.php');

    date_default_timezone_set('America/Sao_Paulo');
    $data = date("Y-m-d");

    $janela = new Janela();
    $arrayJanela = array();
    $arrayJanela = buscarJanelaPorNome($janela, $MySQLi, $_SESSION['nome']);
    if($_POST['save'] == true || isset($_GET['save'])){
        echo messageSuccess('Agendamento Salvo com sucesso!');
    }
    if($_GET['erro'] != null){
        switch ($_GET['erro']) {
            case 'noHour':
                echo messageErro('Para realizar um agendamento é necessário 4 horas de antecedência para o último horário livre.<br>Busque um horário em outra data!');;
                break;
            case 'erroPrint':
                echo messageErro('Falha ao imprimir, por favor tente mais tarde!');
                break;
            case 'erroSalvar':
                echo messageErro('Não foi possível salvar o agendamento, por favor tente mais tarde!');
                break;
            default:
                echo messageErro('Ocorreu um erro, por favor tente mais tarde!');
        }
    }
    
    
    $horario = new Horario();
    $status = '';

    $horarioArray = buscarHorarios($horario, $MySQLi);
?>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Meus agendamentos</h1>
                </div>                
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <form method="post" action="index.php?conteudo=agendamento.php">
                        <div class="form-group mx-sm-2 mb-1">
                            <label>Data</label>
                            <input type="date" name="data" class="form-control" value="<?php echo date('Y-m-d',strtotime($data));?>" ><br>
                            <label>Armazém</label>
                            <select name="armazem" class="form-control">
                                <option value="1">1</option>
                                <option value="2">2</option>
                            </select><br>
                            <button class="btn btn-primary"><span class="fa fa-plus-circle"></span> Novo agendamento</button>
                        </div>
                        
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Visualize aqui todos as transportadoras cadastrados.
                        </div>
                        <div class="panel-body">
                            <table style="text-align: center;" width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Data</th>
                                        <th>Armazém</th>
                                        <th>Horário</th>
                                        <th>Placa do veículo</th>
                                        <th>NF</th>
                                        <th>Operação</th>
                                        <th>Imprimir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    <?php
                                        foreach($arrayJanela as $janela) {
                                            echo '<tr class="odd gradeX">';
                                            echo '<td>'.$janela->getId().'</td>';
                                            echo '<td>'.date('d/m/Y', strtotime($janela->getData())).'</td>';
                                            echo '<td>'.$janela->getArmazem().'</td>';
                                            $horarioDesc = buscarHorario($janela->getIdhorario(), $MySQLi);
                                            echo '<td>'.$horarioDesc->getHorario().'</td>';
                                            echo '<td>'.$janela->getPlacaCavalo().'</td>';
                                            echo '<td>'.$janela->getNF().'</td>';
                                            echo '<td>'.$janela->getOperacao().'</td>';
                                            echo '<td><button class="btn btn-link" onclick="print('.$janela->getId().')"><span class="glyphicon glyphicon-print"></span></button></td>';
                                            echo '</tr>';
                                        }      
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
<script>
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