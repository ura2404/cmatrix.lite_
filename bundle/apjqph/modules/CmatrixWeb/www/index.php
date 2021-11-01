<?php
require_once '../../../defs.php';
require_once '../../../common.php';

\Cmatrix\App::instance();

// --- --- --- --- ----
\CmatrixWeb\Controller::add('Twig','/CmatrixWeb/Twig');
//dump(\CmatrixWeb\Controller::$CONTROLLERS);
//die();

// --- --- --- --- ----
\CmatrixWeb\Model::add('P404','/CmatrixWeb/P404');
\CmatrixWeb\Model::add('Home','/CmatrixWeb/Home');
\CmatrixWeb\Model::add('Login','/CmatrixWeb/Login');
\CmatrixWeb\Model::add('Admin','/CmatrixWeb/Admin');
//dump(\CmatrixWeb\Model::$MODELS);
//die();

// --- --- --- --- ----
\CmatrixWeb\Template::add('404','/CmatrixWeb/404.twig');
\CmatrixWeb\Template::add('Home','/CmatrixWeb/home.twig');
\CmatrixWeb\Template::add('Login','/CmatrixWeb/login.twig');
\CmatrixWeb\Template::add('Admin','/CmatrixWeb/admin.twig');
//dump(\CmatrixWeb\Template::$TEMPLATES);
//die();

// --- --- --- --- ----
\CmatrixWeb\Router::add('404',[
    'template' => '404',
    'model' => 'P404',
    'controller' => 'Twig'
]);

\CmatrixWeb\Router::add(['/','home'],[
    'template' => 'Home',
    'model' => 'Home',
    'controller' => 'Twig'
]);

\CmatrixWeb\Router::add('login',[
    'template' => 'Login',
    'model' => 'Login',
    'controller' => 'Twig'
]);

\CmatrixWeb\Router::add('/^admin/',[
    'template' => 'Admin',
    'model' => 'Admin',
    'controller' => 'Twig'
]);

// --- --- --- --- ----
//echo \Cmatrix\App::instance()->Webpage->Html;
echo \CmatrixWeb\Page::instance()->Html;

?>