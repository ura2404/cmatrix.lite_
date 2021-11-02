<?php
namespace CmatrixWeb\Models;
use \CmatrixWeb as web;

class AdminDictionaries extends Admin implements web\iModel {
    public function getData(){

        return arrayMergeReplace(parent::getData(),[
            'app' => [
                'name' => 'Справочники'
            ]
        ]);
    }
}
?>