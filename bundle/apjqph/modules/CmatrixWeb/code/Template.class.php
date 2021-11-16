<?php
namespace CmatrixWeb;
use \Cmatrix\Exception as ex;

class Template {
    static $INSTANCES = [];
    
    static $TEMPLATES = [];
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
            case 'Data' : return $this->getMyData();
        }
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    private function getMyPath(){
        $Arr = explode('/',ltrim($this->Url,'/'));
        $Module = array_shift($Arr);
        $Path = '/' . $Module . '/templates/' . implode('/',$Arr);
        
        if(!file_exists(CM_ROOT.'/modules'.'/'.$Path)) throw new ex('Template "' .$this->Url. '" is not defined.');        
        return $Path;
    }
    
    // --- --- --- --- ---
    private function getMyData(){
        return file_get_contents($this->Path);
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance($url){
        $Key = $url;
        if(array_key_exists($Key,self::$INSTANCES)) return self::$INSTANCES[$Key];
        return new self($url);
    }
    /*    
    // --- --- --- --- ---
    static function add($name,$url){
        if(array_key_exists($name,self::$TEMPLATES)) throw new ex('Template "'.$name.'" allready exists.');
        return self::$TEMPLATES[$name] = self::instance($url);
    }
    
    // --- --- --- --- ---
    static function get($name){
        if(!array_key_exists($name,self::$TEMPLATES)) throw new ex('Template "'.$name.'" is not exists.');
        return self::$TEMPLATES[$name];
    }
    */
}
?>