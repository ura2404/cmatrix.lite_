<?php
namespace Cmatrix;

class Lang {
    static $INSTANCES = [];
    private $Lang;

    // --- --- --- --- ---
    function __construct(){
        $this->Lang = Hash::getFile(CM_TOP.'/config.json')->getValue('project/lang','_def');
    }

    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Lang' : return $this->Lang;
            default : throw new ex\Property($this,$name);
        }
    }

    // --- --- --- --- ---
    private function getData(){
        return $this->Data;
    }

    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    public function getStr($str){
        if(!is_array($str)) return $str;
        return isset($str[$this->Lang]) ? $str[$this->Lang] : (isset($str['_def']) ? $str['_def'] : null);
        false;
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance(){
        return new self();
    }

    // --- --- --- --- ---
    static function str($str){
        $Key = md5('current');
        if(isset(self::$INSTANCES[$Key])) return self::$INSTANCES[$Key];
        return self::instance()->getStr($str);
    }
}
?>