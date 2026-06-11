<?php
include_once '../../session.php'; 

use Illuminate\Database\Capsule\Manager as Capsule;

function evonik_can_use_eloquent(){
    return class_exists(Capsule::class);
}

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
                if (evonik_can_use_eloquent()) {
                    $userId = Capsule::table('usuario')->insertGetId([
                        'nome' => $this->getNome(),
                        'username' => $this->getUsername(),
                        'password' => $this->getPassword(),
                        'usuarioCriacao' => $this->getUsuarioCriacao(),
                        'dataInclusao' => $this->getData(),
                        'tipo' => $this->getTipo(),
                    ]);

                    $systemAccess = $this->getSystemAccess();
                    if (is_array($systemAccess) && count($systemAccess) > 0) {
                        return $this->addSystemAccess($mysql, $userId, $systemAccess);
                    }

                    return true;
                }
                return false;
            }catch(Exception $e){
                return false;
            }
        }

        public function addSystemAccess($mysql, $userId, $systemAccess){

            try {
                if (evonik_can_use_eloquent()) {
                    $rows = [];
                    foreach ($systemAccess as $systemId) {
                        $rows[] = ['userId' => (int) $userId, 'systemsId' => (int) $systemId];
                    }

                    if (count($rows) > 0) {
                        Capsule::table('userSystems')->insert($rows);
                    }

                    return true;
                }
                return false;

            } catch (Exception $e) {
                return false;
            }

            

        }

        public function editarUsuario($mysql,$id){
            try{
                if (evonik_can_use_eloquent()) {
                    Capsule::table('usuario')
                        ->where('id', (int) $id)
                        ->update([
                            'nome' => $this->getNome(),
                            'username' => $this->getUsername(),
                            'password' => $this->getPassword(),
                            'dataInclusao' => $this->getData(),
                            'usuarioCriacao' => $this->getUsuarioCriacao(),
                            'tipo' => $this->getTipo(),
                        ]);

                    $currentSystemIds = Capsule::table('userSystems')
                        ->where('userId', (int) $id)
                        ->pluck('systemsId')
                        ->map(function($v){ return (int) $v; })
                        ->toArray();

                    $desiredSystemIds = $this->getSystemAccess();
                    if (!is_array($desiredSystemIds)) {
                        $desiredSystemIds = [];
                    }
                    $desiredSystemIds = array_map('intval', $desiredSystemIds);

                    $diff1 = array_diff($currentSystemIds, $desiredSystemIds);
                    $diff2 = array_diff($desiredSystemIds, $currentSystemIds);

                    if(count($diff1) == 0 && count($diff2) == 0) return true;

                    Capsule::table('userSystems')->where('userId', (int) $id)->delete();

                    if(count($desiredSystemIds) == 0) return true;

                    return $this->addSystemAccess($mysql, $id, $desiredSystemIds);
                }
                return false;
            }catch(Exception $e){
                return false;
            }
        }
        public function deletarUsuario($id, $mysql){
            try{
                if (evonik_can_use_eloquent()) {
                    Capsule::table('userSystems')->where('userId', (int) $id)->delete();
                    Capsule::table('usuario')->where('id', (int) $id)->delete();
                    return true;
                }
                return false;
            }catch(Exception $e){
                return false;
            }
        }
        public function listarUsuarios($mysql){
            if (evonik_can_use_eloquent()) {
                return Capsule::table('usuario')
                    ->select(['id','nome','username','password','dataInclusao','tipo','usuarioCriacao'])
                    ->orderBy('nome')
                    ->get()
                    ->map(function($row){ return (array) $row; })
                    ->toArray();
            }
            return [];
        }
        public function buscarUsuario($id, $mysql){
            if (evonik_can_use_eloquent()) {
                return Capsule::table('usuario')
                    ->select([
                        'usuario.id AS user_id',
                        'nome',
                        'username',
                        'password',
                        'dataInclusao',
                        'tipo',
                        'usuarioCriacao',
                        'systemsId',
                    ])
                    ->join('userSystems', 'usuario.id', '=', 'userId')
                    ->where('usuario.id', (int) $id)
                    ->get()
                    ->map(function($row){ return (array) $row; })
                    ->toArray();
            }
            return [];
        }
        public function alterarSenha($id, $mysql, $tipo, $nome){
            try{
                if (evonik_can_use_eloquent()) {
                    Capsule::table('usuario')
                        ->where('id', (int) $id)
                        ->update(['password' => $this->getPassword()]);
                    
                    if($tipo == 'user'){
                        Capsule::table('transportadora')
                            ->where('nome', $nome)
                            ->update(['password' => $this->getPassword()]);
                    }
                    return true;
                }
                return false;
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
                if (evonik_can_use_eloquent()) {
                    Capsule::table('transportadora')->insert([
                        'nome' => $this->getNome(),
                        'username' => $this->getUsername(),
                        'cnpj' => $this->getCNPJ(),
                        'email' => $this->getEmail(),
                        'telefone' => $this->getTelefone(),
                        'celular' => $this->getCelular(),
                        'password' => $this->getPassword(),
                        'data' => $this->getData(),
                        'usuario' => $this->getUsuario(),
                        'cliente_origem' => 'evonik',
                    ]);
                    return true;
                }
                return false;
            }catch(Exception $e){
                return false;
            }
        }


        public function editarTransportadora($mysql,$id){
            try{
                if (evonik_can_use_eloquent()) {
                    Capsule::table('transportadora')
                        ->where('id', (int) $id)
                        ->update([
                            'nome' => $this->getNome(),
                            'username' => $this->getUsername(),
                            'cnpj' => $this->getCNPJ(),
                            'email' => $this->getEmail(),
                            'telefone' => $this->getTelefone(),
                            'celular' => $this->getCelular(),
                            'password' => $this->getPassword(),
                            'data' => $this->getData(),
                            'usuario' => $this->getUsuario(),
                        ]);
                    return true;
                }
                return false;
            }catch(Exception $e){
                return false;
            }
        }
        public function deletarTransportadora($id, $mysql){
            try{
                if (evonik_can_use_eloquent()) {
                    Capsule::table('transportadora')->where('id', (int) $id)->delete();
                    return true;
                }
                return false;
            }catch(Exception $e){
                return false;
            }
        }
        public function listarTransportadora($mysql){
            if (evonik_can_use_eloquent()) {
                return Capsule::table('transportadora')
                    ->select(['id','nome','username','cnpj','email','telefone','celular','password','data','usuario'])
                    ->orderBy('nome')
                    ->get()
                    ->map(function($row){ return (array) $row; })
                    ->toArray();
            }
            return [];
        }
        public function buscarTransportadora($id, $mysql){
            if (evonik_can_use_eloquent()) {
                $row = Capsule::table('transportadora')
                    ->select(['id','nome','username','cnpj','email','telefone','celular','password','data','usuario'])
                    ->where('id', (int) $id)
                    ->first();
                if ($row == null) {
                    return [];
                }
                return [(array) $row];
            }
            return [];
        }
        public function buscarTransportadoraNome($nome, $mysql){
            if (evonik_can_use_eloquent()) {
                $row = Capsule::table('transportadora')
                    ->select(['id','nome','username','cnpj','email','telefone','celular','password','data','usuario'])
                    ->where('nome', $nome)
                    ->first();
                if ($row == null) {
                    return [];
                }
                return [(array) $row];
            }
            return [];
        }
        function listarTransportadoraPorNome($mysql, $nome){
            if (evonik_can_use_eloquent()) {
                return Capsule::table('transportadora')
                    ->select(['id','nome','username','cnpj','email','telefone','celular','password','data','usuario'])
                    ->where('nome', $nome)
                    ->orderBy('nome')
                    ->get()
                    ->map(function($row){ return (array) $row; })
                    ->toArray();
            }
            return [];
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
            if (evonik_can_use_eloquent()) {
                return Capsule::table('horario')
                    ->select(['id','descricao','posicao','status','hora','armazem'])
                    ->get()
                    ->map(function($row){ return (array) $row; })
                    ->toArray();
            }
            return [];
        }
   
        public function buscarHorarios($usuario, $armazem, $data, $mysql){
            date_default_timezone_set("America/Sao_Paulo");
            $dataAtual = date("d-m-Y");
            $hora = date('H:i:s', strtotime('+ 4 hours'));
            $dias = date('d',strtotime($data)) - date('d',strtotime($dataAtual));

            if (evonik_can_use_eloquent()) {
                if($usuario == "user"){
                    if($dias < 1){
                        return Capsule::table('horario')
                            ->where('hora', '>', $hora)
                            ->where('status', 'Livre')
                            ->where('armazem', $armazem)
                            ->whereNotIn('id', function($q) use ($data){
                                $q->select('id_horario')->from('janela')->where('data', $data);
                            })
                            ->get()
                            ->map(function($row){ return (array) $row; })
                            ->toArray();
                    }

                    return Capsule::table('horario as hour')
                        ->where('status', 'Livre')
                        ->where('armazem', $armazem)
                        ->whereNotIn('hour.id', function($q) use ($data){
                            $q->select('id_horario')
                                ->from('janela as jan')
                                ->where('data', $data)
                                ->where('jan.status', '!=', 'Livre');
                        })
                        ->get()
                        ->map(function($row){ return (array) $row; })
                        ->toArray();
                }

                return Capsule::table('horario as hour')
                    ->select(['id','descricao','posicao','status','hora','armazem'])
                    ->where('status', 'Livre')
                    ->where('armazem', $armazem)
                    ->whereNotIn('hour.id', function($q) use ($data){
                        $q->select('id_horario')
                            ->from('janela as jan')
                            ->where('jan.status', '!=', 'Livre')
                            ->where('data', $data);
                    })
                    ->get()
                    ->map(function($row){ return (array) $row; })
                    ->toArray();
            }
            return [];
        }
        public function buscarHorariosEditados($usuario, $mysql, $data){
            if (evonik_can_use_eloquent()) {
                if($usuario == "user"){
                    $hora = date('H:i:s', strtotime('+ 4 hours'));
                    return Capsule::table('janela as j')
                        ->select(['h.id','h.descricao','h.posicao','j.status'])
                        ->join('horario as h', 'j.id_horario', '=', 'h.id')
                        ->where('j.status', 'Livre')
                        ->where('j.data', $data)
                        ->where('h.hora', '>', $hora)
                        ->get()
                        ->map(function($row){ return (array) $row; })
                        ->toArray();
                }

                return Capsule::table('janela as j')
                    ->select(['h.id','h.descricao','h.posicao','j.status'])
                    ->join('horario as h', 'j.id_horario', '=', 'h.id')
                    ->where('j.status', 'Livre')
                    ->where('j.data', $data)
                    ->get()
                    ->map(function($row){ return (array) $row; })
                    ->toArray();
            }
            return [];
        }
        public function buscarHorario($id, $mysql){
            if (evonik_can_use_eloquent()) {
                $row = Capsule::table('horario')
                    ->select(['id','descricao','posicao','status','hora','armazem'])
                    ->where('id', (int) $id)
                    ->first();
                if ($row == null) {
                    return [];
                }
                return [(array) $row];
            }
            return [];
        }
        public function editHorarioStatus($mysql, $id, $status){
            try{
                if (evonik_can_use_eloquent()) {
                    Capsule::table('horario')->where('id', (int) $id)->update(['status' => $status]);
                    return true;
                }
                return false;
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
            if (evonik_can_use_eloquent()) {
                $id = Capsule::table('janela')->max('id');
                return [['id' => $id]];
            }
            return [['id' => null]];
        } 
         
        public function buscarJanelaPorId($mysql, $id){
            if (evonik_can_use_eloquent()) {
                $row = Capsule::table('janela')->where('id', (int) $id)->first();
                if ($row == null) {
                    return [];
                }
                return [(array) $row];
            }
            return [];
        }

        public function salvarJanela($mysql){
            try{
                if (evonik_can_use_eloquent()) {
                    Capsule::table('janela')
                        ->where('id_horario', (int) $this->getIdhorario())
                        ->where('data', $this->getData())
                        ->delete();

                    Capsule::table('janela')->insert([
                        'id_horario' => (int) $this->getIdhorario(),
                        'data' => $this->getData(),
                        'transportadora' => $this->getTransportadora(),
                        'oferta' => $this->getOferta(),
                        'posicao' => $this->getPosicao(),
                        'status' => $this->getStatus(),
                        'tipoVeiculo' => $this->getTipoVeiculo(),
                        'placa_cavalo' => $this->getPlacaCavalo(),
                        'placa_carreta' => $this->getPlacaCarreta(),
                        'operacao' => $this->getOperacao(),
                        'nf' => $this->getNf(),
                        'horaChegada' => null,
                        'inicio_operacao' => null,
                        'doca' => $this->getDoca(),
                        'peso_inicial' => 0.0,
                        'peso_final' => 0.0,
                        'fim_operacao' => null,
                        'usuario' => $this->getNomeusuario(),
                        'dataInclusao' => $this->getDataInclusao(),
                        'armazem' => $this->getArmazem(),
                        'peso' => $this->getPeso(),
                        'destino' => $this->getDestino(),
                    ]);

                    return true;
                }
                return false;
            }catch(Exception $e){
                return false;
            }
        }
        public function listarJanelas($mysql, $data){
            if (evonik_can_use_eloquent()) {
                return Capsule::table('janela')
                    ->select(['id','id_horario','data','transportadora','oferta','posicao','status','tipoVeiculo','placa_cavalo','placa_carreta','operacao','nf','horaChegada','inicio_operacao','doca','peso_inicial','peso_final','fim_operacao','usuario','dataInclusao','armazem','peso','destino'])
                    ->where('data', $data)
                    ->get()
                    ->map(function($row){ return (array) $row; })
                    ->toArray();
            }
            return [];
        }
        public function janelaPorPeriodo($mysql, $dataInicial, $dataFinal){
            if (evonik_can_use_eloquent()) {
                return Capsule::table('janela')
                    ->select(['id','id_horario','data','transportadora','oferta','posicao','status','tipoVeiculo','placa_cavalo','placa_carreta','operacao','nf','horaChegada','inicio_operacao','doca','peso_inicial','peso_final','fim_operacao','usuario','dataInclusao','armazem','peso','destino'])
                    ->where('status', 'Ocupado')
                    ->where('data', '>=', $dataInicial)
                    ->where('data', '<=', $dataFinal)
                    ->get()
                    ->map(function($row){ return (array) $row; })
                    ->toArray();
            }
            return [];
        }
        public function listarJanelasByUser($mysql, $hora, $nome){
            if (evonik_can_use_eloquent()) {
                return Capsule::table('janela as j')
                    ->join('horario as h', 'j.id_horario', '=', 'h.id')
                    ->where('h.hora', '>=', $hora)
                    ->where('transportadora', $nome)
                    ->get()
                    ->map(function($row){ return (array) $row; })
                    ->toArray();
            }
            return [];
        }
        public function listarJanelasCount($mysql, $data, $status, $armazem){
        	
            date_default_timezone_set("America/Sao_Paulo");
            $dataAtual = date("Y-m-d");
            $hora = date('H:i:s', strtotime('+ 4 hours'));
            $dias = date('d',strtotime($data)) - date('d',strtotime($dataAtual));

            if($dataAtual < $data ) $hora = "00:00:01";
            if (evonik_can_use_eloquent()) {
                $userType = isset($_SESSION["tipo"]) ? $_SESSION["tipo"] : '';

                $base = function($q) use ($status, $armazem, $hora, $dias, $data, $userType){
                    $q->where('status', $status)
                      ->where('armazem', $armazem);
                    if ($userType == "user" && $dias < 1) {
                        $q->where('hora', '>', $hora);
                    }
                    $q->whereNotIn('id', function($sub) use ($data){
                        $sub->select('id_horario')->from('janela')->where('data', $data);
                    });
                };

                return Capsule::table('horario')
                    ->where(function($q) use ($base){ $base($q); })
                    ->orWhereIn('id', function($sub) use ($data, $status){
                        $sub->select('id_horario')->from('janela')->where('data', $data)->where('status', $status);
                    })
                    ->get()
                    ->map(function($row){ return (array) $row; })
                    ->toArray();
            }
            return [];
        }
        public function listarJanelasOcupadas($mysql, $data, $armazem){
            if (evonik_can_use_eloquent()) {
                return Capsule::table('janela')
                    ->where('data', $data)
                    ->where('status', 'Ocupado')
                    ->where('armazem', $armazem)
                    ->orderBy('id_horario')
                    ->get()
                    ->map(function($row){ return (array) $row; })
                    ->toArray();
            }
            return [];
        }
        public function listarJanelasOcupadasPorNome($mysql, $nome){
            if (evonik_can_use_eloquent()) {
                return Capsule::table('janela')
                    ->whereNull('fim_operacao')
                    ->where('transportadora', $nome)
                    ->orderBy('data')
                    ->orderBy('id_horario')
                    ->get()
                    ->map(function($row){ return (array) $row; })
                    ->toArray();
            }
            return [];
        }
        public function listarJanelasStatus($mysql, $data, $status){
            if (evonik_can_use_eloquent()) {
                return Capsule::table('janela')
                    ->where('data', $data)
                    ->where('status', $status)
                    ->get()
                    ->map(function($row){ return (array) $row; })
                    ->toArray();
            }
            return [];
        }
        public function insertHorarioStatus($mysql, $id, $status, $nome, $data, $dataInclusao, $armazem){
            try{
                if (evonik_can_use_eloquent()) {
                    Capsule::table('janela')
                        ->where('id_horario', (int) $id)
                        ->where('data', $data)
                        ->where('armazem', $armazem)
                        ->delete();

                    Capsule::table('janela')->insert([
                        'id_horario' => (int) $id,
                        'data' => $data,
                        'status' => $status,
                        'dataInclusao' => $dataInclusao,
                        'usuario' => $nome,
                        'armazem' => $armazem,
                    ]);
                
                    return true;
                }
                return false;
            }catch(Exception $e){
                return false;
            }
        }
        public function editarJanelaId($mysql, $id){
            try{
                if (evonik_can_use_eloquent()) {
                    $data = [
                        'id_horario' => (int) $this->getIdhorario(),
                        'tipoVeiculo' => $this->getTipoVeiculo(),
                        'placa_carreta' => $this->getPlacaCarreta(),
                        'operacao' => $this->getOperacao(),
                        'nf' => $this->getNF(),
                        'doca' => $this->getDoca(),
                        'transportadora' => $this->getTransportadora(),
                        'oferta' => $this->getOferta(),
                        'peso' => $this->getPeso(),
                        'destino' => $this->getDestino(),
                        'placa_cavalo' => $this->getPlacaCavalo(),
                    ];

                    $data['data'] = ($this->getData() != '') ? $this->getData() : null;
                    $data['inicio_operacao'] = ($this->getInicioOperacao()!='') ? $this->getInicioOperacao() : null;
                    $data['horaChegada'] = ($this->getHoraChegada()!='') ? $this->getHoraChegada() : null;
                    $data['fim_operacao'] = ($this->getFimOperacao()!='') ? $this->getFimOperacao() : null;

                    Capsule::table('janela')->where('id', (int) $id)->update($data);
                    return true;
                }
                return false;
                
            }catch(Exception $e){
                return false;
            }
        }
        public function deletarAgendamento($id, $mysql){
            try{
                if (evonik_can_use_eloquent()) {
                    Capsule::table('janela')->where('id', (int) $id)->delete();
                    return true;
                }
                return false;
            }catch(Exception $e){
                return false;
            }
        }

        public function deletarAgendamentoAnt($mysql, $result){ 
            if (evonik_can_use_eloquent()) {
                if ($result instanceof mysqli_result) {
                    while ($dados = $result->fetch_assoc()) {
                        if (!isset($dados['id'])) continue;
                        Capsule::table('janela')->where('id', (int) $dados['id'])->delete();
                    }
                    return;
                }

                foreach ($result as $dados) {
                    if (is_object($dados)) $dados = (array) $dados;
                    if (!isset($dados['id'])) continue;
                    Capsule::table('janela')->where('id', (int) $dados['id'])->delete();
                }
                return;
            }
            return;  
        }
    }
    //funções simples de log sem sets e gets
    //agendamento
    class LogAgendamento{
        public function listarLogJanela($mysql){
            if (evonik_can_use_eloquent()) {
                return Capsule::table('janela_log')
                    ->select(['id','id_horario','data','transportadora','posicao','status','tipoVeiculo','placa_cavalo','placa_carreta','operacao','nf','horaChegada','inicio_operacao','doca','peso_inicial','peso_final','fim_operacao','usuario','dataInclusao','armazem','operacao_tabela','data_operacao_tabela','usuario_operacao_tabela'])
                    ->orderBy('id', 'desc')
                    ->get()
                    ->map(function($row){ return (array) $row; })
                    ->toArray();
            }
            return [];
        } 
        public function updateUsuario($mysql){
            if (evonik_can_use_eloquent()) {
                $id = Capsule::table('janela_log')->orderBy('id', 'desc')->value('id');
                if ($id != null) {
                    Capsule::table('janela_log')
                        ->where('id', (int) $id)
                        ->update(['usuario_operacao_tabela' => $_SESSION['nome']]);
                }
                return;
            }
            return;
        }   
    }
?>
