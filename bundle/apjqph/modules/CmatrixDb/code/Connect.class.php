<?php
namespace CmatrixDb;
use \Cmatrix as cm;
use \Cmatrix\Exception as ex;

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
    public function query($query,$mode=null){
        return $this->Driver->query($query,$mode);
    }

    // --- --- --- --- ---
    public function exec($query,$mode=null){
        return $this->Driver->exec($query,$mode);
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

    // --- --- --- --- ---
    static function get($name='db'){
        $Config = cm\Hash::getFile(CM_TOP.'/config.json')->getValue($name);
        if(!$Config) throw new ex('Connect config is not defined.');
        
        return self::instance($Config);
    }    
}
?>