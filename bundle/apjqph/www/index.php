<?php
require_once '../defs.php';
require_once '../common.php';

// --- --- --- --- ----
\Cmatrix\Controllers::add('Twig',\Cmatrix\Controllers\Twig::get());

// --- --- --- --- ----
\Cmatrix\Routers::add('/',[
    'template' => '/Cmatrix/templates/home.twig',
    'model' => new \Cmatrix\Models\Home,
    'controller' => 'Twig'
]);

\Cmatrix\Routers::add('login',[
    'template' => '/Cmatrix/templates/login',
    'model' => new \Cmatrix\Models\Login,
    'controller' => 'Twig'
]);

echo \Cmatrix\App::instance()->Webpage->Html;
?>