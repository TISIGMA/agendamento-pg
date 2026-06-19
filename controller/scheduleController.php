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
        $this->scheduleLogRepository = new ScheduleLogRepository($this->mySql);
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

            if($this->countRecords($result) == 0) return;

            $data = $this->loadData($result);
            $this->scheduleRepository->delete($id);

            // salvar registro de log de exclusão
            return $this->scheduleLogRepository->save($data[0]);
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

        $originalStartDate = $startDate;
        $originalEndDate = $endDate;
        $startDate = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $startDate )));
        $endDate = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $endDate )));

        if($status == 'Todos'){
            $result = $this->scheduleRepository->findByClientStartDateAndEndDate($client, $startDate, $endDate);
        }else {
            $result = $this->scheduleRepository->findByClientStatusStartDateAndEndDate($client, $status, $startDate, $endDate);
        }

        $data = $this->loadDataByNameValue($result);

        // #region debug-point B:schedule-query-result
        $__dbg=@parse_ini_file(__DIR__ . '/../.dbg/schedule-empty-data.env'); @file_get_contents(($__dbg['DEBUG_SERVER_URL'] ?? 'http://127.0.0.1:7777/event'), false, stream_context_create(['http'=>['method'=>'POST','header'=>"Content-Type: application/json\r\n",'content'=>json_encode(['sessionId'=>($__dbg['DEBUG_SESSION_ID'] ?? 'schedule-empty-data'),'runId'=>'pre-fix','hypothesisId'=>'B','location'=>'controller/scheduleController.php','msg'=>'[DEBUG] schedule query result','data'=>['client'=>$client,'status'=>$status,'original_start'=>$originalStartDate,'original_end'=>$originalEndDate,'query_start'=>$startDate,'query_end'=>$endDate,'result_count'=>count($data),'first_id'=>(count($data) > 0 && isset($data[0]['getId']) ? $data[0]['getId'] : null),'session_customer'=>(isset($_SESSION['customerName']) ? $_SESSION['customerName'] : null)],'ts'=>round(microtime(true)*1000)])]]));
        // #endregion

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

        if($this->countRecords($result) == 0) return new ColumnsPreference();

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

        foreach ($this->getRecordsIterator($result) as $data){
            $createdDate = $this->getRecordValue($data, 'created_date');
            $date = ($createdDate == null || str_contains($createdDate, '0000')) ? '' : date("d/m/Y H:i", strtotime($createdDate));

            $id = $this->getRecordValue($data, 'id');
            $paths[$id] = [
                'id' => $id,
                'type'=> $this->getRecordValue($data, 'type'),
                'path' => $this->getRecordValue($data, 'path'),
                'datetime' => $date
            ];
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
        $schedule->setScaneado(isset($post['scaneado']) ? $post['scaneado'] : 'Não');
        $schedule->setCargaEmQualidade(isset($post['carga_em_qualidade']) ? $post['carga_em_qualidade'] : 'Não');

        return $schedule;
    }

    public function loadPreferenceData($records){

        $columnsPreference = new ColumnsPreference();

        foreach ($this->getRecordsIterator($records) as $data){
            $columnsPreference->setId($this->getRecordValue($data, 'id'));
            $columnsPreference->setPreference($this->getRecordValue($data, 'preference'));
            $columnsPreference->setUserId($this->getRecordValue($data, 'userId'));
            break;
        }

        return $columnsPreference;
    }

    public function loadDataByNameValue($records){

        $schedules = array();

        foreach ($this->getRecordsIterator($records) as $data){
            $schedule = array();

            $schedule['getId'] = $this->getRecordValue($data, 'id');
            $schedule['getTransportadora'] = $this->getRecordValue($data, 'transportadora');
            $schedule['getTipoVeiculo'] = $this->getRecordValue($data, 'tipoVeiculo');
            $schedule['getPlacaCavalo'] = $this->getRecordValue($data, 'placa_cavalo');
            $schedule['getOperacao'] = $this->getRecordValue($data, 'operacao');
            $schedule['getNf'] = $this->getRecordValue($data, 'nf');

            $schedule['getHoraChegada'] = $this->formatDateTime($this->getRecordValue($data, 'horaChegada'));
            $schedule['getInicioOperacao'] = $this->formatDateTime($this->getRecordValue($data, 'inicio_operacao'));
            $schedule['getFimOperacao'] = $this->formatDateTime($this->getRecordValue($data, 'fim_operacao'));

            $schedule['getNomeUsuario'] = $this->getRecordValue($data, 'usuario');
            $schedule['getDataInclusao'] = $this->formatDateTime($this->getRecordValue($data, 'dataInclusao'));
            $schedule['getPeso'] = $this->getRecordValue($data, 'peso');
            $schedule['getDataAgendamento'] = $this->formatDateTime($this->getRecordValue($data, 'data_agendamento'));

            $schedule['getSaida'] = $this->formatDateTime($this->getRecordValue($data, 'saida'));
            $schedule['getSeparacao'] = $this->getRecordValue($data, 'separacao');
            $schedule['getShipmentId'] = $this->getRecordValue($data, 'shipment_id');
            $schedule['getDoca'] = $this->getRecordValue($data, 'doca');
            $schedule['getDo_s'] = $this->getRecordValue($data, 'do_s');
            $schedule['getCidade'] = $this->getRecordValue($data, 'cidade');
            $schedule['getCargaQtde'] = $this->getRecordValue($data, 'carga_qtde');
            $schedule['getObservacao'] = $this->getRecordValue($data, 'observacao');
            $schedule['getDadosGerais'] = $this->getRecordValue($data, 'dados_gerais');
            $schedule['getCliente'] = $this->getRecordValue($data, 'cliente');
            $schedule['getStatus'] = $this->getRecordValue($data, 'status');
            $schedule['getNomeMotorista'] = $this->getRecordValue($data, 'nome_motorista'); 
            $schedule['getPlacaCarreta2'] = $this->getRecordValue($data, 'placa_carreta2');
            $schedule['getDocumentoMotorista'] = $this->getRecordValue($data, 'documento_motorista');
            $schedule['getPlacaCarreta'] = $this->getRecordValue($data, 'placa_carreta');
            $schedule['getOperationId'] = $this->getRecordValue($data, 'operation_type_id');
            $schedule['getOperator'] = $this->getRecordValue($data, 'operator');
            $schedule['getChecker'] = $this->getRecordValue($data, 'checker');
        $schedule['getLastModifiedBy'] = $this->getRecordValue($data, 'last_modified_by');
        $schedule['getLastModifiedDate'] = $this->formatDateTime($this->getRecordValue($data, 'last_modified_date'));
        $schedule['getScaneado'] = $this->getRecordValue($data, 'scaneado');
        $schedule['getCargaEmQualidade'] = $this->getRecordValue($data, 'carga_em_qualidade');

            array_push($schedules, $schedule);
        }

        return $schedules;
    }

    public function loadData($records){

        $schedules = array();

        foreach ($this->getRecordsIterator($records) as $data){
            $schedule = new Schedule();
            $schedule->setId($this->getRecordValue($data, 'id'));
            $schedule->setTransportadora($this->getRecordValue($data, 'transportadora'));
            $schedule->setTipoVeiculo($this->getRecordValue($data, 'tipoVeiculo'));
            $schedule->setPlacaCavalo($this->getRecordValue($data, 'placa_cavalo'));
            $schedule->setOperacao($this->getRecordValue($data, 'operacao'));
            $schedule->setNf($this->getRecordValue($data, 'nf'));
            $schedule->setHoraChegada($this->formatDateTime($this->getRecordValue($data, 'horaChegada')));

            $schedule->setInicioOperacao($this->formatDateTime($this->getRecordValue($data, 'inicio_operacao')));
            $schedule->setFimOperacao($this->formatDateTime($this->getRecordValue($data, 'fim_operacao')));

            $schedule->setNomeUsuario($this->getRecordValue($data, 'usuario'));
            $schedule->setDataInclusao($this->formatDateTime($this->getRecordValue($data, 'dataInclusao')));
            $schedule->setPeso($this->getRecordValue($data, 'peso'));
            $schedule->setDataAgendamento($this->formatDateTime($this->getRecordValue($data, 'data_agendamento')));
            $schedule->setSaida($this->formatDateTime($this->getRecordValue($data, 'saida')));

            $schedule->setSeparacao($this->getRecordValue($data, 'separacao'));
            $schedule->setShipmentId($this->getRecordValue($data, 'shipment_id'));
            $schedule->setDoca($this->getRecordValue($data, 'doca'));
            $schedule->setDo_s($this->getRecordValue($data, 'do_s'));
            $schedule->setCidade($this->getRecordValue($data, 'cidade'));
            $schedule->setCargaQtde($this->getRecordValue($data, 'carga_qtde'));
            $schedule->setObservacao($this->getRecordValue($data, 'observacao'));
            $schedule->setDadosGerais($this->getRecordValue($data, 'dados_gerais'));
            $schedule->setCliente($this->getRecordValue($data, 'cliente'));
            $schedule->setStatus($this->getRecordValue($data, 'status'));
            $schedule->setNomeMotorista($this->getRecordValue($data, 'nome_motorista')); 
            $schedule->setPlacaCarreta2($this->getRecordValue($data, 'placa_carreta2'));
            $schedule->setDocumentoMotorista($this->getRecordValue($data, 'documento_motorista'));
            $schedule->setPlacaCarreta($this->getRecordValue($data, 'placa_carreta'));
            $schedule->setOperationId($this->getRecordValue($data, 'operation_type_id'));
            $schedule->setOperator($this->getRecordValue($data, 'operator'));
            $schedule->setChecker($this->getRecordValue($data, 'checker'));
            $schedule->setLastModifiedBy($this->getRecordValue($data, 'last_modified_by'));
            $schedule->setLastModifiedDate($this->formatDateTime($this->getRecordValue($data, 'last_modified_date')));
            $schedule->setScaneado($this->getRecordValue($data, 'scaneado'));
            $schedule->setCargaEmQualidade($this->getRecordValue($data, 'carga_em_qualidade'));

            $schedule->setAttPickingStatus($this->getRecordValue($data, 'attatchment_picking_status'));
            $schedule->setAttInvoiceStatus($this->getRecordValue($data, 'attatchment_invoice_status'));
            $schedule->setAttCertificateStatus($this->getRecordValue($data, 'attatchment_certificate_status'));
            $schedule->setAttBoardingStatus($this->getRecordValue($data, 'attatchment_boarding_status'));
            $schedule->setAttOtherStatus($this->getRecordValue($data, 'attatchment_other_status'));
    
            array_push($schedules, $schedule);
        }

        return $schedules;
    }

    public function getIdLastError(){
    
        $result = $this->scheduleRepository->getLastError();
        $id = '';

        foreach ($this->getRecordsIterator($result) as $data){
            $id = $this->getRecordValue($data, 'id');
        }

        return $id;
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
