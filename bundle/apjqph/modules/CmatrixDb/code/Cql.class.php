<?php
namespace CmatrixDb;
use \Cmatrix as cm;
use \Cmatrix\Exception as ex;

class Cql{
    private $Datamodel;
    private $StructureProvider;
    
    private $Cond;
    private $Queries = [];
    private $Tables  = [];
    private $Props   = [];
    private $Agg     = [];
    private $Rules   = [];
    private $Values  = [];
    private $Orders  = [];
    private $Limit   = [100,0];

    // --- --- --- --- ---
    /**
     * @param $cond - тип запроса select|insert|update|delete
     */
    function __construct(cm\Ide\iDatamodel $datamodel,$cond){
        $this->Datamodel = $datamodel;
        $this->Cond = $cond;
        $this->StructureProvider = $this->setProvider();
    }
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Query' : return $this->getMyQuery();
            default : throw new ex\Property($this,$name);
        }
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    private function setProvider(){
        $ProviderName = \Cmatrix\Hash::getFile(CM_TOP.'/config.json')->getValue('db/type');
        return Structure\Datamodel\Provider::instance($ProviderName,$this->Datamodel);
    }
    
    // --- --- --- --- ---
    private function getMyQuery(){
        switch($this->Cond){
            case 'select' : return $this->getMySelectQuery();
            case 'insert' : return $this->getMyInsertQuery();
            case 'update' : return $this->getMyUpdateQuery();
            case 'delete' : return $this->getMyDeleteQuery();
        }
        
    }
    
    // --- --- --- --- ---
    private function getMySelectQuery(){
        $_props = function(){
            $_agg = function($code){
                return $this->StructureProvider->ConnectProvider->getSqlAgg($code);
            };
            
            return array_map(function($prop) use($_agg){
                return is_array($prop) ? $_agg($prop[0]) .' AS '. $prop[1] : $_agg($prop);
            },$this->Props);
        };
        
        $_rules = function(){
            $Props = $this->Datamodel->Props;
            $Arr = array_map(function($code,$value) use($Props){
                return $this->StructureProvider->ConnectProvider->sqlPhrase($Props[$code],$value);
            },array_keys($this->Rules),array_values($this->Rules));
            
            return 'WHERE '. implode(' AND ',$Arr);
        };
        
        $_orders = function(){
            $Orders = array_map(function($code,$value){
                return $code .' '. ($value === 'a' ? 'ASC' : 'DESC');
            },array_keys($this->Orders),array_values($this->Orders));
            
            return 'ORDER BY ' . implode(',',$Orders);
        };
        
        $_limit = function(){
            $Limit = $this->Limit;
            return 'LIMIT '. $Limit[0] .' OFFSET '. $Limit[1];
        };
        
        $Queries = [];
        $Queries[] = 'SELECT ' . ($this->Props ? implode(',',$_props()) : '*');
        $Queries[] = 'FROM '. $this->StructureProvider->sqlTableName();
        
        $Query = $this->Rules ? $_rules() : null;
        $Queries[] = $Query;        
        
        $Query = $this->Orders ? $_orders() : null;
        $Queries[] = $Query;
        
        $Query = $this->Limit ? $_limit() : null;
        $Queries[] = $Query;
        
        $Queries = array_filter($Queries,function($value){ return !!$value; });
        //dump($Queries);die();
        
        return implode(' ',$Queries);
    }
    
    // --- --- --- --- ---
    private function getMyInsertQuery(){
        $_props = function(){
            return array_map(function($value){
                return $value;
            },array_keys($this->Values));
        };
        
        $_values = function(){
            return array_map(function($code,$value){
                return $this->StructureProvider->ConnectProvider->sqlValue($this->Datamodel->Props[$code],$value);
            },array_keys($this->Values),array_values($this->Values));
        };
        
        $Queries = [];
        $Queries[] = 'INSERT INTO ' . $this->StructureProvider->sqlTableName();
        $Queries[] = '(' . implode(',',$_props()) . ')';
        $Queries[] = 'VALUES (' . implode(',',$_values()) . ')';
        
        return implode(' ',$Queries);
    }

    // --- --- --- --- ---
    private function getMyUpdateQuery(){
        if(!$this->Rules) return;
        
        $_values = function(){
            return array_map(function($code,$value){
                return $this->StructureProvider->ConnectProvider->sqlPhrase($this->Datamodel->Props[$code],$value,'=',false);
            },array_keys($this->Values),array_values($this->Values));
        };

        $_rules = function(){
            return array_map(function($code,$value){
                return $this->StructureProvider->ConnectProvider->sqlPhrase($this->Datamodel->Props[$code],$value);
            },array_keys($this->Rules),array_values($this->Rules));
        };
        
        $Queries = [];
        $Queries[] = 'UPDATE ' . $this->StructureProvider->sqlTableName();
        
        if($this->Values) $Queries[] = 'SET ' . implode(',',$_values());
        if($this->Rules) $Queries[] = 'WHERE ' . implode(',',$_rules());
        
        return implode(' ',$Queries);
    }
    
    // --- --- --- --- ---
    private function getMyDeleteQuery(){
        if(!$this->Rules) return;
        
        $_rules = function(){
            $Props = $this->Datamodel->Props;
            return array_map(function($code,$value) use($Props){
                return $this->StructureProvider->ConnectProvider->sqlPhrase($Props[$code],$value);
            },array_keys($this->Rules),array_values($this->Rules));
        };
        
        $Queries = [];
        $Queries[] = 'DELETE FROM ' . $this->StructureProvider->sqlTableName();
        $Queries[] = 'WHERE ' . implode(' AND ',$_rules());
        
        return implode(' ',$Queries) .';';
    }
    
    private function checkProp($code){
        if(is_array($code)) $this->Datamodel->getProp($code[0]);
        elseif(strpos($code,'::')!==false) $this->Datamodel->getProp(strAfter($code,'::'));
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    public function fun(\Closure $fun){
        $fun($this);
        return $this;
    }
    
    // --- --- --- --- ---
    /**
     * @param string|array $code
     *     -'id'
     *     -'max::id' - с агрегатной функцией
     *     -['max::id','max_value'] - с алиасом
     */
    public function prop($code){
        $this->checkProp($code);
        $this->Props[] = $code;
        return $this;
    }
    
    // --- --- --- --- ---
    public function props(array $props){
        if(!$props) return $this;
        
        array_map(function($code){
            $this->prop($code);
        },$props);
        
        return $this;
    }
    
    // --- --- --- --- ---
    public function value($code,$value){
        $this->checkProp($code);
        $this->Values[$code] = $value;
        return $this;
    }
    
    // --- --- --- --- ---
    public function values(array $values=null){
        if(!$values) return $this;
        
        array_map(function($code,$value){
            $this->value($code,$value);
        },array_keys($values),array_values($values));
        
        return $this;
    }
    
    // --- --- --- --- ---
    public function rule($code,$value){
        $this->checkProp($code);
        $this->Rules[$code] = $value;
        return $this;
    }
    
    // --- --- --- --- ---
    public function rules(array $rules=null){
        if(!$rules) return $this;
        
        array_map(function($code,$value){
            $this->rule($code,$value);
        },array_keys($rules),array_values($rules));
        
        return $this;
    }
    
    // --- --- --- --- ---
    public function order($code,$value){
        $this->checkProp($code);
        $this->Orders[$code] = $value;
        return $this;
    }

    // --- --- --- --- ---
    public function orders(array $orders=null){
        if(!$orders) return $this;
        
        array_map(function($code,$value){
            $this->order($code,$value);
        },array_keys($orders),array_values($orders));
        
        return $this;
    }

    // --- --- --- --- ---
    public function limit(array $limit=null){
        if(!$limit) return $this;
        
        $this->Limit = [
            $limit['count'],
            $limit['page']*$limit['count']
        ];
        
        return $this;
    }

    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    /**
     * @param \Cmatrix\Ide\iDatamodel
     */
    static function select($datamodel){
        if($datamodel instanceof cm\Ide\iDatamodel) $Datamodel = $datamodel;
        else $Datamodel = cm\Ide\Datamodel::instance($datamodel);
        return new self($Datamodel,'select');
    }
    
    // --- --- --- --- ---
    /**
     * @param \Cmatrix\Ide\iDatamodel
     */
    static function insert($datamodel){
        if($datamodel instanceof cm\Ide\iDatamodel) $Datamodel = $datamodel;
        else $Datamodel = cm\Ide\Datamodel::instance($datamodel);
        return new self($Datamodel,'insert');
    }
    
    // --- --- --- --- ---
    /**
     * @param \Cmatrix\Ide\iDatamodel
     */
    static function update($datamodel){
        if($datamodel instanceof cm\Ide\iDatamodel) $Datamodel = $datamodel;
        else $Datamodel = cm\Ide\Datamodel::instance($datamodel);
        return new self($Datamodel,'update');
    }
    
    // --- --- --- --- ---
    /**
     * @param \Cmatrix\Ide\iDatamodel
     */
    static function delete($datamodel){
        if($datamodel instanceof cm\Ide\iDatamodel) $Datamodel = $datamodel;
        else $Datamodel = cm\Ide\Datamodel::instance($datamodel);
        return new self($Datamodel,'delete');
    }
}
?>