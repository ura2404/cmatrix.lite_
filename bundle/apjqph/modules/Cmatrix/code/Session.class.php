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
        try{
            if($this->isDb){
                if(Db\Sysuser::get([
                    'user' => $user,
                    'pass' => $pass
                ])->isNew) throw new \Exception('Неверная комбинация имени и пароля');
                
                Db\Session::touch();
            }
        }
        catch(\Throwable $e){
            return $e->getMessage();
        }
        
    }

    // --- --- --- --- ---
    public function log(){
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance(){
        return new self();
    }
}
?>