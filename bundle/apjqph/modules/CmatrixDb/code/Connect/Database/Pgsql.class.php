<?php
namespace CmatrixDb\Connect\Database;
use \Cmatrix as cm;
use \CmatrixDb as db;

class Pgsql extends db\Connect\Database implements db\Connect\iDatabase{
    
	public $Type = "pgsql";
	public $Driver = "pdo";
	public $Host = "127.0.0.1";
	public $Port = "5432";
	public $Name = null;
	public $User = null;
	public $Pass = null;
	
    // --- --- --- --- ---
    function __construct(array $config){
        
        // 1. получить свойства класса, исключая статические
		$Reflect = new \ReflectionClass($this);
		$Statics = array_keys($Reflect->getStaticProperties());
		$Props = $Reflect->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PRIVATE);
		$Props = array_filter($Props,function($prop) use($Statics){
		    return !in_array($prop->getName(),$Statics);
		});
		
		// 2. наложить данные из $config
		array_map(function($prop) use($config){
		    $Name = $prop->getName();
		    $Name2 = strtolower($Name);
		    array_key_exists($Name2,$config) ? $this->$Name = $config[$Name2] : null;
		},$Props);
    }

    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    public function query($query){
    }
    
    // --- --- --- --- ---
    public function exec($query){
    }
}
?>