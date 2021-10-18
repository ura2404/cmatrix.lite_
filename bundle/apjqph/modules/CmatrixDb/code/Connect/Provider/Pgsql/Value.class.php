<?php
/**
 * Class \CmatrixDb\Connect\Provider\Pgsql\Value
 *
 * @author ura@itx.ru
 * @version 1.0 2020-12-19
 */

namespace CmatrixDb\Connect\Provider\Pgsql;
use \Cmatrix as cm;

class Value{
    static $INSTANCES = [];
    
    private $Prop;
    private $Value;
    private $Cond;
    
    // --- --- --- --- --- --- --- ---
    function __construct(array $prop,$value,$cond='='){
        $this->Prop = $prop;
        $this->Value = $value;
        $this->Cond = $cond;
    }
    
    // --- --- --- --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Query' : return $this->getMySqlQuery();
        }
    }
    
    // --- --- --- --- ---
    private function getMySqlQuery(){
        $ValType = gettype($this->Value);
        if($ValType === 'string' && strStart($this->Value,'raw::')) return strAfter($this->Value,'raw::');
        
        switch($this->Prop['type']){
            case 'bool' :
                return $this->getBoolValue();
                
            case 'timestamp' :
                return $this->getTsValue();
                
            case '::id::' :
            case 'int' :
            case 'integer' :
                return $this->getIntegerValue();
                
            case 'real' :
                return $this->getRealValue();
                
            case '::hid::' :
            case '::ip::' :
            case '::counter::' :
            case 'string' :
            case 'text' :
            default :
                return $this->getStringValue();
        }        
    }
    
    // --- --- --- --- ---
    private function getBoolValue(){
        if(self::isBool($this->Value) === false) throw new \Exception('Invalid boolean value ['.$this->Value.'].');
        
        $ValType = gettype($this->Value);
        
        if($ValType === 'NULL') $Value = 'null';
        elseif($ValType === 'boolean'){
            if($this->Value === true) $Value = 'true';
            elseif($this->Value === false) $Value = 'false';
            elseif($this->Value === null) $Value = 'null';
        }
        elseif($ValType === 'string'){
            $Value = strtolower($val);
        }
        elseif($ValType === 'integer'){
            if($this->Value === 1) return 'true';
            if($this->Value === -1) return 'false';
            if($this->Value === 0) return 'null';
        }
        
        return strtoupper($Value);
    }
    
    // --- --- --- --- ---
    private function getTsValue(){
        if(!self::isTs($this->Value)) throw new \Exception('Invalid timestamp value ['.$this->Value.'].');
        return $this->Value;
    }
    
    // --- --- --- --- ---
    private function getIntegerValue(){
        if(!self::isInteger($this->Value)) throw new \Exception('Invalid integer value ['.$this->Value.'].');
        return intval($this->Value);
    }
    
    // --- --- --- --- ---
    private function getRealValue(){
        if(is_float($value) || is_integer($value)) return true;
        elseif(is_string($value) && is_numeric($value)) return true;
        return false;
    }
    
    // --- --- --- --- ---
    private function getStringValue(){
        if($this->isBool($this->Value)) return $this->getBoolValue();
        
        return "'" .str_replace("'","''",$this->Value). "'";
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance(array $prop,$value,$cond='='){
        return new self($prop,$value,$cond);
    }
    
    // --- --- --- --- --- --- --- ---
    static function isBool($value){
        $ValType = gettype($value);
        
        if($ValType === 'NULL') return true;    // gettype(null) = 'NULL';
        elseif($ValType === 'boolean'){
            if($value === true || $value === false || $value === null) return true;
        }
        elseif($ValType === 'string'){
            $Value = strtolower($value);
            if($Value === 'true' || $Value === 'false' || $Value === 'null') return true;
        }
        elseif($ValType === 'integer'){
            if($value === 1 || $value === -1 || $value === 0) return true;
        }
        
        return false;
    }
    
    // --- --- --- --- --- --- --- ---
    static function isTs($value){
        if(strtolower($value) === 'curent_timestamp') return true;
        else return preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) (2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]$/',$value) ? true : false;
    }
    
    // --- --- --- --- --- --- --- ---
    static function isInteger($value){
        if(is_integer($value)) return true;
        elseif(is_string($value) && ctype_digit($value)) return true;
        return false;
    }
    
    // --- --- --- --- --- --- --- ---
    static function isReal($value){
        if(!self::isReal($this->Value)) throw new \Eeception('Invalid real value ['.$this->Value.'].');
        return floatval($this->Value);
    }
    
}
?>