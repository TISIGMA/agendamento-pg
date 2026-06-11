<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Labsoft\Models\Customer;

class CustomerRepository{

    private $mySql;

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findByName($customerName){

        try{
            if ($this->canUseEloquent()) {
                return Customer::query()
                    ->select(['id', 'name', 'description'])
                    ->where('name', $customerName)
                    ->get()
                    ->toArray();
            }

            $sql = "SELECT id, name, description
                    FROM customer 
                    WHERE name = '" .$customerName. "'";
                    
            $result = $this->mySql->query($sql);

            return $result;

        }catch(Exception $e){
            return false;
        }
    }

    private function canUseEloquent(){
        return class_exists(Customer::class) && class_exists(Capsule::class);
    }
}

?>
