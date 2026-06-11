
<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Labsoft\Models\ScheduleLog;

class ScheduleLogRepository{

    private $mySql;

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findAll(){

        try{
            if ($this->canUseEloquent()) {
                return Capsule::table('janela_log')
                    ->select($this->getSelectFieldsAll())
                    ->get()
                    ->map(function($row){ return (array) $row; })
                    ->toArray();
            }

            $sql = "SELECT id,data_agendamento,transportadora,status,tipoVeiculo,placa_cavalo,operacao,nf,horaChegada,inicio_operacao,fim_operacao,usuario,dataInclusao,peso,saida,separacao,shipment_id,do_s,cidade,carga_qtde,observacao,dados_gerais,cliente,doca, nome_motorista, placa_carreta2, documento_motorista, placa_carreta, operation_type_id,operator,checker,action,user_action,date_time_action
                    FROM janela_log";  

            $result = $this->mySql->query($sql);

            return $result;

        }catch(Exception $e){
            return false;
        }
    }

    public function findByClientAndShipmentId($client, $shipmentId){

        try{
            if ($this->canUseEloquent()) {
                return Capsule::table('janela_log')
                    ->select($this->getSelectFieldsByShipment())
                    ->where('shipment_id', $shipmentId)
                    ->where('cliente', $client)
                    ->orderBy('date_time_action')
                    ->get()
                    ->map(function($row){ return (array) $row; })
                    ->toArray();
            }

            $sql = "SELECT id,data_agendamento,transportadora,status,tipoVeiculo,placa_cavalo,operacao,nf,horaChegada,inicio_operacao,fim_operacao,usuario,dataInclusao,peso,saida,separacao,shipment_id,do_s,cidade,carga_qtde,observacao,dados_gerais,cliente,doca, nome_motorista, placa_carreta2, documento_motorista, placa_carreta,operator,checker,attatchment_picking_status,attatchment_invoice_status,attatchment_certificate_status,attatchment_boarding_status,attatchment_other_status,schedule_id,action,user_action,date_time_action
                    FROM janela_log
                    WHERE shipment_id = '".$shipmentId."' AND cliente = '".$client."' 
                    ORDER BY date_time_action";

            return $this->mySql->query($sql);

        }catch(Exception $e){
            return false;
        }
    }

    public function save($log){


        try{
            if ($this->canUseEloquent()) {
                $dateSchedule = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $log->getDataAgendamento())));
                $dateInsert = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $log->getDataInclusao())));
                $startOp = (!empty($log->getInicioOperacao())) ? date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $log->getInicioOperacao()))) : null;     
                $timeArrive = (!empty($log->getHoraChegada())) ? date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $log->getHoraChegada()))) : null;
                $endOp = (!empty($log->getFimOperacao())) ? date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $log->getFimOperacao()))) : null;
                $exit = (!empty($log->getSaida())) ? date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $log->getSaida()))) : null;

                ScheduleLog::query()->create([
                    'status' => $log->getStatus(),
                    'tipoVeiculo' => $log->getTipoVeiculo(),
                    'placa_carreta' => $log->getPlacaCarreta(),
                    'operacao' => $log->getOperacao(),
                    'nf' => $log->getNf(),
                    'doca' => $log->getDoca(),
                    'usuario' => $log->getNomeusuario(),
                    'dataInclusao' => $dateInsert,
                    'inicio_operacao' => $startOp,
                    'horaChegada' => $timeArrive,
                    'fim_operacao' => $endOp,
                    'saida' => $exit,
                    'transportadora' => $log->getTransportadora(),
                    'placa_cavalo' => $log->getPlacaCavalo(),
                    'peso' => $log->getPeso(),
                    'data_agendamento' => $dateSchedule,
                    'separacao' => $log->getSeparacao(),
                    'shipment_id' => $log->getShipmentId(),
                    'do_s' => $log->getDo_s(),
                    'cidade' => $log->getCidade(),
                    'carga_qtde' => (int) $log->getCargaQtde(),
                    'observacao' => $log->getObservacao(),
                    'dados_gerais' => $log->getDadosGerais(),
                    'cliente' => $log->getCliente(),
                    'nome_motorista' => $log->getNomeMotorista(),
                    'placa_carreta2' => $log->getPlacaCarreta2(),
                    'documento_motorista' => $log->getDocumentoMotorista(),
                    'operator' => $log->getOperator(),
                    'checker' => $log->getChecker(),
                    'action' => 'delete',
                    'user_action' => $_SESSION['nome'],
                    'date_time_action' => date('Y-m-d H:i:s'),
                ]);

                return 'DELETED';
            }

            $dateSchedule = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $log->getDataAgendamento())));
            $dateInsert = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $log->getDataInclusao())));
            $startOp = (!empty($log->getInicioOperacao())) ? date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $log->getInicioOperacao()))) : '';     
            $timeArrive = (!empty($log->getHoraChegada())) ? date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $log->getHoraChegada()))) : '';
            $endOp = (!empty($log->getFimOperacao())) ? date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $log->getFimOperacao()))) : '';
            $exit = (!empty($log->getSaida())) ? date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $log->getSaida()))) : '';

            $sql = "INSERT INTO janela_log (status,tipoVeiculo,placa_carreta,operacao,nf,doca,usuario,dataInclusao,inicio_operacao,horaChegada,fim_operacao,saida,transportadora,placa_cavalo,peso,data_agendamento,separacao,shipment_id,do_s,cidade,carga_qtde,observacao,dados_gerais,cliente,nome_motorista,placa_carreta2,documento_motorista,operator,checker,action,user_action,date_time_action)"; 
            $sql .= " VALUES('".$log->getStatus()."','".$log->getTipoVeiculo()."','".$log->getPlacaCarreta()."','".$log->getOperacao()."','".$log->getNf()."','".$log->getDoca()."','".$log->getNomeusuario()."','".$dateInsert."',";
            $sql .= "'".$startOp."','".$timeArrive."','".$endOp."','".$exit."','".$log->getTransportadora()."','".$log->getPlacaCavalo()."','".$log->getPeso()."','".$dateSchedule."','".$log->getSeparacao()."','".$log->getShipmentId()."',";
            $sql .= "'".$log->getDo_s()."','".$log->getCidade()."',".$log->getCargaQtde().",'".$log->getObservacao()."','".$log->getDadosGerais()."','".$log->getCliente()."','".$log->getNomeMotorista()."','".$log->getPlacaCarreta2()."','".$log->getDocumentoMotorista()."','".$log->getOperator()."','".$log->getChecker()."',";
            $sql .= "'delete','".$_SESSION['nome']."','".date('Y-m-d H:i:s')."')";

            $result = $this->mySql->query($sql);

            if(!$result) return 'DELETE_ERROR';

            return 'DELETED';

        }catch(Exception $e){
            return 'DELETE_ERROR';
        }
    }

    private function canUseEloquent(){
        return class_exists(ScheduleLog::class) && class_exists(Capsule::class);
    }

    private function getSelectFieldsAll(){
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
            'action',
            'user_action',
            'date_time_action',
        ];
    }

    private function getSelectFieldsByShipment(){
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
            'operator',
            'checker',
            'attatchment_picking_status',
            'attatchment_invoice_status',
            'attatchment_certificate_status',
            'attatchment_boarding_status',
            'attatchment_other_status',
            'schedule_id',
            'action',
            'user_action',
            'date_time_action',
        ];
    }
}
?>
