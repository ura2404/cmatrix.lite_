<?php
namespace CmatrixDb;
use \Cmatrix as cm;
use \Cmatrix\Exception as ex;

class Cql{
    private $Datamodel;
    
    // --- --- --- --- ---
    function __construct(cm\Ide\iDatamodel $datamodel,$cond){
        $this->Datamodel = $datamodel;
        $this->Cond = $cond;
        $this->StructureProvider = $this->setProvider();
    }
    
}
?>