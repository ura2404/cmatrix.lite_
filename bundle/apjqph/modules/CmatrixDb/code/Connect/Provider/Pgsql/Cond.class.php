<?php
/**
 * Class \CmatrixDb\Connect\Provider\Pgsql\Cond
 *
 * @author ura@itx.ru
 * @version 1.0 2020-12-19
 */

namespace CmatrixDb\Connect\Provider\Pgsql;
use \Cmatrix as cm;

class Cond{
    private $Prop;
    private $Value;
    private $Cond;
    private $IsSelect;
    
    // --- --- --- --- --- --- --- ---
    function __construct(array $prop,$value,$cond='=',$isSelect=true){
        $this->Prop = $prop;
        $this->Value = $value;
        $this->Cond = $cond;
        $this->IsSelect = $isSelect;
    }
    
    // --- --- --- --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Query' : return $this->getMySqlQuery();
        }
    }
    
    // --- --- --- --- ---
    private function getMySqlQuery(){
        switch($this->Cond){
            case '=' : 
                if(!$this->IsSelect) return $this->Cond;
                
                if(strStart($this->Value,'(') && strEnd($this->Value,')')) return ' IN ';
                elseif(Value::isBool($this->Value)) return ' IS ';
                elseif(is_array($this->Value)) return ' IN ';
                else return $this->Cond;
                
            case '!=' : 
                if(!$this->IsSelect) return $this->Cond;
                
                if(strStart($this->Value,'(') && strEnd($this->Value,')')) return ' NOT IN ';
                elseif(Value::isBool($this->Value)) return ' IS NOT ';
                else return $this->Cond;
                
            case '%' :
            case '%%' :
            case '%%%' :
                return ' LIKE ';
                
            case '!%' :
            case '!%%' :
            case '!%%%' :
                return ' NOT LIKE ';
                
            default : return $this->Cond;
        }

    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance(array $prop,$value,$cond='=',$isSelect=true){
        return new self($prop,$value,$cond,$isSelect);
    }
  
}
?>