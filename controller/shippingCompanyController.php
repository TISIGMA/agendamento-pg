<?php

require_once('../repository/shippingCompanyRepository.php');
require_once('../model/shippingCompany.php');

class ShippingCompanyController{

    private $shippingCompany;
    private $shippingCompanyRepository;
    private $mySql;

    public function __construct($mySql){

        $this->mySql = $mySql;
        $this->shippingCompanyRepository = new ShippingCompanyRepository($this->mySql);
    }

    public function findByClient($clientName){

        $result = $this->shippingCompanyRepository->findByClient($clientName);
        $data = $this->loadData($result);

        return $data;
    }
    
    public function findAll(){

        $result = $this->shippingCompanyRepository->findAll();
        $data = $this->loadData($result);

        return $data;
    }

    public function save($post){

        try {
  
            return $this->shippingCompanyRepository->save($post);
        
        } catch (Exception $e) {
            return 'SAVE_ERROR';
        }
    }

    public function updateById($id, $name){

        return $this->shippingCompanyRepository->updateById($id, $name);
    }

    public function deleteById($id){

        try {

            return $this->shippingCompanyRepository->deleteById($id);
        
        } catch (Exception $e) {
            return 'DELETE_ERROR';
        }
    }

    public function loadData($records){

        $shippingCompanys = array();

        if (is_array($records) || $records instanceof Traversable) {
            foreach ($records as $data) {
                $shippingCompany = new ShippingCompany();
                $shippingCompany->setId($this->getRecordValue($data, 'id'));
                $shippingCompany->setNome($this->getRecordValue($data, 'nome'));
                $shippingCompany->setUsername($this->getRecordValue($data, 'username'));
                $shippingCompany->setCNPJ($this->getRecordValue($data, 'cnpj'));
                $shippingCompany->setEmail($this->getRecordValue($data, 'email'));
                $shippingCompany->setTelefone($this->getRecordValue($data, 'telefone'));
                $shippingCompany->setCelular($this->getRecordValue($data, 'celular'));
                $shippingCompany->setPassword($this->getRecordValue($data, 'password'));
                $shippingCompany->setData($this->getRecordValue($data, 'data'));
                $shippingCompany->setUsuario($this->getRecordValue($data, 'usuario'));
                $shippingCompany->setClienteOrigem($this->getRecordValue($data, 'cliente_origem'));
                
                array_push($shippingCompanys, $shippingCompany);
            }

            return $shippingCompanys;
        }

        while ($data = $records->fetch_assoc()){ 
            $shippingCompany = new ShippingCompany();
            $shippingCompany->setId($data['id']);
            $shippingCompany->setNome($data['nome']);
            $shippingCompany->setUsername($data['username']);
            $shippingCompany->setCNPJ($data['cnpj']);
            $shippingCompany->setEmail($data['email']);
            $shippingCompany->setTelefone($data['telefone']);
            $shippingCompany->setCelular($data['celular']);
            $shippingCompany->setPassword($data['password']);
            $shippingCompany->setData($data['data']);
            $shippingCompany->setUsuario($data['usuario']);
            $shippingCompany->setClienteOrigem($data['cliente_origem']);
            
            array_push($shippingCompanys, $shippingCompany);
        }

        return $shippingCompanys;
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
