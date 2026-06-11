<?php

require_once('../controller/employeeController.php');
require_once('../utils.php');

if($_SESSION['FUNCTION_ACCESS']['register_employee'] == 'hidden') {
    echo "<script>window.location='/sigma-solutions/schedules/index.php'</script>";
}

$action = 'save';

$employeeController = new EmployeeController($MySQLi);

if(isset($_POST['action']) && $_POST['action'] == 'save'){
    
    $result = $employeeController->save($_POST);

    switch ($result) {
        case 'ALREADY_EXISTS':
            errorAlert('Já existe um colaborador cadastrado com esse nome.');
            break;
        
        case 'SAVE_ERROR':
            errorAlert('Erro ao salvar. Tente novamente mais tarde ou entre em contato com o administrador.');
            break;
        
        case 'SAVED':
            successAlert('Colaborador salvo com sucesso!');
            break;
    }

    $_POST = null;
}

if(isset($_GET['delete']) && $_SERVER['REQUEST_METHOD'] !='POST'){

    $idDelete = $_GET['delete'];

    $result = $employeeController->deleteById($idDelete);

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

    $result = $employeeController->updateById($_POST['id'], $_POST['name'], $_POST['position']);

    switch ($result) {
        case 'UPDATED':
            successAlert('Registro alterado com sucesso.');
            break;
        
        case 'UPDATE_ERROR':
            errorAlert('Erro ao editar. Tente novamente mais tarde ou entre em contato com o administrador.');
            break;
    }
}

$employees = $employeeController->findAll();

?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header" id="title">Colaborador - Novo</h1>
    </div>                
</div>
<div class="row">
    <div class="col-lg-12">
         <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <form role="form-new-employee" method="post" action="#" name="valida">
                        <input type="hidden" name="id" value="" id="id">
                        <input type="hidden" name="action" value="save" id="action">
                        <div class="form-group">
                            <label>Nome</label>
                            <input class="form-control" maxlength="50" name="name" id="name" required>
                        </div>
                        <div class="form-group">
                            <label>Cargo</label>
                                <select name="position" id="position" class="form-control" aria-label="Default select example" required>
                                    <option value="">Selecione...</option>
                                    <option value="conferente">conferente</option>
                                    <option value="operador">operador</option>
                                    </select>
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
                Visualize aqui todos os colaboradores.
            </div>
            <div class="panel-body">
                <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>cargo</th>
                            <th>criado por</th>
                            <th>criado em</th>
                            <th>editado por</th>
                            <th>editado em</th>
                            <th>Editar</th>
                            <th>Excluir</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                            <?php
                                if($employees != null){
                                    foreach($employees as $employee) { 
                                        echo '<tr class="odd gradeX">';
                                        echo '<td>'.$employee->getName().'</td>';
                                        echo '<td>'.$employee->getPosition().'</td>';
                                        echo '<td>'.$employee->getCreatedBy().'</td>';
                                        echo '<td>'.$employee->getCreatedDate().'</td>';
                                        echo '<td>'.$employee->getLastModifiedBy().'</td>';
                                        echo '<td>'.$employee->getLastModifiedDate().'</td>';
                                        echo '<td class="text-center clickble" onclick="editEmployee('.$employee->getId().', \''.$employee->getName().'\', \''.$employee->getPosition().'\' )"><span class="fa fa-edit text-primary"></span></td>';
                                        echo '<td class="text-center"><a href="index.php?customer=tetrapak&conteudo=newEmployee.php&delete='.$employee->getId().'"><span class="fa fa-trash-o text-danger"></span></a></td>';
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