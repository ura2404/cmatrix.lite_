<?php
namespace Cmatrix;

class Dm {
    private $Url;
    private $Props = null;
    
    // --- --- --- --- ---
    function __construct($url){
        $this->Url = $url;
    }
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Props' : return $this->getMyProps();
            case 'Json'  : return $this->getMyJson();
        }
    }
    
    // --- --- --- --- ---
    protected function getMyProps(){
        if($this->Props !== null) return $this->Props;
        
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function get($url){
        return new self($url);
    }
}
?>