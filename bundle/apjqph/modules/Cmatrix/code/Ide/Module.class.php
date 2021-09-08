<?php
namespace Cmatrix\Ide;
use \Cmatrix as cm;

/**
 * Получени любой инофрмации о модуле
 */
class Module {
    private $Url;
    
    private $P_Path = null;
    
    // --- --- --- --- ---
    function __construct($url){
        $this->Url = $url;
    }
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Path' : return $this->getMyPath();
            case 'Datamodels' : return $this->getMyDatamodels();
        }
    }

    // --- --- --- --- ---
    private function getMyPath(){
        if($this->P_Path !== null) return $this->P_Path;
        $Path = CM_ROOT. '/modules' .$this->Url;
        if(!file_exists($Path)) throw new \Exception('Module "'. $this->Url .'" is not found.');
        return $this->P_Path = $Path;
    }
    
    // --- --- --- --- ---
    private function getMyDatamodels(){
        $Root = $this->Path .'/dm';
        if(!file_exists($Root)) return [];
        
        return array_map(function($value){
            return $Url = $this->Url. '/' .strBefore($value,'.dm.php');
        },array_filter(scandir($Root),function($value){
            return $value !== '.' && $value !== '..' && strpos($value,'.dm.php');
        }));
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance($url){
        return new self($url);
    }
}