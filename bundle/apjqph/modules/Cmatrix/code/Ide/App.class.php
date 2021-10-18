<?php
namespace Cmatrix\Ide;
use \Cmatrix as cm;

/**
 */
class App {
    private $P_Path = null;
    
    // --- --- --- --- ---
    function __construct(){
    }
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Path' : return $this->getMyPath();
            case 'Modules' : return $this->getMyModules();
        }
    }

    // --- --- --- --- ---
    private function getMyPath(){
        if($this->P_Path !== null) return $this->P_Path;
        return $this->P_Path = CM_ROOT;
    }
    
    // --- --- --- --- ---
    private function getMyModules(){
        $Root = $this->Path .'/modules';
        if(!file_exists($Root)) return [];
        
        return array_map(function($value){
            return '/'.$value;
        },array_filter(scandir($Root),function($value) use($Root){
            return $value !== '.' && $value !== '..' && is_dir($Root.'/'.$value);
        }));
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance(){
        return new self();
    }
}