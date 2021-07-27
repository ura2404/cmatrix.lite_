<?php
namespace Cmatrix;

/**
 * Управление json описанием datamodel
 */
class Datamodel {
    public $Url;
    
    private $P_Path = null;
    private $P_Json = null;
    private $P_Props = null;
    private $P_OwnProps = null;
    
    // --- --- --- --- ---
    function __construct($url){
        $this->Url = $url;
    }
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Path' : return $this->getMyPath();
            case 'Json' : return $this->getMyJson();
            case 'OwnProps' : return $this->getMyOwnProps();
            case 'Props' : return $this->getMyProps();
        }
    }
    
    // --- --- --- --- ---
    /**
     * @return string - parh to json datamodel file
     */
    protected function getMyPath(){
        if($this->P_Path !== null) return $this->P_Path;
        $Arr = explode('/',ltrim($this->Url,'/'));
        return CM_ROOT.'/modules/'.$Arr[0].'/dm/'.lcfirst($Arr[1]).'.dm.json';
    }
    
    // --- --- --- --- ---
    protected function getMyJson(){
        if($this->P_Json !== null) return $this->P_Json;
        
        if(!file_exists($this->Path)) throw new \Exception('Datamodel "'. $this->Url .'" is not found.');
        return $this->P_Json = Json::getFile($this->Path)->Data;
    }
    
    // --- --- --- --- ---
    protected function getMyProps(){
        if($this->P_Props !== null) return $this->P_Props;
        
        $ParentProps = $this->Json['parent'] !== null ? self::instance($this->Json['parent'])->Props : [];
        $ParentProps = array_map(function($prop){
            unset($prop['own']);
            return $prop;
        } ,$ParentProps);
        
        return $this->P_Props = arrayMergeReplace($ParentProps,$this->OwnProps);
    }

    // --- --- --- --- ---
    protected function getMyOwnProps(){
        if($this->P_OwnProps !== null) return $this->P_OwnProps;
        
        return $this->P_OwnProps = array_map(function($prop){
            $prop['own'] = true;
            return $prop;
        } ,$this->Json['props']);
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance($url){
        return new self($url);
    }
}
?>