<?php
namespace Cmatrix\Models;
use \Cmatrix as cm;

class Common {
    public function getData(){
        return [
            'home' => CM_WHOME,
            'project' => $this->getMyProject()
        ];
    }
    
    // --- --- --- --- ---
    private function getMyProject(){
        $Config = cm\Hash::getFile(CM_TOP.'/config.json');
        return $Config->getValue('project');
    }
}
?>