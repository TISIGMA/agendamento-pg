<?php

require_once('../conn.php');
require_once('../session.php');
require_once('../controller/customerController.php');
require_once('../utils.php');

//controla versão de arquivos css e javascript para forçar o carregamento da versão atualizada
define('Version', '16');

if($_SESSION['nome'] == null){
	header('LOCATION:../index.php');
}
// to panutention  ***  só descomentar para tirar o sistema do ar ****
// session_unset();     
// session_destroy();  
//header('Location:../index.php');

$customerName = null;
$customerDescription = null;

if(isset($_GET['customer']) && $_GET['customer'] != null) {

    $customerController = new CustomerController($MySQLi);
    $customers = $customerController->findByName($_GET['customer']); 

    $_SESSION['customerName'] = $_GET['customer'];
    $_SESSION['customerDescription'] = $customers[0]->getDescription();
    $customerName = $customers[0]->getName();
}

// #region debug-point A:customer-session
$__dbg=@parse_ini_file(__DIR__ . '/../.dbg/schedule-empty-data.env'); @file_get_contents(($__dbg['DEBUG_SERVER_URL'] ?? 'http://127.0.0.1:7777/event'), false, stream_context_create(['http'=>['method'=>'POST','header'=>"Content-Type: application/json\r\n",'content'=>json_encode(['sessionId'=>($__dbg['DEBUG_SESSION_ID'] ?? 'schedule-empty-data'),'runId'=>'pre-fix','hypothesisId'=>'A','location'=>'schedules/index.php','msg'=>'[DEBUG] customer session state','data'=>['get_customer'=>(isset($_GET['customer']) ? $_GET['customer'] : null),'session_customer'=>(isset($_SESSION['customerName']) ? $_SESSION['customerName'] : null),'session_customer_description'=>(isset($_SESSION['customerDescription']) ? $_SESSION['customerDescription'] : null),'session_user_id'=>(isset($_SESSION['id']) ? $_SESSION['id'] : null)],'ts'=>round(microtime(true)*1000)])]]));
// #endregion

$tipoUsuario = 3;
if($_SESSION['tipo'] == 'user'){
   $tipoUsuario = 1; 
}else if($_SESSION['tipo'] == 'user' || $_SESSION['tipo'] == 'gerenciador'){
    $tipoUsuario = 2;
}

$conteudo = "panel.php";

//usado para informar o usuário para limpar o cache do navegador
if($_SESSION['message'] != null && date("d/m/Y") <= date("d/m/Y", strtotime($_SESSION['created_date'] . $_SESSION['duration'] . " days"))){
    
    if(!$_SESSION['message_readed']){
        warningNotification($_SESSION['message']);
        $_SESSION['message_readed'] = true;
    }
}

if(isset($_GET['action']) && $_GET['action'] != null){

    if($_GET['action'] == 'schedule-save') successAlert('Agendamento realizado com sucesso!');
    if($_GET['action'] == 'schedule-update') successAlert('Agendamento atualizado com sucesso!');
    if($_GET['action'] == 'schedule-delete') successAlert('Agendamento excluído com sucesso!');
}

if(isset($_POST['conteudo'])) $conteudo = $_POST['conteudo'];   
	
if(isset($_GET['conteudo'])) {
    $conteudo = $_GET['conteudo'];
    if($conteudo == 'logout') {
        session_unset();     
        session_destroy();  
        header('Location:../index.php');
    }
}   

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Sigma Solutions</title>
    
    <link rel="shortcut icon" href="../assets/ico/sigma.ico">

    <!-- CSS references -->
    <link href="../vendor/morrisjs/morris.css" rel="stylesheet">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
    <link href="../vendor/datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">
    <link href="../vendor/datatables-responsive/dataTables.responsive.css" rel="stylesheet">
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">
    <link href="../custom-style.css?<?php echo Version; ?>" rel="stylesheet">
    <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

    <!-- JS scripts (ordem correta) -->
    <!-- jQuery (primeiro) -->
    <script src="../vendor/jquery/jquery.min.js"></script>
    
    <!-- Moment.js e datetimepicker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/pt-br.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    
    <!-- Demais libs -->
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>
    <script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js"></script>
    <script src="../vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
    <script src="../vendor/datatables-responsive/dataTables.responsive.js"></script>
    <script src="../vendor/raphael/raphael.min.js"></script>
    <script src="../vendor/morrisjs/morris.min.js"></script>
    <!-- <script src="../data/morris-data.js"></script> --> <!-- comentado pois arquivo não existe -->
    <script src="../dist/js/sb-admin-2.js"></script> 
    <script src="../assets/js/jquery.backstretch.min.js"></script>
    <script src="../assets/js/scripts.js"></script>
    <script src="../utils.js?<?php echo Version; ?>"></script>
</head>

<body>
    <div id="wrapper" class="schedule-body">
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-brand">
                <img class="icon-menu-img" src="../images/menu-icon.png"  onmouseover="handleShowMenu()">
            </div>
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="../home.php">SIGMA Solutions</a>
            </div>

            <div class="navbar-brand customer-master-label" >
                <p><?=$_SESSION['customerDescription'] ?> Agendamentos</p>
            </div>

            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <span><?=$_SESSION['nome'] ?></span>
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="../home.php?conteudo=trocar-senha.php"><i class="fa fa-pencil fa-fw"></i> Alterar senha</a></li>
                        <li class="divider"></li>
                        <li><a href="index.php?conteudo=logout"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                </li>
            </ul>
           
            <ul <?=$_SESSION['FUNCTION_ACCESS']['schedule'] ?> class="nav navbar-top-links navbar-right" id="quick-access-nav">
                    
                <ul class="dropdown-menu dropdown-user">
                    <li <?=$_SESSION['FUNCTION_ACCESS']['schedule_new'] ?> >
                        <a href="index.php?conteudo=newSchedule.php&function=new">Novo</a>
                    </li>
                    <li <?=$_SESSION['FUNCTION_ACCESS']['schedule_list'] ?>>
                        <a href="index.php?conteudo=searchSchedule.php">Pesquisar</a>
                    </li>
                </ul>
                
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <span> Agendamentos</span>
                        <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li <?=$_SESSION['FUNCTION_ACCESS']['schedule_new'] ?> >
                            <a href="index.php?conteudo=newSchedule.php&function=new">Novo</a>
                        </li>
                        <li <?=$_SESSION['FUNCTION_ACCESS']['schedule_list'] ?>>
                            <a href="index.php?conteudo=searchSchedule.php">Pesquisar</a>
                        </li>
                    </ul>
                </li>
            </ul>
          
           
            <div class="navbar-default sidebar vertical-menu" id="menu-nav-bar" role="navigation" >
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu" >
                        <li class="dropdown">
                            <a href="index.php?conteudo=panel.php">Painel</a>
                        </i>
                        <li <?=$_SESSION['FUNCTION_ACCESS']['schedule'] ?>>
                            <a href="#"><i></i> Agendamentos </a>
                            <ul class="nav nav-second-level">
                                <li <?=$_SESSION['FUNCTION_ACCESS']['schedule_new'] ?> >
                                    <a href="index.php?conteudo=newSchedule.php&function=new">Novo</a>
                                </li>
                                <li <?=$_SESSION['FUNCTION_ACCESS']['schedule_list'] ?>>
                                    <a href="index.php?conteudo=searchSchedule.php">Pesquisar</a>
                                </li>
                            </ul>
                        </li>
                        <li <?=$_SESSION['FUNCTION_ACCESS']['register'] ?>>
                            <a href="#"><i></i> Cadastros</a>
                            <ul class="nav nav-second-level">
                                <li <?=$_SESSION['FUNCTION_ACCESS']['register_employee'] ?>>
                                    <a href="index.php?conteudo=newEmployee.php">Colaborador</a>
                                </li>
                                <li <?=$_SESSION['FUNCTION_ACCESS']['register_operation_type'] ?>>
                                    <a href="index.php?conteudo=newOperationType.php">Tipo de Operação</a>
                                </li>
                                 <li <?=$_SESSION['FUNCTION_ACCESS']['register_operation_type'] ?>>
                                    <a href="index.php?conteudo=newOperationSource.php">Natureza da Operação</a>
                                </li>
                                <li <?=$_SESSION['FUNCTION_ACCESS']['register_truck_type'] ?>>
                                    <a href="index.php?conteudo=newTruckType.php">Tipo de veículo</a>
                                </li>
                                <li <?=$_SESSION['FUNCTION_ACCESS']['register_shipping_company'] ?>>
                                    <a href="index.php?conteudo=newShippingCompany.php">Transportadoras</a>
                                </li>
                            </ul>
                        </li>
                       <li <?=$_SESSION['FUNCTION_ACCESS']['tracking'] ?>>
                            <a href="#"><i></i> Tracking de registros  </a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="index.php?conteudo=tracking/attTrackingView.php">Anexos</a>
                                </li>
                            </ul>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="index.php?conteudo=tracking/scheduleTrackingView.php">Agendamento</a>
                                </li>
                            </ul>
                        </li>
                        <li <?=$_SESSION['FUNCTION_ACCESS']['register_report'] ?>>
                            <a href="#"><i></i> Relatórios </a>
                            <ul class="nav nav-second-level">
                                <li <?=$_SESSION['FUNCTION_ACCESS']['register_report'] ?> >
                                    <a href="index.php?conteudo=report-panel.php&frequency=yearly">Dashboard anual</a>
                                </li>
                                <li <?=$_SESSION['FUNCTION_ACCESS']['register_report'] ?>>
                                    <a href="index.php?conteudo=report-panel.php&frequency=montly">Dashboard mensal</a>
                                </li>
                            </ul>
                        </li>
                       
                    </ul>
                </div>
            </div>
        </nav>

        <div id="page-wrapper" onmouseover="handleHideMenu()" >
            <?php
                include($conteudo);
            ?>
        </div>
        <a class="dev-fixed-bottom" href="https://labsoftsistemas.com.br/" target="_blank" ><p class="text-muted">&nbsp Desenvolvido por <span class="text-primary" style="font-size:1.2em"><b>LAB</b>soft</span></p></a>
        
    </div>

    <script>
        jQuery(function($){
            $('.telefone').mask('(00)0000-0000');
            //$('.placa').mask('AAA-0000');
            $('.celular').mask('(00)00000-0000');
            $('.cnpj').mask('00.000.000/0000-00');
            $("#data_final").mask("99/99/9999");
            $('.cpf').mask('000.000.000-00');
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#dataTables-example').DataTable({
                responsive: true
            });
        });

        $(".notification").fadeTo(15000, 10).slideUp(500, function(){
            $(this).remove(); 
        });

        $(".alert").fadeTo(5000, 10).slideUp(500, function(){
            $(this).remove(); 
        });

        $(function () {
            $('#datetimepicker1').datetimepicker();
        });

        $('#myModal').on('shown.bs.modal', function () {
            $('#myInput').trigger('focus')
        })
    </script>
    </body>

</html>
