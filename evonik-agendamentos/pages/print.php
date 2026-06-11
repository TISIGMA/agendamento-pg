<?php
require('../../conn.php');
require('../functions.php');
require('class.php');
include_once '../session.php'; 

if(isset($_POST["id"]) && $_POST["id"]!= null){
    $janela = new Janela();
    $janela = buscarJanelaPorId($MySQLi, $_POST['id']);
    $horario = new Horario();
    $horario = buscarHorario( $janela->getIdHorario(), $MySQLi);
}else{
    echo "<script>window.location='index.php?conteudo=meus-agendamentos.php&erro=erroPrint'</script>";
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

<body onload='self.print();self.close();'>
    <div class="fechar">
        <button type="button" class="btn btn-primary btn-lg btn-block" onclick="fechar('<?php echo $_SESSION['tipo'] ?> ')">
            <span class="glyphicon glyphicon-remove"></span>
            Fechar
        </button>
    </div>
    <div class="img-flex">
        <img class="img1" src="../../assets/img/logo.png" class="rounded float-left" alt="...">
        <h3 class="display-1"><strong>Confirmação de agendamento</strong></h3>
        <img class="img1" src="../../assets/img/evonik-logo.png" class="rounded float-right" alt="...">
    </div>
    <div>
        <div class="row-porsonalize">
            <div>
                <div class="label-tittle">
                    <h3><strong>Dados agendamento</strong></h3>
                </div>
                <div class="flex">
                    <div class="label-short">
                        <span><strong>ID agendamento</strong></span>
                    </div>
                    <div class="text-long">
                        <span><?php echo $janela->getID() ?></span>
                    </div> 
                </div>
                <div class="flex">
                    <div class="label-short">
                        <span><strong>Data</strong></span>
                    </div>
                    <div class="text-long">
                        <span><?php echo date("d/m/Y", strtotime($janela->getData())) ?></span>
                    </div> 
                </div>
                <div class="flex">
                    <div class="label-short">
                        <span><strong>Janela</strong></span>
                    </div>
                    <div class="text-long">
                        <span><?php echo $horario->getHorario() ?></span>
                    </div> 
                </div>
                <div class="flex">
                    <div class="label-short">
                        <span><strong>Oferta</strong></span>
                    </div>
                    <div class="text-long">
                        <span><?php echo $janela->getOferta() ?></span>
                    </div> 
                </div>
                <div class="flex">
                    <div class="label-short">
                        <span><strong>Transportadora</strong></span>
                    </div>
                    <div class="text-long">
                        <span><?php echo $janela->getTransportadora() ?></span>
                    </div> 
                </div>
                <div class="flex">
                    <div class="label-short">
                        <span><strong>Tipo de veículo</strong></span>
                    </div>
                    <div class="text-long">
                        <span><?php echo $janela->getTipoVeiculo() ?></span>
                    </div> 
                </div>
                <div class="flex">
                    <div class="label-short">
                        <span><strong>Placa da cavalo</strong></span>
                    </div>
                    <div class="text-long">
                        <span><?php echo $janela->getPlacaCavalo() ?></span>
                    </div> 
                </div>
                <div class="flex">
                    <div class="label-short">
                        <span><strong>Placa da carreta</strong></span>
                    </div>
                    <div class="text-long">
                        <span><?php echo $janela->getPlacaCarreta() ?></span>
                    </div> 
                </div>
                <div class="flex">
                    <div class="label-short">
                        <span><strong>Armazém</strong></span>
                    </div>
                    <div class="text-long">
                        <span><?php echo $janela->getArmazem() ?></span>
                    </div> 
                </div>
                <div class="flex">
                    <div class="label-short">
                        <span><strong>Operação</strong></span>
                    </div>
                    <div class="text-long">
                        <span><?php echo $janela->getOperacao() ?></span>
                    </div> 
                </div>
                <div class="flex">
                    <div class="label-short">
                        <span><strong>Nota fiscal</strong></span>
                    </div>
                    <div class="text-long">
                        <span><?php echo $janela->getNF() ?></span>
                    </div> 
                </div>
                <div class="flex">
                    <div class="label-short">
                        <span><strong>Peso</strong></span>
                    </div>
                    <div class="text-long">
                        <span><?php echo $janela->getPeso() ?></span>
                    </div> 
                </div>
            </div>    
        </div>
    </div>
    <footer class="text-center footer">
        <p><strong>SIGMA TRANSPORTES</strong></p>
        <span><small>Rodovia BR 376 KM 501 s/n Colônia Dna. Luiza, Ponta Grossa - PR, 84043-450</small></span><br>
        <span><small>Telefone: 3222-0365</small></span>
    </footer>
</body>

<style>
    .fechar{
        padding: 10px;
        text-align: center;
        align-items:center;
    }
    p, span{
        font-size: 1.3em;
        align-items: center;
    }
    .img1{
        height:50px;
    }
    .img2{
        height:65px;
    }
    .img-flex{
        padding: 20px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        justify-content: space-between;
    }
    .row-porsonalize{
        width: 98%;
        margin-left:1%;
        margin-bottom: 20px;
        border: solid 1px #dcdcdc;
        border-radius: 10px;
    }
    .flex{
        align-items: center;
        width: 95%;
        height: 40px; 
        margin-left: 2.5%;
        display: flex;
        margin-bottom: 10px;
        background-color: #dcdcdc;
        border: solid 1px #dcdcdc;
        border-radius: 10px;
    }
    .label-short{
        align-items: center;
        width:25%;
        margin-left:20px;
        float:left;
    }
    .text-long{
        width: 60%;
    }
    .label-tittle{
        margin-left:10px; 
        margin-bottom: 40px;
        width:80%;
    }
    footer {
        position: absolute;
        bottom:0;
        width:100%;
        background-color: silver; 
    }
    .footer{
        visibility: hidden;  
    }
    @media print {
        .footer{
            visibility: visible;
        }
        .fechar{
            visibility: hidden;
        }
    }
</style>
<script>
    function fechar(tipoUsuario){
        
        if(tipoUsuario === 'user'){
            window.location='index.php?conteudo=meus-agendamentos.php';
        }else{
            window.location='index.php'; 
        }
    }
</script>
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

