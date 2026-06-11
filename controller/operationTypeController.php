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

            $count = $this->countRecords($result);

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

        if (is_array($records) || $records instanceof Traversable) {
            foreach ($records as $data) {
                $operationType = new OperationType();
                $operationSource = new OperationSource();
    
                $operationType->setId($this->getRecordValue($data, 'id'));
                $operationType->setName($this->getRecordValue($data, 'name'));
                $operationType->setLabel($this->getRecordValue($data, 'label'));
    
                $operationSource->setName($this->getRecordValue($data, 'operationSourceName'));
                $operationSource->setId($this->getRecordValue($data, 'operationSourceId'));
    
                $operationType->setOperationSource($operationSource);
                
                array_push($operationTypes, $operationType);
            }

            return $operationTypes;
        }

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

    private function countRecords($records){
        if (is_array($records)) {
            return count($records);
        }

        if ($records instanceof Traversable) {
            $count = 0;
            foreach ($records as $_) {
                $count++;
            }
            return $count;
        }

        if ($records instanceof mysqli_result) {
            return $records->num_rows;
        }

        return 0;
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
