
<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Labsoft\Models\Schedule;

class ScheduleRepository{

    private $mySql;

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findAll(){

        try{
            if ($this->canUseEloquent()) {
                return Schedule::query()
                    ->select($this->getSelectFields())
                    ->get()
                    ->toArray();
            }

            $sql = "SELECT id,data_agendamento,transportadora,status,tipoVeiculo,placa_cavalo,operacao,nf,horaChegada,inicio_operacao,fim_operacao,usuario,dataInclusao,peso,saida,separacao,shipment_id,do_s,cidade,carga_qtde,observacao,dados_gerais,cliente,doca, nome_motorista, placa_carreta2, documento_motorista, placa_carreta, operation_type_id,operator,checker,last_modified_by,last_modified_date,attatchment_picking_status,attatchment_invoice_status,attatchment_certificate_status,attatchment_boarding_status,attatchment_other_status,scaneado,carga_em_qualidade,carregando_ou_rejeitado
                    FROM janela";  

            $result = $this->mySql->query($sql);

            return $result;

        }catch(Exception $e){
            return false;
        }
    }

    public function findByClient($client){

        try{
            if ($this->canUseEloquent()) {
                return Schedule::query()
                    ->select($this->getSelectFields())
                    ->where('cliente', $client)
                    ->orderBy('data_agendamento')
                    ->get()
                    ->toArray();
            }

            $sql = "SELECT id,data_agendamento,transportadora,status,tipoVeiculo,placa_cavalo,operacao,nf,horaChegada,inicio_operacao,fim_operacao,usuario,dataInclusao,peso,saida,separacao,shipment_id,do_s,cidade,carga_qtde,observacao,dados_gerais,cliente,doca, nome_motorista, placa_carreta2, documento_motorista, placa_carreta, operation_type_id,operator,checker,last_modified_by,last_modified_date,attatchment_picking_status,attatchment_invoice_status,attatchment_certificate_status,attatchment_boarding_status,attatchment_other_status,scaneado,carga_em_qualidade,carregando_ou_rejeitado
                    FROM janela
                    WHERE cliente = '".$client."'
                    ORDER BY data_agendamento";  

            return $this->mySql->query($sql);

        }catch(Exception $e){
            return false;
        }
    }

    public function findByClientStartDateAndEndDate($client, $startDate, $endDate){

        try{
            if ($this->canUseEloquent()) {
                return Schedule::query()
                    ->select($this->getSelectFields())
                    ->where('cliente', $client)
                    ->where('data_agendamento', '>=', $startDate)
                    ->where('data_agendamento', '<=', $endDate)
                    ->orderBy('data_agendamento')
                    ->get()
                    ->toArray();
            }

            $sql = "SELECT id,data_agendamento,transportadora,status,tipoVeiculo,placa_cavalo,operacao,nf,horaChegada,inicio_operacao,fim_operacao,usuario,dataInclusao,peso,saida,separacao,shipment_id,do_s,cidade,carga_qtde,observacao,dados_gerais,cliente,doca, nome_motorista, placa_carreta2, documento_motorista, placa_carreta, operation_type_id,operator,checker,last_modified_by,last_modified_date,attatchment_picking_status,attatchment_invoice_status,attatchment_certificate_status,attatchment_boarding_status,attatchment_other_status,scaneado,carga_em_qualidade,carregando_ou_rejeitado
                    FROM janela
                    WHERE cliente = '".$client."'
                    AND data_agendamento >= '".$startDate."'
                    AND data_agendamento <= '".$endDate."'
                    ORDER BY data_agendamento";  

            return $this->mySql->query($sql);

        }catch(Exception $e){
            return false;
        }
    }

    public function findByClientStatusStartDateAndEndDate($client, $status, $startDate, $endDate){

        try{
            if ($this->canUseEloquent()) {
                return Schedule::query()
                    ->select($this->getSelectFields())
                    ->where('cliente', $client)
                    ->where('status', $status)
                    ->where('data_agendamento', '>=', $startDate)
                    ->where('data_agendamento', '<=', $endDate)
                    ->orderBy('data_agendamento')
                    ->get()
                    ->toArray();
            }

            $sql = "SELECT id,data_agendamento,transportadora,status,tipoVeiculo,placa_cavalo,operacao,nf,horaChegada,inicio_operacao,fim_operacao,usuario,dataInclusao,peso,saida,separacao,shipment_id,do_s,cidade,carga_qtde,observacao,dados_gerais,cliente,doca, nome_motorista, placa_carreta2, documento_motorista, placa_carreta, operation_type_id,operator,checker,last_modified_by,last_modified_date,attatchment_picking_status,attatchment_invoice_status,attatchment_certificate_status,attatchment_boarding_status,attatchment_other_status,scaneado,carga_em_qualidade,carregando_ou_rejeitado
                    FROM janela
                    WHERE cliente = '".$client."'
                    AND status = '".$status."' 
                    AND data_agendamento >= '".$startDate."'
                    AND data_agendamento <= '".$endDate."'
                    ORDER BY data_agendamento";  

            return $this->mySql->query($sql);

        }catch(Exception $e){
            return false;
        }
    }

    public function findById($id){

        try{
            if ($this->canUseEloquent()) {
                return Schedule::query()
                    ->select($this->getSelectFields())
                    ->where('id', (int) $id)
                    ->limit(1)
                    ->get()
                    ->toArray();
            }

            $sql = "SELECT id,data_agendamento,transportadora,status,tipoVeiculo,placa_cavalo,operacao,nf,horaChegada,inicio_operacao,fim_operacao,usuario,dataInclusao,peso,saida,separacao,shipment_id,do_s,cidade,carga_qtde,observacao,dados_gerais,cliente,doca, nome_motorista, placa_carreta2, documento_motorista, placa_carreta, operation_type_id,operator,checker,last_modified_by,last_modified_date,attatchment_picking_status,attatchment_invoice_status,attatchment_certificate_status,attatchment_boarding_status,attatchment_other_status,scaneado,carga_em_qualidade,carregando_ou_rejeitado
                    FROM janela
                    WHERE id = '".$id."'";  

            return $this->mySql->query($sql);

        }catch(Exception $e){
            return false;
        }
    }

    public function delete($id){

        try {
            if ($this->canUseEloquent()) {
                Schedule::query()
                    ->where('id', (int) $id)
                    ->delete();
                return 'DELETED';
            }

            $sql = "DELETE FROM janela WHERE Id = ".$id; 
            $this->mySql->query($sql);
            return 'DELETED';

        } catch (Exception $e) {
            return 'DELETE_ERROR';
        }
    }

    public function save($schedule){

        try{
            if ($this->canUseEloquent()) {
                $data = array();

                if (!empty($schedule->getDataAgendamento())) $data['data_agendamento'] = $schedule->getDataAgendamento();
                $data['transportadora'] = $schedule->getTransportadora();
                $data['status'] = $schedule->getStatus();
                $data['tipoVeiculo'] = $schedule->getTipoVeiculo();
                $data['placa_cavalo'] = $schedule->getPlacaCavalo();
                $data['operacao'] = $schedule->getOperacao();
                $data['nf'] = $schedule->getNf();
                if (!empty($schedule->getHoraChegada())) $data['horaChegada'] = $schedule->getHoraChegada();
                if (!empty($schedule->getInicioOperacao())) $data['inicio_operacao'] = $schedule->getInicioOperacao();
                if (!empty($schedule->getFimOperacao())) $data['fim_operacao'] = $schedule->getFimOperacao();
                $data['usuario'] = $schedule->getNomeusuario();
                $data['dataInclusao'] = $schedule->getDataInclusao();
                $data['peso'] = $schedule->getPeso();
                if (!empty($schedule->getSaida())) $data['saida'] = $schedule->getSaida();
                $data['separacao'] = $schedule->getSeparacao();
                $data['shipment_id'] = $schedule->getShipmentId();
                $data['do_s'] = $schedule->getDo_s();
                $data['cidade'] = $schedule->getCidade();
                $data['carga_qtde'] = (int) $schedule->getCargaQtde();
                $data['observacao'] = $schedule->getObservacao();
                $data['dados_gerais'] = $schedule->getDadosGerais();
                $data['cliente'] = $schedule->getCliente();
                $data['doca'] = $schedule->getDoca();
                $data['nome_motorista'] = $schedule->getNomeMotorista();
                $data['placa_carreta2'] = $schedule->getPlacaCarreta2();
                $data['documento_motorista'] = $schedule->getDocumentoMotorista();
                $data['placa_carreta'] = $schedule->getPlacaCarreta();
                $data['operation_type_id'] = (int) $schedule->getOperationId();
                $data['operator'] = $schedule->getOperator();
                $data['created_date'] = date('Y-m-d H:i:s');
                $data['attatchment_picking_status'] = $schedule->getAttPickingStatus();
                $data['attatchment_invoice_status'] = $schedule->getAttInvoiceStatus();
                $data['attatchment_certificate_status'] = $schedule->getAttCertificateStatus();
                $data['attatchment_boarding_status'] = $schedule->getAttBoardingStatus();
                $data['attatchment_other_status'] = $schedule->getAttOtherStatus();
                $data['checker'] = $schedule->getChecker();
                $data['scaneado'] = $schedule->getScaneado();
                $data['carga_em_qualidade'] = $schedule->getCargaEmQualidade();
                $data['carregando_ou_rejeitado'] = $schedule->getCarregandoOuRejeitado();

                $id = Capsule::table('janela')->insertGetId($data);
                return $id;
            }

            $sql = "INSERT INTO janela SET "; 
            if(!empty($schedule->getDataAgendamento())) $sql .= "data_agendamento = '".$schedule->getDataAgendamento()."',";
            $sql .= "transportadora = '".$schedule->getTransportadora()."',";
            $sql .= "status = '".$schedule->getStatus()."',";
            $sql .= "tipoVeiculo = '".$schedule->getTipoVeiculo()."',";
            $sql .= "placa_cavalo = '".$schedule->getPlacaCavalo()."',";
            $sql .= "operacao = '".$schedule->getOperacao()."',";
            $sql .= "nf = '".$schedule->getNf()."',";
            if(!empty($schedule->getHoraChegada()))  $sql .= "horaChegada = '".$schedule->getHoraChegada()."',";
            if(!empty($schedule->getInicioOperacao())) $sql .= "inicio_operacao = '".$schedule->getInicioOperacao()."',";
            if(!empty($schedule->getFimOperacao())) $sql .= "fim_operacao = '".$schedule->getFimOperacao()."',";
            $sql .= "usuario = '".$schedule->getNomeusuario()."',";
            $sql .= "dataInclusao = '".$schedule->getDataInclusao()."',";
            $sql .= "peso = '".$schedule->getPeso()."',";
            if(!empty($schedule->getSaida())) $sql .= "saida = '".$schedule->getSaida()."',";
            $sql .= "separacao = '".$schedule->getSeparacao()."',";
            $sql .= "shipment_id ='".$schedule->getShipmentId()."',";
            $sql .= "do_s = '".$schedule->getDo_s()."',";
            $sql .= "cidade = '".$schedule->getCidade()."',";
            $sql .= "carga_qtde = ".$schedule->getCargaQtde().",";
            $sql .= "observacao = '".$schedule->getObservacao()."',";
            $sql .= "dados_gerais = '".$schedule->getDadosGerais()."',";
            $sql .= "cliente = '".$schedule->getCliente()."',";
            $sql .= "doca = '".$schedule->getDoca()."',";
            $sql .= "nome_motorista = '".$schedule->getNomeMotorista()."',";
            $sql .= "placa_carreta2 = '".$schedule->getPlacaCarreta2()."',";
            $sql .= "documento_motorista = '".$schedule->getDocumentoMotorista()."',";
            $sql .= "placa_carreta = '".$schedule->getPlacaCarreta()."', ";
            $sql .= "operation_type_id = ".$schedule->getOperationId().", ";
            $sql .= "operator = '".$schedule->getOperator()."', ";
            $sql .= "created_date = '".date('Y-m-d H:i:s')."',";

            $sql .= "attatchment_picking_status = '".$schedule->getAttPickingStatus()."', ";
            $sql .= "attatchment_invoice_status = '".$schedule->getAttInvoiceStatus()."', ";
            $sql .= "attatchment_certificate_status = '".$schedule->getAttCertificateStatus()."', ";
            $sql .= "attatchment_boarding_status = '".$schedule->getAttBoardingStatus()."', ";
            $sql .= "attatchment_other_status = '".$schedule->getAttOtherStatus()."', ";

            $sql .= "checker = '".$schedule->getChecker()."', ";
                $sql .= "scaneado = '".$schedule->getScaneado()."', ";
                $sql .= "carga_em_qualidade = '".$schedule->getCargaEmQualidade()."', ";
                $sql .= "carregando_ou_rejeitado = '".$schedule->getCarregandoOuRejeitado()."'";

                $result = $this->mySql->query($sql);

            if(!$result) return 'SAVE_ERROR';

            return $this->mySql->insert_id;

        }catch(Exception $e){
           
            return 'SAVE_ERROR';
        }
    }

    public function updateAttAction($scheduleId, $field, $actionValue){

        try{
            if ($this->canUseEloquent()) {
                Capsule::table('janela')
                    ->where('id', (int) $scheduleId)
                    ->update([
                        $field => $actionValue,
                        'last_modified_by' => $_SESSION['nome'],
                    ]);

                return true;
            }

            $sql = "UPDATE janela SET ";
            $sql .= $field . " = '".$actionValue."', ";
            $sql .= "last_modified_by = '".$_SESSION['nome']."'";
            $sql .= " WHERE ID = ".$scheduleId;  

            $result = $this->mySql->query($sql);
            return true;

        }catch(Exception $e){
            return false;
        }
    }

    public function updateById($schedule, $id){

        try{
            if ($this->canUseEloquent()) {
                $data = array();

                if (!empty($schedule->getDataAgendamento())) $data['data_agendamento'] = $schedule->getDataAgendamento();
                $data['transportadora'] = $schedule->getTransportadora();
                $data['status'] = $schedule->getStatus();
                $data['tipoVeiculo'] = $schedule->getTipoVeiculo();
                $data['placa_cavalo'] = $schedule->getPlacaCavalo();
                $data['operacao'] = $schedule->getOperacao();
                $data['nf'] = $schedule->getNf();
                if (!empty($schedule->getHoraChegada())) $data['horaChegada'] = $schedule->getHoraChegada();
                if (!empty($schedule->getInicioOperacao())) $data['inicio_operacao'] = $schedule->getInicioOperacao();
                if (!empty($schedule->getFimOperacao())) $data['fim_operacao'] = $schedule->getFimOperacao();
                $data['usuario'] = $schedule->getNomeusuario();
                $data['peso'] = $schedule->getPeso();
                if (!empty($schedule->getSaida())) $data['saida'] = $schedule->getSaida();
                $data['separacao'] = $schedule->getSeparacao();
                $data['shipment_id'] = $schedule->getShipmentId();
                $data['do_s'] = $schedule->getDo_s();
                $data['cidade'] = $schedule->getCidade();
                $data['carga_qtde'] = (int) $schedule->getCargaQtde();
                $data['observacao'] = $schedule->getObservacao();
                $data['dados_gerais'] = $schedule->getDadosGerais();
                $data['cliente'] = $schedule->getCliente();
                $data['doca'] = $schedule->getDoca();
                $data['nome_motorista'] = $schedule->getNomeMotorista();
                $data['placa_carreta2'] = $schedule->getPlacaCarreta2();
                $data['documento_motorista'] = $schedule->getDocumentoMotorista();
                $data['placa_carreta'] = $schedule->getPlacaCarreta();
                $data['operation_type_id'] = (int) $schedule->getOperationId();
                $data['operator'] = $schedule->getOperator();
                $data['last_modified_date'] = date('Y-m-d H:i:s');
                $data['last_modified_by'] = $_SESSION['nome'];
                $data['attatchment_picking_status'] = $schedule->getAttPickingStatus();
                $data['attatchment_invoice_status'] = $schedule->getAttInvoiceStatus();
                $data['attatchment_certificate_status'] = $schedule->getAttCertificateStatus();
                $data['attatchment_boarding_status'] = $schedule->getAttBoardingStatus();
                $data['attatchment_other_status'] = $schedule->getAttOtherStatus();
                $data['checker'] = $schedule->getChecker();
                $data['scaneado'] = $schedule->getScaneado();
                $data['carga_em_qualidade'] = $schedule->getCargaEmQualidade();
                $data['carregando_ou_rejeitado'] = $schedule->getCarregandoOuRejeitado();

        Capsule::table('janela')
            ->where('id', (int) $id)
            ->update($data);

                return 'UPDATED';
            }

            $sql = "UPDATE janela SET ";
            if(!empty($schedule->getDataAgendamento())) $sql .= "data_agendamento = '".$schedule->getDataAgendamento()."',";
            $sql .= "transportadora = '".$schedule->getTransportadora()."',";
            $sql .= "status = '".$schedule->getStatus()."',";
            $sql .= "tipoVeiculo = '".$schedule->getTipoVeiculo()."',";
            $sql .= "placa_cavalo = '".$schedule->getPlacaCavalo()."',";
            $sql .= "operacao = '".$schedule->getOperacao()."',";
            $sql .= "nf = '".$schedule->getNf()."',";
            if(!empty($schedule->getHoraChegada())) $sql .= "horaChegada = '".$schedule->getHoraChegada()."',";
            if(!empty($schedule->getInicioOperacao())) $sql .= "inicio_operacao = '".$schedule->getInicioOperacao()."',";
            if(!empty($schedule->getFimOperacao())) $sql .= "fim_operacao = '".$schedule->getFimOperacao()."',";
            $sql .= "usuario = '".$schedule->getNomeusuario()."',";
            $sql .= "peso = '".$schedule->getPeso()."',";
            if(!empty($schedule->getSaida())) $sql .= "saida = '".$schedule->getSaida()."',";
            $sql .= "separacao = '".$schedule->getSeparacao()."',";
            $sql .= "shipment_id ='".$schedule->getShipmentId()."',";
            $sql .= "do_s = '".$schedule->getDo_s()."',";
            $sql .= "cidade = '".$schedule->getCidade()."',";
            $sql .= "carga_qtde = ".$schedule->getCargaQtde().",";
            $sql .= "observacao = '".$schedule->getObservacao()."',";
            $sql .= "dados_gerais = '".$schedule->getDadosGerais()."',";
            $sql .= "cliente = '".$schedule->getCliente()."',";
            $sql .= "doca = '".$schedule->getDoca()."',";
            $sql .= "nome_motorista = '".$schedule->getNomeMotorista()."', ";
            $sql .= "placa_carreta2 = '".$schedule->getPlacaCarreta2()."',";
            $sql .= "documento_motorista = '".$schedule->getDocumentoMotorista()."',";
            $sql .= "placa_carreta = '".$schedule->getPlacaCarreta()."',";
            $sql .= "operation_type_id = ".$schedule->getOperationId().",";
            $sql .= "operator = '".$schedule->getOperator()."',";
            $sql .= "last_modified_date = '".date('Y-m-d H:i:s')."',";
            $sql .= "last_modified_by = '".$_SESSION['nome']."',";

            $sql .= "attatchment_picking_status = '".$schedule->getAttPickingStatus()."', ";
            $sql .= "attatchment_invoice_status = '".$schedule->getAttInvoiceStatus()."', ";
            $sql .= "attatchment_certificate_status = '".$schedule->getAttCertificateStatus()."', ";
            $sql .= "attatchment_boarding_status = '".$schedule->getAttBoardingStatus()."', ";
            $sql .= "attatchment_other_status = '".$schedule->getAttOtherStatus()."', ";

            $sql .= "checker = '".$schedule->getChecker()."', ";
                $sql .= "scaneado = '".$schedule->getScaneado()."', ";
                $sql .= "carga_em_qualidade = '".$schedule->getCargaEmQualidade()."', ";
                $sql .= "carregando_ou_rejeitado = '".$schedule->getCarregandoOuRejeitado()."'";
                $sql .= " WHERE ID = ".$id;  

            $result = $this->mySql->query($sql);
            return 'UPDATED';

        }catch(Exception $e){
            return 'SAVE_ERROR';
        }
    }

    public function getLastError(){

        try{
            if ($this->canUseEloquent()) {
                return Capsule::table('logError')
                    ->select(['id'])
                    ->orderBy('id', 'desc')
                    ->limit(1)
                    ->get()
                    ->map(function($row){ return (array) $row; })
                    ->toArray();
            }

            $sql = "SELECT id FROM logError order by id desc limit 1";  
            $result = $this->mySql->query($sql);

            return $result;

        }catch(Exception $e){
            return false;
        }
    }

    public function findByClienteAndStartDateAndEndDateAndStatus($startDate, $endDate, $client, $status){
        try {
            if ($this->canUseEloquent()) {
                return Capsule::table('janela as jan')
                    ->select([
                        'jan.id AS janela_id',
                        'os.name AS operation_name',
                        'horaChegada',
                        'inicio_operacao',
                        'fim_operacao',
                        'saida',
                    ])
                    ->join('operation_type as ot', 'jan.operation_type_id', '=', 'ot.id')
                    ->join('operation_source as os', 'ot.operation_source_id', '=', 'os.id')
                    ->where('jan.cliente', $client)
                    ->where('horaChegada', '>=', $startDate)
                    ->where('horaChegada', '<=', $endDate)
                    ->where('jan.status', $status)
                    ->get()
                    ->map(function($row){ return (array) $row; })
                    ->toArray();
            }

            $sql = "SELECT jan.id AS janela_id, os.name AS operation_name, horaChegada, inicio_operacao, fim_operacao, saida 
                    FROM janela AS jan 
                    INNER JOIN operation_type AS ot ON operation_type_id = ot.id
                    INNER JOIN operation_source AS os ON ot.operation_source_id = os.id
                    WHERE jan.cliente = '".$client."' 
                     AND horaChegada >= '".$startDate."' 
                     AND horaChegada <= '".$endDate."'
                     AND jan.status = '".$status."'";

            return $this->mySql->query($sql);

        } catch (Exception $e) {
            return false;
        }
    }

    private function canUseEloquent(){
        return class_exists(Schedule::class) && class_exists(Capsule::class);
    }

    private function getSelectFields(){
        return [
            'id',
            'data_agendamento',
            'transportadora',
            'status',
            'tipoVeiculo',
            'placa_cavalo',
            'operacao',
            'nf',
            'horaChegada',
            'inicio_operacao',
            'fim_operacao',
            'usuario',
            'dataInclusao',
            'peso',
            'saida',
            'separacao',
            'shipment_id',
            'do_s',
            'cidade',
            'carga_qtde',
            'observacao',
            'dados_gerais',
            'cliente',
            'doca',
            'nome_motorista',
            'placa_carreta2',
            'documento_motorista',
            'placa_carreta',
            'operation_type_id',
            'operator',
            'checker',
            'last_modified_by',
            'last_modified_date',
            'attatchment_picking_status',
        'attatchment_invoice_status',
        'attatchment_certificate_status',
        'attatchment_boarding_status',
        'attatchment_other_status',
        'scaneado',
        'carga_em_qualidade',
        'carregando_ou_rejeitado',
    ];
    }
}
?>
