<?php

require_once('repository/systemErrorRepository.php');
require_once('model/systemError.php');

class SystemErrorController{

    private $systemError;
    private $systemErrorRepository;
    private $mySql;

    public function __construct($mySql){

        $this->mySql = $mySql;
        $this->systemErrorRepository = new SystemErrorRepository($this->mySql);
    }

    public function findAll(){
        $result = $this->systemErrorRepository->findAll();
        $data = $this->loadData($result);

        return $data;
    }

    public function save($post){

        try {

            $systemError = new SystemError();
            $systemError = $this->setFields($post, $systemError);
            $result = $this->systemErrorRepository->save($systemError);

            if($result == 'SAVE_ERROR') return $result;

            return $this->saveFiles($result, 'SAVED');  
            
        } catch (Exception $e) {

            $description = $e->getMessage() . '- ' . $e->getTraceAsString();

            $description = str_replace('\'', '"', $description);
            
            echo $description;
    
            return 'SAVE_ERROR';
        }
    }

    public function update($post){

        try {

            $systemError = new SystemError(); 

            $systemError = $this->setFields($post, $systemError);
            $result =  $this->systemErrorRepository->updateById($systemError, $post['id']);
            
            if($result == 'SAVE_ERROR') return $result;

            $hasAttachment = $this->hasThisAttachment($post['id']);

            if($result == 'DELETE_ERROR') throw new Exception("Erro ao deletar anexos", 1);

            if(!$hasAttachment){
                $this->saveFiles($systemError->getId(), 'UPDATED'); 
            }

            return 'UPDATED';
        
        } catch (Exception $e) {
            return 'SAVE_ERROR';
        }
    }

    public function hasThisAttachment($systemErrorId){

        $scheduleDirectory = 'error_files/error_'.$systemErrorId.'/';

        if (!file_exists($scheduleDirectory)) return false;

        $fileName =  $_FILES['file']['name'];

        $pathFile = $scheduleDirectory.$fileName;

        if (file_exists($pathFile)) return true;

        $contentfiles = glob($scheduleDirectory . '*'); 

        foreach($contentfiles as $file) {
            unlink($file); 
        }
        
        return false;
    }

    public function findByUserId(){

        $result = $this->systemErrorRepository->findByUserId($_SESSION['id']);
        $data = $this->loadData($result);

        return $data;
    }


    public function findById($id){

        $result = $this->systemErrorRepository->findById($id);
        $data = $this->loadData($result);

        if(count($data) > 0) return $data[0];

        return $data;
    }

    public function findByIdAndUserId($id){

        $result = $this->systemErrorRepository->findByIdAndUserId($id, $_SESSION['id']);
        $data = $this->loadData($result);

        if(count($data) > 0) return $data[0];

        return $data;
    }

    public function saveFiles($scheduleId, $action){

        try {
    
            $fileName =  $_FILES['file']['name'];

            $scheduleDirectory = 'error_files/error_'.$scheduleId.'/';

            if (!file_exists($scheduleDirectory)) mkdir($scheduleDirectory, 0755);
            
            $tempName = $_FILES['file']['tmp_name'];
            $pathFile = $scheduleDirectory.$fileName;

            if (!file_exists($pathFile)) {
                move_uploaded_file($tempName,$pathFile);
            }

            return $action;

        } catch (Exception $e) {
            return 'SAVE_ERROR';
        }
    }

    public function setFields($post, $systemError){ 

        $fileName = ($_FILES['file'] != null) ? $_FILES['file']['name'] : null;

        if($post['id'] && $post['id'] != null) $systemError->setId($post['id']);
        $systemError->setEmail($post['email']);
        $systemError->setUserId($_SESSION['id']);
        $systemError->setDescription($post['description']);
        $systemError->setCreatedDate(date("Y-m-d H:i:s"));
        $systemError->setFileName($fileName);

        return $systemError;
    }

    public function loadData($records){

        $systemErrors = array();

        while ($data = $records->fetch_assoc()){ 
            $systemError = new SystemError();
            $systemError->setId($data['errorId']);
            $systemError->setUserId($data['user_id']);
            $systemError->setUserName($data['user_name']);
            $systemError->setEmail($data['contact_email']);
            $systemError->setCreatedDate(date("d/m/Y H:i:s", strtotime($data['created_date'])));
            $systemError->setDescription($data['description']);
            $systemError->setStatus($data['status']);
            $systemError->setResolution($data['resolution']);
            $systemError->setFileName($data['attachment_name']);
    
            array_push($systemErrors, $systemError);
        }

        return $systemErrors;
    }
}

?>