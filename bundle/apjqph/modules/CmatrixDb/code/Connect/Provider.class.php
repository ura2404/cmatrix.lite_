<?php
namespace CmatrixDb\Connect;
use \Cmatrix as cm;

class Provider{
    static $INSTANCES = [];
    
    static $PROVIDERS = [
        'mysql' => '\CmatrixDb\Connect\Provider\Mysql',
        'pgsql' => '\CmatrixDb\Connect\Provider\Pgsql'
    ];

    // --- --- --- --- ---
    static function instance($providerName){
        if(!in_array($providerName,array_keys(self::$PROVIDERS))) throw new cm\Exception('Wrong connect provider "' .$providerName. '"');
        
        $Key = md5($providerName);
        
        if(isset(self::$INSTANCES[$Key])) return self::$INSTANCES[$Key];
        
        $ClassName = self::$PROVIDERS[$providerName];
        return self::$INSTANCES[$Key] = new $ClassName;
    }
    
}
?>