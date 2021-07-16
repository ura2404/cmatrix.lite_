<?php
require_once 'Twig/autoload.php';
require_once CM_ROOT.'/modules/Cmatrix/code/utils.php';

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
    else{
        $Path .= '/code/'. implode('/',$Arr) .'.class.php';
    }
    
    if(file_exists($Path)) require_once($Path);
    else throw new \Exception('Class "'. $className .'" file not found.');
    
},true,true);
?>