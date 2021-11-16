<?php
namespace CmatrixWeb;
use \Cmatrix\Exception as ex;

class Model {
    static $INSTANCES = [];
    
    static $MODELS = [];
    private $Mix;
    
    // --- --- --- --- ---
    function __construct($mix){
        $this->Mix = gettype($mix) === 'string' ? $this->getMyModel($mix) : $mix;
        $this->Path;
    }
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Path' : return $this->getMyPath();
            case 'Data' : return $this->getMyData();
            default : throw new ex\Property($this,$name);
        }
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    private function getMyModel($mix){
        $Arr = explode('/',ltrim($mix,'/'));
        $Module = array_shift($Arr);
        $Cl = '\\' . $Module . '\Models\\' . implode('\\',$Arr);
        return new $Cl();
    }
    
    // --- --- --- --- ---
    private function getMyPath(){
        if(gettype($this->Mix) !== 'string') return false;
        
        $Arr = explode('/',ltrim($this->Mix,'/'));
        $Module = array_shift($Arr);
        $Path = '/' . $Module . '/models/' . implode('/',$Arr) . '.model.php';
        if(!file_exists(CM_ROOT.'/modules'.$Path)) throw new ex('Model "' .$this->Mix. '" is not defined.');
        return $Path;
    }
    
    // --- --- --- --- ---
    private function getMyData(){
        if($this->Mix instanceof \Closure) return $this->Mix();
        elseif(is_array($this->Mix)) return $this->Mix;
        elseif($this->Mix instanceof \CmatrixWeb\iModel) return (new $this->Mix())->getData();
        else throw new ex('Model type "' .gettype($this->Mix). '"is invalid');
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    /**
     * @param \Closure|array|string $mix
     */
    static function instance($mix){
        $Key = gettype($mix) === 'string' ? $mix : serialize($mix);
        if(array_key_exists($Key,self::$INSTANCES)) return self::$INSTANCES[$Key];
        return new self($mix);
    }
    /*    
    // --- --- --- --- ---
    static function add($name,$mix){
        if(array_key_exists($name,self::$MODELS)) throw new \Exception('Model "'.$name.'" allready exists.');
        return self::$MODELS[$name] = self::instance($mix);
    }
    
    // --- --- --- --- ---
    static function get($name){
        if(!array_key_exists($name,self::$MODELS)) throw new \Exception('Model "'.$name.'" is not exists.');
        return self::$MODELS[$name];
    }
    */
}
?>