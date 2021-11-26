<?php
namespace TildaTool\Models;
use \Cmatrix as cm;
use \CmatrixWeb as web;

class ToolSupervisor extends \CmatrixWeb\Models\CommonLogin implements web\iModel {
    public function getData(){
        try{
            $Datamodel = web\Ide\Datamodel::instance('/TildaTool/Tool');
            
            $Rfilter = $Datamodel->Rfilter;
            $Pfilter = $Datamodel->Pfilter;
            
            //dump(web\Page::instance()->getParam('f'));
            //$clean = strtr( web\Page::instance()->getParam('f'), ' ', '+');
            //dump($clean);
            //dump(base64_decode( $clean ));
            //dump(json_decode(base64_decode( $clean ),true));
            
            $Data = arrayMergeReplace(parent::getData(),[
                'app' => [
                    'module' => 'Tilda • Режущий инструмент'
                ],
                'table' => [
                    'name' => $Datamodel->Name,
                    'sorts' => $Datamodel->Sorts,
                    'props' => $Datamodel->Props,
                    'lines' => $Datamodel->Lines,
                    'pager' => $Datamodel->Pager,
                    'rfilter' => $Rfilter,
                    'pfilter' => $Pfilter,
                ]
            ]);
            //dump($Data['table']['pfilter']);
            
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
    }
    
    // --- --- --- --- ---
    private function getLines(){
        
        
        return $Datamodel->Lines;
    }
}
?>