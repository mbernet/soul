<?php
if( php_sapi_name() != 'cli' )
{
	header('HTTP/1.0 404 Not Found');
	die('shell only available in console mode');
}



$time_start = microtime(true);
require('core/config.php');
require('app/config/config.php');
require('core/object.php');
require('core/functions.php');
require('core/request.php');
require('core/front.php');
require('core/controller.php');
require('core/router.php');
require('core/autoloader.php');
require('core/registry.php');
require('core/model.php');
require('core/cache.php');
require('core/exception.php');
require('core/shellcommand.php');
require('app/config/bootstrap.php');
require('app/config/paths.php');
require('app/config/routes.php');


set_exception_handler(array('SoulException', 'catchException'));

if(isset($argv[1]) && isset($argv[2]))
{
	$path = 'app/shells/'.strtolower($argv[1]).'.php';
	include($path);
	$class = ucfirst($argv[1]).'Shell';
	$shell = new $class();
	if(method_exists($shell, $argv[2]))
	{
		$shell->params = $argv;
		$shell->$argv[2]();
	}
	else
	{
		echo "Method  {$argv[2]}()  doesn't exists\n";
	}

}
else
{
	die('Missing arguments');
}


$time_end = microtime(true);
$time = $time_end - $time_start;

//echo "<!-- $time seconds -->";
       
function __autoload($name) {
    Autoloader::autoLoadFile($name);
}