<?php
namespace Cmatrix;

class Session {
    private $isDb;
    
    // --- --- --- --- ---
    function __construct(){
        $this->isDb = $this->getMyIsDb();
    }

    // --- --- --- --- ---
    function __get($name){
        switch($name){
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
    // --- --- --- --- ---
    // --- --- --- --- ---
    public function login($user,$pass){
        if($this->isDb){
            $Ob = Db\Session::get([
                'user' => $user,
                'pass' => $pass
            ]);
            
            return $Ob->isNew ? 'Неверная комбинация имени и пароля' : 'OK';
        }
    }

    // --- --- --- --- ---
    public function logout(){
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance(){
        return new self();
    }
}
?>