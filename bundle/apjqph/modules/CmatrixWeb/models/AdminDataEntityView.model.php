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
            
            return arrayMergeReplace(parent::getData(),[
                'app' => [
                    'module' => 'Admin`ка • ' . $Datamodel->Name
                ],
                'datamodel' => [
                    'name' => $Datamodel->Name,
                    'props' => $Datamodel->Props,
                    'css' => $Datamodel->Css,
                    'lines' => $Datamodel->Lines,
                    'total' => $Datamodel->Total,
                    'page' => 17
                ],
            ]);
        }
        catch(\Exception $e){
            return arrayMergeReplace(parent::getData(),[
                'error' => ['Ошибка',$e->getMessage()]
            ]);
        }
        catch(\Throwable $e){
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
    private function getDatamodel(){
        $Arr = explode('/',strAfter(web\Page::instance()->Url,'entity/'));
        return '/'.$Arr[0].'/'.$Arr[1];
        //return cm\Ide\Datamodel::instance($Entity);
    }
    
}
?>