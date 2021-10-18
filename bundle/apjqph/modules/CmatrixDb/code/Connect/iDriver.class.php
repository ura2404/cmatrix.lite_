<?php
namespace CmatrixDb\Connect;
use \CmatrixDb as db;

interface iDriver{
    public function query($query);
    public function exec($query);
}
?>