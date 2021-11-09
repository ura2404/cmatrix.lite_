<?php
namespace CmatrixWeb\Models;
use \Cmatrix as cm;
use \CmatrixWeb as web;

class AdminData extends Admin implements web\iModel {
    public function getData(){

        return arrayMergeReplace(parent::getData(),[
            'app' => [
                'module' => 'Admin`ка • Данные'
            ],
            'data' => $this->getMyData(),
            'tree' => $this->getMyTree()
        ]);
    }
    
    // --- --- --- --- ---
    private function getMyData(){
        $Arr = array_filter(array_map(function($module){
            $Module = cm\Ide\Module::instance($module);
            return [
                'module' => [
                    'name' => $Module->Name,
                    'info' => $Module->Info
                ],
                'datamodels' => array_map(function($dm){
                    $Dm = cm\Ide\Datamodel::instance($dm);
                    return [
                        'name' => $Dm->Name,
                        'name' => $Dm->Info
                    ];
                },$Module->Datamodels)
            ];
        },cm\Ide\App::instance()->Modules),
            function($value){
                return $value['datamodels'];
            }
        );
        dump($Arr,'qqqqqqqq');
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