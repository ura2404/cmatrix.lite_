<?php
namespace CmatrixDb\Connect;
use \CmatrixDb as db;

interface iDatabase{
    public function query($query);
    public function exec($query);
}
?>