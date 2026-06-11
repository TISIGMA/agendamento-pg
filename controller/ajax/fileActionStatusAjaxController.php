<?php

require_once('../../conn.php');
require_once('../../session.php');
require_once('../../repository/scheduleRepository.php');
require_once('../../model/schedule.php');

$repository = new ScheduleRepository($MySQLi);

$scheduleId = $_GET['scheduleId'];
$actionType = $_GET['field'];
$actionValue = $_GET['action'];

$field;

switch ($actionType) {
    case 'picking':{
        $field = 'attatchment_picking_status';
        break;
    }
    case 'invoice':{
        $field = 'attatchment_invoice_status';
        break;
    }
    case 'certificate':{
        $field = 'attatchment_certificate_status';
        break;
    }
    case 'boarding':{
        $field = 'attatchment_boarding_status';
        break;
    }
    case 'other':{
        $field = 'attatchment_other_status';
        break;
    }
}

$result = $repository->updateAttAction($scheduleId, $field, $actionValue); 

if($result->hasError){
    echo false;
}else{
    echo true;
}
?>