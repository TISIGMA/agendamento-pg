<?php

class UserSystemsRepository{

    private $mySql;

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findByUser($userId){

        try{
            $sql = "SELECT id, userId, systemsId FROM userSystems 
                    WHERE userId = " . $userId;  
                    
            return $this->mySql->query($sql);

        }catch(Exception $e){
            return false;
        }
    }


    public function deleteByUser($userId){

        try{
            $sql = "DELETE FROM userSystems 
                    WHERE userId = " . $userId;  
                    
            $result = $this->mySql->query($sql);

            return $result;

        }catch(Exception $e){
            return false;
        }
    }
}

?>