<?php
require_once '../defs.php';
require_once '../common.php';

\Cmatrix\Router::add('/',[
    'template' => 'home',
    'model' => 'home'
]);

echo \Cmatrix\App::get()->Html;
?>