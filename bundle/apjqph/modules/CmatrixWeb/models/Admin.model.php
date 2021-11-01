<?php
namespace CmatrixWeb\Models;
use \CmatrixWeb as web;

class Admin extends Comlogin implements web\iModel {
    public function getData(){

        return arrayMergeReplace(parent::getData(),[
            'url' => [
                'dictionary' => CM_WHOME.'/admin/dictionary'
            ]
        ]);
    }
}
?>