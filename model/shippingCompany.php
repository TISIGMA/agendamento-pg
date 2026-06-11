<?php

class ShippingCompany{

    private $id;
    private $nome;
    private $username;
    private $CNPJ;
    private $email;
    private $telefone;
    private $celular;   
    private $password;
    private $data;
    private $usuario;
    private $clienteOrigem;

    public function setId($id){
        $this->id = $id;
    }
    public function getId(){
        return $this->id;
    }
    public function setNome($nome){
        $this->nome = $nome;
    }
    public function getNome(){
        return $this->nome;
    }
    public function setUsername($username){
        $this->username = $username;
    }
    public function getUsername(){
        return $this->username;
    }
    public function setCNPJ($cnpj){
        $this->CNPJ = $cnpj;
    }
    public function getCNPJ(){
        return $this->CNPJ;
    }
    public function setEmail($email){
        $this->email = $email;
    }
    public function getEmail(){
        return $this->email;
    }
    public function setTelefone($telefone){
        $this->telefone = $telefone;
    }
    public function getTelefone(){
        return $this->telefone;
    }
    public function setCelular($celular){
        $this->celular = $celular;
    }
    public function getCelular(){
        return $this->celular;
    }
    public function setPassword($password){
        $this->password = $password;
    }
    public function getPassword(){
        return $this->password;
    }
    public function setData($data){
        $this->data = $data;
    }
    public function getData(){
        return $this->data;
    }
    public function setUsuario($usuario){
        $this->usuario = $usuario;
    }
    public function getUsuario(){
        return $this->usuario;
    }
    public function setClienteOrigem($clienteOrigem){
        $this->clienteOrigem = $clienteOrigem;
    }
    public function getClienteOrigem(){
        return $this->clienteOrigem;
    }
}

?>