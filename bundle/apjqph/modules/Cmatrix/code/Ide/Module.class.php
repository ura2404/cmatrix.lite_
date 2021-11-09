<?php
namespace Cmatrix\Ide;
use \Cmatrix as cm;

/**
 * Получени любой инофрмации о модуле
 */
class Module {
    private $Url;
    
    private $P_Path = null;
    private $P_Json = null;
    
    // --- --- --- --- ---
    function __construct($url){
        $this->Url = $url;
    }
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Path' : return $this->getMyPath();
            case 'Json' : return $this->getMyJson();
            case 'Name' : return $this->Json['module']['name'];
            case 'Info' : return $this->Json['module']['info'];
            case 'Datamodels' : return $this->getMyDatamodels();
            default : throw new ex\Property($this,$name);
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
    protected function getMyJson(){
        if($this->P_Json !== null) return $this->P_Json;
        $Path = $this->Path.'/module.conf.json';
        
        if(!file_exists($Path)) throw new \Exception('Module "'. $this->Url .'" config file is not found.');
        return $this->P_Json = cm\Json::getFile($Path)->Data;
    }
    
    // --- --- --- --- ---
    private function getMyDatamodels(){
        $Root = $this->Path .'/dm';
        if(!file_exists($Root)) return [];
        
        return array_map(function($value){
            return $Url = $this->Url. '/' .strBefore($value,'.dm.php');
        },array_filter(scandir($Root),function($value){
            return $value !== '.' && $value !== '..' && strpos($value,'.dm.php') && $value[0]!=='_';
        }));
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance($url){
        return new self($url);
    }
}