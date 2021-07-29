<?php
namespace Cmatrix\Structure;
use \Cmatrix as cm;
use \Cmatrix\Structure as st;

class Table {
    static $INSTANCES = [];
    
    protected $Datamodel;
    
    // --- --- --- --- ---
    function __construct(iProvider $datamodel){
        $this->Datamodel = $datamodel;
    }
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Name' : return $this->getMyName();
        }
    }
    
    // --- --- --- --- ---
    private function getMyName(){
        $Datamodel = $this->Datamodel->Datamodel;
        return $this->Datamodel->Prefix . str_replace('/','_',ltrim($Datamodel->Json['code'],'/'));
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    /**
     * @param Datamodel $datamodel
     */
    static function instance(iProvider $datamodel){
        $Key = md5($datamodel->Datamodel->Url);
        if(isset(self::$INSTANCES[$Key])) return self::$INSTANCES[$Key];
        return self::$INSTANCES[$Key]= new self($datamodel);
    }
}
?>