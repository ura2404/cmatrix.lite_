<?php
namespace Cmatrix\Db;

class Session extends Entity{
    private $SysuserId;
    private $Ipv4;
    
    
    private $User;
    private $Pass;
    private $Message;
    
    private $isDb;
    
    // --- --- --- --- ---
    function __construct($user,$pass){
        $this->User = $user;
        $this->Pass = $pass;
        
        $this->isDb = $this->getMyIsDb();
    }

    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Message' : return $this->Message ? $this->Message : 'OK';
        }
    }

    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    protected function getMyIsDb(){
        $Config = Hash::getFile(CM_TOP.'/config.json');
        return $Config->getValue('db/enable');
    }
    
    // --- --- --- --- ---
    protected function login($user,$pass){
        if($this->isDb) return Db\Session::login($user,$pass);
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance($id=null){
        return new self();
    }
}
?>