<?php
namespace Cmatrix\Db;
use \Cmatrix as cm;

class Entity extends cm\Db{
    // props
    public
        $Id,
        $Hid,
        $Active,
        $Deleted,
        $Hidden,
        $SessionId,
        $CreateTs,
        $UpdateTs;
    // props
    
    // --- --- --- --- ---
    function __construct($id=null){
        parent::__construct($id);
    }
}
?>