<?php
namespace CmatrixDb\Structure\Datamodel;
use \Cmatrix as cm;
use \CmatrixDb as db;
use \Cmatrix\Exception as ex;

class Provider{
    static $INSTANCES = [];
    
    static $PROVIDERS = [
        'mysql' => '\CmatrixDb\Structure\Datamodel\Provider\Mysql',
        'pgsql' => '\CmatrixDb\Structure\Datamodel\Provider\Pgsql'
    ];
    
    // --- --- --- --- ---
    protected $Datamodel;
    protected $ConnectProvider;
    protected $Prefix;
    
    // --- --- --- --- ---
    function __construct(cm\Ide\iDatamodel $datamodel, db\Connect\iProvider $connectProvider){
        $this->Datamodel = $datamodel;
        $this->ConnectProvider = $connectProvider;
        $this->Prefix = cm\Hash::getFile(CM_TOP.'/config.json')->getValue('db/prefix');
    }
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'ConnectProvider' : return $this->ConnectProvider;
            case 'SqlInitScript' : return $this->getSqlInitScript();
            default : throw new ex\Property($this,$name);
        }
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance($providerName, cm\Ide\iDatamodel $datamodel){
        if(!in_array($providerName,array_keys(self::$PROVIDERS))) throw new cm\Exception('Wrong structure provider "' .$providerName. '"');
        
        $Key = md5($providerName.$datamodel->Url);
        if(isset(self::$INSTANCES[$Key])) return self::$INSTANCES[$Key];
        
        $ConnectProvider = db\Connect\Provider::instance($providerName);
        
        $ClassName = self::$PROVIDERS[$providerName];
        return self::$INSTANCES[$Key] = new $ClassName($datamodel, $ConnectProvider);
    }
    
}
?>