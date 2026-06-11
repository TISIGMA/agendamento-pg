<?php

    function messageSuccess($msg){
        return '<div class="alert alert-success alert-dismissible">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Sucesso! </strong> '.$msg.'
      </div>';
    }

    function messageErro($msg){
        return '<div class="alert alert-danger alert-dismissible">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>ops! </strong> '.$msg.'
      </div>';
    }
    
    function confirmacao($msg){
     return "";
     }

    function buscarHorarios($horario, $mysql){
      $horarios = $horario->listarHorarios($mysql);
      $horarioArray = array();
      
      $count = 0;
      while ($dados = $horarios->fetch_assoc()){ 
          $horario = new Horario();
          $horario->setId($dados['id']);
          $horario->setHorario($dados['descricao']);
          $horario->setPosicao($dados['posicao']);
          $horario->setStatus($dados['status']);
          $horario->setArmazem($dados['armazem']);
          $horarioArray[$count] = $horario;
          $count++;
      }
      return $horarioArray;
    }
    function buscarHorario($id, $mysql){
      $horario = new Horario();
      $horarios = $horario->buscarHorario($id, $mysql);
      while ($dados = $horarios->fetch_assoc()){ 
          $horario = new Horario();
          $horario->setId($dados['id']);
          $horario->setHorario($dados['descricao']);
          $horario->setPosicao($dados['posicao']);
          $horario->setStatus($dados['status']);
      }
      return $horario;
    }

    function editHorarioStatus($mysql, $id, $status){
      $horario = new Horario();
      return $horario->editHorarioStatus($mysql, $id, $status);
    }
    function buscarTransportadoraUser($transportadora, $mysql, $nome){
      return $transp = $transportadora->buscarTransportadoraNome($nome, $mysql);
    }

    function buscarTransportadora($transportadora, $mysql){
      $transportadoras = $transportadora->listarTransportadora($mysql);
      $transp = array();
      
      $count = 0;
      while ($dados = $transportadoras->fetch_assoc()){ 
          $transportadora = new Transportadora();
          $transportadora->setId($dados['id']);
          $transportadora->setNome($dados['nome']);
          $transportadora->setUsername($dados['username']);
          $transportadora->setCNPJ($dados['cnpj']);
          $transportadora->setEmail($dados['email']);
          $transportadora->setTelefone($dados['telefone']);
          $transportadora->setCelular($dados['celular']);
          $transportadora->setPassword($dados['password']);
          $transportadora->setData($dados['data']);
          $transportadora->setUsuario($dados['usuario']);
          $transp[$count] = $transportadora;
          $count++;
      }
      return $transp;
    }
    function buscarTransportadoraPorNome($transportadora, $mysql,$nome){
      $transportes = $transportadora->listarTransportadoraPorNome($mysql, $nome);

      while ($dados = $transportes->fetch_assoc()){ 
          $transporte = new Transportadora();
          $transporte->setId($dados['id']);
          $transporte->setNome($dados['nome']);
          $transporte->setUsername($dados['username']);
          $transporte->setCNPJ($dados['cnpj']);
          $transporte->setEmail($dados['email']);
          $transporte->setTelefone($dados['telefone']);
          $transporte->setCelular($dados['celular']);
          $transporte->setPassword($dados['password']);
          $transporte->setData($dados['data']);
          $transporte->setUsuario($dados['usuario']);
      }
      return $transporte;
    }

    function buscarHorariosData($usuario, $armazem, $data, $horario, $mysql){
      $janelasArray = array();
      $horarios = $horario->buscarHorarios($usuario, $armazem, $data, $mysql);
      
      $count = 0;
      while ($dados = $horarios->fetch_assoc()){ 
          $horario = new Horario();
          $horario->setId($dados['id']);
          $horario->setHorario($dados['descricao']);
          $horario->setPosicao($dados['posicao']);
          $horario->setStatus($dados['status']);
          $janelasArray[$count] = $horario;
          $count++;
      }
      sort($janelasArray);
      return $janelasArray; 
      
    }

    function contarHorarios(){
      
    }

    function buscarTipoVeiculo($mysql){
      $sql = "select id,descricao from tipoVeiculo order by descricao ";
      $result = $mysql->query($sql);
      return $result;
    }

    function ultimoIdJanela($mysql){
      $janela = new Janela();
      $resultado = $janela->ultimoIdJanela($mysql);
      while ($dados = $resultado->fetch_assoc()){ 
        $id = $dados['id'];
      }
      return $id;
    }

    
    function buscarJanelaPorNome($janela, $mysql, $nome){
      date_default_timezone_set('America/Sao_Paulo');
      $hora = date("H:i:s");
      $janelas = $janela->listarJanelasOcupadasPorNome($mysql, $nome);
      $arrayJanelas = array();
      
      $count = 0;
      while ($dados = $janelas->fetch_assoc()){ 
          $janela = new Janela();
          $janela->setId($dados['id']);
          $janela->setIdhorario($dados['id_horario']);
          $janela->setData($dados['data']);
          $janela->setTransportadora($dados['transportadora']);
          $janela->setOferta($dados['oferta']);
          $janela->setTipoVeiculo($dados['tipoVeiculo']);
          $janela->setPlacaCavalo($dados['placa_cavalo']);
          $janela->setPlacaCarreta($dados['placa_carreta']);
          $janela->setStatus($dados['status']);
          $janela->setPosicao($dados['posicao']);
          $janela->setOperacao($dados['operacao']);
          $janela->setNf($dados['nf']);
          $janela->setDataInclusao($dados['dataInclusao']);
          $janela->setNomeusuario($dados['usuario']);
          $janela->setHoraChegada($dados['horaChegada']);
          $janela->setInicioOperacao($dados['inicio_operacao']);
          $janela->setFimOperacao($dados['fim_operacao']);
          $janela->setArmazem($dados['armazem']);
          $janela->setPeso($dados['peso']);
          $janela->setDestino($dados['destino']);
          $arrayJanelas[$count] = $janela;
          $count++;
      }
      return $arrayJanelas;
    }

    function listarJanelasCount($mysql, $data, $status, $armazem){
      $janela = new Janela();
      $result = $janela->listarJanelasCount($mysql, $data, $status, $armazem);
      return $result->num_rows; 
    }
    function listarJanelasOcupadas($mysql, $data, $armazem){
      $janela = new Janela();
      $result = $janela->listarJanelasOcupadas($mysql, $data, $armazem);
      return $result->num_rows;
    }

    function listarJanelasPorData($mysql, $data, $status, $armazem){
      $janela = new Janela();
      $result = $janela->listarJanelasCount($mysql, $data, $status, $armazem);
      $janelasArray = array();
      $count = 0;
      while ($dados = $result->fetch_assoc()){ 
          $horario = new Horario();
          $horario->setId($dados['id']);
          $horario->setHorario($dados['descricao']);
          $horario->setPosicao($dados['posicao']);
          $horario->setStatus($dados['status']);
          $janelasArray[$count] = $horario;
          $count++;
        
      }
      sort($janelasArray);
      return $janelasArray; 
    }

    function janelaPorPeriodo($mysql, $dataInicial, $dataFinal){
      $janelas = new Janela();
      $result = $janelas->janelaPorPeriodo($mysql, $dataInicial, $dataFinal);
      $arrayJanelas = array();

      $arrayJanelas = loop_JanelasOcupadas($result, $mysql);
      return $arrayJanelas;
    }
    function listarJanelasOcupadasDados($mysql, $data, $armazem){
      $janela = new Janela();
      $result = $janela->listarJanelasOcupadas($mysql, $data, $armazem);
      $arrayJanelas = array();

      $arrayJanelas = loop_JanelasOcupadas($result, $mysql);
      return $arrayJanelas;
    }
    function insertHorarioStatus($mysql, $id, $status, $nome, $data, $armazem){
      date_default_timezone_set("America/Sao_Paulo");
      $dataInclusao = date("Y-m-d");
      $janela = new Janela();
      if($janela->insertHorarioStatus($mysql, $id, $status, $nome, $data, $dataInclusao, $armazem)){
        return true;
      }else{
        return false;
      }
    }

    function buscarJanelaPorId($mysql, $id){
      $janela = new Janela();
      $result = $janela->buscarJanelaPorId($mysql, $id);

      while ($dados = $result->fetch_assoc()){ 
        $janela = new Janela();
        $janela->setId($dados['id']);
        $janela->setIdhorario($dados['id_horario']);
        $janela->setOferta($dados['oferta']);
        $janela->setData($dados['data']);
        $janela->setTransportadora($dados['transportadora']);
        $janela->setTipoVeiculo($dados['tipoVeiculo']);
        $janela->setPlacaCavalo($dados['placa_cavalo']);
        $janela->setPlacaCarreta($dados['placa_carreta']);
        $janela->setStatus($dados['status']);
        $janela->setPosicao($dados['posicao']);
        $janela->setOperacao($dados['operacao']);
        $janela->setDoca($dados['doca']);
        $janela->setNf($dados['nf']);
        $janela->setDataInclusao($dados['dataInclusao']);
        $janela->setNomeusuario($dados['usuario']);
        $janela->setHoraChegada($dados['horaChegada']);
        $janela->setInicioOperacao($dados['inicio_operacao']);
        $janela->setFimOperacao($dados['fim_operacao']);
        $janela->setArmazem($dados['armazem']);
        $janela->setPeso($dados['peso']);
        $janela->setDestino($dados['destino']);

    }
    return $janela;
    }

    function editarJanelaId($janela, $mysql, $id){
      $janela->editarJanelaId($mysql, $id);
      $updateUsuario = new LogAgendamento();
      $updateUsuario->updateUsuario($mysql);
      return true;
    }

    function deletarAgendamento($id, $mysql){
      $janela = new Janela();
      $result = $janela->deletarAgendamento($id, $mysql);
      $updateUsuario = new LogAgendamento();
      $updateUsuario->updateUsuario($mysql);
    }

    //funções log sistema
    //agendamento
    function listarLogAgendamento($mysql){
      $logAgendamento = new LogAgendamento();
      $result = $logAgendamento->listarLogJanela($mysql);
      $count = 0;
      while ($dados = $result->fetch_assoc()){ 
        $janela = new Janela();
        $janela->setId($dados['id']);
        $janela->setIdhorario($dados['id_horario']);
        $janela->setData($dados['data']);
        $janela->setTransportadora($dados['transportadora']);
        $janela->setOferta($dados['oferta']);
        $janela->setTipoVeiculo($dados['tipoVeiculo']);
        $janela->setPlacaCavalo($dados['placa_cavalo']);
        $janela->setPlacaCarreta($dados['placa_carreta']);
        $janela->setStatus($dados['status']);
        $janela->setPosicao($dados['posicao']);
        $janela->setOperacao($dados['operacao']);
        $janela->setDoca($dados['doca']);
        $janela->setNf($dados['nf']);
        $janela->setDataInclusao($dados['dataInclusao']);
        $janela->setNomeusuario($dados['usuario']);
        $janela->setHoraChegada($dados['horaChegada']);
        $janela->setInicioOperacao($dados['inicio_operacao']);
        $janela->setFimOperacao($dados['fim_operacao']);
        $janela->setArmazem($dados['armazem']);
        $janela->setPeso($dados['peso']);
        $janela->setDestino($dados['destino']);
        $janela->setOperacaoTabela($dados['operacao_tabela']);
        $janela->setDataOperacaoTabela($dados['data_operacao_tabela']);
        $janela->setUsuarioOperacaoTabela($dados['usuario_operacao_tabela']);
        $arrayLogJanelas[$count] = $janela;
        $count++;
      } 
      return $arrayLogJanelas;
    }

    //funçõeos de loop
    function loop_JanelasOcupadas($result, $mysql){
      $arrayJanelas = array();
      $count = 0;
      while ($dados = $result->fetch_assoc()){ 
        $janela = new Janela();
        $janela->setId($dados['id']);
        $horario = new Horario();
        $horarioId = $horario->buscarHorario($dados['id_horario'], $mysql);
        while ($dadosHor = $horarioId->fetch_assoc()){
          $janela->setIdhorario($dadosHor['descricao']);
        }
        $janela->setData($dados['data']);
        $janela->setTransportadora($dados['transportadora']);
        $janela->setOferta($dados['oferta']);
        $janela->setTipoVeiculo($dados['tipoVeiculo']);
        $janela->setPlacaCavalo($dados['placa_cavalo']);
        $janela->setPlacaCarreta($dados['placa_carreta']);
        $janela->setStatus($dados['status']);
        $janela->setPosicao($dados['posicao']);
        $janela->setOperacao($dados['operacao']);
        $janela->setNf($dados['nf']);
        $janela->setDataInclusao($dados['dataInclusao']);
        $janela->setNomeusuario($dados['usuario']);
        $janela->setHoraChegada($dados['horaChegada']);
        $janela->setInicioOperacao($dados['inicio_operacao']);
        $janela->setFimOperacao($dados['fim_operacao']);
        $janela->setArmazem($dados['armazem']);
        $janela->setPeso($dados['peso']);
        $janela->setDestino($dados['destino']);
        $arrayJanelas[$count] = $janela;
        $count++;
      }
      return $arrayJanelas;
    }
?>