<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('controller/systemErrorController.php');
require_once('utils.php');

$systemErrorController = new SystemErrorController($MySQLi);

if($_SESSION['tipo'] == 'adm') $systemErrors = $systemErrorController->findAll();
else $systemErrors = $systemErrorController->findByUserId();

if(isset($_GET['feedback']) && $_GET['feedback'] != null){

    $result = $_GET['feedback'];

    switch ($result) {
        case 'UPDATED':
            successAlert('Registro alterado com sucesso.');
            break;
        
        case 'UPDATE_ERROR':
            errorAlert('Erro ao editar. Tente novamente mais tarde ou entre em contato com o administrador.');
            break;
        
        case 'SAVE_ERROR':
            errorAlert('Erro ao salvar. Tente novamente mais tarde ou entre em contato com o administrador.');
            break;
        
        case 'SAVED':
            successAlert('Chamado criado com sucesso!');
            break;

        case 'NOT_RESULT':
            warningAlert('Nenhum registro foi encontraco!');
            break;
    }
}

?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header" id="title">Reportar um Problema</h1>
        <div class="action-area">
            <a href="home.php?conteudo=newSystemError.php" id="btn-salvar" type="button" class="btn btn-primary">Novo</a>
        </div>
    </div>                
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Meus chamados
            </div>
            <div class="panel-body">
                <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Status</th>
                            <?php
                            if($_SESSION['tipo'] == 'adm') echo '<th>Usuário</th>';
                            ?>
                            <th>Data Criação</th>
                            <th>Descrição</th>
                            <th>Resolução</th>
                            <th>Visualizar</th>
                            <th>Editar</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                            <?php
                            if(count($systemErrors) > 0){
                                foreach($systemErrors as $systemError) { 
                                    echo '<tr class="odd gradeX">';
                                    echo '<td>'.$systemError->getId().'</td>';
                                    echo '<td>'.$systemError->getStatus().'</td>';
                                    if($_SESSION['tipo'] == 'adm') echo '<td>'.$systemError->getUserName().'</td>';
                                    echo '<td>'.$systemError->getCreatedDate().'</td>';
                                    echo '<td>'.$systemError->getDescription().'</td>';
                                    echo '<td>'.$systemError->getResolution().'</td>';
                                    echo '<td class="text-center"><a href="home.php?conteudo=newSystemError.php&search='.$systemError->getId().'"><span class="fa fa-search text-primary"></span></a></td>';
                                    echo '<td class="text-center"><a href="home.php?conteudo=newSystemError.php&edit='.$systemError->getId().'"><span class="fa fa-edit text-primary"></span></a></td>';
                                    echo '</tr>';

                                }
                            }
                            ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
