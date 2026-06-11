<?php

require_once('../repository/scheduleRepository.php');
require_once('../model/schedule.php');
require_once('../model/columnsPreference.php');
require_once('../repository/columnsPreferencesRepository.php');
require_once('../repository/attachmentRepository.php');
require_once('../repository/scheduleLogRepository.php');
require_once('../repository/attachmentLogRepository.php');

class ScheduleController{

    private $schedule;
    private $scheduleRepository;
    private $attachmentRepository;
    private $scheduleLogRepository;
    private $attachmentLogRepository;
    private $mySql;

    public function __construct($mySql){

        $this->mySql = $mySql;
        $this->scheduleRepository = new ScheduleRepository($this->mySql);
        $this->attachmentRepository = new AttachmentRepository($this->mySql);
        $this->$scheduleLogRepository = new ScheduleLogRepository($this->mySql);
        $this->attachmentLogRepository = new AttachmentLogRepository($this->mySql);
    }

    public function save($post){

        try {

            $schedule = new Schedule();
            // $schedule->setStatus('Agendado'); 
            
            $schedule = $this->setFields($post, $schedule);

            $result = $this->scheduleRepository->save($schedule);

            if($result == 'SAVE_ERROR') return $result;

            return $this->saveFiles($result, 'SAVED');  
          
            
        } catch (Exception $e) {

            $description = $e->getMessage() . '- ' . $e->getTraceAsString();

            $description = str_replace('\'', '"', $description);
            
            echo $description;
    
            return 'SAVE_ERROR';
        }
    }

    public function delete($id){

        try {
            $result = $this->scheduleRepository->findById($id);

            if($result->num_rows == 0) return;

            $data = $this->loadData($result);
            $this->scheduleRepository->delete($id);

            // salvar registro de log de exclusão
            return $this->$scheduleLogRepository->save($data[0]);
        } catch (Exception $e) {
            return 'DELETE_ERROR';
        }
    }

    public function update($post){

        try {

            $schedule = new Schedule();
            $schedule->setStatus($post['scheduleStatus']); 

            $schedule = $this->setFields($post, $schedule);
            $result =  $this->scheduleRepository->updateById($schedule, $post['id']);
            
            if($result == 'SAVE_ERROR') return $result;
            if($post['filesToRemove'] != '') $this->deleteAttachment($post['filesToRemove'], $schedule->getShipmentId());
            if($result == 'DELETE_ERROR') throw new Exception("Erro ao deletar anexos", 1);

            return $this->saveFiles($schedule->getId(), 'UPDATED');
        } catch (Exception $e) {
            return 'SAVE_ERROR';
        }
    }

    public function deleteAttachment($idsString, $shipmentId){
        $result = $this->attachmentRepository->deleteByCondition(str_replace(';', ',', $idsString));

        // inserir o nome do usuário  responsável pela deleção do arquivo na tabela de log de anexos 
        try {

            $numIds = count(explode(",", $idsString)) == null || count(explode(",", $idsString)) == 0 ? 1 : count(explode(",", $idsString));   
            $this->attachmentLogRepository->updateLastCreated($shipmentId, $numIds);
        } catch (Exception $ex) { }
    }

    public function findByClient($client){

        $result = $this->scheduleRepository->findByClient($client);
        $data = $this->loadData($result);

        return $data;
    }

    public function findByClientStatusStartDateAndEndDate($client, $status, $startDate, $endDate){

        $startDate = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $startDate )));
        $endDate = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $endDate )));

        if($status == 'Todos'){
            $result = $this->scheduleRepository->findByClientStartDateAndEndDate($client, $startDate, $endDate);
        }else {
            $result = $this->scheduleRepository->findByClientStatusStartDateAndEndDate($client, $status, $startDate, $endDate);
        }

        $data = $this->loadDataByNameValue($result);

        return $data;
    }

    public function findById($id){

        $result = $this->scheduleRepository->findById($id);
        $data = $this->loadData($result);

        $data[0] = $this->findAttByScheduleId($data[0]);

        if(count($data) > 0) return $data[0];

        return $data;
    }

    public function saveFiles($scheduleId, $action){

        $files = ['picking'=> $_FILES['file-picking'], 'invoice' => $_FILES['file-invoice'], 'certificate' => $_FILES['file-certificate'], 'boarding' => $_FILES['file-boarding'],'other' => $_FILES['file-other']];
        try {

            foreach ($files as $key => $value) {
                $this->iteratorSaveFiles($key, $value, $scheduleId);
            }

            return $action;
        } catch (Exception $e) {
            return 'SAVE_ERROR';
        }
    }

    public function iteratorSaveFiles($type, $files, $scheduleId){

        try {

            if($files['name'] == null) return;
            $countfiles = count($files['name']);

            if($countfiles == null || $countfiles == 0)  return;

            for($i=0;$i<$countfiles;$i++){
                if(empty($files['name'][$i])) continue;

                $fileName =  $files['name'][$i];
                $scheduleDirectory = 'files/schedule_'.$scheduleId.'/';

                //cria a pasta do agendamento
                if (!file_exists($scheduleDirectory)) mkdir($scheduleDirectory, 0755);

                // cria as pastas por tipo de arquivo
                $scheduleDirectory .= $type.'/';
                if (!file_exists($scheduleDirectory)) mkdir($scheduleDirectory, 0755);

                $tempName = $files['tmp_name'][$i];
                $pathFile = $scheduleDirectory.$fileName;

                if (!file_exists($pathFile)) {
                    move_uploaded_file($tempName,$pathFile);
                    $this->attachmentRepository->save($scheduleId, $pathFile, $type);
                }
            }
            
        } catch (Exception $ex) {
            throw $ex;
            
        }
    } 

    public function savePreferences($columnsDefault, $post){

        $columnsPreference = $post['column'];

        $id = $post['preferenceId'];

        $columnsToSave = array();
        $cont = 0;

        foreach ($columnsDefault as $key => $value) {
            
            $value['show'] = false;
            $value['order'] = $cont + 200;

            $columnsToSave[$key] = $value;
            $cont++;
        }

        $cont = 0;
        foreach ($columnsPreference as $value) {
            
            $column = $columnsToSave[$value];

            $column['show']  = true;
            $column['order'] = $cont;

            $columnsToSave[$value] = $column;
            $cont++;
        }

        $columnsPreference = new ColumnsPreference();
        $columnsPreferencesRepository = new ColumnsPreferencesRepository($this->mySql);

        $columnsPreference->setUserId($_SESSION['id']);
        $columnsPreference->setPreference( json_encode($columnsToSave, JSON_UNESCAPED_UNICODE));

        if($id == null){
            return $columnsPreferencesRepository->save($columnsPreference);
        }

        return $columnsPreferencesRepository->updateById($columnsPreference, $id);

    }

    public function findPreferenceByUser(){

        $columnsPreferencesRepository = new ColumnsPreferencesRepository($this->mySql);
        $result = $columnsPreferencesRepository->findByUser($_SESSION['id']);

        if($result->num_rows == 0) return new ColumnsPreference();

        return $this->loadPreferenceData($result);
    }

    public function sortArray($columns){

        $ordenedColumns = array();

        $cont = 0;
        foreach ($columns as $key => $value){

            $ordenedColumns[$cont] = $value['order'];
            $cont++; 
        }

        array_multisort($ordenedColumns, SORT_ASC, $columns);

        return $columns;
    }

    public function findAttByScheduleId($schedule){

        $result = $this->attachmentRepository->findByScheduleId($schedule->getId());

        $paths = array();

        while ($data = $result->fetch_assoc()){ 
            $date = (str_contains($data['created_date'], '0000') || $data['created_date'] == null) ? '' : date("d/m/Y H:i", strtotime($data['created_date']));
            $paths[$data['id']] = ['id' => $data['id'], 'type'=> $data['type'], 'path' => $data['path'], 'datetime' => $date];
        }
        
        $schedule->setFilesPath($paths);
        return $schedule;
    }

    public function setFields($post, $schedule){

        if($post['id'] && $post['id'] != null) $schedule->setId($post['id']);
        $schedule->setStatus($post['scheduleStatus']);
        $schedule->setTransportadora($post['shippingCompany']);
        $schedule->setTipoVeiculo($post['truckType']);
        $schedule->setPlacaCavalo($post['licenceTruck']);
        // $schedule->setOperacao($operation->getName());
        $schedule->setNf($post['invoice']);
        $schedule->setHoraChegada(date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $post['arrival'] ))));
        if($post['arrival'] == '') $schedule->setHoraChegada('');

        $schedule->setInicioOperacao(date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $post['operationStart'] ))));
        if($post['operationStart'] == '') $schedule->setInicioOperacao('');

        $schedule->setFimOperacao(date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $post['operationDone'] ))));
        if($post['operationDone'] == '') $schedule->setFimOperacao('');

        $schedule->setNomeUsuario($_SESSION['nome']);
        $schedule->setDataInclusao(date("Y-m-d H:i:s"));
        $schedule->setPeso($post['grossWeight']);
        $schedule->setDataAgendamento(date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $post['operationScheduleTime'] ))));

        $schedule->setSaida(date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $post['operationExit'] ))));
        if($post['operationExit'] == '') $schedule->setSaida('');

        $schedule->setSeparacao($post['binSeparation']);
        $schedule->setShipmentId($post['shipmentId']);
        $schedule->setDoca($post['dock']);
        $schedule->setDo_s($post['dos']);
        $schedule->setCidade($post['city']);

        $cargaQtde = $post['pallets'] == null ? 0 : $post['pallets'];
        $schedule->setCargaQtde($cargaQtde);
        $schedule->setObservacao($post['observation']);
        $schedule->setDadosGerais($post['material']);
        $schedule->setCliente($_SESSION['customerName']);

        $schedule->setNomeMotorista($post['driverName']); 
        $schedule->setPlacaCarreta2($post['licenceTrailer2']);
        $schedule->setDocumentoMotorista($post['documentDriver']);
        $schedule->setPlacaCarreta($post['licenceTrailer']);
        $schedule->setOperationId($post['operationType']);
        $schedule->setOperator($post['operator']);
        $schedule->setChecker($post['checker']);
        
        $schedule->setAttPickingStatus($post['picking-status']);
        $schedule->setAttInvoiceStatus($post['invoice-status']);
        $schedule->setAttCertificateStatus($post['certificate-status']);
        $schedule->setAttBoardingStatus($post['boarding-status']);
        $schedule->setAttOtherStatus($post['other-status']);

        return $schedule;
    }

    public function loadPreferenceData($records){

        while ($data = $records->fetch_assoc()){ 
            $columnsPreference = new ColumnsPreference();
            $columnsPreference->setId($data['id']);
            $columnsPreference->setPreference($data['preference']);
            $columnsPreference->setUserId($data['userId']);
        }

        return $columnsPreference;
    }

    public function loadDataByNameValue($records){

        $schedules = array();

        while ($data = $records->fetch_assoc()){ 


            $schedule['getId'] = $data['id'];
            $schedule['getTransportadora'] = $data['transportadora'];
            $schedule['getTipoVeiculo'] = $data['tipoVeiculo'];
            $schedule['getPlacaCavalo'] = $data['placa_cavalo'];
            $schedule['getOperacao'] = $data['operacao'];
            $schedule['getNf'] = $data['nf'];
            $schedule['getHoraChegada'] = date("d/m/Y H:i:s", strtotime($data['horaChegada']));
            if(empty($data['horaChegada'])) $schedule['getHoraChegada'] = '';

            $schedule['getInicioOperacao'] = date("d/m/Y H:i:s", strtotime($data['inicio_operacao']));
            if(empty($data['inicio_operacao'])) $schedule['getInicioOperacao'] = '';

            $schedule['getFimOperacao'] = date("d/m/Y H:i:s", strtotime($data['fim_operacao']));
            if(empty($data['fim_operacao'])) $schedule['getFimOperacao'] = '';

            $schedule['getNomeUsuario'] = $data['usuario'];
            $schedule['getDataInclusao'] = date("d/m/Y H:i:s", strtotime($data['dataInclusao']));
            $schedule['getPeso'] = $data['peso'];
            $schedule['getDataAgendamento'] = date("d/m/Y H:i:s", strtotime($data['data_agendamento']));
            $schedule['getSaida'] = date("d/m/Y H:i:s", strtotime($data['saida']));
            if(empty($data['saida'])) $schedule['getSaida'] = '';

            $schedule['getSeparacao'] = $data['separacao'];
            $schedule['getShipmentId'] = $data['shipment_id'];
            $schedule['getDoca'] = $data['doca'];
            $schedule['getDo_s'] = $data['do_s'];
            $schedule['getCidade'] = $data['cidade'];
            $schedule['getCargaQtde'] = $data['carga_qtde'];
            $schedule['getObservacao'] = $data['observacao'];
            $schedule['getDadosGerais'] = $data['dados_gerais'];
            $schedule['getCliente'] = $data['cliente'];
            $schedule['getStatus'] = $data['status'];
            $schedule['getNomeMotorista'] = $data['nome_motorista']; 
            $schedule['getPlacaCarreta2'] = $data['placa_carreta2'];
            $schedule['getDocumentoMotorista'] = $data['documento_motorista'];
            $schedule['getPlacaCarreta'] = $data['placa_carreta'];
            $schedule['getOperationId'] = $data['operation_type_id'];
            $schedule['getOperator'] = $data['operator'];
            $schedule['getChecker'] = $data['checker'];
            $schedule['getLastModifiedBy'] = $data['last_modified_by'];
            $schedule['getLastModifiedDate'] = date("d/m/Y H:i:s", strtotime($data['last_modified_date']));       
            array_push($schedules, $schedule);
        }

        return $schedules;
    }

    public function loadData($records){

        $schedules = array();

        while ($data = $records->fetch_assoc()){ 
            $schedule = new Schedule();
            $schedule->setId($data['id']);
            $schedule->setTransportadora($data['transportadora']);
            $schedule->setTipoVeiculo($data['tipoVeiculo']);
            $schedule->setPlacaCavalo($data['placa_cavalo']);
            $schedule->setOperacao($data['operacao']);
            $schedule->setNf($data['nf']);
            $schedule->setHoraChegada( date("d/m/Y H:i:s", strtotime($data['horaChegada'])));
            if(empty($data['horaChegada'])) $schedule->setHoraChegada('');

            $schedule->setInicioOperacao(date("d/m/Y H:i:s", strtotime($data['inicio_operacao'])));
            if(empty($data['inicio_operacao'])) $schedule->setInicioOperacao('');

            $schedule->setFimOperacao(date("d/m/Y H:i:s", strtotime($data['fim_operacao'])));
            if(empty($data['fim_operacao'])) $schedule->setFimOperacao('');

            $schedule->setNomeUsuario($data['usuario']);
            $schedule->setDataInclusao(date("d/m/Y H:i:s", strtotime($data['dataInclusao'])));
            $schedule->setPeso($data['peso']);
            $schedule->setDataAgendamento(date("d/m/Y H:i:s", strtotime($data['data_agendamento'])));
            $schedule->setSaida(date("d/m/Y H:i:s", strtotime($data['saida'])));
            if(empty($data['saida'])) $schedule->setSaida('');

            $schedule->setSeparacao($data['separacao']);
            $schedule->setShipmentId($data['shipment_id']);
            $schedule->setDoca($data['doca']);
            $schedule->setDo_s($data['do_s']);
            $schedule->setCidade($data['cidade']);
            $schedule->setCargaQtde($data['carga_qtde']);
            $schedule->setObservacao($data['observacao']);
            $schedule->setDadosGerais($data['dados_gerais']);
            $schedule->setCliente($data['cliente']);
            $schedule->setStatus($data['status']);
            $schedule->setNomeMotorista($data['nome_motorista']); 
            $schedule->setPlacaCarreta2($data['placa_carreta2']);
            $schedule->setDocumentoMotorista($data['documento_motorista']);
            $schedule->setPlacaCarreta($data['placa_carreta']);
            $schedule->setOperationId($data['operation_type_id']);
            $schedule->setOperator($data['operator']);
            $schedule->setChecker($data['checker']);
            $schedule->setLastModifiedBy($data['last_modified_by']);
            $schedule->setLastModifiedDate(date("d/m/Y H:i:s", strtotime($data['last_modified_date'])));

            $schedule->setAttPickingStatus($data['attatchment_picking_status']);

            $schedule->setAttInvoiceStatus($data['attatchment_invoice_status']);
            $schedule->setAttCertificateStatus($data['attatchment_certificate_status']);
            $schedule->setAttBoardingStatus($data['attatchment_boarding_status']);
            $schedule->setAttOtherStatus($data['attatchment_other_status']);
    
            array_push($schedules, $schedule);
        }

        return $schedules;
    }

    public function getIdLastError(){
    
        $result = $this->scheduleRepository->getLastError();
        $id = '';

        while ($data = $result->fetch_assoc()){ 
           $id = $data['id'];
        }

        return $id;
    }
}

?>