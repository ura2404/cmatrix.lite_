<?php
namespace Cmatrix\Models;
use \Cmatrix as cm;

class Comlogin extends Common {
    public function getData(){
        
        return arrayMergeReplace(parent::getData(),[
            'session' => $this->getMySession(),
            'url'     => $this->getMyUrl(),
        ]);
    }
    
    // --- --- --- --- ---
    private function getMySession(){
        $Config = cm\Hash::getFile(CM_TOP.'/config.json');
        $Data = [
            'enable' => $Config->getValue('session/enable'),
            'user' => 'Гость'
        ];
        return $Data;
    }
    
    // --- --- --- --- ---
    private function getMyUrl(){
        return [
            'post'  => 'res/res/post.php',
        ];
    }
}
?>