<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try{
    $sql = "SELECT logError.id as logId, dateError, usuario.nome as username, description 
            FROM logError
            INNER JOIN usuario ON usuario.id = userId
            ORDER BY logError.id DESC LIMIT 400";  

    $result = $MySQLi->query($sql);

    $logs = array();
    $row = array();

    while ($data = $result->fetch_assoc()){ 
        $row = array();
        $row['id'] = $data['logId'];
        $row['date'] = $data['dateError'];
        $row['user'] = $data['username'];
        $row['detail'] = $data['description'];
        array_push($logs, $row);
     }


}catch(Exception $e){
    echo 'ERRRo:';
    var_dump($e);
}

?>

<div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-body">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th scope="column" style="max-width: 20px">ID</th>
                                    <th scope="column" style="max-width: 20px">Data e Hora</th>
                                    <th scope="column" style="max-width: 50px">Usuário</th>
                                    <th scope="column" style="max-width: 500px">Detalhes</th>
                                    <th scope="column" style="max-width: 50px">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
    
                                    foreach ($logs as $log) {
                                        echo '<tr>';
                                        echo '<td class="text-center">'.$log['id'].'</td>';
                                        echo '<td class="text-center">'. date("d/m/Y H:i:s", strtotime($log['date'])).'</td>';
                                        echo '<td class="text-center">'.$log['user'].'</td>';
                                        echo '<td class="text-center">'.$log['detail'].'</td>';
                                        echo '<td><button data-toggle="modal" data-target="#'.$log['id'].'" class="btn btn-primary">Detalhe</button></td>';
                                        echo '<div class="modal fade" id="'.$log['id'].'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
                                        echo '<div class="modal-dialog">';
                                        echo '<div class="modal-content">';
                                                
                                        echo '<div class="modal-header">';
                                        echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                                        echo '<h4 class="modal-title" id="myModalLabel">Detalhe - LOG:'. $log['id'] .'</h4>';
                                        echo '</div>';
                                        echo ' <div class="modal-body">';
                                        echo $log['detail'];
                                        echo '</div>';
                                        echo '<div class="modal-footer">';
                                        echo '<button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>';
                                        echo '</div>';
                                        echo '</div>';
                                        echo '</div>';
                                        echo '</tr>';
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

