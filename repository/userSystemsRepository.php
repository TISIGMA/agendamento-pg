<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Labsoft\Models\UserSystem;

class UserSystemsRepository{

    private $mySql;

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findByUser($userId){

        try{
            if ($this->canUseEloquent()) {
                return UserSystem::query()
                    ->where('userId', (int) $userId)
                    ->get()
                    ->toArray();
            }

            $sql = "SELECT id, userId, systemsId FROM userSystems 
                    WHERE userId = " . $userId;  
                    
            return $this->mySql->query($sql);

        }catch(Exception $e){
            return false;
        }
    }


    public function deleteByUser($userId){

        try{
            if ($this->canUseEloquent()) {
                UserSystem::query()
                    ->where('userId', (int) $userId)
                    ->delete();

                return true;
            }

            $sql = "DELETE FROM userSystems 
                    WHERE userId = " . $userId;  
                    
            $result = $this->mySql->query($sql);

            return $result;

        }catch(Exception $e){
            return false;
        }
    }

    private function canUseEloquent(){
        return class_exists(UserSystem::class) && class_exists(Capsule::class);
    }
}

?>
