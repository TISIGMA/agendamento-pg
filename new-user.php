<?php

require('evonik-agendamentos/pages/class.php');
require('evonik-agendamentos/functions.php');
require_once('controller/systemsController.php');

$systemsController = new SystemsController($MySQLi);
$systemIds = array();

$userTypeValues = [
    ['key'=> 'adm',         'value' => 'Administrador'],
    ['key'=> 'client',      'value' => 'Cliente'],
    ['key'=> 'operator',    'value' => 'Operador'],
    ['key'=> 'gerenciador', 'value' => 'Gerenciador'],
    ['key'=> 'porter',      'value' => 'Portaria'],
    ['key'=> 'user',        'value' => 'Visitante']
];

$usuario = new Usuario();
if(isset($_POST['usuario']) && $_POST['usuario'] != null){
    if($_POST['check_senha'] != $_POST['senha']){
        echo "<script>javascript:history.back(-1)</script>";
    }
    $User = new Usuario();
    $User->setNome($_POST['usuario']);
    $User->setUsername($_POST['username']);
    $User->setPassword($_POST['check_senha']);
    $User->setTipo($_POST['userType']);
    $User->setUsuarioCriacao($_SESSION['nome']);
    $User->setSystemAccess($_POST['systemAccess']);

    date_default_timezone_set('America/Sao_Paulo');
    $data = date('Y-m-d H:i');
    $User->setData($data);
    if($_POST['id'] == null){

        if($User->salvarUsuario($MySQLi)==true){
            echo messageSuccess('Usuário Salvo com sucesso!');
        }else{
            messageErro('Erro ao salvar usuário!<br>Tente mais tarde!');
        }
    }else{

        if($User->editarUsuario($MySQLi, $_POST['id'])==true){
            echo messageSuccess('Usuário editado com sucesso!');
        }else{
            messageErro('Erro ao salvar edição!<br>Tente mais tarde!');
        }
    }  
}

if(isset($_GET['edit'])&& $_GET['edit'] != null){
    $result = $usuario->buscarUsuario($_GET['edit'], $MySQLi);

    foreach(evonik_records_iter($result) as $dados){ 
        $usuario->setId($dados['user_id']);
        $usuario->setNome($dados['nome']);
        $usuario->setUsername($dados['username']);
        $usuario->setPassword($dados['password']);
        $usuario->setTipo($dados['tipo']);
        array_push($systemIds, $dados['systemsId']);
    }
}


$systems = $systemsController->findAll();

?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Novo Usuário</h1>
    </div>                
</div>
<div class="row">
    <div class="col-lg-12">
         <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <form role="form-new-user" method="post" action="#">
                        <input  type="hidden" name="id" value="<?php echo $usuario->getId() ?>" >
                            <div class="form-group">
                                <label>Nome</label>
                                <input class="form-control" name="usuario" placeholder="Nome" maxlength="100" value="<?php echo $usuario->getNome()  ?>" required>
                                <p class="help-block">Insira o nome completo do novo usuário.</p>
                            </div>
                            <div class="form-group">
                                <label>Username</label>
                                <input class="form-control" name="username" maxlength="50" placeholder="Username" value="<?php echo $usuario->getUsername() ?>" required>
                                <p class="help-block">Informe o username que usuário irá usar entrar no sistema.</p>
                            </div>
                            <div class="form-group">
                                <label>Tipo de acesso</label>
                                <select name="userType" class="form-control placeholder" aria-label="Default select example" required>
                                    <option value="">Selecione...</option>
                                    <?php
                                    foreach ($userTypeValues as $userTypeValue) {

                                        $selected = null;
                                        if($usuario->getTipo() == $userTypeValue['key']) $selected = 'selected';

                                        echo '<option value="'.$userTypeValue['key'].'" '.$selected.' >'.$userTypeValue['value'].'</option>';
                                    }
                                    ?>
                                  </select>
                            </div>
                            <div class="form-group">
                                <label>Sistemas</label>
                                <select name="systemAccess[]" class="form-control" multiple required>
                                    <?php
                                        if(count($systems) > 0){
                                            foreach ($systems as $system) {

                                                $selected = in_array($system->getId(), $systemIds) ? 'selected' : null;

                                                echo '<option '.$selected.' value="'.$system->getId().'">'.$system->getDescription().'</option>';
                                            }
                                        }
                                    ?>
                                </select>
                                <p class="help-block">Cuidado, pois aqui você concede acesso aos sistemas da plataforma. :|</p>
                            </div>
                            <div class="form-group">
                                <label>Senha</label>
                                <input class="form-control" id="senha" type="password" minlength="6" maxlength="20" placeholder="Senha" name="senha" value="<?php echo $usuario->getPassword() ?>" required>
                                <p class="help-block">Mínimo de 6 caracteres.</p>
                            </div>
                            <div class="form-group">
                                <label>Repita a senha</label>
                                <input class="form-control" onkeyup="verificaSenha(this, 'senha')" type="password" minlength="6" maxlength="20" placeholder="Senha" name="check_senha" value="<?php echo $usuario->getPassword() ?>" required>
                                <p id="feedback-senha" class="help-block">Mínimo de 6 caracteres.</p>
                            </div>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                            <button type="reset" class="btn btn-danger">Cancelar</button>
                        </form>
                    </div>
                </div>    
            </div>
        </div>
    </div>
</div>

<script>
    var label = document.getElementById('feedback-senha');
    function verificaSenha(senha1, el2){
		var senha2 = document.getElementById(el2);
		if(senha1.value == senha2.value){
			label.innerHTML = 'Senhas iguais';
		}
		else{
			label.innerHTML = 'Senhas não conferem';
		}
		liberaSalvar()
	}
</script>
       
