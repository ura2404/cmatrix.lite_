<?php
namespace CmatrixDb\Connect;
use \Cmatrix as cm;

class Driver{
    static $INSTANCES = [];
    
    static $DRIVERS = [
        'pdo' => '\CmatrixDb\Connect\Driver\Pdo',
        'pgsql' => '\CmatrixDb\Connect\Driver\Pgsql',
        'mysql' => '\CmatrixDb\Connect\Driver\Mysql',
        'sqlite3l' => '\CmatrixDb\Connect\Driver\Sqlite3',
    ];

    // --- --- --- --- ---
    static function instance(array $config, iDatabase $database){
        $DriverName = $config['driver'];
        if(!in_array($DriverName,array_keys(self::$DRIVERS))) throw new cm\Exception('Wrong connect driver "' .$DriverName. '"');
        
        $Key = md5(serialize($config));
        if(isset(self::$INSTANCES[$Key])) return self::$INSTANCES[$Key];
        
        $ClassName = self::$DRIVERS[$DriverName];
        return self::$INSTANCES[$Key] = new $ClassName($config,$database);
    }
    
}
?>