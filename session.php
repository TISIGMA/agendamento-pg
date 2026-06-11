<?php

use Illuminate\Database\Capsule\Manager as Capsule;

define('ROOT_PATH', dirname(__FILE__));

$GLOBAL_MONTHS = ['01'=>'JANEIRO', '02'=>'FEVEREIRO', '03'=>'MARÇO', '04'=>'ABRIL', '05'=>'MAIO', '06'=>'JUNHO', '07'=>'JULHO', '08'=>'AGOSTO', '09'=>'SETEMBRO', '10'=>'OUTUBRO', '11'=>'NOVEMBRO', '12'=>'DEZEMBRO'];

function sec_session_start() {  
    error_reporting(0);
    date_default_timezone_set("America/Sao_Paulo");
    $session_name = 'logado'; 
    $secure=false;
    $httponly = true;

    if (ini_set('session.use_only_cookies', 1) === FALSE) {
       header('Location:index.php');
        exit();
    }
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"],
        $cookieParams["path"], 
        $cookieParams["domain"], 
        $secure,
        $httponly);
    
    session_name($session_name);
    session_start();            

    session_regenerate_id();    
    
}

$pagesNotClearPost = ['searchSchedule.php', 'newSchedule.php', 'searchDailyInfo.php', 'newDailyInfo.php','tracking/attTrackingView.php','tracking/scheduleTrackingView.php'];

sec_session_start();

$contentPost = '';

if(isset($_GET['conteudo']) && $_GET['conteudo'] != null){
    $contentPost = $_GET['conteudo'];
}

if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] =='POST' && !in_array($contentPost, $pagesNotClearPost)){
    
    $request = md5(implode( $_POST ) );

    if( isset( $_SESSION['last_request'] ) && $_SESSION['last_request'] == $request ) {
        unset($_POST);
        $_SESSION['last_request'] = '';
    }
    else {
        $_SESSION['last_request'] = $request;
    }
    
}

function checkNotification($mysqli){

    try {
        if (!session_can_use_eloquent()) {
            return;
        }

        $row = Capsule::table('notification')
            ->select(['message', 'duration', 'created_date'])
            ->limit(1)
            ->first();

        if ($row != null) {
            $_SESSION['message'] = $row->message;
            $_SESSION['duration'] = $row->duration;
            $_SESSION['created_date'] = $row->created_date;
            $_SESSION['message_readed'] = false;
        }

        return;
    } catch (Exception $ex) {
        return;
    }

}


function login($usuario, $senha, $mysqli = null) {

    $data = date('d/m/Y');
    $hora = date('h:i');

    try {
        if (!session_can_use_eloquent()) {
            return false;
        }

        $user = Capsule::table('usuario')
            ->select(['id', 'nome', 'username', 'password', 'dataInclusao', 'tipo'])
            ->where('username', $usuario)
            ->limit(1)
            ->first();

        if ($user == null) {
            return false;
        }

        if ($senha == $user->password) {
            $_SESSION['id'] = $user->id;
            $_SESSION['nome'] = $user->nome;
            $_SESSION['username'] = $user->username;
            $_SESSION['tipo'] = $user->tipo;
            getAccess($mysqli);
            return true;
        }

        return false;
    } catch (Throwable $ex) {
        error_log('ORM login error: ' . $ex->getMessage());
        return false;
    }
}

function getAccess($mysqli = null){

    $FUNCTION_ACCESS = [
        'schedule'=> 'hidden',
        'schedule_new'=> 'hidden',
        'schedule_list'=> 'hidden',
        'register'=> 'hidden',
        'register_operation_type'=> 'hidden',
        'register_truck_type'=> 'hidden',
        'register_shipping_company'=> 'hidden',
        'register_log'=> 'hidden',
        'register_report'=> 'hidden',
        'register_operation_source' => 'hidden',
        'register_employee' => 'hidden',
        'tracking' => 'hidden'
    ];

    $userType = isset($_SESSION['tipo']) ? $_SESSION['tipo'] : '';

    try {
        if (!session_can_use_eloquent()) {
            $_SESSION['FUNCTION_ACCESS'] = $FUNCTION_ACCESS;
            return;
        }

        $rows = Capsule::table('user_access')
            ->select(['id', 'userType', 'functionName'])
            ->where('userType', $userType)
            ->get();

        foreach ($rows as $row) {
            $functionName = $row->functionName;
            if (array_key_exists($functionName, $FUNCTION_ACCESS)) {
                $FUNCTION_ACCESS[$functionName] = '';
            }
        }

        $_SESSION['FUNCTION_ACCESS'] = $FUNCTION_ACCESS;
    } catch (Throwable $ex) {
        error_log('ORM access error: ' . $ex->getMessage());
        $_SESSION['FUNCTION_ACCESS'] = $FUNCTION_ACCESS;
    }
}

function login_check($mysqli = null) {

    if (isset($_SESSION['username'])){
        $username = $_SESSION['username'];

        try {
            if (!session_can_use_eloquent()) {
                return false;
            }

            $exists = Capsule::table('usuario')
                ->where('username', $username)
                ->exists();

            if (!$exists) {
                return false;
            }

            if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 7200)) {
                session_unset();     
                session_destroy();  
                return false;
            }

            $_SESSION['LAST_ACTIVITY'] = time();
            return true;
        } catch (Throwable $ex) {
            error_log('ORM login_check error: ' . $ex->getMessage());
            return false;
        }
        
    } else  return false;
}

function session_can_use_eloquent(){
    return class_exists(Capsule::class);
}
