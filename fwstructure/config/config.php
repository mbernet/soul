<?php
define('DEBUG', true);


define('DIR_ROOT', 'soul');
define("BASE_PATH", "http://localhost/".DIR_ROOT);
if(DIR_ROOT == '')
{
	define('APP_PATH', "/app");
}
else
{
	define('APP_PATH', "/".DIR_ROOT."/app");
}
define('FILES_PATH', APP_PATH . '/'. 'files');
define('DS','/');


define('DEFAULT_ROUTE', 'pages/index');


define('MOD_REWRITE', true);

define('CACHE_DIR', 'tmp/cache');

define('VERSION',1);
define('FRAMEWORK', 'Soul');

define(DISABLE_CACHE, true);


if(DEBUG)
{
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}
else
{
	ini_set('display_errors', 0);
	error_reporting(0);
}

function debug($obj)
{
	if(DEBUG)
	{
	    echo "<pre>";
	    print_r($obj);
	    echo "</pre>";
	}
    
}

