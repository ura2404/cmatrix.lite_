<?php
namespace Cmatrix\Structure;
use \Cmatrix as cm;
use \Cmatrix\Structure as st;

class Prop {
    static $INSTANCES = [];
    
    protected $Datamodel;
    protected $PropCode;
    
    // --- --- --- --- ---
    function __construct(iProvider $datamodel, $propCode){
        $this->Datamodel = $datamodel;
        $this->PropCode = $propCode;
    }
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Name' : return $this->getMyName();
            case 'Type' : return $this->getMyType();
            case 'Default' : return $this->getMyDefault();
            case 'NotNull' : return $this->getMyNn();
        }
    }
    
    // --- --- --- --- ---
    private function getMyName(){
        $Datamodel = $this->Datamodel->Datamodel;
        return $Datamodel->getProp($this->PropCode)['code'];
    }
    
    // --- --- --- --- ---
    private function getMyType(){
        $Provider = $this->Datamodel->Provider;
        $Datamodel = $this->Datamodel->Datamodel;
        return $Provider->getPropType($Datamodel->getProp($this->PropCode));
    }
    
    // --- --- --- --- ---
    private function getMyDefault(){
        $Provider = $this->Datamodel->Provider;
        $Datamodel = $this->Datamodel->Datamodel;
        $Prop = $Datamodel->getProp($this->PropCode);
        
        $Type = $Prop['type'];
        $Value = $Prop['default'];
        
        if($Value === '::counter::') $Value = "raw::" . $Provider->getSqlNextSequence(cm\Structure\Sequence::instance($this->Datamodel,$this->PropCode)->Name);
        elseif($Value === '::now::') $Value = "raw::" . $Provider->getSqlNow();
        elseif($Value === '::hid::') $Value = "raw::" . $Provider->getSqlHid();
        
        $Default = $Provider->sqlValue($Type,$Value);
        return $Default ? 'DEFAULT ' . $Default : null;
    }

    // --- --- --- --- ---
    private function getMyNn(){
        $Provider = $this->Datamodel->Provider;
        $Datamodel = $this->Datamodel->Datamodel;
        $Prop = $Datamodel->getProp($this->PropCode);
        return $Prop['nn'] === true ? $Provider->getSqlNotNull() : null;
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    /**
     * @param array $propCode - имя свойства сущности
     */
    static function instance(iProvider $datamodel, $propCode){
        $Key = md5($datamodel->Datamodel->Url . $propCode);
        if(isset(self::$INSTANCES[$Key])) return self::$INSTANCES[$Key];
        return self::$INSTANCES[$Key] = new self($datamodel, $propCode);
    }
}
?>