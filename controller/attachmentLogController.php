<?php

require_once('../repository/attachmentLogRepository.php');
require_once('../repository/scheduleRepository.php');
require_once('../model/attachmentLog.php');

class AttachmentLogController{

    private $attachmentLog;
    private $attachmentLogRepository;
    private $scheduleRepository;
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
        if($this->countRecords($result) > 0){
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
        if($this->countRecords($result) == 0) return null;

        $shipmentIds = $this->loadScheduleData($result);

        $result = $this->attachmentLogRepository->findByShipmentIds($shipmentIds);
        if($this->countRecords($result) > 0){
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

        foreach ($this->getRecordsIterator($records) as $data){
            $log = new AttachmentLog();
            $log->setId($this->getRecordValue($data, 'id'));
            $log->setPath($this->getRecordValue($data, 'path'));
            $log->setShipmentId($this->getRecordValue($data, 'shipmentId'));
            $log->setCreatedDate($this->getRecordValue($data, 'created_date'));
            $log->setType($this->getRecordValue($data, 'type'));
            $log->setAction($this->getRecordValue($data, 'action'));
            $log->setUserAction($this->getRecordValue($data, 'user_action'));
            $log->setDateTimeAction($this->getRecordValue($data, 'date_time_action'));
            array_push($logs, $log);
        }
        return $logs;
    }

    public function loadScheduleData($records){

        $shipmentIds = array();

        foreach ($this->getRecordsIterator($records) as $data){
            array_push($shipmentIds, $this->getRecordValue($data, 'shipment_id'));
        }
        return $shipmentIds;
    }

    private function getRecordsIterator($records){
        if ($records instanceof mysqli_result) {
            while ($row = $records->fetch_assoc()) {
                yield $row;
            }
            return;
        }

        if (is_array($records)) {
            foreach ($records as $row) {
                yield $row;
            }
            return;
        }

        if ($records instanceof Traversable) {
            foreach ($records as $row) {
                yield $row;
            }
            return;
        }
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
}

?>
