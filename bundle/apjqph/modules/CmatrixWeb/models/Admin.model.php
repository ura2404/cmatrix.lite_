<?php
namespace CmatrixWeb\Models;
use \CmatrixWeb as web;

class Admin extends Comlogin implements web\iModel {
    public function getData(){

        return arrayMergeReplace(parent::getData(),[
            'url' => [
                'dictionaries' => CM_WHOME.'/admin/dictionaries',
                'tables' => CM_WHOME.'/admin/tables',
                'messages' => CM_WHOME.'/admin/messages',
                'setup' => CM_WHOME.'/admin/setup'
            ]
        ]);
    }
}
?>