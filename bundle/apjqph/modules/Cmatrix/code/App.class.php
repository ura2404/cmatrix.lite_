<?php
namespace Cmatrix;
use \CmatrixCore as co;

class App {
    static $INSTANCES = [];
    static $C;
    static $BUF = [];
    
    // --- --- --- --- ---
    function __construct(){
        if(!self::$C){
            self::$C = true;
            
            // 1. Если есть DB, то активировать сессии
            if($this->isDb && $this->isSession ) co\Session::instance(); 
        }
    }

    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'isDb' : return $this->getMyIsDb();
            case 'isSession' : return $this->getMyIsSession();
            case 'isWeb' : return $this->getMyIsWeb();
            case 'Sapi' : return $this->getMySapi();
        }
    }

    // --- --- --- --- ---
    protected function getMyIsDb(){
        if(isset(self::$BUF['db'])) return self::$BUF['db'];
        $Config = Hash::getFile(CM_TOP.'/config.json');
        return self::$BUF['db'] = $Config->getValue('db/enable');
    }

    // --- --- --- --- ---
    protected function getMyIsSession(){
        if(isset(self::$BUF['session'])) return self::$BUF['session'];
        $Config = Hash::getFile(CM_TOP.'/config.json');
        return self::$BUF['session'] = $Config->getValue('session/enable');
    }
    
    // --- --- --- --- ---
    protected function getMyIsWeb(){
        if(isset(self::$BUF['web'])) return self::$BUF['web'];
        $Config = Hash::getFile(CM_TOP.'/config.json');
        return self::$BUF['web'] = $Config->getValue('www/root');
    }
    
    // --- --- --- --- ---
    protected function getMySapi(){
        if(isset(self::$BUF['sapi'])) return self::$BUF['sapi'];
        
        $_sapi = function(){
            $sapi = php_sapi_name();
            if($sapi=='cli') return 'CLI';
            elseif(substr($sapi,0,3)=='cgi') return 'CGI';
            elseif(substr($sapi,0,6)=='apache') return 'APACHE';
            else return $sapi;
        };
        
        return self::$BUF['sapi'] = $_sapi();
    }

    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance(){
        $Key = md5('current');
        if(isset(self::$INSTANCES[$Key])) return self::$INSTANCES[$Key];
        
        return self::$INSTANCES[$Key] = new self;
    }
}
?>