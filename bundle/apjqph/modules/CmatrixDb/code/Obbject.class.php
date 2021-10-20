<?php
namespace CmatrixDb;
use \Cmatrix as cm;
use \Cmatrix\Exception as ex;
use \CmatrixCore as core;

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
            case 'Id' : return $this->Data['id'];
            case 'IsEmpty' : return $this->getMyIsEmpty();
            case 'IsChanged' : return $this->getMyIsChanged();
            case 'Data' : return $this->Data;
            case 'PrevData' : return $this->getPrevData();
            default : 
                if(!array_key_exists($name,$this->Data)) throw new ex\Property($this,$name);
                return $this->Data[$name];
        }
    }

    // --- --- --- --- ---
    function __set($name,$value){
        if(!array_key_exists($name,$this->Data)) throw new ex\Property($this,$name);
        
        // запоминает только первое изменение
        if(!isset($this->Changed[$name])) {
            $this->Changed[$name] = $this->Data[$name];
            $this->Data[$name] = $value;
        }
        elseif(isset($this->Changed[$name]) && ($this->Changed[$name] != $this->Data[$name])){
            $this->Data[$name] = $value;
        }
        
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
        $this->History = true;
        $this->Active  = true;
        return $this;
    }
    
    // --- --- --- --- ---
    private function getMyIsEmpty(){
        return !count(array_filter($this->Data,function($value){ return !!$value; }));
    }

    // --- --- --- --- ---
    private function getMyIsChanged(){
        return !!count($this->Changed);
    }
    
    // --- --- --- --- ---
    private function values($data){
        array_map(function($code,$value){
            $this->value($code,$value);
        },array_keys($data),array_values($data));
    }
    
    // --- --- --- --- ---
    /**
     * @return array - массив изменённых данных
     */
    private function getChanged(){
        return array_intersect_key($this->Data,$this->Changed);
    }

    // --- --- --- --- ---
    /**
     * @return array массив данных без указанных свойств
     */
    private function getData(...$params){
        return array_diff_key($this->Data,array_flip($params));
    }

    // --- --- --- --- ---
    private function getPrevData(...$params){
        $Data = $this->getData(...$params);
        return array_merge($Data,$this->Changed);
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
    public function value($code,$value){
        $this->$code = $value;
        return $this;
    }
    
    // --- --- --- --- ---
    public function create(array $data=null){
        if($data) $this->values($data);
        
        if(!$this->IsChanged) return;
        
        $this->Queries[] = Cql::insert($this->Datamodel)->values($this->getChanged())->Query;
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
        dump($this->Queries);//die();
        
        $Res = $this->Connect->query($this->Queries);
        dump($Res);//die();
        
        if($Res) array_map(function($code,$value){
            $this->Data[$code] = $value;
        },array_keys($Res[0]),array_values($Res[0]));
        
        return $this->flush();
    }

    // --- --- --- --- ---
    public function copy(){
        if(!$this->Id) throw new ex('Невозможно скопировать несуществующую сущность.');
        return $this->flush();
    }

    // --- --- --- --- ---
    public function update(){
        if(!$this->Id) throw new ex('Невозможно обновить несуществующую сущность.');
        if(!$this->IsChanged) return;
        
        if($this->History){
            $this->Queries[] = Cql::insert($this->Datamodel)
                ->values($this->getPrevData('id'))
                ->value('active',null)
                ->value('hidden',null)
                ->value('deleted',null)
                ->Query;
                
            $this->Queries[] = Cql::update($this->Datamodel)
                ->rule('id',$this->Id)
                ->values($this->getChanged())
                ->value('session_upd_id',core\Session::instance()->Session->id)
                ->value('ts_upd','now')
                ->Query;
        }
        else{
            $this->Queries[] = Cql::update($this->Datamodel)
                ->rule('id',$this->Id)
                ->values($this->getChanged())
                ->value('session_upd_id',core\Session::instance()->Session->id)
                ->value('ts_upd','now')
                ->Query;
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
    public function commit(){
        
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