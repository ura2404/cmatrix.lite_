<?php
namespace CmatrixWeb\Models;
use \CmatrixWeb as web;

class P404 extends Comlogin implements web\iModel {
    
    public function getData(){

        return arrayMergeReplace(parent::getData(),[
            'app' => [
                'name' => '404'
            ],
            'page' => [
                'url' => web\Page::instance()->Url
            ]
        ]);
    }
}
?>