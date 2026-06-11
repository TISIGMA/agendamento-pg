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

            $count = $this->countRecords($result);

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

        if (is_array($records) || $records instanceof Traversable) {
            foreach ($records as $data) {
                $employee = new Employee();
                $employee->setId($this->getRecordValue($data, 'id'));
                $employee->setName($this->getRecordValue($data, 'name'));
                $employee->setPosition($this->getRecordValue($data, 'position'));
                $employee->setCreatedDate($this->getRecordValue($data, 'created_date'));
                $employee->setCreatedBy($this->getRecordValue($data, 'user_name'));
                $employee->setLastModifiedDate($this->getRecordValue($data, 'last_modified_date'));
                $employee->setLastModifiedBy($this->getRecordValue($data, 'last_user_name'));
                
                array_push($employees, $employee);
            }

            return $employees;
        }

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
