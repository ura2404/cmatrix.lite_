<?php
namespace CmatrixWeb\Models;
use \Cmatrix as cm;
use \CmatrixCore as co;
use \CmatrixWeb as web;

class AdminTool extends CommonLogin implements web\iModel {
    public function getData(){
        return arrayMergeReplace(parent::getData(),[
            'app' => [
                'module' => 'Admin`ка • Инструменты',
            ],
            'tools' => $this->getMyTools()
        ]);
    }
    
    // --- --- --- --- ---
    private function getMyTools(){
        return [
            [
                'enable' => true,
                'visible' => co\Sysuser::instance()->isMyGroups('coAdmins','coSupervisors'),
                'code' => 'tildaTool',
                'name' => 'Режущий инструмент',
                'icon' => 'fal fa-vector-square',
                'info' => 'Управление справочником режущего инструмента',
                'url' => CM_WHOME.'/tilda/toolSupervisor',
            ]
        ];
    }
}
?>