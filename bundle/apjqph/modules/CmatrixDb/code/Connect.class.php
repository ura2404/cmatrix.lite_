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
    static function instance(array $config=null){
        $Config = $config ? $config : cm\Hash::getFile(CM_TOP.'/config.json')->getValue('db');
        if(!$Config) throw new ex('Connect config is not defined.');
        
        $Key = md5(serialize($Config));
        if(isset(self::$INSTANCES[$Key])) return self::$INSTANCES[$Key];
        return self::$INSTANCES[$Key] = new self($Config);
    }
    
}
?>