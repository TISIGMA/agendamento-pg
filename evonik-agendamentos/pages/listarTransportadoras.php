<?php
    require('class.php');
    require('../../conn.php');
    require('../functions.php');

    date_default_timezone_set("America/Sao_Paulo");
    $transportadora = new Transportadora();

    if(isset($_GET['delete']) && $_GET['delete'] != null){
        if($transportadora->deletarTransportadora($_GET['delete'], $MySQLi)==true){
            echo messageSuccess('Transportadora excluída com sucesso!');
        };
    }

    $transp = array();
    $transp = buscarTransportadora($transportadora, $MySQLi);
?>

   
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Transportadoras</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Visualize aqui todos as transportadoras cadastrados.
                        </div>
                        <div class="panel-body">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Username</th>
                                        <th>CNPJ</th>
                                        <th>E-mail</th>
                                        <th>Telefone</th>
                                        <th>Celular</th>
                                        <th>Data de criação/edição</th>
                                        <th>Criado/alterado por</th>
                                        <th>Editar</th>
                                        <th>Excluir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                        <?php
                                            foreach($transp as $trans) { 
                                                echo '<tr class="odd gradeX">';
                                                echo '<td>'.$trans->getNome().'</td>';
                                                echo '<td>'.$trans->getUsername().'</td>';
                                                echo '<td>'.$trans->getCNPJ().'</td>';
                                                echo '<td>'.$trans->getEmail().'</td>';
                                                echo '<td>'.$trans->getTelefone().'</td>';
                                                echo '<td>'.$trans->getCelular().'</td>';
                                                echo '<td>'.date('d/m/Y', strtotime($trans->getData())).'</td>';
                                                echo '<td>'.$trans->getUsuario().'</td>';
                                                echo '<td class="text-center"><a href="index.php?conteudo=novaTransportadora.php&edit='.$trans->getId().'"><span class="fa fa-edit text-primary"></span></a></td>';
                                                echo '<td class="text-center"><a href="index.php?conteudo=listarTransportadoras.php&delete='.$trans->getId().'"><span class="fa fa-trash-o text-danger"></span></a></td>';
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

     
