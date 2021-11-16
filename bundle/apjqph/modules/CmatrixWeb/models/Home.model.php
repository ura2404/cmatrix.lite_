<?php
namespace CmatrixWeb\Models;
use \CmatrixWeb as web;

class Home extends CommonLogin implements web\iModel {
    
    public function getData(){
        return arrayMergeReplace(parent::getData(),[
            //'projects' => $this->getMyProjects(),
        ]);
    }
    
}
?>