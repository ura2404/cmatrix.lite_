<?php
namespace CmatrixDb\Connect;
use \Cmatrix as cm;

class Database{
    static $INSTANCES = [];
    
    static $DATABASES = [
        'pgsql' => '\CmatrixDb\Connect\Database\Pgsql',
        'mysql' => '\CmatrixDb\Connect\Database\Mysql',
        'sqlite3' => '\CmatrixDb\Connect\Database\Sqlite3',
    ];

    // --- --- --- --- ---
    static function instance(array $config){
        $ProviderName = $config['type'];
        if(!in_array($ProviderName,array_keys(self::$DATABASES))) throw new cm\Exception('Wrong connect database "' .$ProviderName. '"');
        
        $Key = md5(serialize($config));
        if(isset(self::$INSTANCES[$Key])) return self::$INSTANCES[$Key];
        
        $ClassName = self::$DATABASES[$ProviderName];
        return self::$INSTANCES[$Key] = new $ClassName($config);
    }
    
}
?>