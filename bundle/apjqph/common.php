<?php
require_once CM_ROOT.'/modules/Cmatrix/code/utils.php';

switch(CM_MODE){
    case 'development' :
        ini_set('display_errors',1);
        error_reporting(-1);
        break;
    case 'production' :
        ini_set('display_errors',1);
        //error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
        error_reporting(~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
        //error_reporting(0);
        //E_CORE_WARNING
        break;
    default :
        header('HTTP/1.1 503 Service Unavailable.',true,503);
        echo 'Cmatrix fatal error: wrong environment or environment isn\'t defined.';
        exit(1);
}

spl_autoload_register(function($className){
    if(class_exists($className)) return;

    $Arr = explode("\\",$className);
    
    $Path = CM_ROOT .'/modules/'. $Arr[0];
    array_shift($Arr);
    
    if(!count($Arr)) return;
    
    if($Arr[0] === 'Models'){
        array_shift($Arr);
        $Path .= '/models/'. implode('/',$Arr) .'.model.php';
    }
    elseif($Arr[0] === 'Dm' && count($Arr)>1){
        array_shift($Arr);
        $Path .= '/dm/'. implode('/',$Arr) .'.dm.php';
    }
    else{
        $Path .= '/code/'. implode('/',$Arr) .'.class.php';
    }
    
    if(file_exists($Path)) require_once($Path);
    else throw new \Exception('Class "'. $className .'" file not found.');
    
},true,true);
?>