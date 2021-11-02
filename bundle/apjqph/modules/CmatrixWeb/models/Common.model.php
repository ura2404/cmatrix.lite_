<?php
namespace CmatrixWeb\Models;
use \Cmatrix as cm;
use \CmatrixWeb as web;

class Common implements web\iModel {
    public function getData(){
        return [
            'url' => $this->getMyUrl(),
            'app' => $this->getMyProject(),
        ];
    }
    
    // --- --- --- --- ---
    private function getMyUrl(){
        return [
            'home'  => CM_WHOME,
            'login' => CM_WHOME .'/login'
        ];
    }
    
    // --- --- --- --- ---
    private function getMyProject(){
        $Config = cm\Hash::getFile(CM_TOP.'/config.json');
        return $Config->getValue('project');
    }
}
?>