<?php
namespace Cmatrix\Db\Providers;
use \Cmatrix as cm;

class Pgsql extends cm\Db\Provider implements cm\Db\iProvider{

    // --- --- --- --- ---
    function __construct(){
    }
    
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- --- --- --- ---
    /**
     * @retrun string - трансформированное имя
     *     удобно когда образзуются длинные sql имена индексов, счётчиков, ограничений и тд.
     */
    public function transName($name){
        // 1.
        return $name;
        
        // 2.
        return 'cm'.md5($name);
        
        // 3.
        //$Prefix = \Cmatrix\Db\Kernel::get()->CurConfig->getValue('prefix',null);
        //$Prefix = $Prefix ? $Prefix : 'cm';
        //return $Prefix .'_'. md5($name);
    }
    
    // --- --- --- --- ---
    public function getType(){
        return 'pgsql';
    }
    
    // --- --- --- --- ---
    /**
     * @return string - символ коментирования sql строки
     */
    public function getCommSymbol(){
        return '-- ';
    }
    
    // --- --- --- --- ---
    public function getPropType($prop){
        $Type = $prop['type'];
        
        if($Type === '::id::')       return 'BIGINT';
        elseif($Type === '::ip::')   return 'VARCHAR(45)'; // 15 - ipv4, 45 - ipv6
        elseif($Type === '::hid::')  return 'VARCHAR(32)';
        elseif($Type === '::pass::') return 'VARCHAR(255)';
        elseif($Type === 'string')   return 'VARCHAR' .(isset($prop['length']) ? '('. $prop['length'] .')' : null);
        else return strtoupper($Type);
    }

    // --- --- --- --- ---
    public function getSqlNextSequence($seqName){
        return "nextval('". $seqName ."')";
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

    // --- --- --- --- --- --- --- ---
    /**
     * Функция sqlValue
     * Для формирования sql представления значения для подстановки в запросы
     * 
     * @return mix - представление значения
     * 
     */
    public function sqlValue($type,$value,$cond='='){
        if(gettype($value) === 'string' && strStart($value,'raw::')) return strAfter($value,'raw::');
        return Pgsql\Value::instance($type,$value,$cond='=')->SqlValue;
    }        
}
?>