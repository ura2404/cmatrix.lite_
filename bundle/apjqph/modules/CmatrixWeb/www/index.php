<?php
require_once '../../../defs.php';
require_once '../../../common.php';

\Cmatrix\App::instance();

// --- --- --- --- ----
\CmatrixWeb\Controller::add('Twig',\CmatrixWeb\Controller\Twig::get());

// --- --- --- --- ----
\CmatrixWeb\Router::add('/',[
    'template' => '/CmatrixWeb/templates/home.twig',
    'model' => new \CmatrixWeb\Models\Home,
    'controller' => 'Twig'
]);

\CmatrixWeb\Router::add('login',[
    'template' => '/CmatrixWeb/templates/login.twig',
    'model' => new \CmatrixWeb\Models\Login,
    'controller' => 'Twig'
]);

// --- --- --- --- ----
//echo \Cmatrix\App::instance()->Webpage->Html;
echo \CmatrixWeb\Page::instance()->Html;

?>