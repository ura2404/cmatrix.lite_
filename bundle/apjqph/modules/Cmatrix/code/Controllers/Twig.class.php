<?php
namespace Cmatrix\Controllers;

require_once 'Twig/autoload.php';

class Twig {
    
    private $Twig;
    
     // --- --- --- --- ---
    function __construct(){
        $this->Twig = new \Twig_Environment($this->getMyLoader(),[
            'cache' => '/var/tmp',
            'debug' => true,
            'auto_reload' => true
        ]);
    }
   
   // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Controller' : return $this->Twig;
        }
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    private function getMyLoader(){
        return new \Twig_Loader_Filesystem(CM_ROOT .'/modules');
        
        /*
        $Loader = new \Twig_Loader_Filesystem();
        array_map(function($modules) use($Loader){
            $Path = CM_ROOT .'/modules/'. $modules .'/templates';
            if(file_exists($Path)) $Loader->addPath($Path);
        },scandir(CM_ROOT.'/modules'));
        
        return $Loader;
        */
    }

    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance(){
        return new self();
    }
    
    // --- --- --- --- ---
    static function get(){
        return self::instance()->Controller;
    }
}
?>