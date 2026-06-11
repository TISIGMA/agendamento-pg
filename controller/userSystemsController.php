<?php

require_once('repository/userSystemsRepository.php');

class UserSystemsController{

    private $userSystemsRepository;
    private $mySql;

    public function __construct($mySql){

        $this->mySql = $mySql;
        $this->userSystemsRepository = new UserSystemsRepository($this->mySql);
    }

    public function deleteByUser($userId){

        return $this->userSystemsRepository->deleteByUser($userId);
    }
}

?>