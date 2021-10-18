<?php
header("Content-type: application/json");
//header("Content-type: application/octet-stream");

require_once('../../../defs.php');
require_once('../../../common.php');

$Data = \CmatrixWeb\Request::get()->Array;

// --- --- --- --- ---
$Messgae;
try{
    switch($Data['m']){
        case 'li' : \CmatrixCore\Session::instance()->login($Data['u'],$Data['p']);
                    $Message = 'Вы успешно зарегистрированы.';
                    break;
            
        case 'lo' : \CmatrixCore\Session::instance()->logout();
                    $Message = 'Ваш сеанс успешно завершён.';
                    break;
            
        default : throw new \Exception('Bad mode "' .$Data['m']. '"');
    }
    
    echo \CmatrixWeb\Request::create([
        'status' => 1,
        'message' => $Message
    ])->Json;
}

catch(\Throwable $e){
    dump($e);
    
    echo \CmatrixWeb\Request::create([
        'status' => -1,
        'message' => $e->getMessage()
    ])->Json;
}
?>