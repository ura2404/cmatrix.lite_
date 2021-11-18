<?php
namespace CmatrixWeb;
use \Cmatrix\Exception as ex;

class Page {
    private $Url;
    
    // --- --- --- --- ---
    function __construct($url=null){
        $this->Url = $this->calculatePage($url);
    }
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            //case 'Name' : return $this->Pagename;
            case 'Html' : return $this->getMyHtml();
            case 'Url' : return $this->Url;
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
            $Template = $router['template'];
            $Model = $router['model'];
            $Controller = $router['controller'];
            return $Controller->render($Template,$Model);
        };
        
        $Router = Router::get($this->Url);
        if($Router) return $_render($Router);
        else{
            $Router = Router::get('404');
            if($Router) return $_render($Router);
            else die('Router for page '.$this->Url.' is not exists');
        }
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    private function calculatePage($url=null){
        
        $_url = function(){
            if(isset($_SERVER['REDIRECT_STATUS']) && $_SERVER['REDIRECT_STATUS'] == 200){
                $Url = strAfter(trim(rtrim($_SERVER['REDIRECT_QUERY_STRING'],'/')),'cmp=');
            }
            else{
                $Url = trim($_SERVER['REQUEST_URI'],'/');
            }
            return $Url == '' ? '/' : $Url;
        };
        
        return $url ? $url : $_url();
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance($url=null){
        return new self($url);
    }
}
?>