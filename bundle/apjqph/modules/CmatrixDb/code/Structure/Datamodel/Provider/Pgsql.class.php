<?php
namespace CmatrixDb\Structure\Datamodel\Provider;
use \Cmatrix as cm;
use \CmatrixDb as db;

class Pgsql extends db\Structure\Datamodel\Provider implements db\Structure\Datamodel\iProvider{

    // --- --- --- --- ---
    // --- --- --- --- ---
    protected function getSqlInitScript(){
        $Queries = [];
        $Comm = $this->ConnectProvider->getCommSymbol();
        $Url = $this->Datamodel->Url;
        
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
        
        $Queries['main'][] = $Comm . '--- indexes --- dm::' .$Url. ' --------------';
		$Queries['main'][] = $this->sqlCreateIndexes();
		$Queries['main'][] = "";
		
        $Queries['main'][] = $Comm . '--- grants --- dm::' .$Url. ' ----------------';
		$Queries['main'][] = $this->sqlCreateGrants();
		$Queries['main'][] = "";
		
        $Queries['init'][] = $Comm . '--- init --- dm::' .$Url. ' -----------------';
		$Queries['init'][] = $this->sqlCreateInit();
		$Queries['init'][] = "";
		
        $Queries['fk'][] = $Comm . '--- fk --- dm::' .$Url. ' -------------------';
		$Queries['fk'][] = $this->sqlCreateFks($this);
		$Queries['fk'][] = "";
		
        return $Queries;
    }

    // --- --- --- --- --- --- --- ---
    /**
     * @retrun string - трансформированное имя
     *     удобно когда образзуются длинные sql имена индексов, счётчиков, ограничений и тд.
     */
    private function transName($name){
        // 1.
        return $name;
        
        // 2.
        //return 'cm'.md5($name);
        
        // 3.
        //$Prefix = \Cmatrix\Db\Kernel::get()->CurConfig->getValue('prefix',null);
        //$Prefix = $Prefix ? $Prefix : 'cm';
        //return $Prefix .'_'. md5($name);
    }

    // --- --- --- --- ---
    /**
     * @return array - массив sql кода для создания Sequences
     */
    private function sqlSequences(){
        $PropCodes = array_keys(array_filter($this->Datamodel->Props,function($prop){
            return $prop['default'] === '::counter::';
        }));
        
        return array_map(function($propCode){
            $SeqName = $this->sqlSeqName($propCode);
			$Arr[] = 'DROP SEQUENCE IF EXISTS '. $SeqName .' CASCADE;';
			$Arr[] = 'CREATE SEQUENCE '. $SeqName .';';
			return $Arr;
        },$PropCodes);
    }
    
    // --- --- --- --- ---
    /**
     * @return array - массив sql кода для создания Table
     */
    private function sqlTable(){
        $_prop = function($index,$prop){
            $Arr = [];
            $Arr[] = ($index ? ',' : null).$this->sqlPropName($prop['code']);
            $Arr[] = $this->ConnectProvider->getPropType($prop);
            $Arr[] = $prop['default'] ? $this->ConnectProvider->getPropDef($prop,$this) : null;
            $Arr[] = $prop['nn'] === true ? $this->ConnectProvider->getSqlNotNull() : null;
            return implode(' ',array_filter($Arr,function($value){ return !!$value; }));
        };
        
        $_inherits = function(){
            if($this->Datamodel->Parent){
                $ParentStructureProvider = new self($this->Datamodel->Parent, $this->ConnectProvider);
                $ParentTableName = $ParentStructureProvider->sqlTableName();
                return ') INHERITS (' .$ParentTableName. ');';
            }
            else return ');';
        };
        
        $TableName = $this->sqlTableName();
        
        $Arr = [];
        $Arr[] = 'DROP TABLE IF EXISTS '. $TableName .' CASCADE;';
        $Arr[] = 'CREATE TABLE '. $TableName .'(';
        $Arr[] = array_map(function($index,$prop) use($_prop){ return $_prop($index,$prop); },range(0, count($this->Datamodel->Props)-1),$this->Datamodel->Props);
        $Arr[] = $_inherits();
        return $Arr;
    }    

    // --- --- --- --- ---
    /**
     * @return array - массив sql кода для создания PrimaryKeys
     */
    private function sqlCreatePks(){
        $PropCodes = array_keys(array_filter($this->Datamodel->Props,function($prop){
            return !!$prop['pk'];
        }));
        
        $TableName = $this->sqlTableName();
        $PkName = $this->sqlPkName($PropCodes);
        
        $Arr = [];
        $Arr[] = 'ALTER TABLE ' .$TableName. ' DROP CONSTRAINT IF EXISTS ' .$PkName. ' CASCADE;';
        $Arr[] = 'ALTER TABLE ' .$TableName. ' ADD CONSTRAINT ' .$PkName. ' PRIMARY KEY (' .implode(',',$PropCodes). ');';
        return $Arr;
    }
    
    // --- --- --- --- ---
    private function sqlCreateUniques(){
        return $this->sqlCreateIndexes(true);
    }

    // --- --- --- --- ---
    private function sqlCreateIndexes($isUnique=false){
        // подготовка массива групп индексных свойств
        $_indexes = function() use($isUnique){
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
            //dump($props);
            
            // --- 1. кол-во свойств
            $N = count($props);
            
            // --- 2. перебор возможных вариантов
            // кол-во всех вариантов - это 2 в степени, равном кол-ву свойств
            $Ret = [];
            for($i=1; $i<pow(2,$N); $i++){
                // --- 2.1. получить бинарную строку
                //    например: 0010110 - нужно построить условие для свойств с индексами 2,3,5
                
                //   здесь форматирование строки для дополнения лидирующих нулей: %'.05s
                //     - .0 - дополнить нулями
                //     - 5  - общая длина 5 символов
                //     - s  - вовод строчных символов
                $S = str_split(sprintf("%'.0".$N."s",decbin($i)));
                //dump(sprintf("%'.0".$N."s",decbin($i)));
                //dump($S);
                
                // --- 2.2. пометить неиспользуемые варианты (nn=true) и 
                //    например: строка 01, это значить, что обработать нужно свойство с индексом 1,
                //              но свойство с индексом 0 имеет свойство nn=true, что не даёт возможность его игнорировать,
                //              таким образом этот вариант не должен быть учтён, пометим вариант как -1
                $Variants = array_map(function($val,$ind) use($props){
                    if($val === '0' && $props[$ind]['nn']) return -1;
                    return $val;
                },$S,array_keys($S));
                
                // --- 2.3. нужно удалить неиспользуемые варианты (-1)
                $Fl = array_reduce($Variants,function($carry, $item){
                    $carry = $carry && ($item == -1 ? false : true);
                    return $carry;
                },true);
                
                // --- 2.3. если условый нет, продолжить
                if(!$Fl) continue;
                
                // --- 2.4. Создание массива условий
                $Props = [];
                $Rules = [];
                $Arr = array_map(function($var,$index) use($props,&$Props,&$Rules){
                    if($props[$index]['nn'] || (!$props[$index]['nn'] && $var == '1')) $Props[] = $props[$index]['code'];
                    
                    if(!$props[$index]['nn'] && $var == '0') $Rules[] = $props[$index]['code'] .' IS NULL';
                    if(!$props[$index]['nn'] && $var == '1') $Rules[] = $props[$index]['code'] .' IS NOT NULL';
                },$Variants,array_keys($Variants));
                
                if(array_key_exists('active',$this->Datamodel->Props)) $Rules[] = 'active IS NOT NULl';
                
                $Ret[] = [
                    'props' => $Props,
                    'rules' => $Rules
                ];
            }
            return $Ret;
        };
        
        // --- --- --- ---
        $TableName = $this->sqlTableName();
        
        $Arr = array_map(function($group) use($isUnique,$TableName,$_inherit){
            return array_map(function($val) use($isUnique,$TableName,$_inherit){
                $Props = $val['props'];
                $Rules = $val['rules'];
                
                $IndexName = $this->sqlIndexName($Props,$isUnique);
                
                $Arr = [];
                $Arr[] = 'DROP INDEX IF EXISTS ' .$IndexName. ' CASCADE;';
                $Arr[] = 'CREATE' .($isUnique ? ' UNIQUE ' : ' '). 'INDEX ' .$IndexName. ' ON ' .$TableName. ' USING btree (' .implode(',',$Props). ')' .
                    (count($Rules) ? ' WHERE ' .implode(' AND ', $Rules) : null). ';';
                return $Arr;
            },$_inherit($group));
        },$_indexes());
        
        return $Arr;
    }
    
    // --- --- --- --- ---
    private function sqlCreateGrants(){
        $TableName = $this->sqlTableName();
        
		return [
			'GRANT SELECT ON '. $TableName .' TO PUBLIC;',
			'GRANT REFERENCES ON '. $TableName .' TO PUBLIC;'
		];
    }

    // --- --- --- --- ---
    private function sqlCreateFks(){
        $PropCodes = array_keys(array_filter($this->Datamodel->Props,function($prop){
            return !!$prop['association'];
        }));
        if(!count($PropCodes)) return;
        
        $TableName = $this->sqlTablename();
        
        $_to = function($propCode){
            $Prop = $this->Datamodel->getProp($propCode);
            $DstEntity = $Prop['association']['entity'];
            $DstPropCode = $Prop['association']['prop'];
            
            $DstDatamodel = cm\Ide\Datamodel::instance($DstEntity);
            
            $DstProvider = new self($DstDatamodel,$this->ConnectProvider);
            $DstTableName = $DstProvider->sqlTableName();
            $DstPropName = $DstProvider->sqlPropName($DstPropCode);
            
            return $DstTableName .'(' . $DstPropName .')';
        };
        
		return array_map(function($propCode) use($TableName,$_to){
            $PropName = $this->sqlPropName($propCode);
            $FkName = $this->sqlFkName($propCode);
            
            $Arr = [];
            $Arr[] = 'ALTER TABLE ' .$TableName. ' DROP CONSTRAINT IF EXISTS ' .$FkName. ' CASCADE;';
            $Arr[] = 'ALTER TABLE '. $TableName .' ADD CONSTRAINT '. $FkName .' FOREIGN KEY ('. $PropName .') REFERENCES '. $_to($propCode) .';';
            return $Arr;
        },$PropCodes);
    }
    
    // --- --- --- --- ---
    private function sqlCreateInit(){
        $TableName = $this->sqlTablename();
        $Init = $this->Datamodel->Json['init'];
        
        if(!is_array($Init)) return;
        
        return array_map(function($init) use($TableName){
            $init['session_id'] = 1;
            
            $Props = array_map(function($propCode){ return $this->Datamodel->getProp($propCode); },array_keys($init));
            $Props = array_combine(array_keys($init),$Props);
            //dump($Props);
            
            $PropCodes = array_map(function($prop){ return $prop['code']; },$Props);
            $Values = array_values($init);
            
            //dump($PropCodes);
            //dump($Values);
			
			$Values = array_map(function($code,$value) use($Props){
                return $this->ConnectProvider->sqlValue($Props[$code],$value);
			},$PropCodes,$Values);
            
			return 'INSERT INTO '. $TableName .'('. implode(',',$PropCodes) .') VALUES('. implode(',',$Values) .');';
        },$Init);
    }

    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    /**
     * @return string - sql name of table
     */
    public function sqlTableName(){
        return $this->Prefix . str_replace('/','_',ltrim($this->Datamodel->Json['code'],'/'));
    }

    // --- --- --- --- ---
    public function sqlPropName($propCode){
        return $this->Datamodel->getProp($propCode)['code'];
    }
    
    // --- --- --- --- ---
    /**
     * @return string - sql name of sequence
     */
    public function sqlSeqName($propCode){
        $TableName = $this->sqlTableName();
        $PropName = $this->sqlPropName($propCode);
        
        return $this->transName(strtolower($TableName .'__seq__'. $PropName));
    }
    
    // --- --- --- --- ---
    /**
     * @return string - sql name of primary key
     */
    public function sqlPkName(array $propCodes){
        $TableName = $this->sqlTableName();
        
        $_name = function() use($propCodes){
            return implode('_',array_map(function($code){
                return $this->sqlPropName($code);
            },$propCodes));
        };
        
        return $this->transName(strtolower($TableName .'__pk__'. $_name()));
    }
    
    // --- --- --- --- ---
    /**
     * @return string - sql name of index key
     */
    public function sqlIndexName(array $propCodes,$isUnique){
        $TableName = $this->sqlTableName();
        
        $_name = function() use($propCodes){
            return implode('_',array_map(function($code){
                return $this->sqlPropName($code);
            },$propCodes));
        };
        
        $pref = $isUnique ? 'uniq' : 'index';
        
        return $this->transName(strtolower($TableName .'__'. $pref .'__'. $_name()));
    }
    
    // --- --- --- --- ---
    /**
     * @return string - sql name of foreign key
     */
    public function sqlFkName($propCode){
        $TableName = $this->sqlTableName();
        $PropName = $this->sqlPropName($propCode);
        
        return $this->transName(strtolower($TableName .'__fk__'. $PropName));
    }
}
?>