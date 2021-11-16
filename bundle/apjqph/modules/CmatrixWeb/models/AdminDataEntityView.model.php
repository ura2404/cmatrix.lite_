<?php
namespace CmatrixWeb\Models;
use \Cmatrix as cm;
use \Cmatrix\Exception as ex;
use \CmatrixWeb as web;

class AdminDataEntityView extends AdminCommon implements web\iModel {
    public function getData(){
        
        try{
            $Entity = $this->getDatamodel();
            $Datamodel = cm\Ide\Datamodel::instance($Entity);
            
            return arrayMergeReplace(parent::getData(),[
                'app' => [
                    'module' => 'Admin`ка • ' . $Datamodel->Name
                ],
                'datamodel' => [
                    'props' => $Datamodel->Props
                ],
                'e' => $Datamodel->Code
            ]);
        }
        catch(\Exception $e){
            return arrayMergeReplace(parent::getData(),[
                'error' => 'Ошибка.'
            ]);
        }
        catch(\Throwable $e){
            return arrayMergeReplace(parent::getData(),[
                'error' => 'Ошибка.'
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