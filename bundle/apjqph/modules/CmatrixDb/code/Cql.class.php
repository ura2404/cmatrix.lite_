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
    private $Rules   = [];
    private $Values  = [];

    // --- --- --- --- ---
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
        $_rules = function(){
            $Props = $this->Datamodel->Props;
            $Arr = array_map(function($code,$value) use($Props){
                return $this->StructureProvider->ConnectProvider->sqlPhrase($Props[$code],$value);
            },array_keys($this->Rules),array_values($this->Rules));
            
            return 'WHERE '. implode(' AND ',$Arr);
        };
        
        
        $Queries = [];
        
        $Query = 'SELECT ';
        $Query .= $this->Props ? implode(',',$this->Props) : '*';
        $Queries[] = $Query;
        
        $Query = 'FROM '. $this->StructureProvider->sqlTableName();
        $Queries[] = $Query;        
        
        $Query = $this->Rules ? $_rules() : null;
        $Queries[] = $Query;        
        
        $Queries = array_filter($Queries,function($value){ return !!$value; });
        
        return implode(' ',$Queries) .';';
    }
    
    // --- --- --- --- ---
    private function getMyInsertQuery(){
        $Queries = [];
        
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
        
        $Query = 'INSERT INTO ' . $this->StructureProvider->sqlTableName();
        $Queries[] = $Query;
        
        $Query = '(' . implode(',',$_props()) . ')';
        $Queries[] = $Query;
        
        
        $Query = 'VALUES (' . implode(',',$_values()) . ')';
        $Queries[] = $Query;
        
        return implode(' ',$Queries) .';';
    }

    // --- --- --- --- ---
    private function getMyUpdateQuery(){
        if(!$this->Rules) return;
        
        $Queries = [];
        
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
        
        $Query = 'UPDATE ' . $this->StructureProvider->sqlTableName();
        $Queries[] = $Query;
        
        if($this->Values){
            $Query = 'SET ' . implode(',',$_values());
            $Queries[] = $Query;
        }
        
        if($this->Rules){
            $Query = 'WHERE ' . implode(',',$_rules());
            $Queries[] = $Query;
        }
        
        return implode(' ',$Queries) .';';
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
        
        $Query = 'DELETE FROM ' . $this->StructureProvider->sqlTableName();
        $Queries[] = $Query;
        
        $Query = 'WHERE ' . implode(' AND ',$_rules());
        $Queries[] = $Query;
        
        return implode(' ',$Queries) .';';
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    public function fun(\Closure $fun){
        $fun($this);
        return $this;
    }
    
    // --- --- --- --- ---
    public function prop($code){
        $this->Datamodel->getProp($code);
        $this->Props[] = $code;
        return $this;
    }
    
    // --- --- --- --- ---
    public function props(array $props){
        if(!$props) return $this;
        
        $this->Props = array_merge($this->Props,$props);
        return $this;
    }
    
    // --- --- --- --- ---
    public function value($code,$value){
        $this->Datamodel->getProp($code);
        $this->Values[$code] = $value;
        return $this;
    }
    
    // --- --- --- --- ---
    public function values(array $values=null){
        if(!$values) return $this;

        $this->Values = array_merge($this->Values,$values);
        
        return $this;
    }
    
    // --- --- --- --- ---
    public function rule($code,$value){
        $this->Datamodel->getProp($code);
        $this->Rules[$code] = $value;
        return $this;
    }
    
    // --- --- --- --- ---
    public function rules(array $rules=null){
        if(!$rules) return $this;

        $this->Rules = array_merge($this->Rules,$rules);
        
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