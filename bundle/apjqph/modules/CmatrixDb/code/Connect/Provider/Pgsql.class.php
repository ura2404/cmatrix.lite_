<?php
namespace CmatrixDb\Connect\Provider;
use \Cmatrix as cm;
use \CmatrixDb as db;

class Pgsql extends db\Connect\Provider implements db\Connect\iProvider{

    // --- --- --- --- ---
    public function getType(){
        return 'pgsql';
    }

    // --- --- --- --- ---
    public function getCommSymbol(){
        return '-- ';
    }
    
    // --- --- --- --- ---
    public function getPropType(array $prop){
        $Type = $prop['type'];
        $Length = isset($prop['length']) ? '('. $prop['length'] .')' : null;
        
        if($Type === '::id::')       return 'BIGINT';
        elseif($Type === '::ip::')   return 'VARCHAR(45)'; // 15 - ipv4, 45 - ipv6
        elseif($Type === '::hid::')  return 'VARCHAR(32)';
        elseif($Type === '::pass::') return 'VARCHAR(255)';
        elseif($Type === 'string')   return 'VARCHAR' . ($prop['length'] ? '('.$prop['length'].')' : null);
        elseif($Type === 'real')     return 'NUMERIC' . ($prop['length'] ? '('.$prop['length'].')' : '(12,4)');
        else return strtoupper($Type);
    }

    // --- --- --- --- ---
    public function getPropDef(array $prop,db\Structure\Datamodel\iProvider $provider){
        $PropCode = $prop['code'];
        $PropType = $prop['type'];
        $PropDef = $prop['default'];
        
        if($PropDef === '::counter::') $PropDef = 'raw::' . $this->getSqlNextSequence($provider->sqlSeqName($PropCode));
        elseif($PropDef === '::now::') $PropDef = 'raw::' . $this->getSqlNow();
        elseif($PropDef === '::hid::') $PropDef = 'raw::' . $this->getSqlHid();
        
        $PropDef = $this->sqlValue($prop,$PropDef);
        return $PropDef ? 'DEFAULT ' . $PropDef : null;
    }

    // --- --- --- --- ---
    public function getSqlNextSequence($SeqName){
        return "nextval('". $SeqName ."')";
    }

    // --- --- --- --- ---
    public function getSqlNow(){
        return 'CURRENT_TIMESTAMP';
    }

    // --- --- --- --- ---
    public function getSqlHid(){
        return "md5(to_char(now(), 'DDDYYYYNNDDHH24MISSUS') || random())";
    }
    
    // --- --- --- --- ---
    public function getSqlNotNull(){
        return 'NOT NULL';
    }
    

    // --- --- --- --- ---
    public function getSqlAgg($code){
        if(strpos($code,'::') === false) return $code;
        
        $Agg = strBefore($code,'::');
        switch($Agg){
            case 'max' : return 'max(' .strAfter($code,'::'). ')';
            case 'count' : return 'count(' .strAfter($code,'::'). ')';
            default : throw new \Exception('Invalid agg function "' .$Agg.'".');
        }
    }

    // --- --- --- --- ---
    public function sqlPhrase(array $prop,$value,$cond='=',$isSelect=true){
        $Code  = $prop['code'];
        $Value = Pgsql\Value::instance($prop,$value,$cond)->Query;
        $Cond  = Pgsql\Cond::instance($prop,$Value,$cond,$isSelect)->Query;
        
        return $Code . $Cond . $Value;
    }
    
    // --- --- --- --- ---
    public function sqlValue(array $prop,$value,$cond='='){
        if(gettype($value) === 'string' && strStart($value,'raw::')) return strAfter($value,'raw::');
        return Pgsql\Value::instance($prop,$value,$cond='=')->Query;
    }
    
    public function query($query){
        return Pgsql\Connect::instance()->query($query);
    }
    
}
?>