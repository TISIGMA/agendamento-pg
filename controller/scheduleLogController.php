<?php

require_once('../repository/scheduleLogRepository.php');
require_once('../model/scheduleLog.php');

class ScheduleLogController{

    private $scheduleLog;
    private $scheduleLogRepository;
    private $mySql;

    public function __construct($mySql){

        $this->mySql = $mySql;
        $this->scheduleLogRepository = new ScheduleLogRepository($this->mySql);
    }

    public function findByClientAndShipmentId($client, $shipmentId){
        $result = $this->scheduleLogRepository->findByClientAndShipmentId($client, $shipmentId);

        if($this->countRecords($result) == 0 ) return null;
        return $this->loadData($result);
    }

    public function loadData($records){

        $scheduleLogs = array();

        foreach ($this->getRecordsIterator($records) as $data){
            $scheduleLog = new ScheduleLog();
            $scheduleLog->setId($this->getRecordValue($data, 'id'));
            $scheduleLog->setTransportadora($this->getRecordValue($data, 'transportadora'));
            $scheduleLog->setTipoVeiculo($this->getRecordValue($data, 'tipoVeiculo'));
            $scheduleLog->setPlacaCavalo($this->getRecordValue($data, 'placa_cavalo'));
            $scheduleLog->setOperacao($this->getRecordValue($data, 'operacao'));
            $scheduleLog->setNf($this->getRecordValue($data, 'nf'));
            $scheduleLog->setHoraChegada($this->formatDateTime($this->getRecordValue($data, 'horaChegada')));

            $scheduleLog->setInicioOperacao($this->formatDateTime($this->getRecordValue($data, 'inicio_operacao')));

            $scheduleLog->setFimOperacao($this->formatDateTime($this->getRecordValue($data, 'fim_operacao')));

            $scheduleLog->setNomeUsuario($this->getRecordValue($data, 'usuario'));
            $scheduleLog->setDataInclusao($this->formatDateTime($this->getRecordValue($data, 'dataInclusao')));
            $scheduleLog->setPeso($this->getRecordValue($data, 'peso'));
            $scheduleLog->setDataAgendamento($this->formatDateTime($this->getRecordValue($data, 'data_agendamento')));
            $scheduleLog->setSaida($this->formatDateTime($this->getRecordValue($data, 'saida')));

            $scheduleLog->setSeparacao($this->getRecordValue($data, 'separacao'));
            $scheduleLog->setShipmentId($this->getRecordValue($data, 'shipment_id'));
            $scheduleLog->setDoca($this->getRecordValue($data, 'doca'));
            $scheduleLog->setDo_s($this->getRecordValue($data, 'do_s'));
            $scheduleLog->setCidade($this->getRecordValue($data, 'cidade'));
            $scheduleLog->setCargaQtde($this->getRecordValue($data, 'carga_qtde'));
            $scheduleLog->setObservacao($this->getRecordValue($data, 'observacao'));
            $scheduleLog->setDadosGerais($this->getRecordValue($data, 'dados_gerais'));
            $scheduleLog->setCliente($this->getRecordValue($data, 'cliente'));
            $scheduleLog->setStatus($this->getRecordValue($data, 'status'));
            $scheduleLog->setNomeMotorista($this->getRecordValue($data, 'nome_motorista')); 
            $scheduleLog->setPlacaCarreta2($this->getRecordValue($data, 'placa_carreta2'));
            $scheduleLog->setDocumentoMotorista($this->getRecordValue($data, 'documento_motorista'));
            $scheduleLog->setPlacaCarreta($this->getRecordValue($data, 'placa_carreta'));
            $scheduleLog->setOperationId($this->getRecordValue($data, 'operation_type_id'));
            $scheduleLog->setOperator($this->getRecordValue($data, 'operator'));
            $scheduleLog->setChecker($this->getRecordValue($data, 'checker'));
            $scheduleLog->setLastModifiedBy($this->getRecordValue($data, 'last_modified_by'));
            $scheduleLog->setLastModifiedDate($this->formatDateTime($this->getRecordValue($data, 'last_modified_date')));
            $scheduleLog->setAttPickingStatus($this->getRecordValue($data, 'attatchment_picking_status'));
            $scheduleLog->setAttInvoiceStatus($this->getRecordValue($data, 'attatchment_invoice_status'));
            $scheduleLog->setAttCertificateStatus($this->getRecordValue($data, 'attatchment_certificate_status'));
            $scheduleLog->setAttBoardingStatus($this->getRecordValue($data, 'attatchment_boarding_status'));
            $scheduleLog->setAttOtherStatus($this->getRecordValue($data, 'attatchment_other_status'));
            $scheduleLog->setScheduleId($this->getRecordValue($data, 'schedule_id'));
            $scheduleLog->setAction($this->getRecordValue($data, 'action'));
            $scheduleLog->setUserAction($this->getRecordValue($data, 'user_action'));

            $scheduleLog->setDateTimeAction($this->formatDateTime($this->getRecordValue($data, 'date_time_action')));

            array_push($scheduleLogs, $scheduleLog);
        }

        return $scheduleLogs;
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

    private function formatDateTime($value){
        if ($value === null || $value === '') {
            return '';
        }

        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return '';
        }

        return date('d/m/Y H:i:s', $timestamp);
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
