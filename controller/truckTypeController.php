<?php

require_once('../repository/truckTypeRepository.php');
require_once('../model/truckType.php');

class TruckTypeController{

    private $truckType;
    private $truckTypeRepository;
    private $mySql;

    public function __construct($mySql){

        $this->mySql = $mySql;
        $this->truckTypeRepository = new TruckTypeRepository($this->mySql);
    }

    public function findAll(){

        $result = $this->truckTypeRepository->findAll();
        $data = $this->loadData($result);

        if(count($data) > 0) return $data;

        return null;
    }

    public function save($post){

        try {
            $description = $post['description'];

            $result = $this->truckTypeRepository->findByDescription($description);

            $count = $result->fetch_assoc() == null ? 0 : count((array)$result->fetch_assoc());

            if($count > 0) {
                return 'ALREADY_EXISTS';
            }

            return $this->truckTypeRepository->save($post);
        
        } catch (Exception $e) {
            return 'SAVE_ERROR';
        }
    }

    public function updateById($id, $description){

        return $this->truckTypeRepository->updateById($id, $description);
    }

    public function deleteById($id){

        try {

            return $this->truckTypeRepository->deleteById($id);
        
        } catch (Exception $e) {
            return 'DELETE_ERROR';
        }
    }

    public function loadData($records){

        $truckTypes = array();

        if($records == null) return $truckTypes;

        while ($data = $records->fetch_assoc()){ 
            $truckType = new TruckType();
            $truckType->setId($data['id']);
            $truckType->setDescription($data['descricao']);
            
            array_push($truckTypes, $truckType);
        }

        return $truckTypes;
    }
}
?>