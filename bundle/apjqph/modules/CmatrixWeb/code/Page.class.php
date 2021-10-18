<?php
namespace CmatrixWeb;
use \Cmatrix\Exception as ex;

class Page {
    private $Name;
    
    // --- --- --- --- ---
    function __construct($name=null){
        $this->Name = $this->calculatePage($name);
    }
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            //case 'Name' : return $this->Pagename;
            case 'Html' : return $this->getMyHtml();
            default : throw new ex\Property($this,$name);
        }
    }

    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    /**
     * @return string - html содержимое страницы
     */
    private function getMyHtml(){
        $_render = function($router){
            // 1. template
            $Template = $router['template'];
            
            // 2. model
            $Model = isset($router['model']) ? $router['model'] : [];
            if($Model instanceof \Closure) $Data = $Model();
            elseif(!is_array($Model)) $Data = $Model->getData();

            // 3. controller
            $Controller = Controller::get($router['controller']);
            
            return $Controller->render($Template,!$Data ? [] : $Data);
        };
        
        $Router = Router::get($this->Name);
        if($Router) return $_render($Router);
        else{
            $Router = Router::get('404');
            if($Router) return $_render($Router);
            else die('Router for page '.$this->Name.' is not exists');
        }
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    private function calculatePage($name=null){
        
        $_name = function(){
            if(isset($_SERVER['REDIRECT_STATUS']) && $_SERVER['REDIRECT_STATUS'] == 200){
                $Page = strAfter(trim(rtrim($_SERVER['REDIRECT_QUERY_STRING'],'/')),'cmp=');
            }
            else{
                $Page = trim($_SERVER['REQUEST_URI'],'/');
            }
            return $Page == '' ? '/' : $Page;
        };
        
        return $name ? $name : $_name();
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance($name=null){
        return new self($name);
    }
    
    // --- --- --- --- ---
    //static function get($pageName=null){
    //    return self::instance()->setPage($pageName);
    //}
}
?>