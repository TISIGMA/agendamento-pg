<?php

require_once('../controller/shippingCompanyController.php');
require_once('../utils.php');

if($_SESSION['FUNCTION_ACCESS']['register_shipping_company'] == 'hidden') {
    echo "<script>window.location='/sigma-solutions/schedules/index.php'</script>";
}

$action = 'save';

$shippingCompanyController = new ShippingCompanyController($MySQLi);

if(isset($_POST['action']) && $_POST['action'] == 'save'){
    
    $result = $shippingCompanyController->save($_POST);

    switch ($result) {
        case 'ALREADY_EXISTS':
            errorAlert('Já existe uma transportadora cadastrada com esse nome.');
            break;
        
        case 'SAVE_ERROR':
            errorAlert('Erro ao salvar. Tente novamente mais tarde ou entre em contato com o administrador.');
            break;
        
        case 'SAVED':
            successAlert('Transportadora salva com sucesso!');
            break;
    }

    $_POST = null;
}

if(isset($_GET['delete']) && $_SERVER['REQUEST_METHOD'] !='POST'){

    $idDelete = $_GET['delete'];

    $result = $shippingCompanyController->deleteById($idDelete);

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

    $result = $shippingCompanyController->updateById($_POST['id'], $_POST['name']);

    switch ($result) {
        case 'UPDATED':
            successAlert('Registro alterado com sucesso.');
            break;
        
        case 'UPDATE_ERROR':
            errorAlert('Erro ao editar. Tente novamente mais tarde ou entre em contato com o administrador.');
            break;
    }
}

$shippingCompanys = $shippingCompanyController->findByClient('tetrapak');

?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header" id="title">Transportadora - Novo</h1>
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
                                <p class="help-block">Insira o nome completo da nova transportadora.</p>
                            </div>
                        
                            <button id="btn-salvar" type="submit" class="btn btn-primary">Salvar</button>
                            <button type="reset" onclick="resetNewShippingCompany()" class="btn btn-danger">Cancelar</button>
                        </form>
                    </div>
                </div>    
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Visualize aqui todas as transportadoras.
            </div>
            <div class="panel-body">
                <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nome</th>
                            <th>Editar</th>
                            <th>Excluir</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                            <?php
                                foreach($shippingCompanys as $shippingCompany) { 
                                    echo '<tr class="odd gradeX">';
                                    echo '<td>'.$shippingCompany->getId().'</td>';
                                    echo '<td>'.$shippingCompany->getNome().'</td>';
                                    echo '<td class="text-center clickble" onclick="editShippingCompany('.$shippingCompany->getId().', \''.$shippingCompany->getNome().'\' )"><span class="fa fa-edit text-primary"></span></td>';
                                    echo '<td class="text-center"><a href="index.php?customer=tetrapak&conteudo=newShippingCompany.php&delete='.$shippingCompany->getId().'"><span class="fa fa-trash-o text-danger"></span></a></td>';
                                    echo '</tr>';

                                }
                            ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>