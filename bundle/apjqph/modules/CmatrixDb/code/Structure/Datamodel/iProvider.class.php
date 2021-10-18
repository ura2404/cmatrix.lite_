<?php
namespace CmatrixDb\Structure\Datamodel;
use \Cmatrix as cm;
use \CmatrixDb as db;

interface iProvider{
    public function sqlTableName();
    public function sqlPropName($propCode);
    public function sqlSeqName($propCode);
    public function sqlPkName(array $propCodes);
    public function sqlIndexName(array $propCodes,$isUnique);
    public function sqlFkName($propCode);
}
?>