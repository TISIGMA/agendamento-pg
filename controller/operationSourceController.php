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

            $count = $result->fetch_assoc() == null ? 0 : count((array)$result->fetch_assoc());

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
}
?>