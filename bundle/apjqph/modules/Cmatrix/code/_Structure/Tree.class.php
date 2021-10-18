<?php
namespace Cmatrix\Structure;
use \Cmatrix as cm;

class Tree{
    

    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Tree' : return $this->getMyTree();
        }
    }
    
    // --- --- --- --- ---
    /**
     * @return \Cmatrix\Tree - 
     */
    private function getMyTree(){
        
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    /**
     * @param string $module - 
     */
    static function instance($module){
        $Tree = array_map(function($url){
            $Datamodel = cm\Ide\Datamodel::instance($url);
            return [
                'name' => $Datamodel->Url,
                'parent' => $Datamodel->Parent ? $Datamodel->Parent->Url : null
            ];
        },cm\Ide\Module::instance($module)->Datamodels);
        
        return cm\Tree::instance($Tree);
    }}
