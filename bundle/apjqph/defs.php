<?php
define('CM_TOP',realpath(dirname(__FILE__).'/../..'));
define('CM_ROOT',realpath(dirname(__FILE__)));

define('CM_MODE',PHP_SAPI === 'cli' ? 'development' :(isset($_SERVER['CM_MODE']) ? $_SERVER['CM_MODE'] : null));
//define('CM_MODE','production');

if(PHP_SAPI !== 'cli'){
    $Config = json_decode(file_get_contents(CM_TOP.'/config.json'),true);
    if(!$Config || !isset($Config['www']) || !isset($Config['www']['root'])) die('Can-t define "CM_WHOM". Wrong config file!');
    define('CM_WHOME',$_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['SERVER_NAME'] .':'. $_SERVER['SERVER_PORT']. $Config['www']['root']);
}
?>