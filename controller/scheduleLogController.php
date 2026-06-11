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

        if($result->num_rows == 0 ) return null;
        return $this->loadData($result);
    }

    public function loadData($records){

        $scheduleLogs = array();

        while ($data = $records->fetch_assoc()){ 
            $scheduleLog = new ScheduleLog();
            $scheduleLog->setId($data['id']);
            $scheduleLog->setTransportadora($data['transportadora']);
            $scheduleLog->setTipoVeiculo($data['tipoVeiculo']);
            $scheduleLog->setPlacaCavalo($data['placa_cavalo']);
            $scheduleLog->setOperacao($data['operacao']);
            $scheduleLog->setNf($data['nf']);
            $scheduleLog->setHoraChegada( date("d/m/Y H:i:s", strtotime($data['horaChegada'])));
            if(empty($data['horaChegada'])) $scheduleLog->setHoraChegada('');

            $scheduleLog->setInicioOperacao(date("d/m/Y H:i:s", strtotime($data['inicio_operacao'])));
            if(empty($data['inicio_operacao'])) $scheduleLog->setInicioOperacao('');

            $scheduleLog->setFimOperacao(date("d/m/Y H:i:s", strtotime($data['fim_operacao'])));
            if(empty($data['fim_operacao'])) $scheduleLog->setFimOperacao('');

            $scheduleLog->setNomeUsuario($data['usuario']);
            $scheduleLog->setDataInclusao(date("d/m/Y H:i:s", strtotime($data['dataInclusao'])));
            $scheduleLog->setPeso($data['peso']);
            $scheduleLog->setDataAgendamento(date("d/m/Y H:i:s", strtotime($data['data_agendamento'])));
            $scheduleLog->setSaida(date("d/m/Y H:i:s", strtotime($data['saida'])));
            if(empty($data['saida'])) $scheduleLog->setSaida('');

            $scheduleLog->setSeparacao($data['separacao']);
            $scheduleLog->setShipmentId($data['shipment_id']);
            $scheduleLog->setDoca($data['doca']);
            $scheduleLog->setDo_s($data['do_s']);
            $scheduleLog->setCidade($data['cidade']);
            $scheduleLog->setCargaQtde($data['carga_qtde']);
            $scheduleLog->setObservacao($data['observacao']);
            $scheduleLog->setDadosGerais($data['dados_gerais']);
            $scheduleLog->setCliente($data['cliente']);
            $scheduleLog->setStatus($data['status']);
            $scheduleLog->setNomeMotorista($data['nome_motorista']); 
            $scheduleLog->setPlacaCarreta2($data['placa_carreta2']);
            $scheduleLog->setDocumentoMotorista($data['documento_motorista']);
            $scheduleLog->setPlacaCarreta($data['placa_carreta']);
            $scheduleLog->setOperationId($data['operation_type_id']);
            $scheduleLog->setOperator($data['operator']);
            $scheduleLog->setChecker($data['checker']);
            $scheduleLog->setLastModifiedBy($data['last_modified_by']);
            $scheduleLog->setLastModifiedDate(date("d/m/Y H:i:s", strtotime($data['last_modified_date'])));
            $scheduleLog->setAttPickingStatus($data['attatchment_picking_status']);
            $scheduleLog->setAttInvoiceStatus($data['attatchment_invoice_status']);
            $scheduleLog->setAttCertificateStatus($data['attatchment_certificate_status']);
            $scheduleLog->setAttBoardingStatus($data['attatchment_boarding_status']);
            $scheduleLog->setAttOtherStatus($data['attatchment_other_status']);
            $scheduleLog->setScheduleId($data['schedule_id']);
            $scheduleLog->setAction($data['action']);
            $scheduleLog->setUserAction($data['user_action']);

            $scheduleLog->setDateTimeAction(date("d/m/Y H:i:s", strtotime($data['date_time_action'])));
            if(empty($data['date_time_action'])) $scheduleLog->setDateTimeAction('');

            array_push($scheduleLogs, $scheduleLog);
        }

        return $scheduleLogs;
    }
}

?>