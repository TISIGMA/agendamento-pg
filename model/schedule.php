<?php

class Schedule{
    private $id;
    private $data;
    private $transportadora;
    private $status;
    private $tipoVeiculo;
    private $placaCavalo;
    private $operacao;
    private $nf;
    private $horaChegada;
    private $inicioOperacao;
    private $fimOperacao;
    private $nomeUsuario;
    private $dataInclusao;
    private $peso;
    private $data_agendamento;
    private $saida;
    private $separacao;
    private $shipment_id;
    private $do_s;
    private $cidade;
    private $carga_qtde;
    private $observacao;
    private $dadosGerais;
    private $cliente;
    private $usuarioAlteracao;
    private $dataAlteracao;
    private $doca;
    private $nomeMotorista;
    private $placaCarreta2;
    private $documentoMotorista;
    private $placaCarreta;
    private $filesPath;
    private $operationId;
    private $operator;
    private $checker;
    private $createdDate;
    private $lastModifiedBy;
    private $lastModifiedDate;
    private $attPickingStatus;
    private $attOtherStatus;


    public function setId($id){
        $this->id = $id;
    }
    public function getId(){
        return $this->id;
    }
    public function setData($data){
        $this->data = $data;
    }
    public function getData(){
        return $this->data;
    }
    public function setTransportadora($transportadora){
        $this->transportadora = $transportadora;
    }
    public function getTransportadora(){
        return $this->transportadora;
    }
    public function setStatus($status){
        $this->status = $status;
    }
    public function getStatus(){
        return $this->status;
    }
    public function setTipoVeiculo($tipoVeiculo){
        $this->tipoVeiculo = $tipoVeiculo;
    }
    public function getTipoVeiculo(){
        return $this->tipoVeiculo;
    }
    public function setPlacaCavalo($placaCavalo){
        $this->placaCavalo = $placaCavalo;
    }
    public function getPlacaCavalo(){
        return $this->placaCavalo;
    }
    public function setOperacao($operacao){
        $this->operacao = $operacao;
    }
    public function getOperacao(){
        return $this->operacao;
    }
    public function setNf($nf){
        $this->nf = $nf;
    }
    public function getNf(){
        return $this->nf;
    }
    public function setHoraChegada($horaChegada){
        $this->horaChegada = $horaChegada;
    }
    public function getHoraChegada(){
        return $this->horaChegada;
    }
    public function setInicioOperacao($inicioOperacao){
        $this->inicioOperacao = $inicioOperacao;
    }
    public function getInicioOperacao(){
        return $this->inicioOperacao;
    }
    public function setFimOperacao($fimOperacao){
        $this->fimOperacao = $fimOperacao;
    }
    public function getFimOperacao(){
        return $this->fimOperacao;
    }
    public function setNomeUsuario($nomeUsuario){
        $this->nomeUsuario = $nomeUsuario;
    }
    public function getNomeUsuario(){
        return $this->nomeUsuario;
    }
    public function setDataInclusao($dataInclusao){
        $this->dataInclusao = $dataInclusao;
    }
    public function getDataInclusao(){
        return $this->dataInclusao;
    }
    public function setPeso($peso){
        $this->peso = $peso;
    }
    public function getPeso(){
        return $this->peso;
    }
    public function setDataAgendamento($data_agendamento){
        $this->data_agendamento = $data_agendamento;
    }
    public function getDataAgendamento(){
        return $this->data_agendamento;
    }
    public function setSaida($saida){
        $this->saida = $saida;
    }
    public function getSaida(){
        return $this->saida;
    }
    public function setSeparacao($separacao){
        $this->separacao = $separacao;
    }
    public function getSeparacao(){
        return $this->separacao;
    }
    public function setShipmentId($shipment_id){
        $this->shipment_id = $shipment_id;
    }
    public function getShipmentId(){
        return $this->shipment_id;
    }
    public function setDo_s($do_s){
        $this->do_s = $do_s;
    }
    public function getDo_s(){
        return $this->do_s;
    }
    public function setCidade($cidade){
        $this->cidade = $cidade;
    }
    public function getCidade(){
        return $this->cidade;
    }
    public function setCargaQtde($carga_qtde){
        $this->carga_qtde = $carga_qtde;
    }
    public function getCargaQtde(){
        return $this->carga_qtde;
    }
    public function setObservacao($observacao){
        $this->observacao = $observacao;
    }
    public function getObservacao(){
        return $this->observacao;
    }
    public function setDadosGerais($dadosGerais){
        $this->dadosGerais = $dadosGerais;
    }
    public function getDadosGerais(){
        return $this->dadosGerais;
    }  
    public function setCliente($cliente){
        $this->cliente = $cliente;
    }
    public function getCliente(){
        return $this->cliente;
    }
    public function setUsuarioAlteracao($usuarioAlteracao){
        $this->usuarioAlteracao = $usuarioAlteracao;
    }
    public function getUsuarioAlteracao(){
        return $this->usuarioAlteracao;
    }
    public function setDataAlteracao($dataAlteracao){
        $this->dataAlteracao = $dataAlteracao;
    }
    public function getDataAlteracao(){
        return $this->dataAlteracao;
    } 
    public function setDoca($doca){
        $this->doca = $doca;
    }
    public function getDoca(){
        return $this->doca;
    } 
    public function setNomeMotorista($nomeMotorista){
        $this->nomeMotorista = $nomeMotorista;
    }
    public function getNomeMotorista(){
        return $this->nomeMotorista;
    } 
    public function setPlacaCarreta2($placaCarreta2){
        $this->placaCarreta2 = $placaCarreta2;
    }
    public function getPlacaCarreta2(){
        return $this->placaCarreta2;
    } 
    public function setDocumentoMotorista($documentoMotorista){
        $this->documentoMotorista = $documentoMotorista;
    }
    public function getDocumentoMotorista(){
        return $this->documentoMotorista;
    } 
    public function setPlacaCarreta($placaCarreta){
        $this->placaCarreta = $placaCarreta;
    }
    public function getPlacaCarreta(){
        return $this->placaCarreta;
    }
    public function setFilesPath($filesPath){
        $this->filesPath = $filesPath;
    }
    public function getFilesPath(){
        return $this->filesPath;
    } 
    public function setOperationId($operationId){
        $this->operationId = $operationId;
    }
    public function getOperationId(){
        return $this->operationId;
    }

    public function setOperator($operator){
        $this->operator = $operator;
    }
    public function getOperator(){
        return $this->operator;
    }
    public function setChecker($checker){
        $this->checker = $checker;
    }
    public function getChecker(){
        return $this->checker;
    }
    public function setCreatedDate($createdDate){
        $this->createdDate = $createdDate;
    }
    public function getCreatedDate(){
        return $this->createdDate;
    }
    public function setLastModifiedBy($lastModifiedBy){
        $this->lastModifiedBy = $lastModifiedBy;
    }
    public function getLastModifiedBy(){
        return $this->lastModifiedBy;
    }
    public function setLastModifiedDate($lastModifiedDate){
        $this->lastModifiedDate = $lastModifiedDate;
    }
    public function getLastModifiedDate(){
        return $this->lastModifiedDate;
    }

    public function setAttPickingStatus($attPickingStatus){
        $this->attPickingStatus = $attPickingStatus;
    }
    public function getAttPickingStatus(){
        return $this->attPickingStatus;
    }

    public function setAttInvoiceStatus($attInvoiceStatus){
        $this->attInvoiceStatus = $attInvoiceStatus;
    }
    public function getAttInvoiceStatus(){
        return $this->attInvoiceStatus;
    }

    public function setAttCertificateStatus($attCertificateStatus){
        $this->attCertificateStatus = $attCertificateStatus;
    }
    public function getAttCertificateStatus(){
        return $this->attCertificateStatus;
    }

    public function setAttBoardingStatus($attBoardingStatus){
        $this->attBoardingStatus = $attBoardingStatus;
    }
    public function getAttBoardingStatus(){
        return $this->attBoardingStatus;
    }

    public function setAttOtherStatus($attOtherStatus){
        $this->attOtherStatus = $attOtherStatus;
    }
    public function getAttOtherStatus(){
        return $this->attOtherStatus;
    }
}

?>