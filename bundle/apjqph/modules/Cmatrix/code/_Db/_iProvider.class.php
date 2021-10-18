<?php
namespace Cmatrix\Db;

interface iProvider{
    public function getType();
    public function getCommSymbol();
    public function getPropType($prop);
    public function getPropDef($prop,\Cmatrix\Structure\iProvider $provider);
    public function getSqlNextSequence($prop);
    public function getSqlNow();
    public function getSqlHid();
}