
<?php
require('class.php');
require('../../conn.php');
require('../functions.php');

date_default_timezone_set("America/Sao_Paulo");
$data = date("Y-m-d");

if(isset($_GET['save'])){
    echo messageSuccess('Agendamento Salvo com sucesso!');
}

if(isset($_POST['date'])&& $_POST['date'] != null){
    $data = $_POST['date']; 
}
 //variaveis armazem 1
$livres1 = listarJanelasCount($MySQLi, $data, "Livre","1");
if(!listarJanelasCount($MySQLi, $data, "Livre", "1")) 
    $livres1 = "0";
$ocupadas1 = listarJanelasOcupadas($MySQLi, $data, "1");
if(!listarJanelasOcupadas($MySQLi, $data, "1")) $ocupadas1 = "0";

$horario = new Horario();
$array = array();

$bloqueadas1 = (count(buscarHorarios($horario, $MySQLi))/2) - $ocupadas1 - $livres1;


//variaveis armazem 2
$livres2 = listarJanelasCount($MySQLi, $data, "Livre", "2");
if(!listarJanelasCount($MySQLi, $data, "Livre", "2")) 
    $livres2 = "0";
$ocupadas2 = listarJanelasOcupadas($MySQLi, $data, "2");
if(!listarJanelasOcupadas($MySQLi, $data, "2")) $ocupadas2 = "0";

$bloqueadas2 = (count(buscarHorarios($horario, $MySQLi))/2) - $ocupadas2 - $livres2;

$tipoUsuario = '';
if($_SESSION['tipo'] == 'user'){
   $tipoUsuario = 'style=display:none;"'; 
}
?>

            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Painel de Janelas</h1>
                    <form class="form-inline" method="post" action="index.php?conteudo=panel.php">
                        <div class="form-group mx-sm-3 mb-2">
                            <input name="date" type="date" class="form-control" value="<?php echo date('Y-m-d',strtotime($data));?>">
                        </div>
                        <button type="submit" class="btn btn-primary fa fa-search"></button>
                    </form><br>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <strong>Armazém 1</strong>
                        </div>
                        <div class="panel-body">
                            <div class="panel-group" id="accordion">
                                <div class="col-lg-3 col-md-6">
                                    <div class="panel panel-primary">
                                        <div class="panel-heading">
                                            <div class="row">
                                                <div class="col-xs-3">
                                                    <i class="fa fa-times fa-5x"></i>
                                                </div>
                                                <div  class="col-xs-9 text-right">
                                                    <div class="huge"><?=$ocupadas1 ?></div>
                                                    <div>Ocupadas</div>
                                                </div>
                                            </div>
                                        </div>
                                        <a <?=$tipoUsuario ?> href="index.php?conteudo=janelaOcupada.php&data=<?=$data ?>&armazem=1">
                                            <div class="panel-footer">
                                                <span class="pull-left">Detalhes</span>
                                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                <div class="clearfix"></div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="panel panel-green">
                                        <div class="panel-heading">
                                            <div class="row">
                                                <div class="col-xs-3">
                                                    <i class="fa fa-unlock fa-5x"></i>
                                                </div>
                                                <div class="col-xs-9 text-right">
                                                    <div class="huge"><?=$livres1 ?></div>
                                                    <div>Livres</div>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="index.php?conteudo=janela.php&data=<?=$data ?>&status=Livre&titulo=Livres&armazem=1">
                                            <div <?=$tipoUsuario ?> class="panel-footer">
                                                <span class="pull-left">Detalhes</span>
                                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                <div class="clearfix"></div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="panel panel-red">
                                        <div class="panel-heading">
                                            <div class="row">
                                                <div class="col-xs-3">
                                                    <i class="fa fa-lock fa-5x"></i>
                                                </div>
                                                <div class="col-xs-9 text-right">
                                                    <div class="huge"><?=$bloqueadas1 ?></div>
                                                    <div>Bloqueadas</div>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="index.php?conteudo=janela.php&data=<?=$data ?>&status=Bloqueado&titulo=Bloqueados&armazem=1">
                                            <div <?=$tipoUsuario ?> class="panel-footer">
                                                <span class="pull-left">Detalhes</span>
                                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                <div class="clearfix"></div>
                                            </div>
                                        </a>
                                    </div>
                                </div>       
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <strong>Armazém 2</strong>
                        </div>
                        <div class="panel-body">
                            <div class="panel-group" id="accordion">
                                <div class="col-lg-3 col-md-6">
                                    <div class="panel panel-primary">
                                        <div class="panel-heading">
                                            <div class="row">
                                                <div class="col-xs-3">
                                                    <i class="fa fa-times fa-5x"></i>
                                                </div>
                                                <div  class="col-xs-9 text-right">
                                                    <div class="huge"><?=$ocupadas2 ?></div>
                                                    <div>Ocupadas</div>
                                                </div>
                                            </div>
                                        </div>
                                        <a <?=$tipoUsuario ?> href="index.php?conteudo=janelaOcupada.php&data=<?=$data ?>&armazem=2">
                                            <div class="panel-footer">
                                                <span class="pull-left">Detalhes</span>
                                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                <div class="clearfix"></div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="panel panel-green">
                                        <div class="panel-heading">
                                            <div class="row">
                                                <div class="col-xs-3">
                                                    <i class="fa fa-unlock fa-5x"></i>
                                                </div>
                                                <div class="col-xs-9 text-right">
                                                    <div class="huge"><?=$livres2 ?></div>
                                                    <div>Livres</div>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="index.php?conteudo=janela.php&data=<?=$data ?>&status=Livre&titulo=Livres&armazem=2">
                                            <div <?=$tipoUsuario ?> class="panel-footer">
                                                <span class="pull-left">Detalhes</span>
                                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                <div class="clearfix"></div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="panel panel-red">
                                        <div class="panel-heading">
                                            <div class="row">
                                                <div class="col-xs-3">
                                                    <i class="fa fa-lock fa-5x"></i>
                                                </div>
                                                <div class="col-xs-9 text-right">
                                                    <div class="huge"><?=$bloqueadas2 ?></div>
                                                    <div>Bloqueadas</div>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="index.php?conteudo=janela.php&data=<?=$data ?>&status=Bloqueado&titulo=Bloqueados&armazem=2">
                                            <div <?=$tipoUsuario ?> class="panel-footer">
                                                <span class="pull-left">Detalhes</span>
                                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                <div class="clearfix"></div>
                                            </div>
                                        </a>
                                    </div>
                                </div>       
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
            

      