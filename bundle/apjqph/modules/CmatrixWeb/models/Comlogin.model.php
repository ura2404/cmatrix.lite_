<?php
namespace CmatrixWeb\Models;
use \Cmatrix as cm;
use \CmatrixWeb as web;

class Comlogin extends Common implements web\iModel {
    public function getData(){
        return arrayMergeReplace(parent::getData(),[
            'session' => $this->getMySession(),
            'url'     => $this->getMyUrl(),
        ]);
    }
    
    // --- --- --- --- ---
    private function getMySession(){
        $Config = cm\Hash::getFile(CM_TOP.'/config.json');
        
        $Session = \CmatrixCore\Session::instance()->Session;
        $Sysuser = \CmatrixCore\Session::instance()->Sysuser;
        
        $Data = [
            'enable' => $Config->getValue('session/enable'),
            'user' => array_intersect_key($Sysuser->Data,array_flip(['code','name','lk'])),
            'hid' => $Session->Data['hid']
        ];
        return $Data;
    }
    
    // --- --- --- --- ---
    private function getMyUrl(){
        return [
            'post' => CM_WHOME.'/res/CmatrixWeb/post.php',
            'profile' => CM_WHOME.'/profile',
            'session' => CM_WHOME.'/session',
            'admin' => CM_WHOME.'/admin'
        ];
    }
}
?>