<?php
namespace CmatrixDb;
use \Cmatrix as cm;
use \Cmatrix\Exception as ex;

class Obbject{
    private $Datamodel;
    private $Connect;
    
    /**
     * bool $Active - признак какую запись искать (active is TRUE)
     */
    private $Active = true;
    
    /**
     * bool $Active - признак сохранения истории
     */
    private $History = true;
    
    private $Queries = [];
    private $Data = [];
    private $Changed = [];

    // --- --- --- --- ---
    function __construct(cm\Ide\iDatamodel $datamodel){
        $this->Datamodel = $datamodel;
        $this->Connect = $this->setConnect();
        $this->init();
    }
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'IsEmpty' : return $this->getMyIsEmpty();
            case 'Data' : return $this->Data;
            default : throw new ex\Property($this,$name);
        }
    }

    // --- --- --- --- ---
    function __set($name,$value){
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    private function init(){
        array_map(function($propCode){
            $this->Data[$propCode] = null;
        },array_keys($this->Datamodel->Props));
    }
    
    // --- --- --- --- ---
    private function flush(){
        $this->Queries = [];
        return $this;
    }
    
    // --- --- --- --- ---
    private function getMyIsEmpty(){
        return !count(array_filter($this->Data,function($value){ return !!$value; }));
    }
    
    // --- --- --- --- ---
    private function setValues($data){
        array_map(function($code,$value){
            $this->$code = $value;
        },array_keys($data),array_values($data));
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    public function setConnect($config=null){
        $Config = $config ? $config : \Cmatrix\Hash::getFile(CM_TOP.'/config.json')->getValue('db');
        if(!$Config) throw new ex('Database config is not defined.');
        return Connect::instance($Config);
    }
    
    // --- --- --- --- ---
    public function active($value=true){
        $this->Active = $value;
        return $this;
    }
    
    // --- --- --- --- ---
    public function history($value=true){
        $this->History = $value;
        return $this;
    }
    
    // --- --- --- --- ---
    public function create(array $data=null){
        $this->Queries[] = Cql::insert($this->Datamodel)->values($data)->value('active',$this->Active)->Query;
        //dump($this->Queries);die();
        
        $Res = $this->Connect->exec($this->Queries);
        //dump($Res);
        
        return $this->flush();
    }
    
    // --- --- --- --- ---
    public function get($id=null){
        $this->Queries[] = Cql::select($this->Datamodel)->fun(function($ob) use($id){
            if(is_array($id)) $ob->rules($id);
            else $ob->rule('id',$id);
        })->rule('active',$this->Active)->Query;
        //dump($this->Queries);die();
        
        $Res = $this->Connect->query($this->Queries);
        //dump($Res);
        
        if($Res) array_map(function($code,$value){
            $this->Data[$code] = $value;
        },array_keys($Res[0]),array_values($Res[0]));
        
        return $this->flush();
    }

    // --- --- --- --- ---
    public function copy(array $data){
        return $this->flush();
    }

    // --- --- --- --- ---
    public function update(array $data){
        if($this->History){
            $this->Queries[] = Cql::update($this->Datamodel)->rule('id',$this->Data['id'])->value('active',null)->value('deleted',true)->Query;
        }
        else{
            $this->Queries[] = Cql::update($this->Datamodel)->rule('id',$this->Data['id'])->values($data)->Query;
        }
        //dump($this->Queries);die();
        
        $Res = $this->Connect->exec($this->Queries);
        //dump($Res);
        
        return $this->flush();
    }
    
    // --- --- --- --- ---
    public function delete(){
        if($this->History){
            $this->Queries[] = Cql::update($this->Datamodel)->rule('id',$this->Data['id'])->value('active',null)->value('deleted',true)->Query;
        }
        else{
            $this->Queries[] = Cql::delete($this->Datamodel)->rule('id',$this->Data['id'])->Query;
        }
        //dump($this->Queries);die();
        
        $Res = $this->Connect->exec($this->Queries);
        //dump($Res);
        
        return $this->flush();
    }

    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    /**
     * @param \Cmatrix\Ide\iDatamodel
     */
    static function instance($datamodel){
        if($datamodel instanceof cm\Ide\iDatamodel) $Datamodel = $datamodel;
        else $Datamodel = cm\Ide\Datamodel::instance($datamodel);
        return new self($Datamodel);
    }
}
?>