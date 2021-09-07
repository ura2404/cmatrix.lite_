<?php
namespace Cmatrix\Structure\Providers;
use \Cmatrix as cm;
use \Cmatrix\Structure as st;

class Pgsql extends st\Provider{
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
		
/*        
        $Queries['init'][] = $Comm . '--- init --- dm::' .$Url. ' -----------------';
		$Queries['init'][] = $provider->sqlCreateInit($this);
		$Queries['init'][] = "";
		
*/        
        $Queries['fk'][] = $Comm . '--- fk --- dm::' .$Url. ' -------------------';
		$Queries['fk'][] = $this->sqlCreateFks($this);
		$Queries['fk'][] = "";
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
            
			$Name = st\Sequence::instance($this,$prop['code'])->Name;
			
			$Arr[] = 'DROP SEQUENCE IF EXISTS '. $Name .' CASCADE;';
			$Arr[] = 'CREATE SEQUENCE '. $Name .';';
			
			return $Arr;
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
        $Table = st\Table::instance($this);
        
        $Arr[] = 'DROP TABLE IF EXISTS '. $Table->Name .' CASCADE;';
        $Arr[] = 'CREATE TABLE '. $Table->Name .'(';
        $Arr = array_merge($Arr,array_map(function($propName){
            $Arr = [];
            $Prop = st\Prop::instance($this,$propName);
            
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
        $Props = array_keys(array_filter($this->Datamodel->Props,function($prop){
            return !!$prop['pk'];
        }));
        
        if(!count($Props)) return;
        
        $Table = st\Table::instance($this);
		$Pk = st\Pk::instance($this,$Props);
        //dump($Pk);
        
        $Arr = [];
        $Arr[] = 'ALTER TABLE ' .$Table->Name. ' DROP CONSTRAINT IF EXISTS ' .$Pk->Name. ' CASCADE;';
        $Arr[] = 'ALTER TABLE ' .$Table->Name. ' ADD CONSTRAINT ' .$Pk->Name. ' PRIMARY KEY (' .implode(',',$Props). ');';
        
        return $Arr;
    }
    
    // --- --- --- --- ---
    private function sqlCreateUniques(){
        return $this->sqlCreateIndexes(true);
    }

    // --- --- --- --- ---
    private function sqlCreateIndexes($isUnique=false){
        $Table = st\Table::instance($this);
        
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
                
                $Ret[] = [
                    'props' => $Props,
                    'rules' => $Rules
                ];
            }
            return $Ret;
        };

        // --- --- --- ---
        $Arr = array_map(function($group) use($isUnique,$Table,$_inherit){
            return array_map(function($val) use($isUnique,$Table,$_inherit){
                $Props = $val['props'];
                $Rules = $val['rules'];
                
                $Index = st\Index::instance($this,$Props,$isUnique);

                $Arr = [];
                $Arr[] = 'DROP INDEX IF EXISTS ' .$Index->Name. ' CASCADE;';
                $Arr[] = 'CREATE' .($isUnique ? ' UNIQUE ' : ' '). 'INDEX ' .$Index->Name. ' ON ' .$Table->Name. ' USING btree (' .implode(',',$Props). ')' .
                    (count($Rules) ? ' WHERE ' .implode(' AND ', $Rules) : null). ';';
                return $Arr;
            },$_inherit($group));
        },$_indexes());
        
        return $Arr;
    }
    
    // --- --- --- --- ---
    private function sqlCreateGrants(){
        $Table = st\Table::instance($this);
        
		return [
			'GRANT SELECT ON '. $Table->Name .' TO PUBLIC;',
			'GRANT REFERENCES ON '. $Table->Name .' TO PUBLIC;'
		];
    }
    
    // --- --- --- --- ---
    private function sqlCreateFks(){
        $Props = array_keys(array_filter($this->Datamodel->Props,function($prop){
            return !!$prop['association'];
        }));
        
        if(!count($Props)) return;
        
        //
        $_to = function(){
            
        };
        
        $Table = st\Table::instance($this);
		$Fk = st\Fk::instance($this,$Props);
		
		$Arr = array_map(function($prop) use($Table){
            $Arr = [];
    		$Arr[] = 'ALTER TABLE '. $Table->Name .' ADD CONSTRAINT '. $FkName .' FOREIGN KEY ('. $FieldName .') REFERENCES '. $_to($prop) .';';
		    
		},$Props);
        
        dump($Arr);
        
        
        //==============================
        return ;
        $PkProps = array_keys(array_filter($this->Datamodel->Props,function($prop){
            return !!$prop['pk'];
        }));
        
        if(!count($PkProps)) return;
        
        $Table = st\Table::instance($this);
		$Pk = st\Pk::instance($this,$PkProps);
        //dump($Pk);
        
        $Arr = [];
        $Arr[] = 'ALTER TABLE ' .$Table->Name. ' DROP CONSTRAINT IF EXISTS ' .$Pk->Name. ' CASCADE;';
        $Arr[] = 'ALTER TABLE ' .$Table->Name. ' ADD CONSTRAINT ' .$Pk->Name. ' PRIMARY KEY (' .implode(',',$PkProps). ');';
        
        return $Arr;
          //=============
        return;
        $Arr= [];
        $TableName = $this->Datamodel->Tablename; 
        $Props = $this->Datamodel->Props;
        
        $_to = function($prop){
            $Url  = $prop['association']['entity'];
            $Prop = $prop['association']['prop'];
            $Datamodel = kernel\Structure\Datamodel::get($Url);
            $Tablename = $Datamodel->Tablename;
            $FieldName = $Datamodel->getProp($Prop)['code'];
            return $Tablename .'(' . $FieldName .')';
        };
        
		foreach($Props as $code=>$prop){
			if(!$prop['association']) continue;
			$FieldName = $prop['code'];
            //$FkName = strtolower($TableName .'_fk_'. $FieldName);
            $FkName = strtolower($TableName .'_fk_'. md5($FieldName));
			$Arr[] = 'ALTER TABLE '. $TableName .' ADD CONSTRAINT '. $FkName .' FOREIGN KEY ('. $FieldName .') REFERENCES '. $_to($prop) .';';
		}
		
		return $Arr;
    }
}
?>