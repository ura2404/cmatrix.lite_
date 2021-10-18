<?php
namespace Cmatrix\Exception;
use \Cmatrix as cm;

class Property extends cm\Exception{

    // --- --- --- --- --- --- ---
    public function __construct($ob,$propName) {
        $Message = 'CmatrixError: class [' .get_class($ob). '] property [' .$propName. '] is not defined';
        parent::__construct($Message);
	}
}
?>