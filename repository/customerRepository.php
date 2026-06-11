<?php

class CustomerRepository{

    private $mySql;

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findByName($customerName){

        try{
            $sql = "SELECT id, name, description
                    FROM customer 
                    WHERE name = '" .$customerName. "'";
                    
            $result = $this->mySql->query($sql);

            return $result;

        }catch(Exception $e){
            return false;
        }
    }
}

?>