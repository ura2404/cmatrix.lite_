<?php
namespace Cmatrix\Structure;
use \Cmatrix as cm;
use \Cmatrix\Structure as st;

class Pk {
    static $INSTANCES = [];
    
    protected $Datamodel;
    protected $Props;
    
    // --- --- --- --- ---
    function __construct(iProvider $datamodel, array $props){
        $this->Datamodel = $datamodel;
        $this->Props = $props;
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
        return $Provider->transName(
            strtolower(
                Table::instance($this->Datamodel)->Name .'__pk__'. implode(',',
                    array_map(
                        function($prop){ return Prop::instance($this->Datamodel,$prop)->Name; }
                    ,$this->Props)
                )
            )
        );
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance(iProvider $datamodel, array $props){
        $Key = md5($datamodel->Datamodel->Url . implode($props));
        if(isset(self::$INSTANCES[$Key])) return self::$INSTANCES[$Key];
        return self::$INSTANCES[$Key] = new self($datamodel, $props);
    }
}
?>