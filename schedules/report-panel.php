<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once('../utils.php');
require_once('../controller/scheduleChartsController.php');

setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');

$scheduleChartsController = new ScheduleChartsController($MySQLi);

$currentYear = date("Y");
$currentMonth = date("m");

$selectedMonth = $currentMonth;
$selectedYear = $currentYear;

$startDate;
$endDate;
$chartPath;

$period;

if( isset($_POST['year']) && $_POST['year'] != null){
    $selectedYear = $_POST['year'];
}

if( isset($_POST['month']) && $_POST['month'] != null){
    $selectedMonth = $_POST['month'];
}

if( isset($_GET['frequency']) && $_GET['frequency'] != null){

    $frequency = $_GET['frequency'];
    
    if($frequency == 'yearly'){
        $period = 'Anual';
        $startDate = $selectedYear.'-01-01 00:00:00';
        $endDate = $selectedYear.'-12-31 23:59:59';

        $chartPath = 'charts/yearly-operation.php';

        $LOAD_INLOAD = $scheduleChartsController->findByClienteAndStartDateAndEndDateAndStatus($startDate, $endDate, 'Liberado');
    }else{
        $period = 'Mensal';
        $startDate = $selectedYear.'-'.$selectedMonth.'-01 00:00:00';
        $endDate = $selectedYear.'-'.$selectedMonth.'-31 23:59:59';

        $chartPath = 'charts/montly-operation.php';

        $LOAD_INLOAD = $scheduleChartsController->findByClienteAndStartDateAndEndDateAndStatus($startDate, $endDate, 'Liberado');
    }
}


?>
<head>
    <meta charset="utf-8">
</head>
<body class="body-chart">
    <div class="row">
        <div class="col-lg-12">
            <div class="panel-title">
                <p>Dashboard <?=$period?></p>
            </div>
            <div class="functions-group">
                <form method="post" action="#">
                    <div class="row-element-group">
                        

                            <?php

                            if($frequency == 'montly'){
                                echo '<div class="form-group">';
                                echo '<label>Mes</label>';
                                echo '<select name="month" id="month" class="form-control" aria-label="Default select example" required>';
                                foreach ($GLOBAL_MONTHS as $key => $month) {
                                    $selected = '';
                                    
                                    if($key == $selectedMonth) $selected = 'selected';
                                    
                                    echo '<option value="'.$key.'" '.$selected.'>'.$month.'</option>';
                                }

                                echo ' </select>';
                                echo '</div>';
                            }
                           
                            ?>

                        <div class="form-group">
                            <label>Ano</label>
                            <select name="year" id="year" class="form-control" aria-label="Default select example" required>
                                <?php

                                $optionYear = 2022;
                            
                                while ($optionYear <= $currentYear) {

                                    $selected = '';

                                    if($optionYear == $selectedYear) $selected = 'selected';
                                    
                                    echo '<option value="'.$optionYear.'" '.$selected.'>'.$optionYear.'</option>';
                                    $optionYear = $optionYear + 1;
                                }
                                
                                ?>
                            </select>
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
        <?php
        include($chartPath);
        ?>
    </div>
</body>