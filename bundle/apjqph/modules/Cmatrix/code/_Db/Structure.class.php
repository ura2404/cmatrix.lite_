<?php
namespace Cmatrix\Db;
use \Cmatrix as cm;
use \Cmatrix\Exception as ex;

class Structure extends cm\Exception {
    private $Datamodels;
    private $StructureProvider;
    private $ConnectProvider;

    // --- --- --- --- ---
    function __construct(array $datamodels, cm\Db\Structure\iProvider $structureProvider,cm\Db\Connect\iProvider $connectProvider){
        $this->Datamodels = $datamodels;
        $this->StructureProvider = $structureProvider;
        $this->ConnectProvider = $connectProvider;
    }
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'InitScript' : return $this->getInitScript('all');
            default : throw new ex\Property($this,$name);
        }
    }
    
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    public function getInitScript($target){
        $Queries = array_map(function($datamodel) use($target){
            return $this->StructureProvider->getInitScript($target,$this->ConnectProvider,$datamodel);
        },$this->Datamodels);
        
        dump($Queries);
        
        //return implode("\n",array2line($Queries));
    }


    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    /**
     * @param string $datamodel - 
     * @param string $provider - 
     */
    static function instance($datamodelUrl,$providerName){
        $StructureProvider = cm\Db\Structure\Provider::instance($providerName);
        $ConnectProvider = cm\Db\Connect\Provider::instance($providerName);
        
        if(substr_count($datamodelUrl,'/')>1){
            $Datamodels = [cm\Ide\Datamodel::instance($datamodelUrl)];
        }
        else{
            //return new self(Structure\Tree::instance($datamodelUrl)->PlainTree,$providerName);
        }
        return new self($Datamodels,$StructureProvider,$ConnectProvider);
        
        
        
        /*
        $Datamodel = $datamodel instanceof cm\Ide\Datamodel ? $datamodel :  cm\Ide\Datamodel::instance($datamodel);
        $Provider = $provider instanceof cm\Db\iProvider ? $provider : cm\Db\Provider::instance($provider);
        
        if(!in_array($Provider->Type,array_keys(self::$PROVIDERS))) throw new \Exception('Wrong provider "' .$name. '"');
        
        $Key = md5($Datamodel->Url.$Provider->Type);
        if(isset(self::$INSTANCES[$Key])) return self::$INSTANCES[$Key];
        
        $Cl = self::$PROVIDERS[$Provider->Type];
        return self::$INSTANCES[$Key] = new $Cl($Datamodel,$Provider);
        */
    }
}
?>