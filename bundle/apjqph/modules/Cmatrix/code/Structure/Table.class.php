<?php
namespace Cmatrix\Structure;
use \Cmatrix as cm;
use \Cmatrix\Structure as st;

class Table {
    static $INSTANCES = [];
    
    protected $Provider;
    
    // --- --- --- --- ---
    function __construct(iProvider $provider){
        $this->Provider = $provider;
    }
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Name' : return $this->getMyName();
        }
    }
    
    // --- --- --- --- ---
    private function getMyName(){
        $Datamodel = $this->Provider->Datamodel;
        $Prefix = $this->Provider->Prefix;
        return $Prefix . str_replace('/','_',ltrim($Datamodel->Json['code'],'/'));
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    /**
     * @param Datamodel $datamodel
     */
    static function instance(iProvider $provider){
        $Key = md5($provider->Datamodel->Url);
        if(isset(self::$INSTANCES[$Key])) return self::$INSTANCES[$Key];
        return self::$INSTANCES[$Key]= new self($provider);
    }
}
?>