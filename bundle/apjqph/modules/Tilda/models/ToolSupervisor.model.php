<?php
namespace Tilda\Models;
use \Cmatrix as cm;
use \CmatrixWeb as web;

class ToolSupervisor extends \CmatrixWeb\Models\CommonLogin implements web\iModel {
    public function getData(){
        try{
            $Datamodel = web\Ide\Datamodel::instance('/CmatrixCore/Session');
            
            return arrayMergeReplace(parent::getData(),[
                'app' => [
                    'module' => 'Tilda • Режущий инструмент'
                ],
                'table' => [
                    'name' => $Datamodel->Name,
                    'sorts' => $Datamodel->Sorts,
                    'props' => $Datamodel->Props,
                    'lines' => $Datamodel->Lines,
                    //'total' => $Datamodel->Total,
                    'pager' => $Datamodel->Pager,
                    'filter' => $Datamodel->Filter,
                    'rfilter' => web\Page::instance()->getParam('r')
                ]
            ]);
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
}
?>