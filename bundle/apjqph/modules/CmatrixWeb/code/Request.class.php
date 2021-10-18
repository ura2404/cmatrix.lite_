<?php
namespace CmatrixWeb;
use \Cmatrix as cm;
use \Cmatrix\Exception as ex;

class Request {

    private $Data;
    
    // --- --- --- --- ---
    function __construct($data){
        $this->Data = $data;
    }

    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Array' : parse_str($this->Data,$Data);
                           return $Data;
                            
            case 'Data'      : return $this->getData();
            case 'Json'      : return $this->getMyJson();
            //case 'BinDecode' : return $this->binDecode();
            //case 'BinEncode' : return $this->binEncode();
            default : throw new ex\Property($this,$name);
        }
    }
    
    // --- --- --- --- ---
    private function getMyJson(){
        return cm\Json::create($this->Data)->Encode;
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function create(array $data){
        return new self($data);
    }
    
    // --- --- --- --- ---
    static function get(){
        $Data = file_get_contents('php://input');
        return new self($Data);
    }
}