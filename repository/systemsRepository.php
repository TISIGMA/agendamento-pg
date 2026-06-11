<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Labsoft\Models\System;

class SystemsRepository{

    private $mySql;

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findAll(){

        try{
            if ($this->canUseEloquent()) {
                return System::query()
                    ->select([
                        'id AS systemId',
                        'name',
                        'description',
                        'systemUrl',
                        'iconPath',
                    ])
                    ->get()
                    ->toArray();
            }

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
            if ($this->canUseEloquent()) {
                return System::query()
                    ->select([
                        'systems.id AS systemId',
                        'systems.name',
                        'systems.description',
                        'systems.systemUrl',
                        'systems.iconPath',
                    ])
                    ->join('userSystems', 'systems.id', '=', 'userSystems.systemsId')
                    ->where('userSystems.userId', (int) $userId)
                    ->get()
                    ->toArray();
            }

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

    private function canUseEloquent(){
        return class_exists(System::class) && class_exists(Capsule::class);
    }
}
?>
