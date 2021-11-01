<?php
namespace CmatrixWeb;
use \CmatrixWeb as web;

interface iController{
    public function render(web\Template $template,web\Model $model);
}
?>