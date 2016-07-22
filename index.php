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
require($coreDir.'/autoloader.php');
require($coreDir.'/registry.php');
require($coreDir.'/model.php');
require($coreDir.'/cache.php');
require($coreDir.'/exception.php');
require('app/config/bootstrap.php');
require('app/config/paths.php');

set_exception_handler(array('SoulException', 'catchException'));

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    require('app/config/routes.php');
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        throw new SoulException("Method not allowed");
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        FrontController::dispatch($handler['controller'], $handler['action'], $_GET, $_POST, $vars, $_GET);
        break;
    case FastRoute\Dispatcher::NOT_FOUND:
        $req_array = explode('/', $uri);
        $action['controller']   = str_replace('_', '', ucwords($req_array[1], '_')).'Controller';
        $action['file']         = strtolower($req_array[1]);
        $action['function']     = $req_array[2];
        $action['vars']         = array_slice($req_array, 2);
        FrontController::dispatch($action['controller'], $action['function'], $_GET, $_POST, $action['vars'], $_GET);
        break;
}