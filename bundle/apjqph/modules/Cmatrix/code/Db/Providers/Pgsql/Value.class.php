<?php
/**
 * Class \Cmatrix\Db\Provider\Pgsql\Value
 *
 * @author ura@itx.ru
 * @version 1.0 2020-12-19
 */

namespace Cmatrix\Db\Providers\Pgsql;
use \Cmatrix as cm;

class Value{
    private $Type;
    private $Value;
    private $Cond;
    
    // --- --- --- --- --- --- --- ---
    function __construct($type,$value,$cond='='){
        $this->Type = $type;
        $this->Value = $value;
        $this->Cond = $cond;
    }
    
    // --- --- --- --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'SqlValue' : return $this->getMySqlValue();
        }
    }
    
    // --- --- --- --- ---
    private function getMySqlValue(){
        switch($this->Type){
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
        return "'" .str_replace("'","''",$this->Value). "'";
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance($type,$value,$cond='='){
        return new self($type,$value,$cond);
    }
    
    // --- --- --- --- --- --- --- ---
    static function isBool($value){
        $ValType = gettype($value);
        
        if($ValType === 'NULL') return true;    // gettype(null) = 'NULL';
        elseif($ValType === 'boolean'){
            if($value === true || $value === false || $value === null) return true;
        }
        elseif($ValType === 'string'){
            $Value = strtolower($val);
            if($Value === 'true' || $Value === 'false' || $Value === 'null') return true;
        }
        elseif($ValType === 'integer'){
            if($Value === 1 || $Value === -1 || $Value === 0) return true;
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

class Value2{
    
    private $Value;
    private $Cond;
    
    // --- --- --- --- --- --- --- ---
    function __construct($value,$cond='='){
        $this->Value = $value;
        $this->Cond = $cond;
    }
    
    // --- --- --- --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'TsValue'      : return $this->getMyTsValue();
            case 'BoolValue'    : return $this->getMyBoolValue();
            case 'IntegerValue' : return $this->getMyIntegerValue();
            case 'RealValue'    : return $this->getMyRealValue();
            case 'StringValue'  : return $this->getMyStringValue();
            
            /*
            case 'isDayY'   : return $this->getMyIsDayY();
            case 'isDayYM'  : return $this->getMyIsDayYM();
            case 'isDayYMD' : return $this->getMyIsDayYMD();
            
            case 'isDayTimeYMDH'   : return $this->getMyIsDayTimeYMDH();
            case 'isDayTimeYMDHM'  : return $this->getMyIsDayTimeYMDHM();
            case 'isDayTimeYMDHMS' : return $this->getMyIsDayTimeYMDHMS();
            
            case 'isTimeH'   : return $this->getMyIsTimeH();
            case 'isTimeHM'  : return $this->getMyIsTimeHM();
            case 'isTimeHMS' : return $this->getMyIsTimeHMS();
            */
            
            default : throw new ex\Property($this,$name);
        }
    }
    
    // --- --- --- --- --- --- --- ---
    private function getMyTsValue(){
        if(!self::isTs($this->Value)) throw new ex\Error('Invalid timestamp value ['.$this->Value.'].');
        return $this->Value;
    }
    
    // --- --- --- --- --- --- --- ---
    private function getMyBoolValue(){
        if(!self::isBool($this->Value)) throw new ex\Error('Invalid boolean value ['.$this->Value.'].');
        
        $ValType = gettype($this->Value);
        if($ValType === 'boolean'){
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

    // --- --- --- --- --- --- --- ---
    private function getMyIntegerValue(){
        if(!self::isInteger($this->Value)) throw new ex\Error('Invalid integer value ['.$this->Value.'].');
        return intval($this->Value);
    }
    
    // --- --- --- --- --- --- --- ---
    private function getMyRealValue(){
        if(!self::isReal($this->Value)) throw new ex\Error('Invalid real value ['.$this->Value.'].');
        return floatval($this->Value);
    }

    // --- --- --- --- --- --- --- ---
    private function getMyStringValue(){
        return "'" .str_replace("'","''",$this->Value). "'";
    }
    
    /*
    // --- --- --- --- --- --- --- ---
    private function getMyIsTs(){
        return preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) (2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]$/',$this->Value) ? true : false;
    }
    
    // --- --- --- --- --- --- --- ---
    private function getMyIsDayY(){
        return preg_match('/^[0-9]{4}$/',$value) ? true : false;
    }
    
    // --- --- --- --- --- --- --- ---
    private function getMyIsDayYM(){
        return preg_match('/^[0-9]{4}-(0[1-9]|1[012])$/',$value) ? true : false;
    }
    
    // --- --- --- --- --- --- --- ---
    private function getMyIsDayYMD(){
        return preg_match('/^[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[10])$/',$value) ? true : false;
    }
    
    // --- --- --- --- --- --- --- ---
    private function getMyIsDayTimeYMDH(){
        return preg_match('/^[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[10]) (2[0-3]|[01][0-9])$/',$value) ? true : false;
    }
    
    // --- --- --- --- --- --- --- ---
    private function getMyIsDayTimeYMDHM(){
        return preg_match('/^[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[10]) (2[0-3]|[01][0-9]):[0-5][0-9]$/',$value) ? true : false;
    }

    // --- --- --- --- --- --- --- ---
    private function getMyIsDayTimeYMDHMS(){
        return preg_match('/^[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[10]) (2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]$/',$value) ? true : false;
    }
    
    // --- --- --- --- --- --- --- ---
    private function getMyIsTimeH(){
        return preg_match('/^(2[0-3]|[01][0-9])$/',$value) ? true : false;
    }
    
    // --- --- --- --- --- --- --- ---
    private function getMyIsTimeHM(){
        return preg_match('/^(2[0-3]|[01][0-9]):[0-5][0-9]$/',$value) ? true : false;
    }
    
    // --- --- --- --- --- --- --- ---
    private function getMyIsTimeHMS(){
        return preg_match('/^(2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]$/',$value) ? true : false;
    }
    */
    
    // --- --- --- --- --- --- --- ---
    // --- --- --- --- --- --- --- ---
    // --- --- --- --- --- --- --- ---
    static function get($value,$cond='='){
        return new self($value,$cond);
    }
    
    // --- --- --- --- --- --- --- ---
    static function isTs($value){
        if(strtolower($value) === 'curent_timestamp') return true;
        else return preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) (2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]$/',$value) ? true : false;
    }
    
    // --- --- --- --- --- --- --- ---
    static function isBool($value){
        $ValType = gettype($value);
        if($ValType === 'boolean'){
            if($value === true || $value === false || $value === null) return true;
        }
        elseif($ValType === 'string'){
            $Value = strtolower($val);
            if($Value === 'true' || $Value === 'false' || $Value === 'null') return true;
        }
        elseif($ValType === 'integer'){
            if($Value === 1 || $Value === -1 || $Value === 0) return true;
        }
        
        return false;
    }

    // --- --- --- --- --- --- --- ---
    static function isInteger($value){
        if(is_integer($value)) return true;
        elseif(is_string($value) && ctype_digit($value)) return true;
        return false;
    }
    
    // --- --- --- --- --- --- --- ---
    static function isReal($value){
        if(is_float($value) || is_integer($value)) return true;
        elseif(is_string($value) && is_numeric($value)) return true;
        return false;
    }
    
}
?>