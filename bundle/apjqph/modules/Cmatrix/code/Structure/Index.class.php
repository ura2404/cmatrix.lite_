<?php
namespace Cmatrix\Structure;
use \Cmatrix as cm;
use \Cmatrix\Structure as st;

class Index {
    static $INSTANCES = [];
    
    protected $Datamodel;
    protected $Props;
    protected $isUnique;
    
    // --- --- --- --- ---
    function __construct(iProvider $datamodel, array $props, $isUnique){
        $this->Datamodel = $datamodel;
        $this->Props = $props;
        $this->isUnique = $isUnique;
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
                Table::instance($this->Datamodel)->Name .'__'.($this->isUnique ? 'uniq' : 'index').'__'. implode('_',
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
    static function instance(iProvider $datamodel, array $props, $isUnique){
        $Key = md5($datamodel->Datamodel->Url . implode($props));
        if(isset(self::$INSTANCES[$Key])) return self::$INSTANCES[$Key];
        return self::$INSTANCES[$Key] = new self($datamodel, $props, $isUnique);
    }
}
?>