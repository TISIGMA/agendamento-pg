<?php

class ScheduleChart{
    private $id;
    private $operationSourceName;
    private $horaChegada;
    private $inicioOperacao;
    private $fimOperacao;
    private $saida;

    public function setId($id){
        $this->id = $id;
    }
    public function getId(){
        return $this->id;
    }
    public function setOperationSourceName($operationSourceName){
        $this->operationSourceName = $operationSourceName;
    }
    public function getOperationSourceName(){
        return $this->operationSourceName;
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
    public function setSaida($saida){
        $this->saida = $saida;
    }
    public function getSaida(){
        return $this->saida;
    }
}

?>