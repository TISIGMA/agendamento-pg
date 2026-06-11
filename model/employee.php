
<?php

class Employee{

    private $id;
    private $name;
    private $position;
    private $created_date;
    private $created_by;
    private $last_modified_date;
    private $last_modified_by;

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

    public function setPosition($position){
        $this->position = $position;
    }
    public function getPosition(){
        return $this->position;
    }

    public function setCreatedDate($createdDate){
        $this->createdDate =  date("d/m/Y H:i:s", strtotime($createdDate));
    }
    public function getCreatedDate(){
        return $this->createdDate;
    }

    public function setCreatedBy($createdBy){
        $this->createdBy = $createdBy;
    }
    public function getCreatedBy(){
        return $this->createdBy;
    }

    public function setLastModifiedDate($lastModifiedDate){
        if($lastModifiedDate == null) {
            $this->lastModifiedDate = '';
            return;
        } 
        $this->lastModifiedDate = date("d/m/Y H:i:s", strtotime($lastModifiedDate));
    }
    public function getLastModifiedDate(){
        return $this->lastModifiedDate;
    }

    public function setLastModifiedBy($lastModifiedBy){
        $this->lastModifiedBy = $lastModifiedBy;
    }
    public function getLastModifiedBy(){
        return $this->lastModifiedBy;
    }
}
?>