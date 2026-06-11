<?php
class Systems{

    private $id;
    private $name;
    private $description;
    private $systemUrl;
    private $iconPath;

    public function setId($id){
        $this->id = $id;
    }
    public function getId(){
        return $this->id;
    }

    public function setName($name){
        $this->name = $name;
    }
    public function getName(){
        return $this->name;
    }

    public function setDescription($description){
        $this->description = $description;
    }
    public function getDescription(){
        return $this->description;
    }

    public function setSystemUrl($systemUrl){
        $this->systemUrl = $systemUrl;
    }
    public function getSystemUrl(){
        return $this->systemUrl;
    }

    public function setIconPath($iconPath){
        $this->iconPath = $iconPath;
    }
    public function getIconPath(){
        return $this->iconPath;
    }
}

?>