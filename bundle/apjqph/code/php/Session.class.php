<?php
namespace Cmatrix;

class Session {
    private $User;
    private $Pass;
    private $Message;
    
    // --- --- --- --- ---
    function __construct($user,$pass){
        $this->User = $user;
        $this->Pass = $pass;
    }

    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Message' : return $this->Message ? $this->Message : 'OK цлуцлауцдлжцул уцдлаждцлйжуаждцлу аждцлу аэждлуцаждэлцуажд цжцлужалцжулаэжцдлуаждцу жцдула жцдлу аждц цужэдал жцдлу аждцлу ацулаждлуцй';
        }
    }
    
    // --- --- --- --- ---
    static function login($user,$pass){
        return new self($user,$pass);
    }
}
?>