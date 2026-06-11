<?php
class ColumnsPreference{

    private $id;
    private $preference;
    private $userId;

    public function setId($id){
        $this->id = $id;
    }
    public function getId(){
        return $this->id;
    }

    public function setPreference($preference){
        $this->preference = $preference;
    }
    public function getPreference(){
        return $this->preference;
    }

    public function setUserId($userId){
        $this->userId = $userId;
    }
    public function getUserId(){
        return $this->userId;
    }
}

?>