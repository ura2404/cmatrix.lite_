<?php
namespace Cmatrix\Db;
use \Cmatrix as cm;

class Provider {
    static $PROVIDERS = [
        'mysql' => '\Cmatrix\Db\Providers\Mysql',
        'pgsql' => '\Cmatrix\Db\Providers\Pgsql'
    ];
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Type' : return $this->getType();
        }
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    public function getScript($provider,$isFormat=false){
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance($name){
        if(!in_array($name,array_keys(self::$PROVIDERS))) throw new \Exception('Wrong provider "' .$name. '"');
        $Cl = self::$PROVIDERS[$name];
        return new $Cl();
    }
}
?>