<?php
$coreDir = __DIR__;
if(file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
}
spl_autoload_register(function($class) {
    Autoloader::autoLoadFile($class);
});

require($coreDir.'/config.php');
require('app/config/config.php');
require($coreDir.'/object.php');
require($coreDir.'/functions.php');
require($coreDir.'/request.php');
require($coreDir.'/front.php');
require($coreDir.'/controller.php');
require($coreDir.'/router.php');
require($coreDir.'/autoloader.php');
require($coreDir.'/registry.php');
require($coreDir.'/model.php');
require($coreDir.'/cache.php');
require($coreDir.'/exception.php');
require('app/config/routes.php');
require('app/config/bootstrap.php');
require('app/config/paths.php');

set_exception_handler(array('SoulException', 'catchException'));

$array_uri = Router::get_route();

FrontController::dispatch($array_uri['controller'],$array_uri['function'], $array_uri['file'], $_GET, $_POST, $array_uri['vars'], $array_uri['args']);