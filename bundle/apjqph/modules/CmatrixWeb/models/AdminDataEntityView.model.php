<?php
namespace CmatrixWeb\Models;
use \Cmatrix as cm;
use \Cmatrix\Exception as ex;
use \CmatrixWeb as web;

class AdminDataEntityView extends AdminCommon implements web\iModel {
    public function getData(){
        
        try{
            $Entity = $this->getDatamodel();
            $Datamodel = web\Ide\Datamodel::instance($Entity);
            
            //dump($Datamodel->Sorts);
            
            $Data = arrayMergeReplace(parent::getData(),[
                'app' => [
                    'module' => 'Admin`ка • ' . $Datamodel->Name
                ],
                'table' => [
                    'name' => $Datamodel->Name,
                    'sorts' => $Datamodel->Sorts,
                    'props' => $Datamodel->Props,
                    'lines' => $Datamodel->Lines,
                    //'total' => $Datamodel->Total,
                    'pager' => $Datamodel->Pager,
                    'rfilter' => web\Page::instance()->getParam('r')
                ],
            ]);
            //dump($Data);
            
            return $Data;
        }
        catch(\Exception1 $e){
            return arrayMergeReplace(parent::getData(),[
                'error' => ['Ошибка',$e->getMessage()]
            ]);
        }
        catch(\Throwable1 $e){
            return arrayMergeReplace(parent::getData(),[
                'error' => ['Ошибка',$e->getMessage()]
            ]);
        }
        
        /*
        return arrayMergeReplace(parent::getData(),[
            'app' => [
                'module' => 'Admin`ка • Данные',
            ],
            'page' => [
                'url'=> CM_WHOME .'/'. web\Page::instance()->Url,
            ],
            'data' => $this->getMyData(),
            'tree' => $this->getMyTree()
        ]);
        */
    }
    
    // --- --- --- --- ---
    private function getMyUrl(){
        return CM_WHOME . '/' . web\Page::instance()->Page;
    }
    
    // --- --- --- --- ---
    private function getDatamodel(){
        $Arr = explode('/',strBefore(strAfter(web\Page::instance()->Url,'entity/'),'&'));
        return '/'.$Arr[0].'/'.$Arr[1];
        //return cm\Ide\Datamodel::instance($Entity);
    }
}
?>