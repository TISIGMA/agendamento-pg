<?php

require_once('../repository/customerRepository.php');
require_once('../model/customer.php');

class CustomerController{

    private $customer;
    private $customerRepository;
    private $mySql;

    public function __construct($mySql){

        $this->mySql = $mySql;
        $this->customerRepository = new CustomerRepository($this->mySql);
    }

    public function findByName($customerName){

        $result = $this->customerRepository->findByName($customerName);
        $data = $this->loadData($result);

        if(count($data) > 0) return $data;

        return null;
    }

    public function loadData($records){

        $customers = array();

        if (is_array($records) || $records instanceof Traversable) {
            foreach ($records as $data) {
                $customer = new Customer();
                $customer->setId($this->getRecordValue($data, 'id'));
                $customer->setName($this->getRecordValue($data, 'name'));
                $customer->setDescription($this->getRecordValue($data, 'description'));
                
                array_push($customers, $customer);
            }

            return $customers;
        }

        while ($data = $records->fetch_assoc()){ 
            $customer = new Customer();
            $customer->setId($data['id']);
            $customer->setName($data['name']);
            $customer->setDescription($data['description']);
            
            array_push($customers, $customer);
        }

        return $customers;
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
