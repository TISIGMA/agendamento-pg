<?php
require('class.php');
require('../functions.php');
require('../../session.php');
require('../../conn.php');

if(isset($_POST['horario']) && $_POST['horario'] != null){
    date_default_timezone_set("America/Sao_Paulo");
    $dataInclusao = date("Y-m-d");

    $janela = new Janela();
    $janela->setIdhorario($_POST['horario']);
    $janela->setData($_POST['data']);
    $janela->setTransportadora($_POST['transportadora']);
    $janela->setOferta($_POST['oferta']);
    $janela->setTipoVeiculo($_POST['tipoVeiculo']);
    $janela->setPlacaCavalo($_POST['placaCavalo']);
    $janela->setPlacaCarreta($_POST['placaCarreta']);
    $janela->setOperacao($_POST['op']);
    $janela->setNf($_POST['nf']);
    $janela->setDataInclusao($dataInclusao);
    $janela->setNomeusuario($$_SESSION['nome']);

    $janela->salvarJanela($MySQLi);
    if($janela->salvarJanela($MySQLi)==true){
        echo messageSuccess('Agendamento Salvo com sucesso!');
    }else{
        messageErro('Erro ao salvar agendamento!<br>Tente mais tarde!');
    }
    
}
?>