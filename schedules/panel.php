<?php

require_once('../controller/scheduleController.php');
require_once('../utils.php');

date_default_timezone_set("America/Sao_Paulo");


$scheduleController = new ScheduleController($MySQLi);

$statusList = ['Agendado','Aguardando', 'Em operação', 'Fim de operação', 'Liberado'];

$startDate = date("d/m/Y") . ' 00:00:00';
$endDate = date("d/m/Y") . ' 23:59:59';

$scheduled = 0;
$waiting = 0;
$inOperation = 0;
$operationDone = 0;
$done = 0;
$inlocal = 0;

if(isset($_GET['startDate']) && $_GET['startDate'] != null){

    $startDate = $_GET['startDate'];
    $endDate = $_GET['endDate'];
}


if(isset($_POST['startDate']) && $_POST['startDate'] != null){

    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
}

// if(isset($_POST['order-action']) && $_POST['order-action'] != null){

//     if(isset($_POST['column']) && count($_POST['column']) > 0){
        
//         $result = $scheduleController->savePreferences($columns, $_POST);

//         switch ($result) {
//             case 'SAVED':
//                 successAlert('Preferências salvas com sucesso!');
//                 break;
            
//             case 'UPDATED':
//                 successAlert('Preferências atualizadas com sucesso!');
//                 break;
            
//             case 'SAVE_ERROR':
//                 errorAlert('Ocorreu um erro ao salvar a preferência. Tente novamente ou entre em contato com o administrador.');
//                 break;
//         }
//     }
// }

$schedules = $scheduleController->findByClientStatusStartDateAndEndDate($_SESSION['customerName'], 'Todos', $startDate, $endDate);

$scheduled = 0;
$waiting = 0;
$inOperation = 0;
$operationDone = 0;
$done = 0;

foreach ($schedules as $schedule) {

    switch ($schedule['getStatus']) {
        case 'Agendado':
            $scheduled++;
            break;

        case 'Aguardando':
            $waiting++;
            $inlocal++;
            break;

        case 'Em operação':
            $inOperation++;
            $inlocal++;
            break;

        case 'Fim de operação':
            $operationDone++;
            $inlocal++;
            break;

        case 'Liberado':
            $done++;
            break;
        
        default:
            $scheduled++;
            break;
    }
    
}

?>

<body onload="progressTimer()">
    <div class="row">
        <div class="col-lg-12">
            <div class="panel-title">
                <p>Painel</p>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="automatedTimeSwitch" onchange="HandleChangeAutomatedTimeSwitch()" checked>
                    <label class="form-check-label" for="automatedTimeSwitch">
                        Ativar atualização automática
                    </label>
                </div>
            </div>
            <div class="functions-group">
                <form method="post" id="panel-form" action="index.php?conteudo=panel.php&startDate=<?=$startDate?>&endDate=<?=$endDate?>">
                    <div class="row-element-group">
                        <div class="form-group">
                            <label>Data inicial</label>
                            <div class='input-group date' id='datetimepicker1'>
                                <input name="startDate" id="startDate" type='text' data-date-format="DD/MM/YYYY HH:mm:ss" class="form-control" value="<?=$startDate ?>" onblur="dateTimeHandleBlur(this)" required  minlength="19" maxlength="19" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Data final</label>
                            <div class='input-group date' id='datetimepicker1'>
                                <input name="endDate" id="endDate" type='text' data-date-format="DD/MM/YYYY HH:mm:ss" class="form-control" onblur="dateTimeHandleBlur(this)" value="<?=$endDate ?>" minlength="19" maxlength="19"  required/>
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Buscar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel-title">
                <h1 class="display-2">Agendamentos</h1>
            </div>
            <div class="label-terminal-panel">
                <div>
                    <span>Total de <span class="label-big-number"><?=$inlocal ?></span> veículos no terminal</span>
                    
                </div>
            </div>
            <div class="panel-home">
                <div class="schedule-box-status box-gray" onclick="navigateToSearch('Agendado')">
                    <div class="box-home-header">
                        <p>Agendados</p>
                        <img src="../images/home-icons/schedule-truck.png"></img>
                        <p class="home-box-text"><?=$scheduled ?></p>
                    </div>
                </div>
                <div class="schedule-box-status box-orange" onclick="navigateToSearch('Aguardando')">
                    <div class="box-home-header">
                        <p>Aguardando</p>
                        <img src="../images/home-icons/empty-truck.png"></img>
                        <p class="home-box-text"><?=$waiting ?></p>
                    </div>
                </div>
                <div class="schedule-box-status box-blue" onclick="navigateToSearch('Em operação')">
                    <div class="box-home-header">
                        <p>Em operação</p>
                        <img src="../images/home-icons/operation-truck.png"></img>
                        <p class="home-box-text"><?=$inOperation ?></p>
                    </div>
                </div>
                <div class="schedule-box-status box-yellow" onclick="navigateToSearch('Fim de operação')">
                    <div class="box-home-header">
                        <p>Fim de operação</p>
                        <img src="../images/home-icons/full-truck.png"></img>
                        <p class="home-box-text"><?=$operationDone ?></p>
                    </div>
                </div>
                <div class="schedule-box-status box-green" onclick="navigateToSearch('Liberado')">
                    <div class="box-home-header">
                        <p>Liberados</p>
                        <img src="../images/home-icons/done-truck.png"></img>
                        <p class="home-box-text"><?=$done ?></p>
                    </div>
                    
                </div>
            </div>
            <div class="panel-progress">
                <progress id="panel-progress" value="60000" max="60000"></progress>
            </div>
        </div>
    </div>
</body>