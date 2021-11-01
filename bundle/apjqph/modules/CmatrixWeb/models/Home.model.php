<?php
namespace CmatrixWeb\Models;
use \CmatrixWeb as web;

class Home extends Comlogin implements web\iModel {
    
    public function getData(){
        return arrayMergeReplace(parent::getData(),[
            //'projects' => $this->getMyProjects(),
        ]);
    }
    
}
?>