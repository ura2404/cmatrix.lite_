<?php
namespace CmatrixDb\Structure;
use \Cmatrix as cm;
use \CmatrixDb as db;
use \Cmatrix\Exception as ex;

class Datamodel{
    static $INSTANCES = [];
    
    private $StructureProviders;

    // --- --- --- --- ---
    function __construct(array $structureProviders){
        $this->StructureProviders = $structureProviders;
    }
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'SqlInitScript' : return $this->getSqlInitScript();
            default : throw new ex\Property($this,$name);
        }
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    private function getSqlInitScript(){
        $Queries = array_map(function($structureProvider){
            return $structureProvider->SqlInitScript;
        },$this->StructureProviders);
        
        if(count($this->StructureProviders)>1){
            $Main = [];
            $Fk = [];
            $Init = [];
            array_map(function($query) use(&$Main, &$Fk, &$Init){
                //dump($query);
                
                $Main[] = $query['main'];
                $Fk[] = $query['fk'];
                $Init[] = $query['init'];
                
            },$Queries);
            
            return implode("\n",array2line(array_merge($Main, $Init, $Fk)));
        }
        else return implode("\n",array2line($Queries));
        
        //dump($Queries);
        
        //return implode("\n",array2line($Queries));
    }
    
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    /**
     * @param string $datamodelUrl - 
     * @param string $providerName - 
     */
    static function instance($datamodelUrl,$providerName){
        $Key = md5($datamodelUrl.$providerName);
        if(isset(self::$INSTANCES[$Key])) return self::$INSTANCES[$Key];
        
        if($datamodelUrl==='all'){
            $Arr = array_map(function($moduleUrls) use($providerName){
                return array_map(function($datamodelUrl) use($providerName){
                    $Datamodel = cm\Ide\Datamodel::instance($datamodelUrl);
                    return db\Structure\Datamodel\Provider::instance($providerName,$Datamodel);
                },db\Structure\Datamodel\Tree::instance($moduleUrls)->PlainTree);
            },cm\Ide\App::instance()->Modules);
            
            $Arr = array_filter($Arr,function($value){
                return !!$value;
            });
            
            $StructureProviders = array_values($Arr)[0];
        }
        elseif(substr_count($datamodelUrl,'/')>1){
            $Datamodel = cm\Ide\Datamodel::instance($datamodelUrl);
            $StructureProviders = [ db\Structure\Datamodel\Provider::instance($providerName,$Datamodel) ];
        }
        else{
            $DatamodelUrls = db\Structure\Datamodel\Tree::instance($datamodelUrl)->PlainTree;
            $StructureProviders = array_map(function($datamodelUrl) use($providerName){
                $Datamodel = cm\Ide\Datamodel::instance($datamodelUrl);
                return db\Structure\Datamodel\Provider::instance($providerName,$Datamodel);
            },$DatamodelUrls);
            
        }

        return self::$INSTANCES[$Key] = new self($StructureProviders);
    }
}
?>