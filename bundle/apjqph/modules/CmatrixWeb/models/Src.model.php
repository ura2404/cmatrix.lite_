<?php
namespace CmatrixWeb\Models;
use \Cmatrix as cm;
use \CmatrixWeb as web;

class Src extends Common implements web\iModel {
    public function getData(){
        
        //return arrayMergeReplace(parent::getData(),[]);
        return parent::getData();
    }
}
?>