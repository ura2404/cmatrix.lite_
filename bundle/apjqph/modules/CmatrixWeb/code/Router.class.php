<?php
namespace CmatrixWeb;
use \Cmatrix\Exception as ex;

class Router {
    static $ROUTERS = [];

    // --- --- --- --- ---
    function __construct(){
    }

    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    public function addRouter($match,$data){
        if(array_key_exists($match,self::$ROUTERS)) throw new \Exception('Router '.$match.' allready exists');
        
        $data['template'] = Template::get($data['template']);
        $data['model'] = Model::get($data['model']);
        $data['controller'] = Controller::get($data['controller']);
        
        self::$ROUTERS[$match] = $data;
        return $this;
    }

    // --- --- --- --- ---
    /**
     * @param strinf $name - имя страницы
     */
    public function getRouter($name){
        $_simple = function($match,$router) use($name){
            if($name !== $match) return;
            return $router;
        };
        
        $_match =  function($match,$router) use($name){
            if(!preg_match($match,$name)) return;
            return $router;
        };
        
        $Router = null;
        foreach(self::$ROUTERS as $match=>$router){
            $match = strval($match);
            if(strlen($match)>2 && $match[0] == '/' && $match[strlen($match)-1] == '/') $Router = $_match($match,$router);
            else $Router = $_simple($match,$router);
            
            if($Router) break;
        }
        
        return $Router;
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance(){
        return new self();
    }
    
    // --- --- --- --- ---
    static function add($match,array $data){
        if(is_array($match)) array_map(function($val) use($data){
            self::add($val,$data);
        },$match);
        
        else self::instance()->addRouter($match,$data);
    }
    
    // --- --- --- --- ---
    /**
     * @param $name - page name
     */
    static function get($name){
        return self::instance()->getRouter($name);
    }
}
?>