<?php
class AttachmentLog{

    private $id;
    private $path;
    private $shipmentId;
    private $createdDate;
    private $type;
    private $action;
    private $userAction;
    private $dateTimeAction;

    public function setId($id){
        $this->id = $id;
    }
    public function getId(){
        return $this->id;
    }

    public function setPath($path){
        $this->path = $path;
    }
    public function getPath(){
        return $this->path;
    }

    public function setShipmentId($shipmentId){
        $this->shipmentId = $shipmentId;
    }
    public function getShipmentId(){
        return $this->shipmentId;
    }

    public function setCreatedDate($createdDate){
        $this->createdDate = date("d/m/Y H:i:s", strtotime($createdDate));
    }
    public function getCreatedDate(){
        return $this->createdDate;
    }

    public function setType($type){
        $this->type = $type;
    }
    public function getType(){
        return $this->type;
    }

    public function setAction($action){
        $this->action = $action;
    }
    public function getAction(){
        return $this->action;
    }

    public function setUserAction($userAction){
        $this->userAction = $userAction;
    }
    public function getUserAction(){
        return $this->userAction;
    }

    public function setDateTimeAction($dateTimeAction){
        $this->dateTimeAction = date("d/m/Y H:i:s", strtotime($dateTimeAction));
    }
    public function getDateTimeAction(){
        return $this->dateTimeAction;
    }
}
?>