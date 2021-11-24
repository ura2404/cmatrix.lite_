<?php
require_once '../../../defs.php';
require_once '../../../common.php';

\Cmatrix\App::instance();

// --- --- --- --- ----
\CmatrixWeb\Router::add('404',[
    'template' => '/CmatrixWeb/404.twig',
    'model' => '/CmatrixWeb/P404',
    'controller' => '/CmatrixWeb/Twig'
]);

\CmatrixWeb\Router::add(['/','home'],[
    'template' => '/CmatrixWeb/home.twig',
    'model' => '/CmatrixWeb/Home',
    'controller' => '/CmatrixWeb/Twig'
]);

\CmatrixWeb\Router::add('login',[
    'template' => '/CmatrixWeb/login.twig',
    'model' => '/CmatrixWeb/Login',
    'controller' => '/CmatrixWeb/Twig'
]);

\CmatrixWeb\Router::add(['/^admin$/','lk'],[
    'template' => '/CmatrixWeb/admin.twig',
    'model' => '/CmatrixWeb/Admin',
    'controller' => '/CmatrixWeb/Twig'
]);

\CmatrixWeb\Router::add('admin/tables',[
    'template' => '/CmatrixWeb/adminTables.twig',
    'model' => '/CmatrixWeb/AdminTables',
    'controller' => '/CmatrixWeb/Twig'
]);

\CmatrixWeb\Router::add('admin/data',[
    'template' => '/CmatrixWeb/adminData.twig',
    'model' => '/CmatrixWeb/AdminData',
    'controller' => '/CmatrixWeb/Twig'
]);

\CmatrixWeb\Router::add('admin/tool',[
    'template' => '/CmatrixWeb/adminTool.twig',
    'model' => '/CmatrixWeb/AdminTool',
    'controller' => '/CmatrixWeb/Twig'
]);

\CmatrixWeb\Router::add('/^entity\/.*\/.*(?<!setup)$/',[
    'template' => '/CmatrixWeb/adminDataEntityView.twig',
    'model' => '/CmatrixWeb/AdminDataEntityView',
    'controller' => '/CmatrixWeb/Twig'
]);

\CmatrixWeb\Router::add('/^entity\/.*\/.*\/(setup)$/',[
    'template' => '/CmatrixWeb/adminDataEntitySetup.twig',
    'model' => '/CmatrixWeb/AdminDataEntitySetup',
    'controller' => '/CmatrixWeb/Twig'
]);

\CmatrixWeb\Router::add('/^tilda\/toolSupervisor/',[
    'template' => '/Tilda/toolSupervisor.twig',
    'model' => '/Tilda/ToolSupervisor',
    'controller' => '/CmatrixWeb/Twig'
]);

// --- --- --- --- ----
echo \CmatrixWeb\Page::instance()->Html;
?>