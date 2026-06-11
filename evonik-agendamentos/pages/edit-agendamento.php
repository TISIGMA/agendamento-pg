<?php
require('class.php');
require('../functions.php');

if(isset($_GET['id']) && $_GET['id'] != null){
    date_default_timezone_set("America/Sao_Paulo");
    $id = $_GET['id'];
    $janela = new Janela();
    $JanelaEdit = buscarJanelaPorId($MySQLi, $_GET['id']);
    $hora = buscarHorario($JanelaEdit->getIdHorario(), $MySQLi);
    $data = date('d-m-Y', strtotime($JanelaEdit->getData()));
}

if(isset($_POST['transportadora']) && $_POST['transportadora']){
    $janela = new Janela();
    $janela->setId($_POST['id']);
    $janela->setData($_POST['data']);
    $janela->setIdhorario($_POST['horario']);
    $janela->setTransportadora($_POST['transportadora']);
    $janela->setOferta($_POST['oferta']);
    $janela->setTipoVeiculo($_POST['tipoVeiculo']);
    $janela->setPlacaCavalo($_POST['placaCavalo']);
    $janela->setPlacaCarreta($_POST['placaCarreta']);
    $janela->setOperacao($_POST['op']);
    $janela->setDoca($_POST['doca']);
    $janela->setNf($_POST['nf']);
    $janela->setPeso($_POST['peso']);
    $janela->setDestino($_POST['destino']);

    if($_POST['inicioOperacaoData']!=null){
        $janela->setInicioOperacao($_POST['inicioOperacaoData'].' '.$_POST['inicioOperacaoHora']);
    }else{
        $janela->setInicioOperacao(null);
    }
    if($_POST['fimOperacaoData']!=null){
        $janela->setFimOperacao($_POST['fimOperacaoData'].' '.$_POST['fimOperacaoHora']);
    }else{
        $janela->setFimOperacao(null);
    }
    if(editarJanelaId($janela, $MySQLi, $id)==true){
        echo "<script>window.location='index.php?conteudo=panel.php&save'</script>";	
    }else{
        messageErro('Erro ao editar agendamento!<br>Tente mais tarde!');
    }
}
$tipoVeiculo = buscarTipoVeiculo($MySQLi);
$transportadora = new Transportadora();
$horario = new Horario();

$arraytransportadora = buscarTransportadora($transportadora, $MySQLi);
$arrayHorarios = array();
$horario = new Horario();
$arrayHorarios = buscarHorariosData($_SESSION["tipo"], $JanelaEdit->getArmazem(), $data, $horario, $MySQLi);
 
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Editar Operação</h1>
    </div>                
</div>
<div class="row">
    <div class="col-lg-12">
         <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                            <div class="form-group mx-sm-2 mb-1">
                                <label>Data</label>
                                <p><?php echo date('d/m/Y',strtotime($data)) ?></p>  
                                <label>Armazém</label>
                                <p><?php echo $JanelaEdit->getArmazem(); ?></p>       
                            </div>
                        <form role="form-new-user" method="post"  action="#" name="valida">
                            <input type="hidden" name="id">
                            
                            <div class="form-group">
                                <input name="id" value="<?=$id ?>" hidden> 
                                <input name="data" value="<?= date('Y/m/d',strtotime($data)) ?>" hidden>
                                <label>Horário</label>
                                <select name="horario" class="form-control">
                                <?php
                                    foreach($arrayHorarios as $horario){
                                        if($hora->getHorario() == $horario->getHorario()){
                                            echo "<option selected='selected' value='".$horario->getId()."'>".$horario->getHorario()."</option>";
                                        }else{
                                            echo "<option value='".$horario->getId()."'>".$horario->getHorario()."</option>";
                                        }
                                    }
                                ?>   
                                </select>
                            </div>
                            <div class="form-group">
                            <!-- alterações bloqueadas -->
                                <!-- <label>Oferta</label> -->
                                <input class="form-control" value="<?= $JanelaEdit->getOferta()?>" name="oferta" type="hidden" placeholder="Oferta" >
                            </div>
                            <div class="form-group">
                                <label>Transportadora</label>
                                <select name="transportadora" class="form-control">
                                <?php
                                    if($_SESSION['tipo'] == 'user'){
                                        echo "<option value='".$arraytransportadora->getNome()."'>".$arraytransportadora->getNome()."</option>";
                                    }else{
                                        foreach($arraytransportadora as $transportadora){
                                            if($JanelaEdit->getTransportadora()==$transportadora->getNome()){
                                                echo "<option selected='selected' value='".$transportadora->getNome()."'>".$transportadora->getNome()."</option>";
                                            }else{
                                                echo "<option value='".$transportadora->getNome()."'>".$transportadora->getNome()."</option>";
                                            }   
                                              
                                        }
                                    }
                                    
                                ?>   
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tipo de Veículo</label>
                                <select name="tipoVeiculo" class="form-control">
                                <?php
                                    while ($dados = $tipoVeiculo->fetch_assoc()){ 
                                        if($JanelaEdit->getTipoVeiculo() == $dados['descricao']){
                                            echo "<option selected='selected' value='".$dados['descricao']."'>".$dados['descricao']."</option>";
                                        }else{
                                            echo "<option value='".$dados['descricao']."'>".$dados['descricao']."</option>";
                                        }
                                    }
                                
                                ?>   
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Placa do cavalo</label>
                                <input style="text-transform: uppercase" value="<?= $JanelaEdit->getPlacaCavalo()?>" class="form-control placa" type="text" name="placaCavalo" id="placa" placeholder="placa do cavalo" name="placaCavalo" maxlength="8" minlength="8" required>
                            </div>
                            <div class="form-group">
                                <label>Placa do carreta</label>
                                <input style="text-transform: uppercase" value="<?= $JanelaEdit->getPlacaCarreta()?>" class="form-control placa" type="text" name="placaCarreta" id="placa" placeholder="placa da carreta" name="placaCarreta" maxlength="8" minlength="8">
                            </div>
                            <div class="form-group">
                                <label>Operação</label>
                                <select name="op" class="form-control">
                                <?php
                                    if($JanelaEdit->getOperacao() == "carga"){
                                        echo '<option selected="selected" value="carga">Carga</option>';
                                        echo '<option value="descarga">Descarga</option>';
                                    }else{
                                        echo '<option value="carga">Carga</option>';
                                        echo '<option selected="selected" value="descarga">Descarga</option>';
                                    }
                                ?>
                                    
                                    
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Nota Fiscal</label>
                                <input class="form-control" value="<?= $JanelaEdit->getNF()?>" name="nf" type="text" id="celular" placeholder="Nota fiscal" name="nf" >
                            </div>
                            <div class="form-group">
                                <label>Peso</label>
                                <input class="form-control" value="<?= $JanelaEdit->getPeso()?>" name="peso" type="number"  placeholder="Peso" >
                            </div>
                            <div class="form-group">
                                <label>Destino</label>
                                <input class="form-control" value="<?= $JanelaEdit->getDestino()?>" name="destino" type="text" placeholder="Destino">
                            </div>
                            <div class="form-group">
                                <label>Início Operação - Data</label>
                                <input type="date" name="inicioOperacaoData" class="form-control" value="<?php  if($JanelaEdit->getInicioOperacao() != null) echo date('Y-m-d',strtotime($JanelaEdit->getInicioOperacao())) ?>" >
                            </div>
                            <div class="form-group">
                                <label>Início Operação - Horário</label>
                                <input type="time" name="inicioOperacaoHora" class="form-control" value="<?php if($JanelaEdit->getInicioOperacao() != null) echo date('H:i',strtotime($JanelaEdit->getInicioOperacao())) ?>" >
                            </div>
                            <div class="form-group">
                                <label>Doca</label>
                                <input name="doca" class="form-control" value="<?php if($JanelaEdit->getDoca() != null) echo $JanelaEdit->getDoca() ?>" >
                            </div>
                            <div class="form-group">
                                <label>Fim Operação - Data</label>
                                <input type="date" name="fimOperacaoData" class="form-control" value="<?php  if($JanelaEdit->getFimOperacao() != null) echo date('Y-m-d',strtotime($JanelaEdit->getFimOperacao())) ?>" >
                            </div>
                            <div class="form-group">
                                <label>Fim Operação - Horário</label>
                                <input type="time" name="fimOperacaoHora" class="form-control" value="<?php if($JanelaEdit->getFimOperacao() != null) echo date('H:i',strtotime($JanelaEdit->getFimOperacao())) ?>" >
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





       