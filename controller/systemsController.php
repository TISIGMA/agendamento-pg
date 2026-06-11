<?php

require_once('repository/systemsRepository.php');
require_once('model/systemsClass.php');

class SystemsController{

    private $systems;
    private $systemsRepository;
    private $mySql;

    public function __construct($mySql){

        $this->mySql = $mySql;
        $this->systemsRepository = new SystemsRepository($this->mySql);
    }

    public function findAll(){

        $result = $this->systemsRepository->findAll();
        $data = $this->loadData($result);

        return $data;
    }

    public function findByUser($userId){

        $result = $this->systemsRepository->findByUser($userId);
        $data = $this->loadData($result);

        if(count($data) > 0) return $data;

        return null;
    }

    public function loadData($records){

        $systemsList = array();

        if (is_array($records) || $records instanceof Traversable) {
            foreach ($records as $data) {
                $systems = new Systems();
                $systems->setId($this->getRecordValue($data, 'systemId'));
                $systems->setName($this->getRecordValue($data, 'name'));
                $systems->setDescription($this->getRecordValue($data, 'description'));
                $systems->setSystemUrl($this->getRecordValue($data, 'systemUrl'));
                $systems->setIconPath($this->getRecordValue($data, 'iconPath'));
                
                array_push($systemsList, $systems);
            }

            return $systemsList;
        }

        while ($data = $records->fetch_assoc()){ 
            $systems = new Systems();
            $systems->setId($data['systemId']);
            $systems->setName($data['name']);
            $systems->setDescription($data['description']);
            $systems->setSystemUrl($data['systemUrl']);
            $systems->setIconPath($data['iconPath']);
            
            array_push($systemsList, $systems);
        }

        return $systemsList;
    }

    private function getRecordValue($record, $field){
        if (is_array($record) && array_key_exists($field, $record)) {
            return $record[$field];
        }

        if (is_object($record) && isset($record->$field)) {
            return $record->$field;
        }

        return null;
    }
}

?>
