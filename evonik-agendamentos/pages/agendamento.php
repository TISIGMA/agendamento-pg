<?php
require('class.php');
require('../functions.php');
include_once '../../session.php';

if(isset($_POST['horario']) && $_POST['horario'] != null){
    date_default_timezone_set("America/Sao_Paulo");
    $dataInclusao = date("Y-m-d");
    $horario = new Horario();
    $posicao = buscarHorario($_POST['horario'], $MySQLi);
    $janela = new Janela();
    $janela->setIdhorario($_POST['horario']);
    $janela->setData($_POST['data']);
    $janela->setTransportadora($_POST['transportadora']);
    $janela->setOferta($_POST['oferta']);
    $janela->setTipoVeiculo($_POST['tipoVeiculo']);
    $janela->setPlacaCavalo($_POST['placaCavalo']);
    $janela->setPlacaCarreta($_POST['placaCarreta']);
    $janela->setStatus('Ocupado');
    $janela->setPosicao($posicao->getPosicao());
    $janela->setOperacao($_POST['op']);
    $janela->setNf($_POST['nf']);
    $janela->setDataInclusao($dataInclusao);
    $janela->setNomeusuario($_SESSION['nome']);
    $janela->setHoraChegada(null);
    $janela->setInicioOperacao(null);
    $janela->setFimOperacao(null);
    $janela->setArmazem($_POST['armazem']);
    $janela->setPeso($_POST['peso']);
    $janela->setDestino($_POST['destino']);
    //$janela->deletarAgendamentoAnt($MySQLi);
    if($janela->salvarJanela($MySQLi)==true ){  
        $idJanela = new Janela();
        $id = ultimoIdJanela($MySQLi);
        ?>
    
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
    <script>
    //bloqueio das novas funcionalidades - inserido o location 
        jQuery(function($){
            $('#MyModal').modal('show');
        });
        
         //window.location='index.php?conteudo=meus-agendamentos.php&save';
    </script>

    <?php
    }else{
        echo "<script>window.location='index.php?conteudo=meus-agendamentos.php&erro=erroSalvar'</script>";	
    }
}

date_default_timezone_set("America/Sao_Paulo");
$hora = date('H:i:s');
$dataAtual = date("Y-m-d");

$tipoVeiculo = buscarTipoVeiculo($MySQLi);
$transportadora = new Transportadora();
$horario = new Horario();
$data = $_POST['data'];
$armazem = $_POST['armazem'];
if(isset($_POST['data'])&& $_POST['data'] != null){
    
    if($_SESSION['tipo'] == "user"){
        if($hora > date('H:i:s', strtotime('19:20:00')) && $dataAtual == $data){
            echo "<script>window.location='index.php?conteudo=meus-agendamentos.php&erro=noHour'</script>";	  
        }
    }   
}


if($_SESSION["tipo"] == "user"){
    $arraytransportadora = buscarTransportadoraPorNome($transportadora, $MySQLi, $_SESSION['nome']);
    $arrayHorarios = array();
    $arrayHorarios = listarJanelasPorData($MySQLi, $data, "Livre", $armazem);
    if(!$arrayHorarios){
        echo "<script>window.location='index.php?conteudo=meus-agendamentos.php&erro=noHour'</script>";	
    }
}else{
    $arraytransportadora = buscarTransportadora($transportadora, $MySQLi);
    $arrayHorarios = array();
    $arrayHorarios =listarJanelasPorData($MySQLi, $data, "Livre", $armazem);
} 
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Agendamento</h1>
    </div>                
</div>
<div class="row">
    <div class="col-lg-3">
        <form>
            <div class="form-group">
                <label>Data</label>
                <p><?= date('d/m/Y', strtotime($data))?></p>
                <label>Armazém</label>
                <p><?= $_POST['armazem']?></p>
            </div>
        </form>
    </div>
    <div class="col-lg-12">
         <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <form role="form-new-user" method="post" action="#" name="valida">
                            <input type="hidden" name="id">
                            <div class="form-group">
                                <input name="armazem" value="<?=$_POST['armazem'] ?>" type="hidden">
                                <input name="data" value="<?=$data ?>" type="hidden">
                                <label>Horário</label>
                                <select name="horario" class="form-control">
                                <?php
                                    foreach($arrayHorarios as $horario){
                                        echo "<option value='".$horario->getId()."'>".$hoje." ".$horario->getHorario()."</option>";   
                                    }
                                ?>   
                                </select>
                            </div>
                            <div class="form-group">
                            <!-- alterações bloqueadas -->
                                <label>Oferta</label>
                                <input class="form-control" type="number" id="oferta" placeholder="oferta" name="oferta" maxlength="14">
                            </div>
                            <div class="form-group">
                                <label>Transportadora</label>
                                <select name="transportadora" class="form-control">
                                <?php
                                    if($_SESSION['tipo'] == 'user'){
                                        echo "<option value='".$arraytransportadora->getNome()."'>".$arraytransportadora->getNome()."</option>";
                                    }else{
                                        foreach($arraytransportadora as $transportadora){   
                                            if($_SESSION['tipo'] == 'user'){
                                                if($_SESSION['nome'] == $transportadora->getNome()){ 
                                                    echo "<option value='".$transportadora->getNome()."' selected>".$transportadora->getNome()."</option>";
                                                }
                                                else{
                                                    echo "<option  value='".$transportadora->getNome()."'>".$transportadora->getNome()."</option>";
                                                }
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
                                        echo "<option value='".$dados['descricao']."'>".$dados['descricao']."</option>";
                                    }
                                
                                ?>   
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Placa do cavalo</label>
                                <input style="text-transform: uppercase" class="form-control placa" type="text" id="placa" placeholder="placa do cavalo" name="placaCavalo" maxlength="8" minlength="8" required>
                            </div>
                            <div class="form-group">
                                <label>Placa do carreta</label>
                                <input style="text-transform: uppercase" class="form-control placa" type="text"  id="placa" placeholder="placa da carreta" name="placaCarreta" maxlength="8" minlength="7">
                            </div>
                            <div class="form-group">
                                <label>Operação</label>
                                <select name="op" class="form-control">
                                    <option value="carga">Carga</option>
                                    <option value="descarga">Descarga</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Nota Fiscal</label>
                                <input class="form-control" name="nf" type="text" placeholder="Nota fiscal" name="notaFiscal" >
                            </div>
                            <div class="form-group">
                                <label>Peso</label>
                                <input class="form-control" name="peso" type="number" placeholder="Peso"  maxlength="6">
                            </div>
                            <div class="form-group">
                                <label>Destino</label>
                                <input class="form-control" name="destino" type="text" placeholder="Destino" >
                            </div>
                           
                            <button type="submit" class="btn btn-primary">Salvar</button>
                            <button type="reset" class="btn btn-danger">Cancelar</button>
                        </form>
                       

                       <!-- modal de confirmação  -->
                        <div class="modal fade" id="MyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                <form method="post" action="print.php">
                                    <div class="modal-header">
                                        <input type="hidden" value="meus-agendamentos.php" name="conteudo">
                                        <input type="hidden" value="true" name="save">
                                        <input type="hidden" value=<?=$id ?> name="id">
                                        <h5 class="modal-title" id="exampleModalLabel">Atenção</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Deseja imprimir o agendamento?
                                    </div>
                                    
                                    <div class="modal-footer">
                                        <button type="button" onclick="salvaAgendamento('<?php echo $_SESSION['tipo'] ?> ')" class="btn btn-secondary" data-dismiss="modal">Não</button>
                                        <button type="submit"  class="btn btn-primary">Sim</button>
                                    </div>
                                    </div>
                                </form>
                            </div>
                        </div>

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

    function salvaAgendamento(tipoUsuario){
        
        if(tipoUsuario === 'user'){
            window.location='index.php?conteudo=meus-agendamentos.php&save';
        }else{
            window.location='index.php';    
        }   
    }

</script>





       