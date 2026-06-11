<?php
require('class.php');
require('../functions.php');

$transportadora = new Transportadora();
if(isset($_POST['nome']) && $_POST['nome'] != null){
    if($_POST['check_senha'] == $_POST['senha']){
        $Transp = new Transportadora();
        $Transp->setNome($_POST['nome']);
        $Transp->setUsername($_POST['username']);
        $Transp->setCNPJ($_POST['cnpj']);
        $Transp->setEmail($_POST['email']);
        $Transp->setTelefone($_POST['telefone']);
        $Transp->setCelular($_POST['celular']);
        $Transp->setPassword($_POST['check_senha']);
        $Transp->setUsuario($_SESSION['nome']);
        date_default_timezone_set('America/Sao_Paulo');
        $data = date('Y-m-d H:i');
        $Transp->setData($data);
        if($_POST['id'] == null){
            if($Transp->salvarTransportadora($MySQLi)==true){
                echo messageSuccess('Transportadora salva com sucesso!');
            }else{
                echo messageErro('Erro ao salvar Transportadora!<br>Tente mais tarde!');
            }
        }else{
            if($Transp->editarTransportadora($MySQLi, $_POST['id'])==true){
                echo messageSuccess('Transportadora editado com sucesso!');
            }else{
                messageErro('Erro ao salvar edição!<br>Tente mais tarde!');
            }
        }  

    }else{
        echo "<script>javascript:history.back(-1)</script>";
    }  
}


if(isset($_GET['edit'])&& $_GET['edit'] != null){
    $result = $transportadora->buscarTransportadora($_GET['edit'], $MySQLi);
    while ($dados = $result->fetch_assoc()){ 
        $transportadora->setId($dados['id']);
        $transportadora->setNome($dados['nome']);
        $transportadora->setUsername($dados['username']);
        $transportadora->setCNPJ($dados['cnpj']);
        $transportadora->setEmail($dados['email']);
        $transportadora->setTelefone($dados['telefone']);
        $transportadora->setCelular($dados['celular']);
        $transportadora->setPassword($dados['password']);
    }
}

?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Nova Transportadora</h1>
    </div>                
</div>
<div class="row">
    <div class="col-lg-12">
         <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <form role="form-new-user" method="post" action="#" name="valida">
                        <input type="hidden" name="id" value="<?php echo $transportadora->getId() ?>">
                            <div class="form-group">
                                <label>Transportadora</label>
                                <input class="form-control" maxlength="100" name="nome" placeholder="Nome da transportadora" value="<?php echo $transportadora->getNome()  ?>" required>
                                <p class="help-block">Insira o nome completo da nova transportadora.</p>
                            </div>
                            <div class="form-group">
                                <label>Username</label>
                                <input class="form-control" maxlength="50" name="username" placeholder="Username" value="<?php echo $transportadora->getUsername() ?>" required>
                                <p class="help-block">Informe o username que a transportadora irá usar para entrar no sistema.</p>
                            </div>
                            <div class="form-group">
                                <label>CNPJ</label>
                                <input class="form-control cnpj" id="cnpj" name="cnpj" placeholder="CNPJ" value="<?php echo $transportadora->getCNPJ() ?>" required>
                            </div>
                            <div class="form-group">
                                <label>E-mail</label>
                                <input class="form-control" maxlength="100" placeholder="E-mail" name="email" value="<?php echo $transportadora->getEmail() ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Telefone</label>
                                <input class="form-control telefone" type="text" id="telefone" placeholder="Telefone" name="telefone" value="<?php echo $transportadora->getTelefone() ?>" >
                            </div>
                            <div class="form-group">
                                <label>Celular</label>
                                <input class="form-control celular" type="text" id="celular" placeholder="Celular" name="celular" value="<?php echo $transportadora->getCelular() ?>" >
                            </div>
                            <div class="form-group">
                                <label>Senha</label>
                                <input class="form-control" type="password" minlength="6" maxlength="20" id="senha" placeholder="Senha" name="senha" value="<?php echo $transportadora->getPassword() ?>" required>
                                <p class="help-block">Mínimo de 6 caracteres.</p>
                            </div>
                            <div class="form-group">
                                <label>Repita a senha</label>
                                <input class="form-control" type="password" minlength="6" maxlength="20" onkeyup="verificaSenha(this, 'senha')" placeholder="Repita a senha" name="check_senha" value="<?php echo $transportadora->getPassword() ?>" required >
                                <p id="feedback-senha" class="help-block">Mínimo de 6 caracteres.</p>
                            </div>
                            <button id="btn-salvar" type="submit" class="btn btn-primary">Salvar</button>
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
            document.getElementById('btn-salvar').disabled = false;
		}
		else{
			label.innerHTML = 'Senhas não conferem';
            document.getElementById('btn-salvar').disabled = true;
		}
		liberaSalvar()
	}
</script>





       