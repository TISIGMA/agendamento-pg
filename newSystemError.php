<?php

require_once('controller/systemErrorController.php');
require_once('model/systemError.php');
require_once('utils.php');

$systemError = new SystemError();

$action = 'save';

$readonlyFields = 'readonly';

if($_SESSION['tipo'] == 'adm') $readonlyFields = '';

$systemErrorController = new SystemErrorController($MySQLi);

$editId = '';
$readonly = '';
$disabled = '';


if(isset($_POST['action']) && $_POST['action'] == 'save'){
    
    $result = $systemErrorController->save($_POST);

    switch ($result) {
        case 'SAVE_ERROR':
            echo "<script>window.location='home.php?conteudo=searchSystemError.php&feedback=SAVE_ERROR'</script>";	
            break;
        
        case 'SAVED':
            echo "<script>window.location='home.php?conteudo=searchSystemError.php&feedback=SAVED'</script>";	
            break;
    }

    $_POST = null;
}


if(isset($_POST['action']) && $_POST['action'] == 'edit'){

    $result = $systemErrorController->update($_POST);

    switch ($result) {
        case 'UPDATED':
            echo "<script>window.location='home.php?conteudo=searchSystemError.php&feedback=UPDATED'</script>";	
            break;
        
        case 'UPDATE_ERROR':
            echo "<script>window.location='home.php?conteudo=searchSystemError.php&feedback=UPDATE_ERROR'</script>";	
            break;
    }
}

if(isset($_GET['search']) && $_GET['search'] != null){

    $action = 'search';
    $searchId = $_GET['search'];
    if($_SESSION['tipo'] == 'adm'){
        $systemError = $systemErrorController->findById($searchId);
    }else{
        $systemError = $systemErrorController->findByIdAndUserId($searchId);

        if($systemError == null){
            echo "<script>window.location='home.php?conteudo=searchSystemError.php&feedback=NOT_RESULT'</script>";
        }
    }

    $readonly = 'readonly';
    $disabled = 'disabled';
}

if(isset($_GET['edit']) && $_GET['edit'] != null){

    $action = 'edit';
    $editId = $_GET['edit'];
    if($_SESSION['tipo'] == 'adm'){
        $systemError = $systemErrorController->findById($editId);
    }else{
        $systemError = $systemErrorController->findByIdAndUserId($editId);

        if($systemError == null){
            echo "<script>window.location='home.php?conteudo=searchSystemError.php&feedback=NOT_RESULT'</script>";
        }
    }
    
    $readonly = '';
    $disabled = '';
}

?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header" id="title">Reportar um Problema</h1>
    </div>                
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <form role="form-new-user" method="post" action="#" name="valida" enctype="multipart/form-data" onsubmit="return errorReportValidate('<?=$action ?>')">
                    <div class="row">
                        <div class="col-lg-6">
                            <input type="hidden" name="id" value="<?=$editId ?>" id="id">
                            <input type="hidden" name="action" value="<?=$action ?>" id="action">
                            <div class="form-group">
                                <label>Status</label>
                                <input type='text' class="invisible-disabeld-field form-control" value="<?php if($systemError->getStatus()==null) echo 'Novo'; else echo $systemError->getStatus() ?>" name="status" id="status" readonly/> 
                            </div>
                            <div class="form-group">
                                <label>Email para contato (Informe um e-mail para que o suporte possa entrar em contato)</label>
                                <input class="form-control" maxlength="100" name="email" id="email" value="<?=$systemError->getEmail() ?>" required <?=$disabled ?>>
                                <div class="feedback" id="mail-feedback">Informe um e-mail válido.</div>
                            </div>

                            <div class="form-group">
                                <label>Descrição do problema (Descreva em poucas palavras o problema ocorrido)</label>
                                <textarea class="form-control" maxlength="100" name="description" required <?=$disabled ?>><?=$systemError->getDescription() ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Resolução do problema</label>
                                <textarea class="form-control" maxlength="100" name="resolution" <?=$readonlyFields ?> <?=$disabled ?>><?=$systemError->getResolution() ?></textarea>
                            </div>

                            <div class="full-container">
                                <p class="mt-5 text-left">
                                    <label for="attachment">
                                        <a class="btn btn-primary text-light" role="button" aria-disabled="false" <?=$disabled ?>>+ Anexos</a>   
                                    </label>
                                    <input type="file" name="file" id="attachment" style="visibility: hidden; position: absolute;" onchange="handleReportChangeFiles()"/>
                                    
                                </p>
                                <p id="files-area">
                                    <span id="filesList">
                                        <span id="files-names">
                                            <?php

                                            if((isset($_GET['search']) && $_GET['search'] != null) || (isset($_GET['edit']) && $_GET['edit'] != null)){
                            
                                                if($systemError->getFileName() != null){

                                                    $filePath =  'error_files/error_' . $systemError->getId() . '/'.$systemError->getFileName();
    
                                                    echo '<span class="file-block">';
    
                                                    if($readonly != 'readonly'){
                                                        echo    '<span class="file-delete" id="'.$systemError->getId().'" onclick="removeFile(this, true)">+</span>';
                                                    }
    
                                                    echo    '<a class="file-saved" href="'.$filePath.'" download>';
                                                    echo        '<span class="name">'.$systemError->getFileName().'</span>';
                                                    echo    '</a>';
                                                    echo '</span>';
                                                }
                                                
                                            }
                                            ?>
                                        </span>
                                    </span>
                                </p>
                            </div> 
                            <input id="filesToRemove" name="filesToRemove" type="hidden" value="">
                        </div>
                        <div class="column-end col-lg-6">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <?php
                                    if($action == 'save'){
                                        echo '<label>Data</label>'; 
                                    }else{
                                        echo '<label>Criado em</label>';
                                    }
                                    ?>
                                    <input type='text' class="invisible-disabeld-field form-control" value="<?php if($systemError->getCreatedDate() == null) echo date("d/m/Y"); else echo $systemError->getCreatedDate() ?>" name="status" id="status" readonly/> 
                                </div>
                            </div>
                        </div>
                    </div>    
                    <div class="btn-group-end">
                        <a href="home.php?conteudo=searchSystemError.php" class="btn btn-light">Meus chamados</a>
                        <button id="btn-salvar" type="submit" class="btn btn-primary" <?=$disabled ?>>Salvar</button>
                        <button type="reset" class="btn btn-danger">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>