<?php
namespace Cmatrix;

class Webpage {
    private $Pagename;
    
    // --- --- --- --- ---
    function __construct(){
    }
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Name' : return $this->Pagename;
            case 'Html' : return $this->getMyHtml();
        }
    }

    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    private function getMyHtml(){
        $_render = function($router){
            // 1. template
            //$Template = $router['template'].'.twig';
            $Template = $router['template'];
            
            // 2. model
            $Model = isset($router['model']) ? $router['model'] : [];
            if($Model instanceof \Closure) $Data = $Model();
            elseif(!is_array($Model)) $Data = $Model->getData();

            // 3. controller
            $Controller = Controllers::get($router['controller']);
            
            return $Controller->render($Template,!$Data ? [] : $Data);
        };
        
        $Router = Routers::get($this->Name);
        if($Router) return $_render($Router);
        else{
            $Router = Routers::get('404');
            if($Router) return $_render($Router);
            else die('Router for page '.$this->Name.' is not exists');
        }
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    private function setPage($pageName=null){
        
        $_pageName = function(){
            if(isset($_SERVER['REDIRECT_STATUS']) && $_SERVER['REDIRECT_STATUS'] == 200){
                $Page = strAfter(trim(rtrim($_SERVER['REDIRECT_QUERY_STRING'],'/')),'cmp=');
            }
            else{
                $Page = trim($_SERVER['REQUEST_URI'],'/');
            }
            return $Page == '' ? '/' : $Page;
        };
        
        $this->Pagename = $pageName ? $pageName : $_pageName();
        return $this;
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance(){
        return new self();
    }
    
    // --- --- --- --- ---
    static function get($pageName=null){
        return self::instance()->setPage($pageName);
    }
}
?>