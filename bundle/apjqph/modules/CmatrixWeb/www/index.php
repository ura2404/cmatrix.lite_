<?php
require_once '../../../defs.php';
require_once '../../../common.php';

\Cmatrix\App::instance();

// --- --- --- --- ----
\CmatrixWeb\Controller::add('twig','/CmatrixWeb/Twig');
//dump(\CmatrixWeb\Controller::$CONTROLLERS);
//die();

// --- --- --- --- ----
\CmatrixWeb\Model::add('P404','/CmatrixWeb/P404');
\CmatrixWeb\Model::add('home','/CmatrixWeb/Home');
\CmatrixWeb\Model::add('login','/CmatrixWeb/Login');
\CmatrixWeb\Model::add('admin','/CmatrixWeb/Admin');
\CmatrixWeb\Model::add('adminTables','/CmatrixWeb/AdminTables');
\CmatrixWeb\Model::add('adminDictionaries','/CmatrixWeb/AdminDictionaries');
//dump(\CmatrixWeb\Model::$MODELS);
//die();

// --- --- --- --- ----
\CmatrixWeb\Template::add('404','/CmatrixWeb/404.twig');
\CmatrixWeb\Template::add('home','/CmatrixWeb/home.twig');
\CmatrixWeb\Template::add('login','/CmatrixWeb/login.twig');
\CmatrixWeb\Template::add('admin','/CmatrixWeb/admin.twig');
\CmatrixWeb\Template::add('adminTables','/CmatrixWeb/adminTables.twig');
\CmatrixWeb\Template::add('adminDictionaries','/CmatrixWeb/adminDictionaries.twig');
//dump(\CmatrixWeb\Template::$TEMPLATES);
//die();

// --- --- --- --- ----
\CmatrixWeb\Router::add('404',[
    'template' => '404',
    'model' => 'P404',
    'controller' => 'twig'
]);

\CmatrixWeb\Router::add(['/','home'],[
    'template' => 'home',
    'model' => 'home',
    'controller' => 'twig'
]);

\CmatrixWeb\Router::add('login',[
    'template' => 'login',
    'model' => 'login',
    'controller' => 'twig'
]);

\CmatrixWeb\Router::add(['/^admin$/','lk'],[
    'template' => 'admin',
    'model' => 'admin',
    'controller' => 'twig'
]);

\CmatrixWeb\Router::add('admin/tables',[
    'template' => 'adminTables',
    'model' => 'adminTables',
    'controller' => 'twig'
]);

\CmatrixWeb\Router::add('admin/dictionaries',[
    'template' => 'adminDictionaries',
    'model' => 'adminDictionaries',
    'controller' => 'twig'
]);

// --- --- --- --- ----
//echo \Cmatrix\App::instance()->Webpage->Html;
echo \CmatrixWeb\Page::instance()->Html;

?>