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

        while ($data = $records->fetch_assoc()){ 
            $customer = new Customer();
            $customer->setId($data['id']);
            $customer->setName($data['name']);
            $customer->setDescription($data['description']);
            
            array_push($customers, $customer);
        }

        return $customers;
    }
}

?>