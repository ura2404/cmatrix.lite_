<?php
namespace Cmatrix\Structure;
use \Cmatrix as cm;

class Provider implements iProvider{
    static $INSTANCES = [];
    
    static $PROVIDERS = [
        'mysql' => '\Cmatrix\Structure\Providers\Mysql',
        'pgsql' => '\Cmatrix\Structure\Providers\Pgsql'
    ];
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    /**
     * @param string | \Cmatrix\Datamodel $datamodel - 
     * @param string | cm\Db\Provider $provider - 
     */
    static function instance($datamodel,$provider){
        $Datamodel = $datamodel instanceof cm\Ide\Datamodel ? $datamodel :  cm\Ide\Datamodel::instance($datamodel);
        $Provider = $provider instanceof cm\Db\iProvider ? $provider : cm\Db\Provider::instance($provider);
        
        if(!in_array($Provider->Type,array_keys(self::$PROVIDERS))) throw new \Exception('Wrong provider "' .$name. '"');
        
        $Key = md5($Datamodel->Url.$Provider->Type);
        if(isset(self::$INSTANCES[$Key])) return self::$INSTANCES[$Key];
        
        $Cl = self::$PROVIDERS[$Provider->Type];
        return self::$INSTANCES[$Key] = new $Cl($Datamodel,$Provider);
    }
}

class Datamodel22 {
    
    static $INSTANCES = [];

    private $Datamodel;
    private $Provider;
    private $Prefix;
    
    // --- --- --- --- ---
    function __construct(cm\Ide\Datamodel $datamodel, cm\Db\Provider $provider){
        $this->Datamodel = $datamodel;
        $this->Provider = $provider;
        $this->Prefix = \Cmatrix\Hash::getFile(CM_TOP.'/config.json')->getValue('db/prefix');
    }
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Datamodel' : return $this->Datamodel;
            case 'Provider' : return $this->Provider;
            case 'Script' : return $this->getMyScript();
        }
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    private function getMyScript(){
        $Queries = [];
        $Comm = $this->Provider->getCommSymbol();
        $Url = $this->Datamodel->Url;
        
        //$this->Model->Parent ? $Queries['parent'] = (new self($this->Model->Parent))->getSqlCreate($provider) : null;
        
        //$Queries['main'][] = $Comm . '-------------------------------------------------------------------';
        //$Queries['main'][] = $Comm . '--- dm::' .$Url. '---------------------------';
        //$Queries['main'][] = "";
        
        $Queries['main'][] = $Comm . '--- sequence --- dm::' .$Url. ' -------------';
        $Queries['main'][] = $this->sqlSequences();
        $Queries['main'][] = "";
        
        $Queries['main'][] = $Comm . '--- table --- dm::' .$Url. ' ----------------';
        $Queries['main'][] = $this->sqlTable();
        $Queries['main'][] = "";
        
        $Queries['main'][] = $Comm . '--- pk --- dm::' .$Url. ' -------------------';
        $Queries['main'][] = $this->sqlCreatePks();
        $Queries['main'][] = "";
        
        $Queries['main'][] = $Comm . '--- uniques --- dm::' .$Url. ' --------------';
		$Queries['main'][] = $this->sqlCreateUniques();
		$Queries['main'][] = "";
        
/*        
        $Queries['main'][] = $Comm . '--- indexes --- dm::' .$Url. ' --------------';
		$Queries['main'][] = $provider->sqlCreateIndexes($this);
		$Queries['main'][] = "";
		
        $Queries['main'][] = $Comm . '--- grant --- dm::' .$Url. ' ----------------';
		$Queries['main'][] = $provider->sqlCreateGrant($this);
		$Queries['main'][] = "";
		
        $Queries['init'][] = $Comm . '--- init --- dm::' .$Url. ' -----------------';
		$Queries['init'][] = $provider->sqlCreateInit($this);
		$Queries['init'][] = "";
		
        $Queries['fk'][] = $Comm . '--- fk --- dm::' .$Url. ' -------------------';
		$Queries['fk'][] = $provider->sqlCreateFk($this);
		$Queries['fk'][] = "";
*/        
        //dump($Queries);
        
        //return $Queries;
        //return implode("\n",array2line($Queries))."\n";
        //return implode("\n",array2line($Queries));
        
        //dump("\n**********************************");
        //dump('**********************************');
        //dump('**********************************');
        
        //return implode("\n",array2line($Queries));
        //return implode("\n", $Queries);
    }

    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    /**
     * @return array - массив sql кода для создания Sequences
     */
    
    private function sqlSequences(){
        $Arr = array_map(function($prop){
            $Arr = [];
			$Name = Sequence::instance($this,$prop['code'])->Name;
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
     * @return array - массив sql кода для создания Table
     */
    private function sqlTable(){
        $Arr = [];
        $Table = Table::instance($this);
        
        $Arr[] = 'DROP TABLE IF EXISTS '. $Table->Name .' CASCADE;';
        $Arr[] = 'CREATE TABLE '. $Table->Name .'(';
        $Arr = array_merge($Arr,array_map(function($propName){
            $Arr = [];
            $Prop = Prop::instance($this,$propName);
            
            $Arr[] = $Prop->Name;
            $Arr[] = $Prop->Type;
            $Arr[] = $Prop->Default;
            $Arr[] = $Prop->NotNull;
            
            return implode(" ",array_filter($Arr,function($value){ return !!$value;}));
        },array_keys($this->Datamodel->Props)));
        $Arr[] = $this->Parent ? ') INHERITS ('. $this->Parent->TableName .');' : ');';
        
        return $Arr;
    }    

    // --- --- --- --- ---
    /**
     * @return array - массив sql кода для создания PrimaryKeys
     */
    private function sqlCreatePks(){
        $PkProps = array_keys(array_filter($this->Datamodel->Props,function($prop){
            return !!$prop['pk'];
        }));
        
        if(!count($PkProps)) return;
        
        $Table = Table::instance($this);
		$Pk = Pk::instance($this,$PkProps);
        //dump($Pk);
        
        $Arr = [];
        $Arr[] = 'ALTER TABLE ' .$Table->Name. ' DROP CONSTRAINT IF EXISTS ' .$Pk->Name. ' CASCADE;';
        $Arr[] = 'ALTER TABLE ' .$Table->Name. ' ADD CONSTRAINT ' .$Pk->Name. ' PRIMARY KEY (' .implode(',',$PkProps). ');';
        
        return $Arr;
    }
    
    // --- --- --- --- ---
    private function sqlCreateUniques(){
        return $this->sqlCreateIndexes(false);
    }

    // --- --- --- --- ---
    private function sqlCreateIndexes($isUnique=false){
        $Table = Table::instance($this);
        
        // подготовка массива групп индексных свойств
        $_src = function() use($isUnique){
            if($isUnique) return $this->Datamodel->Uniques;
            
            $Indexes = $this->Datamodel->Indexes;
            // добавить ссылочные поля
            $Association = $this->Datamodel->Association;
            if(count($Association)) foreach($Association as $ass) $Indexes[] = [$ass];
            //dump($Indexes);die();
            
            return $Indexes;
        };
        
        // формирование условий для групповой индексации свойств
        // нюанс для pgsql связан с некорректоной индексации null полей, особенно в связке с не null полями.
        $_inherit = function($props){
            // --- 1. кол-во свойств
            $N = count($props);
            
            // --- 2. перебор возможных вариантов
            // кол-во всех вариантов - это 2 в степени, равном кол-ву свойств
            $Ret = [];
            for($i=1; $i<pow(2,$N); $i++){
                // --- 2.1. получить бинарную строку, кодирующую
                //    например: 0010110 - нужно построить условие для свойств с индексами 2,3,5
                
                //   здесь форматирование строки для дополнения лидирующих нулей: %'.05s
                //     - .0 - дополнить нулями
                //     - 5  - общая длина 5 символов
                //     - s  - вовод строчных символов
                $S = str_split(sprintf("%'.0".$N."s",decbin($i)));
                dump(sprintf("%'.0".$N."s",decbin($i)));
            }
            
        };
        
    /*  
        // кол-во свойств
        $N = count($props);
        
        // перебор возможных вариантов
        $Ret = [];
        for($i=1; $i<pow(2,$N); $i++){
            // 1. получить бинарную строку
            //   например: 0010110
            //   здесь форматирование строки для дополнения лидирующих нулей: %'.05s
            //     - .0 - дополнить нулями
            //     - 5  - общая длина 5 символов
            //     - s  - вовод строчных символов
            $S = str_split(sprintf("%'.0".$N."s",decbin($i)));
            
            // 2. получить все варианты и пометить неиспользуемые варианты (nn=true)
            $Variants = array_map(function($val,$ind) use($props){
                if($val === '0' && $props[$ind]['nn']) return -1;
                return $val;
            },$S,array_keys($S));
            
            // 3. удалить неиспользуемые варианты
            $Fl = array_reduce($Variants,function($carry, $item){
                $carry = $carry && ($item == -1 ? false : true);
                return $carry;
            },true);
            if(!$Fl) continue;
            
            // 3. перебрать варианты для условий
            // если символ '0' - условие is null
            // если символ '1' - условие is not null
            //dump($Variants);
            
            $Props = [];
            $Rules = [];
            $Arr = array_map(function($var,$index) use($props,&$Props,&$Rules){
                if($props[$index]['nn'] || (!$props[$index]['nn'] && $var == '1')) $Props[] = $props[$index]['code'];
                
                if(!$props[$index]['nn'] && $var == '0') $Rules[] = $props[$index]['code'] .' IS NULL';
                if(!$props[$index]['nn'] && $var == '1') $Rules[] = $props[$index]['code'] .' IS NOT NULL';
            },$Variants,array_keys($Variants));
            
            $Ret[] = [
                'props' => $Props,
                'rules' => $Rules
            ];
        }
        //dump($Ret);
        return $Ret;
    */
      $Arr = array_map(function($group) use($isUnique,$Table,$_inherit){
            $Arr = array_map(function($val) use($isUnique,$Table,$_inherit){
            },$_inherit($group));
        },$_src());
    }
    
/*
    // --- --- --- --- --- --- --- ---
    public function sqlCreateUniques(structure\iModel $model){
        return $this->sqlCreateIndexes($model,true);
    }

    // --- --- --- --- --- --- --- ---
    public function sqlCreateIndexes(structure\iModel $model,$isUnique=false){
        $TableName  = $this->getTableName($model->Model);
        
        $_src = function() use($model,$isUnique){
            if($isUnique) return $model->Model->Uniques;
            
            $Indexes = $model->Model->Indexes;
            $Association = $model->Model->Association;
            if(count($Association)) foreach($Association as $ass) $Indexes[] = [$ass];
            //dump($Indexes);die();
            
            return $Indexes;
        };
        
        $Arr = array_map(function($group) use($model,$isUnique,$TableName){
            $Arr = array_map(function($val) use($model,$isUnique,$TableName){
                $Props = $val['props'];
                $Rules = $val['rules'];
                
                if($isUnique && $model->isActiveProp) $Rules[] = 'active IS NOT NULL';
                
                $IndexName = $TableName .'__' .($isUnique ? 'uniq' : 'index'). '__'. $this->getTransName(implode('_',$Props));
                $IndexProps = implode(',',$Props);
                
                $Arr = [];
                $Arr[] = 'DROP INDEX IF EXISTS ' .$IndexName. ' CASCADE;';
                $Arr[] = 'CREATE INDEX ' .$IndexName. ' ON ' .$TableName. ' USING btree (' .$IndexProps. ')' .(count($Rules) ? ' WHERE '.implode(' AND ',$Rules) : null). ';';
                
                //так не работает
                //$Arr[] = 'ALTER TABLE ' .$TableName. ' DROP CONSTRAINT IF EXISTS ' .$IndexName. ' CASCADE;';
                //$Arr[] = 'ALTER TABLE ' .$TableName. ' ADD CONSTRAINT ' .$IndexName. ' UNIQUE (' .$IndexProps. ')' .(count($Rules) ? ' WHERE '.implode(' AND ',$Rules) : null). ';';
                return implode("\n",$Arr);
                
            },$this->inheritIndex($group));
            
            return implode("\n",$Arr);
            
        },$_src());
        
        return implode("\n",$Arr);
    }
*/    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    /**
     * @param string | \Cmatrix\Datamodel $datamodel - 
     * @param string | cm\Db\Provider $provider - 
     */
    static function instance($datamodel,$provider){
        $Datamodel = $datamodel instanceof cm\Ide\Datamodel ? $datamodel :  cm\Ide\Datamodel::instance($datamodel);
        $Provider = $provider instanceof cm\Db\iProvider ? $provider : cm\Db\Provider::instance($provider);
        
        $Key = md5($Datamodel->Url.$Provider->Type);
        if(isset(self::$INSTANCES[$Key])) return self::$INSTANCES[$Key];
        
        return self::$INSTANCES[$Key] = new self($Datamodel,$Provider);
    }
}
?>