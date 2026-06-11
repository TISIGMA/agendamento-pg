<?php
    require('class.php');
    require('../../conn.php');
    require('../functions.php');

    $titulo = $_GET['titulo'];
    $data = $_GET['data'];
    $status = $_GET['status'];
    $armazem = $_GET['armazem'];

    if(isset($_GET['idhorario']) && $_GET['idhorario'] != null){
        $result = insertHorarioStatus($MySQLi, $_GET['idhorario'], $_GET['statusAcao'], $_SESSION['nome'], $data, $armazem);
        if($result == true){
            messageSuccess('Horário bloqueado com sucesso!');
        }else{
            messageErro('Erro ao bloquear o horário.<br>Tente mais tarde!');
        }
    }
    $janelas = listarJanelasPorData($MySQLi, $data, $status, $armazem);

?>
   
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Horários <?=$titulo ?><br><small>Armazém <?= $armazem?></small></h1>
                </div>
            </div>
            <?php 
            if($_SESSION['tipo'] != "user" && $titulo == "Livres"){
                echo '<div class="row">
                    <div class="col-lg-3">
                        <form method="post" action="index.php?conteudo=agendamento.php">
                            <div class="form-group mx-sm-2 mb-1">
                                <label>Data</label>
                                <input name="armazem" value="'.$armazem.'" type="hidden">
                                <input type="date" name="data" class="form-control" value="'.date('Y-m-d',strtotime($data)).'" ><br>
                                <button class="btn btn-primary"><span class="fa fa-plus-circle"></span> Novo agendamento</button>
                            </div>
                        </form><br>
                    </div>
                </div>';
            }
            
            ?>
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                        <div class="panel-body">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
                                        <th>Horário</th>
                                        <th>Posição</th>
                                        <th>status</th>
                                        <th>Gerenciar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                        <?php
                                            
                                            foreach($janelas as $horario) {
                                                switch($status){
                                                    case 'Livre':{
                                                        $TDTitulo = "Bloquear";
                                                        $evento = '<a title="Bloquear" href="index.php?conteudo=janela.php&idhorario='.$horario->getId().'&statusAcao=Bloqueado&data='.$data.'&status=Livre&titulo=Livres&armazem='.$armazem.'" class="fa fa-lock"></a>';
                                                        break;
                                                    }
                                                    case 'Bloqueado':{
                                                        $TDTitulo = "Liberar";
                                                        $evento = '<a title="Liberar" href="index.php?conteudo=janela.php&idhorario='.$horario->getId().'&statusAcao=Livre&data='.$data.'&status=Bloqueado&titulo=Bloqueados&armazem='.$armazem.'" class="fa fa-unlock"></a>';
                                                        $class = 'danger';
                                                        break;
                                                    }
                                                }
                                                echo '<tr class="'.$class.'">'; 
                                                echo '<td>'.$horario->getHorario().'</td>';
                                                echo '<td>'.$horario->getPosicao().'</td>';;
                                                echo '<td>'.$status.'</td>';
                                                echo '<td>'.$evento.'</td>';
                                                echo '</tr>';
                                                $contLin++;
                                            }
                                        ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
