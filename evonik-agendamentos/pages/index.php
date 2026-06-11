<?php

require('../../conn.php');
require('../../session.php');

if($_SESSION['nome'] == null){
	header('LOCATION:../../index.php');
}

$tipoUsuario = 3;
if($_SESSION['tipo'] == 'user'){
   $tipoUsuario = 1; 
}else if($_SESSION['tipo'] == 'user' || $_SESSION['tipo'] == 'gerenciador'){
    $tipoUsuario = 2;
}

$conteudo = "panel.php";

if(isset($_POST['conteudo'])) $conteudo = $_POST['conteudo'];   
	
if(isset($_GET['conteudo'])) {
    $conteudo = $_GET['conteudo'];
    if($conteudo == 'logout') {
        session_destroy();
        header('Location:../../index.php');
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
    <link rel="shortcut icon" href="../../assets/ico/sigma.ico">
    <title>SIGMA Agendamentos</title>
    <link href="../../vendor/datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">

<!-- DataTables Responsive CSS -->
    <link href="../../vendor/datatables-responsive/dataTables.responsive.css" rel="stylesheet">
    <link href="../../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
    <link href="../../dist/css/sb-admin-2.css" rel="stylesheet">
    <link href="../../vendor/morrisjs/morris.css" rel="stylesheet">
    <link href="../../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="../../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">


    <link href="../../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">


    <link href="../../vendor/datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">

    <link href="../../vendor/datatables-responsive/dataTables.responsive.css" rel="stylesheet">


    <link href="../../dist/css/sb-admin-2.css" rel="stylesheet">

    <link href="../../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
</head>

<body>

    <div id="wrapper">
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="../../home.php">SIGMA Solutions</a>
            </div>
            <!-- /.navbar-header -->
            
            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <span><?=$_SESSION['nome'] ?></span>
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="../../home.php?conteudo=trocar-senha.php"><i class="fa fa-pencil fa-fw"></i> Alterar senha</a></li>
                        <li class="divider"></li>
                        <li><a href="index.php?conteudo=logout"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <a href="index.php"><i class="fa fa-dashboard fa-fw"></i> Painel</a>
                        </li>
                        <?php
                        if($tipoUsuario != 1 && $tipoUsuario != 2){
                            echo '
                            <li>
                                <a href="#"><i class="fa fa-list-alt"></i> LOG sistema  <span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="index.php?conteudo=log_agendamento.php">Agendamento</a>
                                    </li>
                                </ul>
                            </li>';
                        }
                        if($tipoUsuario != 1){
                            echo '
                            <li>
                                <a href="index.php?conteudo=configurar.php"><i class="fa fa-cog"></i> Configurar horários</a>
                            </li>';
                        }
                        if($tipoUsuario != 1){
                        //bloquenado funcionalidades novas - basta inserir dentro do echo
                       
                            echo '
                            <li>
                                <a href="index.php?conteudo=relatorio.php"><i class="fa fa-list-ul"></i> Relatório</a>
                            </li>
                            
                            <li>
                                <a href="#"><i class="fa fa-truck"></i> Transportadoras  <span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="index.php?conteudo=novaTransportadora.php">Cadastrar Transportadora</a>
                                    </li>
                                    <li>
                                        <a href="index.php?conteudo=listarTransportadoras.php">Listar Transportadoras</a>
                                    </li>
                                </ul>
                            </li>';
                        }
                        if($tipoUsuario == 1){
                            echo '
                            <li>
                                <a href="index.php?conteudo=meus-agendamentos.php"><i class="fa fa-book"></i> Meus agendamentos</a>
                            </li>';
                        }
                        
                             ?>
                        
                    </ul>
                    
                </div>
            </div>
            
        </nav>
        <div id="page-wrapper">
            <?php
                include($conteudo);
            ?>
        </div>
        <a style="text-decoration:none;" href="http://labsoft.tech/" target="_blank" ><p style="text-align:center; width:100%;" class="text-muted">&nbsp Desenvolvido por <span class="text-primary" style="font-size:1.2em"><b>LAB</b>soft</span></p></a>
        
    </div>
    <script src="../../vendor/jquery/jquery.min.js"></script>
    <script src="../../vendor/raphael/raphael.min.js"></script>
    <script src="../../vendor/morrisjs/morris.min.js"></script>
    <script src="../../data/morris-data.js"></script>
    <script src="../../vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="../../vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
    <script src="../../vendor/datatables-responsive/dataTables.responsive.js"></script>
    <script src="../../vendor/jquery/jquery.min.js"></script>

    
    <script src="../../vendor/bootstrap/js/bootstrap.min.js"></script>


    <script src="../../vendor/metisMenu/metisMenu.min.js"></script>


    <script src="../../vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="../../vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
    <script src="../../vendor/datatables-responsive/dataTables.responsive.js"></script>

   <script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js"></script>
   <script>
        jQuery(function($){
            $('.telefone').mask('(00)0000-0000');
            //$('.placa').mask('AAA-0000');
            $('.celular').mask('(00)00000-0000');
            $('.cnpj').mask('00.000.000/0000-00');
            $("#data_final").mask("99/99/9999");
        });
    </script>
    <script src="../../jQuery-Mask-Plugin-master/"></script>

   
    <script src="../../dist/js/sb-admin-2.js"></script>

    <script>
        $(document).ready(function() {
            $('#dataTables-example').DataTable({
                responsive: true
            });
    });
        window.setTimeout(function() {
            $(".alert").fadeTo(50000, 10).slideUp(50000, function(){
                $(this).remove(); 
            });
        }, 4000);
    </script>

    </body>

</html>
