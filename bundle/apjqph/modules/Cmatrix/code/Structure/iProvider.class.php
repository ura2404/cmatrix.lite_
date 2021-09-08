<?php
namespace Cmatrix\Structure;

interface iProvider{
    public function sqlSeqName($propCode);
    
    public function sqlTableName();
    
    public function sqlPropName($propCode);
    public function sqlPropType($propCode);
    
    public function sqlPkName(array $propCodes);
    public function sqlFkName($propCode);
    
    public function sqlIndexName(array $propCodes,$isUnique);
}