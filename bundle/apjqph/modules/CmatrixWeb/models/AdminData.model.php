<?php
namespace CmatrixWeb\Models;
use \CmatrixWeb as web;

class AdminData extends Admin implements web\iModel {
    public function getData(){

        return arrayMergeReplace(parent::getData(),[
            'app' => [
                'name' => 'Admin`ка • Данные'
            ]
        ]);
    }
}
?>