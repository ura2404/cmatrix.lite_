#!/usr/bin/php
<?php

/**
 * Создатель таблиц
 * 
 * @author ura@urx.su
 * @version 1.0 2016-03-23
 * @version 2.0 2019-03-18
 * @version 3.0 2021-07-21
 */

require_once '../defs.php';
require_once "../common.php";

// для отмены дублирования вывода exceptions
ini_set('display_errors',0);

//\cmKernel\Kernel::reg();

// --- --- --- --- --- --- --- ---
$_version = function(){
    $file = file_get_contents(__FILE__);
    $javadoc = strBefore(strAfter($file,'/**'),'*/');
    $version = strBefore(strRAfter($javadoc,'@version'),"\n");
    return $version;
};

// --- --- --- --- --- --- --- ---
$_help = function($text){
	echo 'Ошибка: '. $text . PHP_EOL;
	print '
Использование: 
  1. php -f dbGenerator.php dbcreate <login> <password>, где
        - login - логин администратора БД
        - password - пароль администратора БД
        
  2. php -f dbGenerator.php <mode> <target[/provider]> <url>, где
        -mode   - режим: script,check,create,update,fkcreate,update,init
        -target - цель: all, dm, ds,
        -provider - mysql, pgsql, default - pgsql
        -url    - url проекта или сущности, например "/Cmatrix" или "/Cmatrix/Session", 
        
    Mode (режим):
        -script - вывод SQL-скриптов
        -check - проверка соответствия объектов DB их описаниям;
        -create - создания в DB таблицы сущности;
        -fkcreate - создания внешних ключей в DB для таблицы сущности;
        -update - обновление в DB таблицы сущности;
        -init - наполнение в DB таблицы сущности начальными данными.
    Target (цель):
        -all - 
        -dm - 
        -ds - 
    
    Provider (провайдер):
        -mysql
        -pgsql
        
    Url
        -all - 
        -module url - url моуля, например /Cmatrix
        -entity url - url сущности, например /CmatrixSession/Sysuser
';
	echo PHP_EOL;
	die();
};

// --- --- --- --- --- --- --- ---
$_script = function($target,$url) use($_help){
    if(!$target) $_help('Не указана цель');    
    if(!$url) $_help('Не указан url');    
    
    $ProviderName = strAfter($target,'/');
    $Target = strBefore($target,'/');
    
    $ProviderName = $ProviderName ? $ProviderName : \Cmatrix\Hash::getFile(CM_TOP.'/config.json')->getValue('db/type');
    
    switch($target){
        //case 'dm' : return \Cmatrix\Structure::instance($url,$Provider)->Script;
        case 'dm' : return \CmatrixDb\Structure\Datamodel::instance($url,$ProviderName)->SqlInitScript;
        
        //case 'datamodel' : return \cmKernel\Structure\Datamodel::get($url)->getScript($Provider,false);
        //case 'datasource' : return \cmKernel\Structure\Datasource::get($url)->getScript($Provider,true);
        default : $_help('Неверная цель');
    }
    /*
    if(strpos($url,'/') === false) return cmKernel\Db\Structure\Module::get($url,$provider)->Creationscript;
    else return cmKernel\Db\Structure\Entity::get($url,$provider)->Creationscript;
    */
};

// --- --- --- --- --- --- --- ---
// --- --- --- --- --- --- --- ---
// --- --- --- --- --- --- --- ---
echo "\n---------------------------------------------\n";
$version = $_version();
echo "DB creator" .($version ? ' v'.$version : null). " by © ura@urx.su\n\n";

$mode = isset($argv[1]) ? $argv[1] : null;
$arg2 = isset($argv[2]) ? $argv[2] : null;
$arg3 = isset($argv[3]) ? $argv[3] : null;

switch($mode){
    case 'script' :
        $target = $arg2;
        $url    = $arg3;
        
        echo "----------------------------------------------------------------------\n";
        echo "-- start -------------------------------------------------------------\n";
        echo "----------------------------------------------------------------------\n";
        echo "\n";
        
        echo $_script($target,$url) . "\n";
        
        echo "\n";
        echo "----------------------------------------------------------------------\n";
        echo "-- end ---------------------------------------------------------------\n";
        echo "----------------------------------------------------------------------\n";
        
        echo "\n";
        echo "\n";
        break;
        
    default: $_help('Не указан или неверный режим');
}

?>