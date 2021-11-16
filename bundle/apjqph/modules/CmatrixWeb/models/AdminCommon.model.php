<?php
namespace CmatrixWeb\Models;
use \Cmatrix as cm;
use \CmatrixWeb as web;

class AdminCommon extends CommonLogin implements web\iModel {
    public function getData(){
        
        //return arrayMergeReplace(parent::getData());
        return parent::getData();
    }
}
?>