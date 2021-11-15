<?php
namespace CmatrixWeb\Models;
use \Cmatrix as cm;
use \CmatrixWeb as web;

class AdminData extends Comlogin implements web\iModel {
    public function getData(){
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
    }
    
    // --- --- --- --- ---
    private function getMyData(){
        return array_filter(array_map(function($module){
            $Module = cm\Ide\Module::instance($module);
            return [
                'module' => [
                    'code' => $Module->Code,
                    'name' => $Module->Name,
                    'baloon' => $Module->Baloon
                ],
                'datamodels' => array_map(function($dm){
                    $Datamodel = cm\Ide\Datamodel::instance($dm);
                    return [
                        'code' => $Datamodel->Code,
                        'name' => $Datamodel->Name,
                        'baloon' => $Datamodel->Baloon
                    ];
                },$Module->Datamodels)
            ];
        },cm\Ide\App::instance()->Modules),
            function($value){
                return $value['datamodels'];
            }
        );
    }
    
    // --- --- --- --- ---
    private function getMyTree(){
        return [
            'nodes' => [
                [
                    'label' => 'node1',
                    'nodes' => [
                        [
                            'label' => 'node11',
                        ],
                        [
                            'label' => 'node12',
                            'nodes' => [
                                [
                                    'label' => 'node121',
                                ],
                                [
                                    'label' => 'node122',
                                ],
                            ]
                        ],
                    ]
                ],
                [
                    'label' => 'node2',
                ],
                [
                    'label' => 'node2',
                ],
                [
                    'label' => 'node2',
                ],
                [
                    'label' => 'node2',
                ],
                [
                    'label' => 'node2',
                ],
                [
                    'label' => 'node2',
                ],
                [
                    'label' => 'node2',
                ],
                [
                    'label' => 'node2',
                ],
                [
                    'label' => 'node2',
                ],
                [
                    'label' => 'node2',
                ],
                [
                    'label' => 'node2',
                ],
                [
                    'label' => 'node2',
                ],
                [
                    'label' => 'node2',
                ],
                [
                    'label' => 'node2',
                ],
            ]
        ];
    }
}
?>