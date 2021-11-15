<?php
namespace CmatrixWeb\Models;
use \Cmatrix as cm;
use \CmatrixWeb as web;

class AdminDataViewEntity extends Comlogin implements web\iModel {
    public function getData(){
        
        try{
            $Entity = strAfter(web\Page::instance()->Url,'admin/data/view/entity');
            cm\Ide\Datamodel::instance($Entity);
        }
        catch(\Exception $e){
            return arrayMergeReplace(parent::getData(),[
                'error' => 'Ошибка'
            ]);
        }
        catch(\Throwable $e){
            return arrayMergeReplace(parent::getData(),[
                'error' => 'Ошибка'
            ]);
        }
        
        return arrayMergeReplace(parent::getData(),[
            /*
            'app' => [
                'module' => 'Admin`ка • Данные',
            ],
            'page' => [
                'url'=> CM_WHOME .'/'. web\Page::instance()->Url,
            ],
            'data' => $this->getMyData(),
            'tree' => $this->getMyTree()
            */
        ]);
    }
}
?>