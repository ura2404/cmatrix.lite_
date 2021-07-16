<?php
namespace Cmatrix\Db;
use \Cmatrix as cm;

class Entity extends cm\Db{
    public
        $Id,
        $Hid,
        $Active,
        $Deleted,
        $Hidden,
        $SessionId,
        $CreateTs,
        $UpdateTs;
    
    // --- --- --- --- ---
    function __construct($id=null){
        parent::__construct($id);
    }
}
?>