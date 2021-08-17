<?php
namespace Cmatrix;
use \Cmatrix as cm;

class Structure{
    static $INSTANCES = [];
    
    static $PROVIDERS = [
        'mysql' => '\Cmatrix\Structure\Providers\Mysql',
        'pgsql' => '\Cmatrix\Structure\Providers\Pgsql'
    ];
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    /**
     * @param string | \Cmatrix\Datamodel $datamodel - 
     * @param string | cm\Db\Provider $provider - 
     */
    static function instance($datamodel,$provider){
        $Datamodel = $datamodel instanceof cm\Ide\Datamodel ? $datamodel :  cm\Ide\Datamodel::instance($datamodel);
        $Provider = $provider instanceof cm\Db\iProvider ? $provider : cm\Db\Provider::instance($provider);
        
        if(!in_array($Provider->Type,array_keys(self::$PROVIDERS))) throw new \Exception('Wrong provider "' .$name. '"');
        
        $Key = md5($Datamodel->Url.$Provider->Type);
        if(isset(self::$INSTANCES[$Key])) return self::$INSTANCES[$Key];
        
        $Cl = self::$PROVIDERS[$Provider->Type];
        return self::$INSTANCES[$Key] = new $Cl($Datamodel,$Provider);
    }
}