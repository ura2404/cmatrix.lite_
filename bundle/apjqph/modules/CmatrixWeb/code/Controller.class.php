<?php
namespace CmatrixWeb;
use \Cmatrix\Exception as ex;

class Controller {
    static $INSTANCES = [];
    private $Url;
    
    // --- --- --- --- ---
    function __construct($url){
        $this->Url = $url;
        $this->Path;
    }
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Path' : return $this->getMyPath();
            case 'Controller' : return $this->getMyController();
            default : throw new ex\Property($this,$name);
        }
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    private function getMyPath(){
        $Arr = explode('/',ltrim($this->Url,'/'));
        $Module = array_shift($Arr);
        $Path = '/' . $Module . '/controllers/' . implode('/',$Arr) . '.controller.php';
        
        if(!file_exists(CM_ROOT.'/modules'.'/'.$Path)) throw new ex('Controller "' .$this->Url. '" is not defined.');
        return $Path;
    }
    
    // --- --- --- --- ---
    private function getMyController(){
        $Arr = explode('/',ltrim($this->Url,'/'));
        $Module = array_shift($Arr);
        $Cl = '\\' . $Module . '\Controllers\\' . implode('\\',$Arr);
        return new $Cl();
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance($url){
        $Key = $url;
        if(array_key_exists($Key,self::$INSTANCES)) return self::$INSTANCES[$Key];
        return (new self($url))->Controller;
    }
}
?>