<?php

require_once('../controller/operationSourceController.php');
require_once('../utils.php');

if($_SESSION['FUNCTION_ACCESS']['register_operation_type'] == 'hidden') {
    echo "<script>window.location='/sigma-solutions/schedules/index.php'</script>";
}

$action = 'save';

$operationSourceController = new OperationSourceController($MySQLi);

if(isset($_POST['action']) && $_POST['action'] == 'save'){
    
    $result = $operationSourceController->save($_POST);

    switch ($result) {
        case 'ALREADY_EXISTS':
            errorAlert('Já existe uma natureza de operação cadastrada com esse nome.');
            break;
        
        case 'SAVE_ERROR':
            errorAlert('Erro ao salvar. Tente novamente mais tarde ou entre em contato com o administrador.');
            break;
        
        case 'SAVED':
            successAlert('Tipo de operação salvo com sucesso!');
            break;
    }

    $_POST = null;
}

if(isset($_GET['delete']) && $_SERVER['REQUEST_METHOD'] !='POST'){

    $idDelete = $_GET['delete'];

    $result = $operationSourceController->deleteById($idDelete);

    switch ($result) {
        case 'DELETED':
            successAlert('Registro excluído com sucesso.');
            break;
        
        case 'DELETE_ERROR':
            errorAlert('Erro ao excluir. Tente novamente mais tarde ou entre em contato com o administrador.');
            break;
    }

    $_POST = null;
}

if(isset($_POST['action']) && $_POST['action'] == 'edit'){

    $result = $operationSourceController->updateById($_POST['id'], $_POST['name']);

    switch ($result) {
        case 'UPDATED':
            successAlert('Registro alterado com sucesso.');
            break;
        
        case 'UPDATE_ERROR':
            errorAlert('Erro ao editar. Tente novamente mais tarde ou entre em contato com o administrador.');
            break;
    }
}

$operationSources = $operationSourceController->findAll();

?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header" id="title">Natureza da Operação - Novo</h1>
    </div>                
</div>
<div class="row">
    <div class="col-lg-12">
         <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <form role="form-new-user" method="post" action="#" name="valida">
                        <input type="hidden" name="id" value="" id="id">
                        <input type="hidden" name="action" value="save" id="action">
                            <div class="form-group">
                                <label>Nome</label>
                                <input class="form-control" maxlength="100" name="name" id="name" required>
                            </div>
                        
                            <button id="btn-salvar" type="submit" class="btn btn-primary">Salvar</button>
                        </form>
                    </div>
                </div>    
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Visualize aqui todos os registros de natureza de operação.
            </div>
            <div class="panel-body">
                <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nome</th>
                            <th>Editar</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                            <?php
                            if(count($operationSources) > 0){
                                foreach($operationSources as $operationSource) { 
                                    echo '<tr class="odd gradeX">';
                                    echo '<td>'.$operationSource->getId().'</td>';
                                    echo '<td>'.$operationSource->getName().'</td>';
                                    echo '<td class="text-center clickble" onclick="editOperationSource('.$operationSource->getId().', \''.$operationSource->getName().'\' )"><span class="fa fa-edit text-primary"></span></td>';
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