<?php
namespace CmatrixCore;
use \Cmatrix as cm;
use \CmatrixDb as db;
use \Cmatrix\Exception as ex;

class Sysuser {
    static $INSTANCES = [];
    
    // --- --- --- --- ---
    function __construct(){
    }

    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    public function isMyGroups(...$groups){
        return true;
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance(){
        $Key = md5('current');
        if(isset(self::$INSTANCES[$Key])) return self::$INSTANCES[$Key];
        
        return self::$INSTANCES[$Key] = new self;
    }
}
?>