<?php

class SystemError{
    private $id;
    private $userId;
    private $userName;
    private $email;
    private $createdDate;
    private $fileName;
    private $description;
    private $status;
    private $resolution;

    public function setId($id){
        $this->id = $id;
    }
    public function getId(){
        return $this->id;
    }
    public function setUserId($userId){
        $this->userId = $userId;
    }
    public function getUserId(){
        return $this->userId;
    }
    public function setUserName($userName){
        $this->userName = $userName;
    }
    public function getuserName(){
        return $this->userName;
    }
    public function setEmail($email){
        $this->email = $email;
    }
    public function getEmail(){
        return $this->email;
    }
    public function setCreatedDate($createdDate){
        $this->createdDate = $createdDate;
    }
    public function getCreatedDate(){
        return $this->createdDate;
    }
    public function setFileName($fileName){
        $this->fileName = $fileName;
    }
    public function getFileName(){
        return $this->fileName;
    }
    public function setDescription($description){
        $this->description = $description;
    }
    public function getDescription(){
        return $this->description;
    }
    public function setStatus($status){
        $this->status = $status;
    }
    public function getStatus(){
        return $this->status;
    }
    public function setResolution($resolution){
        $this->resolution = $resolution;
    }
    public function getResolution(){
        return $this->resolution;
    }
}

?>