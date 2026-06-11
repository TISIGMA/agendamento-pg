<?php
include_once '../../session.php'; 

    class Usuario{
        private $id;
        private $nome;
        private $username;
        private $password;
        private $data;
        private $tipo;
        private $usuarioCriacao;
        private $systemAccess;

	public function __construct(){
        }
        public function Usuario(){
        }
        public function setId($id){
            $this->id = $id;
        }
        public function getId(){
            return $this->id;
        }
        public function setNome($nome){
            $this->nome = $nome;
        }
        public function getNome(){
            return $this->nome;
        }
        public function setUsername($username){
            $this->username = $username;
        }
        public function getUsername(){
            return $this->username;
        }
        public function setPassword($password){
            $this->password = $password;
        }
        public function getPassword(){
            return $this->password;
        }
        public function setData($data){
            $this->data = $data;
        }
        public function getData(){
            return $this->data;
        }
        public function setTipo($tipo){
            $this->tipo = $tipo;
        }
        public function getTipo(){
            return $this->tipo;
        }
        public function setUsuarioCriacao($nome){
            $this->usuarioCriacao = $nome;
        }
        public function getUsuarioCriacao(){
            return $this->usuarioCriacao;
        }
        public function setSystemAccess($systemAccess){
            $this->systemAccess = $systemAccess;
        }
        public function getSystemAccess(){
            return $this->systemAccess;
        }

        public function salvarUsuario($mysql){
            try{
                $sql = "insert into usuario(nome, username, password, usuarioCriacao, dataInclusao, tipo) values ('".$this->getNome()."','".$this->getUsername()."','".$this->getPassword()."','".$this->getUsuarioCriacao()."', '".$this->getData()."', '".$this->getTipo()."')";
                $mysql->query($sql);

                $systemAccess = $this->getSystemAccess();

                if(count($systemAccess) > 0){
                    
                    $last_id = $mysql->insert_id;

                    if($this->addSystemAccess($mysql, $last_id, $systemAccess)) return true;
                    else return false;
                    
                }

                return true;
            }catch(Exception $e){
                return false;
            }
        }

        public function addSystemAccess($mysql, $userId, $systemAccess){

            try {
                foreach ($systemAccess as $systemId) {
                        
                    $sql = "INSERT INTO userSystems(userId, systemsId) VALUES (".$userId.", ".$systemId." )";
                    $mysql->query($sql); 
                }

                return true;

            } catch (Exception $e) {
                return false;
            }

            

        }

        public function editarUsuario($mysql,$id){
            try{
               
                $sql = "update usuario set nome = '".$this->getNome()."', username = '".$this->getUsername()." ', password = '".$this->getPassword()."', dataInclusao = '".$this->getData()."', usuarioCriacao = '".$this->getUsuarioCriacao()."', tipo = '".$this->getTipo()."' where id = ".$id;
                $mysql->query($sql);

                $sql = "SELECT id, userId, systemsId FROM userSystems 
                        WHERE userId = " . $id; 
                        
                $result = $mysql->query($sql);

                $systemIds = array();
                $userSystemIds = array();

                while ($data = $result->fetch_assoc()){ 
                    
                    array_push($userSystemIds, $data['id']);
                    array_push($systemIds, $data['systemsId']);
                }

                $diff1 = array_diff($systemIds, $this->getSystemAccess());
                $diff2 = array_diff($this->getSystemAccess(), $systemIds);

                if(count($diff1) == 0 && count($diff2) == 0) return true;
                else {
                    
                    $sql = "DELETE FROM userSystems 
                            WHERE userId = " . $id; 

                    $mysql->query($sql);

                    if($this->addSystemAccess($mysql, $id, $this->getSystemAccess())) return true;
                    else return false;
                }

                return true;
            }catch(Exception $e){
                return false;
            }
        }
        public function deletarUsuario($id, $mysql){
            try{
                $sql = 'delete from usuario where id = '.$id;
                $mysql->query($sql);
                return true;
            }catch(Exception $e){
                return false;
            }
        }
        public function listarUsuarios($mysql){
            $sql = "select id,nome,username,password,dataInclusao,tipo,usuarioCriacao from usuario";
            $result = $mysql->query($sql);
            return $result;
        }
        public function buscarUsuario($id, $mysql){
            $sql = "select usuario.id AS user_id,nome,username,password,dataInclusao,tipo,usuarioCriacao, systemsId
                    from usuario 
                    inner join userSystems on usuario.id = userId
                    where usuario.id = ".$id;

            $result = $mysql->query($sql);
            return $result;
        }
        public function alterarSenha($id, $mysql, $tipo, $nome){
            try{
                $sql = "update usuario set password = '".$this->getPassword()."' where id = ".$id;
                $mysql->query($sql);
                
                if($tipo == 'user'){
                    $sql = "update transportadora set password = '".$this->getPassword()."' where nome = '".$nome."'";
                    $mysql->query($sql);
                }
                return true;
            }catch(Exception $e){
                return false;
            }
        }

    }

    class Transportadora{
        private $nome;
        private $username;
        private $CNPJ;
        private $email;
        private $telefone;
        private $celular;   
        private $password;
        private $data;
        private $usuario;

        public function setId($id){
            $this->id = $id;
        }
        public function getId(){
            return $this->id;
        }
        public function setNome($nome){
            $this->nome = $nome;
        }
        public function getNome(){
            return $this->nome;
        }
        public function setUsername($username){
            $this->username = $username;
        }
        public function getUsername(){
            return $this->username;
        }
        public function setCNPJ($cnpj){
            $this->CNPJ = $cnpj;
        }
        public function getCNPJ(){
            return $this->CNPJ;
        }
        public function setEmail($email){
            $this->email = $email;
        }
        public function getEmail(){
            return $this->email;
        }
        public function setTelefone($telefone){
            $this->telefone = $telefone;
        }
        public function getTelefone(){
            return $this->telefone;
        }
        public function setCelular($celular){
            $this->celular = $celular;
        }
        public function getCelular(){
            return $this->celular;
        }
        public function setPassword($password){
            $this->password = $password;
        }
        public function getPassword(){
            return $this->password;
        }
        public function setData($data){
            $this->data = $data;
        }
        public function getData(){
            return $this->data;
        }
        public function setUsuario($usuario){
            $this->usuario = $usuario;
        }
        public function getUsuario(){
            return $this->usuario;
        } 

        public function salvarTransportadora($mysql){
            try{
                $sql = "insert into transportadora(nome, username, cnpj, email, telefone,celular, password, data, usuario, cliente_origem) values ('".$this->getNome()."','".$this->getUsername()."','".$this->getCNPJ()."','".$this->getEmail()."','".$this->getTelefone()."','".$this->getCelular()."','".$this->getPassword()."', '".$this->getData()."', '".$this->getUsuario()."', 'evonik')";
                $mysql->query($sql);
                return true;
            }catch(Exception $e){
                return false;
            }
        }


        public function editarTransportadora($mysql,$id){
            try{
               
                $sql = "update transportadora set nome = '".$this->getNome()."', username = '".$this->getUsername()."', cnpj = '".$this->getCNPJ()." ', email = '".$this->getEmail()."', telefone = '".$this->getTelefone()."',celular = '".$this->getCelular()."', password = '".$this->getPassword()."', data = '".$this->getData()."', usuario = '".$this->getUsuario()."' where id = ".$id;
                $mysql->query($sql);
                return true;
            }catch(Exception $e){
                return false;
            }
        }
        public function deletarTransportadora($id, $mysql){
            try{
                $sql = 'delete from transportadora where id = '.$id;
                $mysql->query($sql);
                return true;
            }catch(Exception $e){
                return false;
            }
        }
        public function listarTransportadora($mysql){
            $sql = "select id,nome,username,cnpj,email,telefone,celular,password,data,usuario from transportadora order by nome";
            $result = $mysql->query($sql);
            return $result;
        }
        public function buscarTransportadora($id, $mysql){
            $sql = "select id,nome,username,cnpj,email,telefone,celular,password,data,usuario from transportadora where id = ".$id;
            $result = $mysql->query($sql);
            return $result;
        }
        public function buscarTransportadoraNome($nome, $mysql){
            $sql = "select id,nome,username,cnpj,email,telefone,celular,password,data,usuario from transportadora where nome = '".$nome."'";
            $result = $mysql->query($sql);
            return $result;
        }
        function listarTransportadoraPorNome($mysql, $nome){
            $sql = "select id,nome,username,cnpj,email,telefone,celular,password,data,usuario from transportadora where nome = '".$nome."'";
            $result = $mysql->query($sql);
            return $result;
        }
    }


    class Horario{
        private $id;
        private $horario;
        private $posicao;
        private $status;
        private $hora;
        private $armazem;

        public function getId(){
            return $this->id;
        }
        public function setId($id){
            $this->id = $id;
        }
        public function getHorario(){
            return $this->horario;
        }
        public function setHorario($horario){
            $this->horario = $horario;
        }
        public function getPosicao(){
            return $this->posicao;
        }
        public function setPosicao($posicao){
            $this->posicao = $posicao;
        }
        public function getStatus(){
            return $this->status;
        }
        public function setStatus($status){
            $this->status = $status;
        }
        public function getHora(){
            return $this->hora;
        }
        public function setHora($hora){
            $this->hora = $hora;
        }
        public function getArmazem(){
            return $this->armazem;
        }
        public function setArmazem($armazem){
            $this->armazem = $armazem;
        }

        public function listarHorarios($mysql){
            $sql = "select id,descricao,posicao,status,hora,armazem from horario";
            $result = $mysql->query($sql);
            return $result;
        }
   
        public function buscarHorarios($usuario, $armazem, $data, $mysql){
            date_default_timezone_set("America/Sao_Paulo");
            $dataAtual = date("d-m-Y");
            $hora = date('H:i:s', strtotime('+ 4 hours'));
            $dias = date('d',strtotime($data)) - date('d',strtotime($dataAtual));

            if($usuario == "user"){
                if($dias < 1){
                    $sql = "select * from horario where hora >'".$hora ."' and status = 'Livre' and armazem = '".$armazem."' and id not in 
                    (select id_horario from janela where data = '".$data."' order by data, id_horario)";
                    $result = $mysql->query($sql);
                    return $result;
                }else{
                    $sql = "select * from horario hour where status='Livre' and armazem = '".$armazem."' and hour.id not in 
                    (select id_horario from janela jan where data = '".$data."' and jan.status != 'Livre'  order by data, id_horario)";
                    
                    $result = $mysql->query($sql);
                    return $result;
                }
            }
            else{
                $sql = "select id,descricao,posicao,status,hora,armazem from horario hour where status='Livre' and armazem = '".$armazem."' and hour.id not in (select id_horario from janela jan where jan.status != 'Livre' and data = '".$data."')";
                $result = $mysql->query($sql);
                return $result;
            }
        }
        public function buscarHorariosEditados($usuario, $mysql, $data){
            if($usuario == "user"){
                $hora = date('H:i:s', strtotime('+ 4 hours'));
                $sql = "select h.id, h.descricao, h.posicao, j.status from janela j 
                inner join horario h on j.id_horario = h.id where j.status = 'Livre' 
                and j.data = '".$data."' and h.hora >'".$hora ."'";
            }
            else{
                $sql = "select h.id, h.descricao, h.posicao, j.status from janela j 
                inner join horario h on j.id_horario = h.id where j.status = 'Livre' 
                and j.data = '".$data."' ";
            }
            $result = $mysql->query($sql);
            return $result;
        }
        public function buscarHorario($id, $mysql){
            $sql = "select id,descricao,posicao,status,hora,armazem from horario where id = ".$id;
            $result = $mysql->query($sql);
            return $result; 
        }
        public function editHorarioStatus($mysql, $id, $status){
            try{
                $sql = "update horario set status = '".$status."' where id = ".$id;
                $result = $mysql->query($sql);              
                return true;
            }catch(Exception $e){
                return false;
            }
        }

    }

    class Janela{
        private $id;
        private $idHorario;
        private $data;
        private $transportadora;
        private $oferta;
        private $posicao;
        private $status;
        private $tipoVeiculo;
        private $placaCavalo;
        private $placaCarreta;
        private $operacao;
        private $nf;
        private $horaChegada;
        private $inicioOperacao;
        private $doca;
        private $pesoInicial;
        private $pesoFinal;
        private $fimOperacao;
        private $nomeUsuario;
        private $dataInclusao;
        private $armazem;
        private $operacaoTabela;
        private $dataOperacaoTabela;
        private $usuarioOperacaoTabela;
        private $peso;
        private $destino;

        public function setId($id){
            $this->id = $id;
        }
        public function getId(){
            return $this->id;
        }
        public function setIdhorario($idHorario){
            $this->idHorario = $idHorario;
        }
        public function getIdhorario(){
            return $this->idHorario;
        }
        public function setData($data){
            $this->data = $data;
        }
        public function getData(){
            return $this->data;
        }
        public function setTransportadora($transportadora){
            $this->transportadora = $transportadora;
        }
        public function getTransportadora(){
            return $this->transportadora;
        }
        public function setOferta($oferta){
            $this->oferta = $oferta;
        }
        public function getOferta(){
            return $this->oferta;
        }
        public function setPosicao($posicao){
            $this->posicao = $posicao;
        }
        public function getPosicao(){
            return $this->posicao;
        }
        public function setStatus($status){
            $this->status = $status;
        }
        public function getStatus(){
            return $this->status;
        }
        public function setTipoVeiculo($tipoVeiculo){
            $this->tipoVeiculo = $tipoVeiculo;
        }
        public function getTipoVeiculo(){
            return $this->tipoVeiculo;
        }
        public function setPlacaCavalo($placaCavalo){
            $this->placaCavalo = $placaCavalo;
        }
        public function getPlacaCavalo(){
            return $this->placaCavalo;
        }
        public function setPlacaCarreta($placaCarreta){
            $this->placaCarreta = $placaCarreta;
        }
        public function getPlacaCarreta(){
            return $this->placaCarreta;
        }
        public function setOperacao($operacao){
            $this->operacao = $operacao;
        }
        public function getOperacao(){
            return $this->operacao;
        }
        public function setNf($nf){
            $this->nf = $nf;
        }
        public function getNf(){
            return $this->nf;
        }
        public function setHoraChegada($horaChegada){
            $this->horaChegada = $horaChegada;
        }
        public function getHoraChegada(){
            return $this->horaChegada;
        }
        public function setInicioOperacao($inicioOperacao){
            $this->inicioOperacao = $inicioOperacao;
        }
        public function getInicioOperacao(){
            return $this->inicioOperacao;
        }
        public function setDoca($doca){
            $this->doca = $doca;
        }
        public function getDoca(){
            return $this->doca;
        }
        public function setPesoInicial($pesoInicial){
            $this->pesoInicial = $pesoInicial;
        }
        public function getPesoInicial(){
            return $this->pesoInicial;
        }
        public function setPesoFinal($pesoFinal){
            $this->pesoFinal = $pesoFinal;
        }
        public function getPesoFinal(){
            return $this->pesoFinal;
        }
        public function setFimOperacao($fimOperacao){
            $this->fimOperacao = $fimOperacao;
        }
        public function getFimOperacao(){
            return $this->fimOperacao;
        }
        public function setNomeusuario($nomeUsuario){
            $this->nomeUsuario = $nomeUsuario;
        }
        public function getNomeusuario(){
            return $this->nomeUsuario;
        }
        public function setDataInclusao($dataInclusao){
            $this->dataInclusao = $dataInclusao;
        }
        public function getDataInclusao(){
            return $this->dataInclusao;
        }
        public function setArmazem($armazem){
            $this->armazem = $armazem;
        }
        public function getArmazem(){
            return $this->armazem;
        }
        public function setPeso($peso){
            $this->peso = $peso;
        }
        public function getPeso(){
            return $this->peso;
        }
        public function setDestino($destino){
            $this->destino = $destino;
        }
        public function getDestino(){
            return $this->destino;
        }
        //variaveis log
        public function setOperacaoTabela($operacaoTabela){
            $this->operacaoTabela = $operacaoTabela;
        }
        public function getOperacaoTabela(){
            return $this->operacaoTabela;
        }
        public function setDataOperacaoTabela($dataOperacaoTabela){
            $this->dataOperacaoTabela = $dataOperacaoTabela;
        }
        public function getDataOperacaoTabela(){
            return $this->dataOperacaoTabela;
        }
        public function setUsuarioOperacaoTabela($usuarioOperacaoTabela){
            $this->usuarioOperacaoTabela = $usuarioOperacaoTabela;
        }
        public function getUsuarioOperacaoTabela(){
            return $this->usuarioOperacaoTabela;
        }


        public function ultimoIdJanela($mysql){
            $sql = "select MAX(ID) as id from janela";
            $result = $mysql->query($sql);
            return $result;
        } 
         
        public function buscarJanelaPorId($mysql, $id){
            $sql = "select * from janela where id = ".$id;
            $result = $mysql->query($sql);
            return $result; 
        }

        public function salvarJanela($mysql){
            try{
                $sql = "select id from janela where id_horario = ".$this->getIdhorario()." and data = '".$this->getData()."'";
                $result = $mysql->query($sql);
                $this::deletarAgendamentoAnt($mysql, $result);
                $sql = "insert into janela(id_horario,data,transportadora,oferta,posicao,status,tipoVeiculo,placa_cavalo,placa_carreta,operacao,nf,horaChegada,inicio_operacao,doca,peso_inicial,peso_final,fim_operacao,usuario,dataInclusao,armazem,peso,destino) values ('".$this->getIdhorario()."','".$this->getData()."','".$this->getTransportadora()."','".$this->getOferta()."','".$this->getPosicao()."','".$this->getStatus()."','".$this->getTipoVeiculo()."','".$this->getPlacaCavalo()."', '".$this->getPlacaCarreta()."', '".$this->getOperacao()."', '".$this->getNf()."', null, null, '".$this->getDoca()."', 0.0, 0.0, null, '".$this->getNomeusuario()."', '".$this->getDataInclusao()."','".$this->getArmazem()."', '".$this->getPeso()."','".$this->getDestino()."')";
                $resultado = $mysql->query($sql);
                if (!$resultado) {
                   return false;
                 }else{
                    return true;
                 }
            }catch(Exception $e){
                return false;
            }
        }
        public function listarJanelas($mysql, $data){
            $sql = "select id,id_horario,data,transportadora,oferta,posicao,status,tipoVeiculo,placa_cavalo,placa_carreta,operacao,nf,horaChegada,inicio_operacao,doca,peso_inicial,peso_final,fim_operacao,usuario,dataInclusao,armazem, peso, destino from janela where data = ".$data;
            $result = $mysql->query($sql);
            return $result;
        }
        public function janelaPorPeriodo($mysql, $dataInicial, $dataFinal){
            $sql = "select id,id_horario,data,transportadora,oferta,posicao,status,tipoVeiculo,placa_cavalo,placa_carreta,operacao,nf,horaChegada,inicio_operacao,doca,peso_inicial,peso_final,fim_operacao,usuario,dataInclusao,armazem, peso, destino from janela where status='Ocupado' and data >= '".$dataInicial."' and data <='".$dataFinal."'";
            $result = $mysql->query($sql);
            return $result;
        }
        public function listarJanelasByUser($mysql, $hora, $nome){
            $sql = "select * from janela j inner join
                    horario h on j.id_horario = h.id
                    where h.hora >= '".$hora."' and transportadora = '".$nome."'";
            $result = $mysql->query($sql);
            return $result; 
        }
        public function listarJanelasCount($mysql, $data, $status, $armazem){
        	
            date_default_timezone_set("America/Sao_Paulo");
            $dataAtual = date("Y-m-d");
            $hora = date('H:i:s', strtotime('+ 4 hours'));
            $dias = date('d',strtotime($data)) - date('d',strtotime($dataAtual));

            if($dataAtual < $data ) $hora = "00:00:01";
            if($_SESSION["tipo"] == "user"){
                if($dias < 1){
                    //TESTE DE NOVA QUERY
                    $sql = "select * from horario where status = '".$status."' and hora >'".$hora ."' and armazem =  '".$armazem."' and id not in
                    (select id_horario from janela where data = '".$data."') or id in
                    (select id_horario from janela where data = '".$data."' and status = '".$status."')";
                    
                    //echo $sql;
                    $result = $mysql->query($sql);
       
                    return $result;
                }else{
                    $sql = "select * from horario where status = '".$status."' and armazem = '".$armazem."' and id not in
                    (select id_horario from janela where data = '".$data."') or id in
                    (select id_horario from janela where data = '".$data."' and status = '".$status."')";
                    $result = $mysql->query($sql);
                    
                    
                    return $result;
                }
            }else{
                //TESTE DE NOVA QUERY
                $sql = "select * from horario where status = '".$status."' and armazem =  '".$armazem."' and id not in
                (select id_horario from janela where data = '".$data."') or id in
                (select id_horario from janela where data = '".$data."' and status = '".$status."')";
                $result = $mysql->query($sql);
                //echo $sql;
                return $result;
            }

            
        }
        public function listarJanelasOcupadas($mysql, $data, $armazem){
            $sql = "select * from janela where data = '".$data."' and status = 'Ocupado' and armazem = '".$armazem."' order by id_horario";
            $result = $mysql->query($sql);
            return $result;
        }
        public function listarJanelasOcupadasPorNome($mysql, $nome){
            $sql = "select * from janela where fim_operacao is null and transportadora = '".$nome."' 
            order by data, id_horario";
            $result = $mysql->query($sql);
            return $result;
        }
        public function listarJanelasStatus($mysql, $data, $status){
            $sql = "select * from janela where data = '".$data."' and status = '".$status."'";
            $result = $mysql->query($sql);
            return $result;
        }
        public function insertHorarioStatus($mysql, $id, $status, $nome, $data, $dataInclusao, $armazem){
            try{
                $sql = "delete from janela where id_horario = ".$id." and data = '".$data."' and armazem = ".$armazem;
                $mysql->query($sql);
                $sql = "insert into janela (id_horario, data, status, dataInclusao, usuario, armazem) 
                values (".$id.", '".$data."', '".$status."', '".$dataInclusao."', '".$nome."', '".$armazem."')";
                $mysql->query($sql);
                
                return true;
            }catch(Exception $e){
                return false;
            }
        }
        public function editarJanelaId($mysql, $id){
            try{
                $sql = "update janela set
                id_horario=".$this->getIdhorario().","; 
                if($this->getData() != '') $sql .= "data='".$this->getData()."',";
                else $sql .= "data= null,";
                $sql .= "tipoVeiculo='".$this->getTipoVeiculo()."',";
                $sql .= "placa_carreta='".$this->getPlacaCarreta()."',";
                $sql .= "operacao='".$this->getOperacao()."',";
                $sql .= "nf='".$this->getNF()."',";
                $sql .= "doca='".$this->getDoca()."',";
                if($this->getInicioOperacao()!='') $sql .= "inicio_operacao='".$this->getInicioOperacao()."',";
                else $sql .= "inicio_operacao=null,";
                if($this->getHoraChegada()!='') $sql .= "horaChegada='".$this->getHoraChegada()."',";
                else $sql .= "horaChegada=null,";
                if($this->getFimOperacao() != '') $sql .= "fim_operacao='".$this->getFimOperacao()."',";
                else $sql .= "fim_operacao=null,";
                $sql .= "transportadora='".$this->getTransportadora()."',";
                $sql .= "oferta='".$this->getOferta()."',";
                $sql .= "peso='".$this->getPeso()."', ";
                $sql .= "destino='".$this->getDestino()."', ";
                $sql .= "placa_cavalo='".$this->getPlacaCavalo()."' where id = ".$id;
              
                $resultado = $mysql->query($sql);
                if (!$resultado) {
                   return false;
                 }else{
                    return true;
                 }
                return true;
                
            }catch(Exception $e){
                return false;
            }
        }
        public function deletarAgendamento($id, $mysql){
            try{
                $sql = "delete from janela where id =".$id;
                $result = $mysql->query($sql);
                return true;
            }catch(Exception $e){
                return false;
            }
        }

        public function deletarAgendamentoAnt($mysql, $result){ 
            while ($dados = $result->fetch_assoc()){ 
                $id = $dados['id'];
                $sql = "delete from janela where id = ".$id;
                $mysql->query($sql);           
            }       
            return;  
        }
    }
    //funções simples de log sem sets e gets
    //agendamento
    class LogAgendamento{
        public function listarLogJanela($mysql){
            $sql = "select id,id_horario,data,transportadora,posicao,status,tipoVeiculo,placa_cavalo,placa_carreta,operacao,nf,horaChegada,inicio_operacao,doca,peso_inicial,peso_final,fim_operacao,usuario,dataInclusao,armazem,operacao_tabela,data_operacao_tabela,usuario_operacao_tabela from janela_log order by id desc";
            $result = $mysql->query($sql);
            return $result;
        } 
        public function updateUsuario($mysql){
            $sql = "select * from janela_log order by id desc limit 1";
                $result = $mysql->query($sql);
                while ($dados = $result->fetch_assoc()){ 
                    $id = $dados['id'];
                }
                $sql = "update janela_log set usuario_operacao_tabela = '"
                . $_SESSION['nome']. "' where id = " . $id;
                $mysql->query($sql);
        }   
    }
?>