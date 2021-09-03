<?php
namespace Cmatrix;

class App {
    private $P_IsDb = null;
    
    static $WEBPAGE;
    
    static $PAGE;
    static $PARAMS;

    private $Twig;

    // --- --- --- --- ---
    function __construct(){
    }

    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Webpage' : return $this->getMyWebpage();
            
            case 'isDb' : return $this->getMyIsDb();
        }
    }

    // --- --- --- --- ---
    protected function getMyIsDb(){
        if($this->P_IsDb !== null) return $this->P_IsDb;
        $Config = Hash::getFile(CM_TOP.'/config.json');
        return $this->P_IsDb = $Config->getValue('db/enable');
    }
    
    // --- --- --- --- ---
    private function getMyWebpage(){
        if(self::$WEBPAGE) return self::$WEBPAGE;
        
        return self::$WEBPAGE = Webpage::get();
    }

    // --- --- --- --- ---
    private function getMyHtml____(){
        try{
            $_render = function($router){
                if(!$router) throw new \Exception('router is not defined');
                
                $Template = $router['template'].'.twig';
                $Model = isset($router['model']) ? $router['model'] : [];
                if($Model instanceof \Closure) $Data = $Model();
                else{
                    $ClassName = "\\Cmatrix\\Models\\".ucfirst($Model);
                    $Data = (new $ClassName())->getData();
                }
                
                return $this->Twig->render($Template,!$Data ? [] : $Data);
            };
            
            $_simple = function($router){
                //dump($router,'simple');
                if(self::$PAGE !== $router['match']) return;
                return $router;
            };
            
            $_match =  function($router){
                //dump($router,'match');
                if(!preg_match($router['match'],self::$PAGE)) return;
                return $router;
            };
            
            foreach(\Cmatrix\Router::$ROUTERS as $router){
                $Match = $router['match'];
                if(strlen($Match)>2 && $Match[0] == '/' && $Match[strlen($Match)-1] == '/') $Router = $_match($router);
                else $Router = $_simple($router);
                
                if($Router) break;
            }
            
            if($Router) return $_render($Router);
            else if(isset(\Cmatrix\Router::$ROUTERS['404'])) $_render(\Cmatrix\Router::$ROUTERS['404']);
            else die('Router for page '.self::$PAGE.' is not exists');
        }
        //catch(\Exception $e)
        catch(\Throwable2 $e){
            dump($e->getMessage());
            dump($e->getTrace());
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