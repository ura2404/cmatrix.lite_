<?php
namespace Cmatrix;

class Db {
    private $Dm = null;
    
    // --- --- --- --- ---
    function __construct($id=null){
        $this->prepare();
    }
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'IsNew' : return $this->getMyIsNew();
            case 'Url'   : return $this->getMyUrl();
            case 'Dm'    : return $this->getMyDm();
        }
    }
    
    // --- --- --- --- ---
    protected function prepare(){
        $Props = Dm::get($this->Url)->Props;
    }
    
    // --- --- --- --- ---
    protected function getMyIsNew(){
        
    }

    // --- --- --- --- ---
    protected function getMyUrl(){
        return '/'. implode('/',(explode('\Dm\\',get_class($this))));
    }

    // --- --- --- --- ---
    protected function getMyDm(){
        if($this->Dm !== null) return $this->Dm;
        else return $this->Dm = Dm::get($this->Url);
    }    
}
?>