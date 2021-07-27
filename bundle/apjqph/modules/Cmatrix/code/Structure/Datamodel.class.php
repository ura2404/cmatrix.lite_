<?php
namespace Cmatrix\Structure;
use \Cmatrix as cm;

class Datamodel {
    private $Prefix;
    private $Datamodel;
    private $Provider;
    
    // --- --- --- --- ---
    function __construct(cm\Datamodel $datamodel, cm\Db\Provider $provider){
        $this->Datamodel = $datamodel;
        $this->Provider = $provider;
        $this->Prefix = \Cmatrix\Hash::getFile(CM_TOP.'/config.json')->getValue('db/prefix');
    }
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'TableName' : return $this->getMyTableName();
            case 'Script' : return $this->getMyScript();
        }
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    private function getMyScript(){
        $Queries = [];
        
        //$this->Model->Parent ? $Queries['parent'] = (new self($this->Model->Parent))->getSqlCreate($provider) : null;
        
        //$Queries['main'][] = '-- -------------------------------------------------------------------';
        //$Queries['main'][] = '-- --- dm::' .$this->Model->Url. '---------------------------';
        //$Queries['main'][] = "";
        $Queries['main'][] = '-- --- sequence --- dm::' .$this->Datamodel->Url. ' -------------';
        $Queries['main'][] = $this->sqlSequence();
        $Queries['main'][] = "";
        
        $Queries['main'][] = '-- --- table --- dm::' .$this->Datamodel->Url. ' ----------------';
        $Queries['main'][] = $this->sqlTable();
        $Queries['main'][] = "";

/*        
        $Queries['main'][] = '-- --- pk --- dm::' .$this->Model->Url. ' -------------------';
        $Queries['main'][] = $provider->sqlCreatePk($this);
        $Queries['main'][] = "";

        $Queries['main'][] = '-- --- uniques --- dm::' .$this->Model->Url. ' --------------';
		$Queries['main'][] = $provider->sqlCreateUniques($this);
		$Queries['main'][] = "";
        
        $Queries['main'][] = '-- --- indexes --- dm::' .$this->Model->Url. ' --------------';
		$Queries['main'][] = $provider->sqlCreateIndexes($this);
		$Queries['main'][] = "";
		
        $Queries['main'][] = '-- --- grant --- dm::' .$this->Model->Url. ' ----------------';
		$Queries['main'][] = $provider->sqlCreateGrant($this);
		$Queries['main'][] = "";
		
        $Queries['init'][] = '-- --- init --- dm::' .$this->Model->Url. ' -----------------';
		$Queries['init'][] = $provider->sqlCreateInit($this);
		$Queries['init'][] = "";
		
        $Queries['fk'][] = '-- --- fk --- dm::' .$this->Model->Url. ' -------------------';
		$Queries['fk'][] = $provider->sqlCreateFk($this);
		$Queries['fk'][] = "";
*/        
        dump($Queries);
        return $Queries;
        
        return implode("\n",array2line($Queries))."\n";
        //return implode("\n",array2line($Queries));
        //return implode("\n", $Queries);
    }

    // --- --- --- --- --- --- --- ---
    /**
     * @retrun string - трансформированное имя
     */
    private function sqlTransName($name){
        // 1.
        return $name;
        
        // 2.
        return 'cm'.md5($name);
        
        // 3.
        //$Prefix = \Cmatrix\Db\Kernel::get()->CurConfig->getValue('prefix',null);
        //$Prefix = $Prefix ? $Prefix : 'cm';
        //return $Prefix .'_'. md5($name);
    }
    
    // --- --- --- --- ---
    /**
     * @return strung - sql имя таблицы
     */
    private function sqlTableName(){
        return $this->Prefix . str_replace('/','_',ltrim($this->Datamodel->Json['code'],'/'));
    }
    
    // --- --- --- --- ---
    /**
     * @param string $propCode - имя свойства
     * @return string - sql имя squence для свойства
     */
    private function getPropSequenceName($propCode){
        $Name = $this->sqlTableName() .'__seq__'. $propCode;
        $Name = strtolower($Name);
        
        return $this->sqlTransName($Name);
    }


    // --- --- --- --- ---
    /**
     * @return array - массив sql кода для создания sequences
     */
    private function sqlSequence(){
        $Arr = array_map(function($prop){
            $Arr = [];
			$Name = $this->getPropSequenceName($prop['code']);
			$Arr[] = 'DROP SEQUENCE IF EXISTS '. $Name .' CASCADE;';
			$Arr[] = 'CREATE SEQUENCE '. $Name .';';
			return implode("\n", $Arr);
        },array_filter($this->Datamodel->Props,function($prop){
            return $prop['default'] === '::counter::';
        }));
        return $Arr;
    }


    // --- --- --- --- ---
    /**
     * @return array - массив sql кода для создания таблицы
     */
    private function sqlTable(){
        $TableName = $this->sqlTableName();
        
        $Arr = [];
        $Arr[] = 'DROP TABLE IF EXISTS '. $TableName .' CASCADE;';
        $Arr[] = 'CREATE TABLE '. $TableName .'(';
        $Arr[] = $Parent ? ') INHERITS ('. $ParentTableName .');' : ');';
        
        return $Arr;
    }    

/*
    public function sqlCreateTable(structure\iModel $model){
        $Parent = $model->Model->Parent;
        $TableName = $this->getTableName($model->Model);
        $ParentTableName = $Parent ? (new self())->getTableName($Parent) : null;
        
        $Arr = [];
        $Arr[] = 'DROP TABLE IF EXISTS '. $TableName .' CASCADE;';
        $Arr[] = 'CREATE TABLE '. $TableName .'(';
        $Arr[] = implode(",\n",array_map(function($prop) use($model){
            $Arr = [];
            $Arr[] = $this->getPropName($model->Model,$prop['code']);
            $Arr[] = $this->getPropType($prop);
            $Arr[] = $this->getPropDefault($model,$prop);
            $Arr[] = $this->getPropNotNull($prop);
            
            return implode(" ",array_filter($Arr,function($val){ return !!$val; }));
        },$model->Model->OwnProps));
        $Arr[] = $Parent ? ') INHERITS ('. $ParentTableName .');' : ');';

        return $Arr;
    }
*/    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    public function getScript($provider,$isFormat=false){
        //if(strpos($this->Dm->Url,'/')!==false) $Script = Provider::instance($provider)->getScript($this->Dm);
        
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance($url,$provider){
        return new self(cm\Datamodel::instance($url),cm\Db\Provider::instance($provider));
    }
}
?>