<?php
namespace Cmatrix;

class Db {
    private $P_Dm = null;
    
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
        $Props = $this->Dm->Props;
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
        if($this->P_Dm !== null) return $this->P_Dm;
        else return $this->P_Dm = Dm::instance($this->Url);
    }    
}
?>