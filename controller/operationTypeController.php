<?php

require_once('../repository/operationTypeRepository.php');
require_once('../model/operationType.php');
require_once('../model/operationSource.php');

class OperationTypeController{

    private $operationType;
    private $operationTypeRepository;
    private $mySql;

    public function __construct($mySql){

        $this->mySql = $mySql;
        $this->operationTypeRepository = new OperationTypeRepository($this->mySql);
    }

    public function findByClient($client){

        $result = $this->operationTypeRepository->findByClient($client);
        $data = $this->loadData($result);

        return $data;
    }

    public function findAll(){

        $result = $this->operationTypeRepository->findAll();
        $data = $this->loadData($result);

        return $data;
    }

    public function save($post){

        try {
            $name = $post['name'];
            $operationSourceId = $post['operationSource'];

            $label = $this->buildLabel($name);

            $result = $this->operationTypeRepository->findByName($name);

            $count = $result->fetch_assoc() == null ? 0 : count((array)$result->fetch_assoc());

            if($count > 0) {
                return 'ALREADY_EXISTS';
            }

            return $this->operationTypeRepository->save($name, $label, $_SESSION['customerName'], $operationSourceId);
        
        } catch (Exception $e) {
            return 'SAVE_ERROR';
        }
    }

    public function updateById($id, $name, $operationSourceId){

        $label = $this->buildLabel($name);

        return $this->operationTypeRepository->updateById($id, $name, $label, $operationSourceId);
    }

    public function deleteById($id){

        try {

            return $this->operationTypeRepository->deleteById($id);
        
        } catch (Exception $e) {
            return 'DELETE_ERROR';
        }
    }

    public function loadData($records){
        $operationTypes = array();

        while ($data = $records->fetch_assoc()){ 
            $operationType = new OperationType();
            $operationSource = new OperationSource();

            $operationType->setId($data['id']);
            $operationType->setName($data['name']);
            $operationType->setLabel($data['label']);

            $operationSource->setName($data['operationSourceName']);
            $operationSource->setId($data['operationSourceId']);

            $operationType->setOperationSource($operationSource);
            
            array_push($operationTypes, $operationType);
        }

        return $operationTypes;
    }

    public function buildLabel($name){

        $label = str_replace(' ', '_', strtolower($name));
        $label = str_replace('ç', 'c', $label);
        return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"), explode(" ","a e i o u n"), $label);
    }
}
?>