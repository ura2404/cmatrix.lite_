<?php
namespace Cmatrix\Structure;
use \Cmatrix as cm;
use \Cmatrix\Structure as st;

class Sequence {
    static $INSTANCES = [];
    
    protected $PropName;
    protected $Datamodel;
    
    // --- --- --- --- ---
    function __construct(iProvider $datamodel, $propName){
        $this->Datamodel = $datamodel;
        $this->PropName = $propName;
    }
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Name' : return $this->getMyName();
        }
    }

    // --- --- --- --- ---
    private function getMyName(){
        $Provider = $this->Datamodel->Provider;
        return $Provider->transName(strtolower(Table::instance($this->Datamodel)->Name .'__seq__'. Prop::instance($this->Datamodel,$this->PropName)->Name));
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance(iProvider $datamodel, $propCode){
        $Key = md5($datamodel->Datamodel->Url . $propCode);
        if(isset(self::$INSTANCES[$Key])) return self::$INSTANCES[$Key];
        return self::$INSTANCES[$Key] = new self($datamodel, $propCode);
    }
}
?>