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

        while ($data = $records->fetch_assoc()){ 
            $scheduleChart = new ScheduleChart();
            $scheduleChart->setId($data['janela_id']);
            $scheduleChart->setOperationSourceName($data['operation_name']);
            $scheduleChart->setHoraChegada( $data['horaChegada']);
            $scheduleChart->setInicioOperacao($data['inicio_operacao']);
            $scheduleChart->setFimOperacao($data['fim_operacao']);   
            $scheduleChart->setSaida($data['saida']);

            array_push($scheduleCharts, $scheduleChart);
        }

        return $scheduleCharts;
    }
}

?>