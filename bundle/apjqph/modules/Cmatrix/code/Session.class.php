<?php
namespace Cmatrix;

class Session {
    private $PIsDb = null;
    
    // --- --- --- --- ---
    function __construct(){
        $this->isDb = $this->getMyIsDb();
    }

    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'IsDb' : return $this->getMyIsDb();
        }
    }

    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    protected function getMyIsDb(){
        if($this->PIsDb !== null) return $this->PIsDb;
        $Config = Hash::getFile(CM_TOP.'/config.json');
        return $this->PIsDb = $Config->getValue('db/enable');
    }

    // --- --- --- --- ---
    protected function dbLogin($user,$pass){
        if((new Dm\Sysuser([
            'user' => $user,
            'pass' => $pass
        ]))->isNew) throw new \Exception('Неверная комбинация имени и пароля.');
        
        $this->touch();
    }

    // --- --- --- --- ---
    protected function dbLogout(){
    }

    // --- --- --- --- ---
    protected function touch(){
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    public function login($user,$pass){
        try{
            if($this->IsDb) $this->dbLogin($user,$pass);
            else throw new \Exception('Система авторизации неактивна.');
        }
        catch(\Throwable $e){
            return $e->getMessage();
        }
    }

    // --- --- --- --- ---
    public function logout(){
        try{
            if($this->isDb) $this->dbLogout();
            else throw new \Exception('Система авторизации неактивна.');
        }
        catch(\Throwable $e){
            return $e->getMessage();
        }
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance(){
        return new self();
    }
}
?>