<?php
namespace CmatrixDb;
use \Cmatrix as cm;

class Connect{
    static $INSTANCES = [];
    
    private $Driver;
    
    // --- --- --- --- ---
    function __construct(array $config){
        $this->Driver = Connect\Driver::instance($config,Connect\Database::instance($config));
    }

    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    public function query($query){
        return $this->Driver->query($query);
    }

    // --- --- --- --- ---
    public function exec($query){
        return $this->Driver->exec($query);
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance(array $config){
        $Key = md5(serialize($config));
        if(isset(self::$INSTANCES[$Key])) return self::$INSTANCES[$Key];
        return self::$INSTANCES[$Key] = new self($config);
    }
    
}
?>