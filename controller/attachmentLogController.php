<?php

require_once('../repository/attachmentLogRepository.php');
require_once('../repository/scheduleRepository.php');
require_once('../model/attachmentLog.php');

class AttachmentLogController{

    private $attachmentLog;
    private $attachmentLogRepository;
    private $mySql;

    public function __construct($mySql){

        $this->mySql = $mySql;
        $this->attachmentLogRepository = new AttachmentLogRepository($this->mySql);
        $this->scheduleRepository = new ScheduleRepository($this->mySql);
    }

    public function findByShipmentId($shipmentId){

        $logs = array();

        // depois busca os logs dos registros deletados
        $result = $this->attachmentLogRepository->findByShipmentId($shipmentId);
        if($result->num_rows > 0){
            $logs = array_merge($logs, $this->loadData($result));
        }

        if(count($logs) > 0) {
            //ordenar array pela data da ação dos anexos
            return $this->sortByDate($logs);
        }

        return null;
    }

    function findByClientStartDateAndEndDate($client, $startDate, $endDate){

        $logs = array();
        $startDate = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $startDate )));
        $endDate = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $endDate )));

        $result = $this->scheduleRepository->findByClientStartDateAndEndDate($client, $startDate, $endDate);
        if($result->num_rows == 0) return null;

        $shipmentIds = $this->loadScheduleData($result);

        $result = $this->attachmentLogRepository->findByShipmentIds($shipmentIds);
        if($result->num_rows > 0){
            $logs = array_merge($logs, $this->loadData($result));
        }
        
        // monta o map
        $ordered = [];
        foreach ($logs as $key => $log) {
            if(!array_key_exists($log->getShipmentId(), $ordered)){
                $ordered[$log->getShipmentId()] = [$log];
            }else{
                array_push($ordered[$log->getShipmentId()], $log);
            }
        }

        // ordena os valores dentro de cada elemento do mapa
        $newLogs = array();
        foreach($ordered as $key => $value){
            $value = $this->sortByDate($value);

            //cria a nova lista ordenada
            foreach($value as $unit){
                array_push($newLogs, $unit);
            }
        }
        return $newLogs;
    }

    function sortByDate($array){
        usort($array, function($a, $b) {
            return date('d/m/Y H:i:s', strtotime($a->getDateTimeAction())) <=> date('d/m/Y H:i:s', strtotime($b->getDateTimeAction()));
        });

        return $array;
    }

    public function loadData($records){

        $logs = array();

        while ($data = $records->fetch_assoc()){ 
            $log = new AttachmentLog();
            $log->setId($data['id']);
            $log->setPath($data['path']);
            $log->setShipmentId($data['shipmentId']);
            $log->setCreatedDate($data['created_date']);
            $log->setType($data['type']);
            $log->setAction($data['action']);
            $log->setUserAction($data['user_action']);
            $log->setDateTimeAction($data['date_time_action']);
            array_push($logs, $log);
        }
        return $logs;
    }

    public function loadScheduleData($records){

        $shipmentIds = array();

        while ($data = $records->fetch_assoc()){ 
            array_push($shipmentIds, $data['shipment_id']);
        }
        return $shipmentIds;
    }
}

?>