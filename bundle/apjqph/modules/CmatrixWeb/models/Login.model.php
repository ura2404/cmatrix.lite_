<?php
namespace CmatrixWeb\Models;
use \CmatrixWeb as web;

class Login extends Common implements web\iModel {
    public function getData(){

        return arrayMergeReplace(parent::getData(),[
        ]);
    }
}
?>