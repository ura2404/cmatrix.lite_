<?php
namespace Cmatrix\Db\Structure\Provider;
use \Cmatrix as cm;

class Pgsql extends cm\Db\Structure\Provider implements cm\Db\Structure\iProvider{
    
    // --- --- --- --- ---
    private function getDatamodelInitScript(cm\Db\Connect\iProvider $connectProvider, cm\Ide\iDatamodel $datamodel){
        $Queries = [];
        $Comm = $connectProvider->getCommSymbol();
        $Url = $datamodel->Url;
        
        $Queries['main'][] = $Comm . '--- sequence --- dm::' .$Url. ' -------------';
        $Queries['main'][] = $this->sqlSequences($datamodel);
        $Queries['main'][] = "";
        
        return $Queries;
    }
    
    // --- --- --- --- ---
    private function sqlSequences($datamodel){
        $PropCodes = array_keys(array_filter($datamodel->Props,function($prop){
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
    // --- --- --- --- ---
    // --- --- --- --- ---
    public function getInitScript($target, cm\Db\Connect\iProvider $connectProvider, cm\Ide\iDatamodel $datamodel){
        
        switch($target){
            case 'dm' : return $this->getDatamodelInitScript($connectProvider, $datamodel);
        }
    }


}
?>