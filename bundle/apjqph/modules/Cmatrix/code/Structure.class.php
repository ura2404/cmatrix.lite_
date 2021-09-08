<?php
namespace Cmatrix;
use \Cmatrix as cm;

class Structure{
    static $PROVIDERS = [
        'mysql' => '\Cmatrix\Structure\Providers\Mysql',
        'pgsql' => '\Cmatrix\Structure\Providers\Pgsql'
    ];
    
    private $Datamodels = [];
    private $Provider;
    
    // --- --- --- --- ---
    function __construct(array $datamodels, $provider){
        $this->Datamodels = $datamodels;
        $this->Provider = $provider;
    }
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Script' : return $this->getMyScript();
        }
    }
    
    // --- --- --- --- ---
    private function getMyScript(){
        $Queries = array_map(function($datamodel){
            return Structure\Provider::instance($datamodel,$this->Provider)->Script;
        },$this->Datamodels);
        //dump($Queries);
        
        return implode("\n",array2line($Queries));
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    /**
     * @param string $datamodel - 
     * @param string $provider - 
     */
    static function instance($datamodel,$provider){
        if(!in_array($provider,array_keys(self::$PROVIDERS))) throw new \Exception('Wrong provider "' .$name. '"');
        
        if(substr_count($datamodel,'/')>1){
            return new self([$datamodel],$provider);
        }
        else{
            return new self(Structure\Tree::instance($datamodel)->PlainTree,$provider);
        }
        
        
        
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