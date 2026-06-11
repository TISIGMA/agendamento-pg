<?php

require_once('../repository/scheduleRepository.php');
require_once('../model/scheduleChart.php');


class ScheduleChartsController{

    private $ScheduleChart;
    private $scheduleRepository;
    private $mySql;

    public function __construct($mySql){

        $this->mySql = $mySql;
        $this->scheduleRepository = new ScheduleRepository($this->mySql);
    }

    public function findByClienteAndStartDateAndEndDateAndStatus($startDate, $endDate, $status){

        
        $result = $this->scheduleRepository->findByClienteAndStartDateAndEndDateAndStatus($startDate, $endDate, $_SESSION['customerName'], $status);

        $data = $this->loadData($result);

        return $data;
    }

    public function loadData($records){

        $scheduleCharts = array();

        foreach ($this->getRecordsIterator($records) as $data){
            $scheduleChart = new ScheduleChart();
            $scheduleChart->setId($this->getRecordValue($data, 'janela_id'));
            $scheduleChart->setOperationSourceName($this->getRecordValue($data, 'operation_name'));
            $scheduleChart->setHoraChegada($this->getRecordValue($data, 'horaChegada'));
            $scheduleChart->setInicioOperacao($this->getRecordValue($data, 'inicio_operacao'));
            $scheduleChart->setFimOperacao($this->getRecordValue($data, 'fim_operacao'));   
            $scheduleChart->setSaida($this->getRecordValue($data, 'saida'));

            array_push($scheduleCharts, $scheduleChart);
        }

        return $scheduleCharts;
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
}

?>
