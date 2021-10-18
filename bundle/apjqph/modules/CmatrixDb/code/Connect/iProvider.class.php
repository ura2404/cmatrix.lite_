<?php
namespace CmatrixDb\Connect;
use \CmatrixDb as db;

interface iProvider{
    public function getType();
    public function getCommSymbol();
    public function getPropType(array $prop);
    public function getPropDef(array $prop, db\Structure\Datamodel\iProvider $provider);
    public function getSqlNextSequence($prop);
    public function getSqlNow();
    public function getSqlHid();
    public function getSqlNotNull();
}
?>