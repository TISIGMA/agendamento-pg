<?php

require_once('../repository/operationSourceRepository.php');
require_once('../model/operationSource.php');

class OperationSourceController{

    private $operationSource;
    private $operationSourceRepository;
    private $mySql;

    public function __construct($mySql){

        $this->mySql = $mySql;
        $this->operationSourceRepository = new OperationSourceRepository($this->mySql);
    }

    public function findAll(){

        $result = $this->operationSourceRepository->findAll();
        $data = $this->loadData($result);

        return $data;
    }

    public function save($post){

        try {
            $name = $post['name'];

            $label = $this->buildLabel($name);

            $result = $this->operationSourceRepository->findByName($name);

            $count = $this->countRecords($result);

            if($count > 0) {
                return 'ALREADY_EXISTS';
            }

            return $this->operationSourceRepository->save($name, $label, $_SESSION['customerName']);
        
        } catch (Exception $e) {
            return 'SAVE_ERROR';
        }
    }

    public function updateById($id, $name){

        $label = $this->buildLabel($name);

        return $this->operationSourceRepository->updateById($id, $name, $label);
    }

    public function deleteById($id){

        try {

            return $this->operationSourceRepository->deleteById($id);
        
        } catch (Exception $e) {
            return 'DELETE_ERROR';
        }
    }

    public function loadData($records){

        $operationSources = array();

        if (is_array($records) || $records instanceof Traversable) {
            foreach ($records as $data) {
                $operationSource = new OperationSource();
                $operationSource->setId($this->getRecordValue($data, 'id'));
                $operationSource->setName($this->getRecordValue($data, 'name'));
                $operationSource->setLabel($this->getRecordValue($data, 'label'));
                
                array_push($operationSources, $operationSource);
            }

            return $operationSources;
        }

        while ($data = $records->fetch_assoc()){ 
            $operationSource = new OperationSource();
            $operationSource->setId($data['id']);
            $operationSource->setName($data['name']);
            $operationSource->setLabel($data['label']);
            
            array_push($operationSources, $operationSource);
        }

        return $operationSources;
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
