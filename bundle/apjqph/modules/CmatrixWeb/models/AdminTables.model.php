<?php
namespace CmatrixWeb\Models;
use \CmatrixWeb as web;

class AdminTables extends Admin implements web\iModel {
    public function getData(){

        return arrayMergeReplace(parent::getData(),[
        ]);
    }
}
?>