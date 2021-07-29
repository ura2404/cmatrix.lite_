<?php
namespace Cmatrix\Db;

interface iProvider{
    public function getType();
    public function getCommSymbol();
    public function getPropType($prop);
    public function getSqlNextSequence($prop);
    public function getSqlNow();
    public function getSqlHid();
}