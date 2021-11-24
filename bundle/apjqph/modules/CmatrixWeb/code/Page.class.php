<?php
namespace CmatrixWeb;
use \Cmatrix\Exception as ex;

class Page {
    static $INSTANCES = [];
    
    private $Url;
    private $Params;
    
    // --- --- --- --- ---
    function __construct($url=null){
        $this->Url = $this->calculatePage($url);
        $this->Params = $this->parseParams($url);
    }
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            //case 'Name' : return $this->Pagename;
            case 'Html' : return $this->getMyHtml();
            case 'Url' : return $this->Url;
            case 'Page' : return $this->getMyPage();
            case 'Params' : return $this->Params;
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
    private function parseParams($url){
        $Params = array_diff_key($_REQUEST,array_flip(['cmp']));
        return $Params;
    }
    
    // --- --- --- --- ---
    /**
     * url страницы без параметров
     */
    private function getMyPage(){
        $Url = $this->Url;
        $Url = strBefore($Url,'?');
        $Url = strBefore($Url,'&');
        return $Url;
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    public function getParam($name=null){
        $Params = $this->Params;
        return ($name && array_key_exists($name,$Params)) ? $Params[$name] : null;
    }

    // --- --- --- --- ---
    public function setParam($name,$value){
        if($value) $this->Params[$name] = $value;
        else unset($this->Params[$name]);
        return $this;
    }

    // --- --- --- --- ---
    /**
     * Получить оперативный url
     */
    public function getUrl(){
        return CM_WHOME. '/' .$this->Page. (count($this->Params) ? '?'.implode('&',array_map(function($key,$value){
            return $key .'='. $value;
        },array_keys($this->Params),array_values($this->Params))) : null);
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance($url=null){
        $Key = $url ? $url : 'home';
        if(array_key_exists($Key,self::$INSTANCES)) return self::$INSTANCES[$Key];
        return new self($url);
    }
}
?>