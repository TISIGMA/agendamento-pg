<?php

require('conn.php');
require('session.php');

// to manutention
// session_unset();     
// session_destroy();  
// header('Location:../index.php');

if($_SESSION['nome'] == null) header('LOCATION:index.php');

$conteudo = "systems.php";

if(isset($_POST['conteudo'])) $conteudo = $_POST['conteudo'];   
	
if(isset($_GET['conteudo'])) {
    $conteudo = $_GET['conteudo'];
    if($conteudo == 'logout') {
        session_destroy();
        header('Location:index.php');
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
    <link rel="shortcut icon" href="assets/ico/sigma.ico">
    <title>SIGMA Solutions</title>
    
    <!-- DataTables Responsive CSS -->
    <link href="vendor/morrisjs/morris.css" rel="stylesheet">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
    <link href="vendor/datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">
    <link href="vendor/datatables-responsive/dataTables.responsive.css" rel="stylesheet">
    <link href="dist/css/sb-admin-2.css" rel="stylesheet">
    <link href="custom-style.css" rel="stylesheet">
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <script src="vendor/raphael/raphael.min.js"></script>
    <script src="vendor/morrisjs/morris.min.js"></script>
    <script src="data/morris-data.js"></script>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="vendor/metisMenu/metisMenu.min.js"></script>
    <script src="vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
    <script src="vendor/datatables-responsive/dataTables.responsive.js"></script>
    <script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js"></script>
    <script src="../jQuery-Mask-Plugin-master/"></script>
    <script src="dist/js/sb-admin-2.js"></script>
    <script src="utils.js"></script>
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
                <a class="navbar-brand" href="index.php">SIGMA Solutions</a>
            </div>
            <!-- /.navbar-header -->
            
            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <span><?=$_SESSION['nome'] ?></span>
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li>
                            <a href="home.php?conteudo=trocar-senha.php">
                                <i class="fa fa-pencil fa-fw"></i> Alterar senha
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="home.php?conteudo=logout"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                        <?php
                        if($_SESSION['tipo'] == 'adm'){
                        echo '<a href="#"><i class="fa fa-user"></i> Usuários </a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="home.php?conteudo=new-user.php">Cadastrar Usuário</a>
                                </li>
                                <li>
                                    <a href="home.php?conteudo=listarUsuarios.php">Listar Usuários</a>
                                </li>
                            </ul>
                        </li>';
                        }?>

                        <li>
                            <a href="home.php?conteudo=searchSystemError.php">Reportar um problema</a>
                        </li>
                    </ul>
                </div>
            </div>
            
        </nav>
        <div id="page-wrapper">
            <?php
                include($conteudo);
            ?>
        </div>
        <a class="dev-fixed-bottom" href="http://labsoft.tech/" target="_blank" ><p class="text-muted">&nbsp Desenvolvido por <span class="text-primary" style="font-size:1.2em"><b>LAB</b>soft</span></p></a>
    </div>
    
    <script>
        jQuery(function($){
            $('.telefone').mask('(00)0000-0000');
            //$('.placa').mask('AAA-0000');
            $('.celular').mask('(00)00000-0000');
            $('.cnpj').mask('00.000.000/0000-00');
            $("#data_final").mask("99/99/9999");
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#dataTables-example').DataTable({
                responsive: true
            });
    });
        
        $(".alert").fadeTo(1000, 10).slideUp(500, function(){
            $(this).remove(); 
        });
        

    </script>

    </body>
</html>
