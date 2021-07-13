<?php
header("Content-type: application/json");
//header("Content-type: application/octet-stream");
require_once('../defs.php');
require_once('../common.php');

$Data = \Cmatrix\Req::get()->Array;

// --- --- --- --- ---
$Messgae;
try{
    switch($Data['m']){
        case 'li' : $Message = \Cmatrix\Session::login($Data['u'],$Data['p'])->Message;
                    break;
        case 'lo' : $Message = \Cmatrix\Session::logout()->Message;
                    break;
            
        default : throw new \Exception('Bad mode "' .$Data['m']. '"');
    }
    
    echo \Cmatrix\Req::create([
        'status' => 1,
        'message' => $Message
    ])->Json;
}

catch(\Throwable2 $e){
    dump($e->getMessage());
}
?>