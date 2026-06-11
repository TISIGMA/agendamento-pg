<?php

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

if( $_SERVER['REQUEST_METHOD'] =='POST' && !in_array($contentPost, $pagesNotClearPost)){
    
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
        if ($stmt = $mysqli->prepare("SELECT message, duration, created_date FROM notification LIMIT 1")){  
        
            $stmt->execute();
            $stmt->store_result();
    
            $stmt->bind_result($message, $duration, $created_date);
            $stmt->fetch();
    
            if ($stmt->num_rows == 1){ 
                
                $_SESSION['message'] = $message;
                $_SESSION['duration'] = $duration;
                $_SESSION['created_date'] = $created_date;
                $_SESSION['message_readed'] = false;            
            }
        }
        return;
    } catch (Exception $ex) {
        return;
    }

}


function login($usuario, $senha, $mysqli) {

    $data = date('d/m/Y');
    $hora = date('h:i');

    if ($stmt = $mysqli->prepare("SELECT id,nome, username, password, dataInclusao, tipo FROM usuario  WHERE username = ? LIMIT 1")){  
        
        $stmt->bind_param('s', $usuario);
        $stmt->execute();
        $stmt->store_result();

        $stmt->bind_result($id, $nome, $username,$password, $data_inclusao, $tipo);
        $stmt->fetch();

        if ($stmt->num_rows == 1){ 

            if ($senha == $password){ 
                $_SESSION['id'] = $id;
                $_SESSION['nome'] = $nome;
                $_SESSION['username'] = $username;
                $_SESSION['tipo'] = $tipo;
                getAccess($mysqli);
                return true;

            } else return false;
            
        } else return false;
    }
}

function getAccess($mysqli){

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

    $sql = "SELECT id, userType, functionName
            FROM user_access 
            WHERE userType = '".$_SESSION['tipo'] ."'";

                    
    $result = $mysqli->query($sql);

    while ($data = $result->fetch_assoc()){ 
        $FUNCTION_ACCESS[$data['functionName']] = '';

    }

    $_SESSION['FUNCTION_ACCESS'] = $FUNCTION_ACCESS;
}

function login_check($mysqli) {

    if (isset($_SESSION['username'])){
        $username = $_SESSION['username'];
        if ($stmt = $mysqli->prepare("SELECT id,nome, username, password, dataInclusao, tipo FROM usuario  WHERE username = ? LIMIT 1")){
            $stmt->bind_param('i', $id);
            $stmt->execute();  
            $stmt->store_result();

            if($stmt->num_rows == 1) {
                if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 7200)) {
                    session_unset();     
                    session_destroy();  
                    return false;
                }
                else{
                    $_SESSION['LAST_ACTIVITY'] = time();
                    return true;
                }
            }else return false;
        } 

        else return false;
        
    } else  return false;
}