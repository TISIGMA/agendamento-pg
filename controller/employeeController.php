<?php

require_once('../repository/employeeRepository.php');
require_once('../model/employee.php');

class EmployeeController{

    private $employee;
    private $employeeRepository;
    private $mySql;

    public function __construct($mySql){

        $this->mySql = $mySql;
        $this->employeeRepository = new EmployeeRepository($this->mySql);
    }

    public function findAll(){

        $result = $this->employeeRepository->findAll();
        $data = $this->loadData($result);

        if(count($data) > 0) return $data;

        return null;
    }

    public function save($post){

        try {
            $position = $post['position'];
            $name = $post['name'];

            $result = $this->employeeRepository->findByNameAndPosition($name, $position);

            if($result->num_rows > 0){
                echo 'erro ao buscar duplicado';
                return 'SAVE_ERROR';
            }

            $count = $result->fetch_assoc() == null ? 0 : count((array)$result->fetch_assoc());

            if($count > 0) {
                return 'ALREADY_EXISTS';
            }

            return $this->employeeRepository->save($post);
        
        } catch (Exception $e) {
            return 'SAVE_ERROR';
        }
    }

    public function updateById($id, $name, $position){

        return $this->employeeRepository->updateById($id, $name, $position);
    }

    public function deleteById($id){

        try {

            return $this->employeeRepository->deleteById($id);
        
        } catch (Exception $e) {
            return 'DELETE_ERROR';
        }
    }

    public function loadData($records){

        $employees = array();

        if($records == null) return $employees;

        while ($data = $records->fetch_assoc()){ 
            $employee = new Employee();
            $employee->setId($data['id']);
            $employee->setName($data['name']);
            $employee->setPosition($data['position']);
            $employee->setCreatedDate($data['created_date']);
            $employee->setCreatedBy($data['user_name']);
            $employee->setLastModifiedDate($data['last_modified_date']);
            $employee->setLastModifiedBy($data['last_user_name']);
            
            array_push($employees, $employee);
        }

        return $employees;
    }
}
?>