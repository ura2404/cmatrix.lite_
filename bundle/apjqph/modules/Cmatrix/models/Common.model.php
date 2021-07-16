<?php
namespace Cmatrix\Models;
use \Cmatrix as cm;

class Common {
    public function getData(){
        return [
            'url'     => $this->getMyUrl(),
            'project' => $this->getMyProject(),
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