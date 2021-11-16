<?php
namespace CmatrixWeb\Models;
use \CmatrixWeb as web;

class P404 extends CommonLogin implements web\iModel {
    
    public function getData(){

        return arrayMergeReplace(parent::getData(),[
            'app' => [
                'module' => '404'
            ],
            'page' => [
                'url' => web\Page::instance()->Url
            ]
        ]);
    }
}
?>