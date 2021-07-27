#!/usr/bin/php
<?php
/**
 * Настроечный скрипт
 */
$ProjectRoot = realpath(dirname(__FILE__) .'/../../..');
$BundleRoot = realpath(dirname(__FILE__) .'/..');
require_once $BundleRoot.'/code/utils.php';

$_config = function() use($ProjectRoot,$BundleRoot){

    $File = $ProjectRoot.'/config.json';

    $_create = function() use($ProjectRoot){
        $Arr =[
            'www' => [
                'root' => '/'.basename($ProjectRoot)
            ]
        ];

        return $Arr;
    };

    $_update = function() use($File){
        $Arr = json_decode(file_get_contents($File),true);
        return $Arr;
    };

    if(!file_exists($File)) $Json = $_create();
    else $Json = $_update();

    file_put_contents($File, json_encode($Json,
        JSON_PRETTY_PRINT             // форматирование пробелами
        | JSON_UNESCAPED_SLASHES      // не экранировать /
        | JSON_UNESCAPED_UNICODE      // не кодировать текст
    ));
};

$_htpasswd = function() use($BundleRoot){
    $Htaccess = $BundleRoot . '/www/.htaccess';
    $Htpasswd = $BundleRoot . '/www/.htpasswd';

    $File = file_get_contents($Htaccess);
    $Arr = explode('AuthUserFile ', $File);
    $Arr2 = explode(PHP_EOL,$Arr[1]);

    $Arr2[0] = $Htpasswd;
    $Arr[1] = implode(PHP_EOL,$Arr2);
    $File = implode('AuthUserFile ', $Arr);

    file_put_contents($Htaccess, $File);
};

$_rewrite = function() use($ProjectRoot,$BundleRoot){
    $_base = function() use($ProjectRoot){
        $File = $ProjectRoot.'/config.json';
        $Arr = json_decode(file_get_contents($File),true);
        return $Arr['www']['root'];
    };

    $Htaccess = $BundleRoot . '/www/.htaccess';
    $File = file_get_contents($Htaccess);
    $Arr = explode('RewriteBase ', $File);
    $Arr2 = explode(PHP_EOL,$Arr[1]);

    $Arr2[0] = $_base();
    $Arr[1] = implode(PHP_EOL,$Arr2);
    $File = implode('RewriteBase ', $Arr);

    file_put_contents($Htaccess, $File);
};

$_config();
$_htpasswd();
$_rewrite();
?>