<?php
namespace Cmatrix\Structure\Providers;
use \Cmatrix as cm;
use \Cmatrix\Structure as st;

class Pgsql extends st\Provider implements st\iProvider{
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
		
        //dump($Queries);
        
        return $Queries;
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
     * @return array - ???????????? sql ???????? ?????? ???????????????? Sequences
     */
    
    private function sqlSequences(){
        $PropCodes = array_keys(array_filter($this->Datamodel->Props,function($prop){
            return $prop['default'] === '::counter::';
        }));
        
        return array_map(function($code){
            $SeqName = $this->sqlSeqName($code);
			$Arr[] = 'DROP SEQUENCE IF EXISTS '. $SeqName .' CASCADE;';
			$Arr[] = 'CREATE SEQUENCE '. $SeqName .';';
			return $Arr;
        },$PropCodes);
    }

    // --- --- --- --- ---
    /**
     * @return array - ???????????? sql ???????? ?????? ???????????????? Table
     */
    private function sqlTable(){
        $_prop = function($index,$prop){
            $PropName = $this->sqlPropName($prop['code']);
            $PropType = $this->Provider->getPropType($prop);
            
            $Arr = [];
            $Arr[] = ($index ? ',' : null).$this->sqlPropName($prop['code']);
            $Arr[] = $this->Provider->getPropType($prop);
            $Arr[] = $prop['default'] ? $this->Provider->getPropDef($prop,$this) : null;
            $Arr[] = $prop['nn'] === true ? $this->Provider->getSqlNotNull() : null;
            return implode(' ',array_filter($Arr,function($value){ return !!$value; }));
        };
        
        $_inherits = function(){
            if($this->Datamodel->Parent){
                $ParentProvider = st\Provider::instance($this->Datamodel->Parent->Url,$this->Provider);
                $ParentTableName = $ParentProvider->sqlTableName();
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
     * @return array - ???????????? sql ???????? ?????? ???????????????? PrimaryKeys
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
        // ???????????????????? ?????????????? ?????????? ?????????????????? ??????????????
        $_indexes = function() use($isUnique){
            if($isUnique) return $this->Datamodel->Uniques;
            
            $Indexes = $this->Datamodel->Indexes;
            
            // ???????????????? ?????????????????? ????????
            $Association = $this->Datamodel->Association;
            if(count($Association)) foreach($Association as $ass) $Indexes[] = [$ass];
            //dump($Indexes);die();
            
            return $Indexes;
        };
        
        // ???????????????????????? ?????????????? ?????? ?????????????????? ???????????????????? ??????????????
        // ?????????? ?????? pgsql ???????????? ?? ?????????????????????????? ???????????????????? null ??????????, ???????????????? ?? ???????????? ?? ???? null ????????????.
        $_inherit = function($props){
            //dump($props);
            
            // --- 1. ??????-???? ??????????????
            $N = count($props);
            
            // --- 2. ?????????????? ?????????????????? ??????????????????
            // ??????-???? ???????? ?????????????????? - ?????? 2 ?? ??????????????, ???????????? ??????-???? ??????????????
            $Ret = [];
            for($i=1; $i<pow(2,$N); $i++){
                // --- 2.1. ???????????????? ???????????????? ????????????
                //    ????????????????: 0010110 - ?????????? ?????????????????? ?????????????? ?????? ?????????????? ?? ?????????????????? 2,3,5
                
                //   ?????????? ???????????????????????????? ???????????? ?????? ???????????????????? ???????????????????? ??????????: %'.05s
                //     - .0 - ?????????????????? ????????????
                //     - 5  - ?????????? ?????????? 5 ????????????????
                //     - s  - ?????????? ???????????????? ????????????????
                $S = str_split(sprintf("%'.0".$N."s",decbin($i)));
                //dump(sprintf("%'.0".$N."s",decbin($i)));
                //dump($S);
                
                // --- 2.2. ???????????????? ???????????????????????????? ???????????????? (nn=true) ?? 
                //    ????????????????: ???????????? 01, ?????? ??????????????, ?????? ???????????????????? ?????????? ???????????????? ?? ???????????????? 1,
                //              ???? ???????????????? ?? ???????????????? 0 ?????????? ???????????????? nn=true, ?????? ???? ???????? ?????????????????????? ?????? ????????????????????????,
                //              ?????????? ?????????????? ???????? ?????????????? ???? ???????????? ???????? ??????????, ?????????????? ?????????????? ?????? -1
                $Variants = array_map(function($val,$ind) use($props){
                    if($val === '0' && $props[$ind]['nn']) return -1;
                    return $val;
                },$S,array_keys($S));
                
                // --- 2.3. ?????????? ?????????????? ???????????????????????????? ???????????????? (-1)
                $Fl = array_reduce($Variants,function($carry, $item){
                    $carry = $carry && ($item == -1 ? false : true);
                    return $carry;
                },true);
                
                // --- 2.3. ???????? ?????????????? ??????, ????????????????????
                if(!$Fl) continue;
                
                // --- 2.4. ???????????????? ?????????????? ??????????????
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
            
            $DstProvider = st\Provider::instance($DstEntity,$this->Provider);
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
            $Props = array_map(function($propCode){ return $this->Datamodel->getProp($propCode); },array_keys($init));
            $PropCodes = array_map(function($prop){ return $prop['code']; },$Props);
            $PropTypes = array_map(function($prop){ return $prop['type']; },$Props);
            $Values = array_values($init);
            
			$PropCodes[] = 'session_id';
			$PropTypes[] = '::id::';
			$Values[] = '1';
			
			$Values = array_map(function($type,$value){
                return $this->Provider->sqlValue($type,$value);
			},$PropTypes,$Values);
            
			return 'INSERT INTO '. $TableName .'('. implode(',',$PropCodes) .') VALUES('. implode(',',$Values) .');';
        },$Init);
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    /**
     * @return string - sql name of table
     */
    public function sqlSeqName($propCode){
        $TableName = $this->sqlTableName();
        $PropName = $this->sqlPropName($propCode);
        
        return $this->Provider->transName(strtolower($TableName .'__seq__'. $PropName));
    }
    
    // --- --- --- --- ---
    /**
     * @return string - sql name of table
     */
    public function sqlTableName(){
        return $this->Prefix . str_replace('/','_',ltrim($this->Datamodel->Json['code'],'/'));
    }

    // --- --- --- --- ---
    /**
     * @return string - sql name of field
     */
    public function sqlPropName($propCode){
        return $this->Datamodel->getProp($propCode)['code'];
    }
    
    // --- --- --- --- ---
    /**
     * @return string - sql type of field
     */
    public function sqlPropType($propCode){
        $Type = $prop['type'];
        
        if($Type === '::id::')       return 'BIGINT';
        elseif($Type === '::ip::')   return 'VARCHAR(45)'; // 15 - ipv4, 45 - ipv6
        elseif($Type === '::hid::')  return 'VARCHAR(32)';
        elseif($Type === '::pass::') return 'VARCHAR(255)';
        elseif($Type === 'string')   return 'VARCHAR' .(isset($prop['length']) ? '('. $prop['length'] .')' : null);
        else return strtoupper($Type);
        
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
        
        return $this->Provider->transName(strtolower($TableName .'__pk__'. $_name()));
    }
    
    // --- --- --- --- ---
    /**
     * @return string - sql name of foreign key
     */
    public function sqlFkName($propCode){
        $TableName = $this->sqlTableName();
        $PropName = $this->sqlPropName($propCode);
        
        return $this->Provider->transName(strtolower($TableName .'__fk__'. $PropName));
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
        
        return $this->Provider->transName(strtolower($TableName .'__'. $pref .'__'. $_name()));
    }
}
?>