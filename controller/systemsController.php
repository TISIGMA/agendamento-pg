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
}

?>