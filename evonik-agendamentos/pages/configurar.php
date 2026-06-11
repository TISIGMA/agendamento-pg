<?php
    require('class.php');
    require('../../conn.php');
    require('../functions.php');

    if(isset($_GET['id']) && $_GET['id'] != null){
        $result = editHorarioStatus($MySQLi, $_GET['id'], $_GET['status']);
        if($result == true){
            messageSuccess('Horário bloqueado com sucesso!');
        }else{
            messageErro('Erro ao bloquear o horário.<br>Tente mais tarde!');
        }
    }
    $horario = new Horario();
    $horarios = buscarHorarios($horario, $MySQLi);

?>
   
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Horários</h1>
                </div>
            </div>
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                        <div class="panel-body">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
                                        <th>Horário</th>
                                        <th>Armazém</th>
                                        <th>Posição</th>
                                        <th>status</th>
                                        <th>Gerenciar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                        <?php
                                            $contLin = 0;
                                            foreach($horarios as $horario) {
                                                
                                                $class = 'odd gradeX ';
                                                switch($horario->getStatus()){
                                                    case 'Livre':{
                                                        $evento = '<a title="Bloquear" id="unlock" onclick="editarHorario(this,'.$horario->getId().', '.$contLin.')" class="fa fa-lock"></a>';
                                                        break;
                                                    }
                                                    case 'Bloqueado':{
                                                        $TDTitulo = "Liberar";
                                                        $evento = '<a title="Liberar" id="lock" onclick="editarHorario(this,'.$horario->getId().', '.$contLin.')" class="fa fa-unlock"></a>';
                                                        $class = 'danger';
                                                        break;
                                                    }
                                                }
                                                echo '<tr id="tr'.$contLin.'" class="'.$class.'">'; 
                                                echo '<td>'.$horario->getHorario().'</td>';
                                                echo '<td>'.$horario->getArmazem().'</td>';
                                                echo '<td>'.$horario->getPosicao().'</td>';;
                                                echo '<td id="td'.$contLin.'">'.$horario->getStatus().'</td>';
                                                echo '<td>'.$evento.'</td>';
                                                echo '</tr>';
                                                $contLin++;
                                            }
                                        ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

    <script type="text/javascript">
        function editarHorario(element,id, numerador){
            tr = document.getElementById("tr"+numerador);//.style.backgroundColor = cor;
            tdStatus = document.getElementById("td"+numerador);
            if(element.id == "unlock"){
                status = "Bloqueado";
            }
            else{
                status = "Livre";
            } 
            if(window.XMLHttpRequest) {
                req = new XMLHttpRequest();
            }
            else if(window.ActiveXObject) {
                req = new ActiveXObject("Microsoft.XMLHTTP");
            }
            var url = "ajax-horarios.php?id="+id+"&status="+status;
            req.open("Get", url, true); 

            req.onreadystatechange = function() {
                if(req.readyState == 4 && req.status == 200) {
                    if(status == "Livre"){
                        element.className = "fa fa-lock";
                        element.id = "unlock";
                        tr.className = "odd gradeX";
                        tdStatus.innerHTML = "Livre";
                    }else{
                        element.className = "fa fa-unlock"; 
                        element.id = "lock";
                        tr.className = "danger";
                        tdStatus.innerHTML = "Bloqueado";
                    }
                    
                }
            }
            req.send(null);
        }
</script>
