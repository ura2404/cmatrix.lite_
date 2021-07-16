<?php
namespace Cmatrix;

class Controllers {
    static $CONTROLLERS = [];
    
    // --- --- --- --- ---
    function __construct(){
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    public function addController($name,$ob){
        if(array_key_exists($name,self::$CONTROLLERS)) throw new \Exception('Controller '.$name.' allready exists');
        
        self::$CONTROLLERS[$name] = $ob;
        return $this;
    }
    
    // --- --- --- --- ---
    public function getController($name){
        if(!array_key_exists($name,self::$CONTROLLERS)) throw new \Exception('Controller '.$name.' is not exists');
        return self::$CONTROLLERS[$name];
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance(){
        return new self();
    }
    
    // --- --- --- --- ---
    static function add($name,$ob){
        return self::instance()->addController($name,$ob);
    }
    
    // --- --- --- --- ---
    static function get($name){
        return self::instance()->getController($name);
    }
    
}
?>