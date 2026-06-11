<?php

class SystemsRepository{

    private $mySql;

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findAll(){

        try{
            $sql = "SELECT id AS systemId, name, description, systemUrl, iconPath
                    FROM systems";  
                    
            $result = $this->mySql->query($sql);

            return $result;

        }catch(Exception $e){
            return false;
        }
    }

    public function findByUser($userId){

        try{
            $sql = "SELECT systems.id AS systemId, name, description, systemUrl, iconPath
                    FROM systems 
                    INNER JOIN userSystems ON systems.id = userSystems.systemsId
                    WHERE userSystems.userId = " . $userId;  
                    
            $result = $this->mySql->query($sql);

            return $result;

        }catch(Exception $e){
            return false;
        }
    }
}
?>