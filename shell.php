<?php
if( php_sapi_name() != 'cli' )
{
	header('HTTP/1.0 404 Not Found');
	die('shell only available in console mode');
}

require_once 'vendor/autoload.php';
spl_autoload_register(function($class) {
	Autoloader::autoLoadFile($class);
});
$time_start = microtime(true);
$coreDir = __DIR__;

require($coreDir.'/config.php');
require('app/config/config.php');
require($coreDir.'/object.php');
require($coreDir.'/functions.php');
require($coreDir.'/request.php');
require($coreDir.'/front.php');
require($coreDir.'/controller.php');
require($coreDir.'/autoloader.php');
require($coreDir.'/registry.php');
require($coreDir.'/model.php');
require($coreDir.'/cache.php');
require($coreDir.'/exception.php');
require($coreDir.'/shellcommand.php');
require('app/config/bootstrap.php');
require('app/config/paths.php');
//require('app/config/routes.php');


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
		$shell->{$argv[2]}();
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
