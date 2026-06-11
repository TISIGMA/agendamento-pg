
<?php
class ScheduleLogRepository{

    private $mySql;

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findAll(){

        try{
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
}
?>