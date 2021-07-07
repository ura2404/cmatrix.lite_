<?php
require_once '../defs.php';
require_once '../common.php';

\Cmatrix\Controller::add('twig',new \Twig_Environment(new \Twig_Loader_Filesystem(CM_ROOT.'/templates/'), [
    'cache' => '/var/tmp',
    'debug' => true,
    'auto_reload' => true
]));

\Cmatrix\Router::add('/',[
    'template' => 'home',
    'model' => 'home',
    'controller' => 'twig'
]);

\Cmatrix\Router::add('login',[
    'template' => 'login',
    'model' => 'login',
    'controller' => 'twig'
]);

echo \Cmatrix\App::instance()->Webpage->Html;
?>