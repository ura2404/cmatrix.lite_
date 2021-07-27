<?php
namespace Cmatrix;

class Hash {
    private $Data = [];

    // --- --- --- --- ---
    function __construct($data){
        $this->Data = $data;
    }

    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    public function getValue($url,$def=null){
        $_rec = function($arr,$ini) use($def,&$_rec){
            if(count($arr) > 1){
                $Ind = array_shift($arr);
                return isset($ini[$Ind]) ? $_rec($arr,$ini[$Ind]) : false;
            }
            else return array_key_exists($arr[0],$ini) ? $ini[$arr[0]] : ($def !== null ? $def : false);
        };

        return $_rec(explode('/',trim($url,'/')),$this->Data);
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    /**
     * @param string $path - путь к json файлу
     */
    static function getFile($path){
        if(!file_exists($path)) throw new \Exception('Wrong json file');
        $Arr = json_decode(file_get_contents($path),true);
        return new self($Arr);
    }
    
}
?>