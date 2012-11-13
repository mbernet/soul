<?php
$time_start = microtime(true);
require('core/config.php');
require('core/functions.php');
require('app/config/config.php');
require('core/request.php');
require('core/front.php');
require('core/controller.php');
require('core/router.php');
require('core/autoloader.php');
require('core/registry.php');
require('core/model.php');
require('core/cache.php');
require('core/exception.php');
require('app/config/routes.php');

set_exception_handler(array('SoulException', 'catchException'));

$array_uri = Router::get_route();
FrontController::dispatch($array_uri['controller'],$array_uri['function'], $array_uri['file'], $_GET, $_POST, $array_uri['vars'], $array_uri['args']); 
$time_end = microtime(true);
$time = $time_end - $time_start;

//echo "<!-- $time seconds -->";
       
function __autoload($name) {
    Autoloader::autoLoadFile($name);
}