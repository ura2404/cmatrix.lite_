<?php
namespace CmatrixWeb\Ide;
use \Cmatrix as cm;
use \Cmatrix\Exception as ex;
use \CmatrixDb as db;
use \CmatrixWeb as web;

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
        $Props = $this->Datamodel->Props;
        array_map(function($code,$prop) use(&$Props){
            $prop['label'] = $prop['label'] ? cm\Lang::str($prop['label']) : ($prop['name'] ? cm\Lang::str($prop['name']) : $prop['code']);
            $prop['baloon'] = $prop['baloon'] ? cm\Lang::str($prop['baloon']) : null;
            $prop['sort'] = 'sort';
            $prop['hsort'] = CM_WHOME. '/'.web\Page::instance()->Url;
            
            $Props[$code] = $prop;
        },array_keys($Props),array_values($Props));
        
        $Props = array_merge([
            'row_index'=>[
                'code' => 'row_index',
                'type' => 'integer',
                'baloon' => 'Выбрать все записи'
            ]
        ],$Props);
        
        //dump($Props);
        
        return $Props;
    }
    
    // --- --- --- --- ---
    private function getMyCss(){
        $_align = function($prop){
            switch($prop['code']){
                case 'status' : return 'center';
            }
            
            switch($prop['type']){
                case '::id::' :
                case 'integer' :
                case 'real' :
                    return 'right';
                        
                case '::hid::' :
                case 'bool' :
                case 'timestamp' :
                    return 'center';
                
                default : return 'left';
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
            array_map(function($code,$td) use(&$tr){
                $Type = $this->Datamodel->Props[$code]['type'];
                if($Type === 'timestamp') $td = strBefore($td,'.');
                elseif($Type === 'bool'){
                    //return $td === true ? ''
                    //return $td;
                }
                return $tr[$code] = $td;
            },array_keys($tr),array_values($tr));
            
            return array_merge(['row_index' => ++$Iterator ],$tr);
        },$Res);
        
        //dump($Res);
        
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