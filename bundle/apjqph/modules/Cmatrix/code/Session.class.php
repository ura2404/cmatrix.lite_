<?php
namespace Cmatrix;

class Session {
    
    // --- --- --- --- ---
    function __construct(){
    }

    // --- --- --- --- ---
    function __get($name){
        switch($name){
        }
    }

    // --- --- --- --- ---
    // --- --- --- --- ---
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
            if(App::instance()->isDb) $this->dbLogin($user,$pass);
            else throw new \Exception('Система авторизации неактивна.');
        }
        catch(\Throwable $e){
            return $e->getMessage();
        }
    }

    // --- --- --- --- ---
    public function logout(){
        try{
            if(App::instance()->isDb) $this->dbLogout();
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