<?php
namespace CmatrixWeb\Ide;
use \Cmatrix as cm;
use \Cmatrix\Exception as ex;
use \CmatrixDb as db;

class Datamodel{
    static $INSTANCES = [];
    private $Datamodel;

    // --- --- --- --- ---
    function __construct($url){
        $this->Datamodel = cm\Ide\Datamodel::instance($url);
    }
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Code' : return $this->Datamodel->Url;
            case 'Name' : return $this->Datamodel->Name;
            case 'Props' : return $this->getMyProps();
            case 'Lines' : return $this->getMyLines();
            case 'Total' : return $this->getMyTotal();
            case 'Css' : return $this->getMyCss();
            default : throw new ex\Property($this,$name);
        }
    }
    
    // --- --- --- --- ---
    private function getMyProps(){
        $Props = array_merge([
            'row_index'=>[
                'code' => 'row_index',
                'type' => 'integer',
            ]
        ],$this->Datamodel->Props);
        return $Props;
    }
    
    // --- --- --- --- ---
    private function getMyCss(){
        $_align = function($prop){
            switch($prop['type']){
                case '::id::' :
                case 'integer' :
                    return 'text-right';
                        
                case ':hid::' :
                    return 'text-center';
                    
                case 'bool' :
                    return 'text-center';
                    
                default : return 'text-left';
            }
            
        };
        
        return array_map(function($prop) use($_align){
            return [
                'align' => $_align($prop),
            ];
            return $prop;
        },$this->Props);
    }
    
    // --- --- --- --- ---
    private function getMyLines(){
        $Query = db\Cql::select($this->Datamodel);
        $Res = db\Connect::instance()->query($Query);
        
        $Iterator = 0;
        $Res = array_map(function($tr) use(&$Iterator){
            return array_merge(['row_index' => ++$Iterator ],$tr);
        },$Res);
        
        return $Res;
    }
    
    // --- --- --- --- ---
    private function getMyTotal(){
        $Query = db\Cql::select($this->Datamodel)->prop('count::id','qaz');
        $Res = db\Connect::instance()->query($Query,\PDO::FETCH_NUM);
        
        if(!$Res) return 0;
        return array_values($Res)[0][0];
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance($url){
        $Key = $url;
        if(array_key_exists($Key,self::$INSTANCES)) return self::$INSTANCES[$Key];
        return new self($url);
    }
}
?>