<?php
namespace Cmatrix\Db;
use \Cmatrix as cm;

class Sysuser extends Entity{
    public
        $Name,
        $Surname,
        $Midname;
    
    // --- --- --- --- ---
    function __construct($id=null){
        parent::__construct($id);
    }
}
?>