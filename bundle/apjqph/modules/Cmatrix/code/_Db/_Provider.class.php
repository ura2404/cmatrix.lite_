<?php
namespace Cmatrix\Db;
use \Cmatrix as cm;

class Provider {

    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Type' : return $this->getType();
        }
    }
}
?>