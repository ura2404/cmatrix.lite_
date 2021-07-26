<?php
namespace Cmatrix;

class Dm {
    private $Url;
    
    private $P_Path = null;
    private $P_Props = null;
    
    // --- --- --- --- ---
    function __construct($url){
        $this->Url = $url;
    }
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Props' : return $this->getMyProps();
            case 'OwnProps' : return $this->getMyOwnProps();
            case 'Json'  : return $this->getMyJson();
            case 'Path'  : return $this->getMyPath();
        }
    }
    
    // --- --- --- --- ---
    protected function getMyPath(){
        if($this->P_Path !== null) return $this->P_Path;
    }
    
    // --- --- --- --- ---
    protected function getMyProps(){
        if($this->P_Props !== null) return $this->P_Props;
        
    }

    // --- --- --- --- ---
    protected function getMyOwnProps(){
        
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance($url){
        return new self($url);
    }
}
?>