<?php
namespace SoulFramework;

class Soul {

    protected $appDirName = 'app';
    function __construct($appDirName) {
        $this->appDirName = $appDirName;
    }

    public function init() {
        set_exception_handler(array('SoulFramework\SoulException', 'catchException'));

        $dispatcher = \FastRoute\simpleDispatcher(function(\FastRoute\RouteCollector $r) {
            require($this->appDirName.'/Config/routes.php');
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
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                throw new SoulException("Method not allowed");
                break;
            case \FastRoute\Dispatcher::FOUND:
                FrontController::dispatch('App\Controller\\'.$routeInfo[1]['controller'], $routeInfo[1]['action'], $_GET, $_POST, $routeInfo[2], array_merge($routeInfo[1], $routeInfo[2]));
                break;
            case \FastRoute\Dispatcher::NOT_FOUND:
                $req_array = explode('/', $uri);
                $action['controller']   = 'App\Controller\\'.str_replace('_', '', ucwords($req_array[1])).'Controller';
                $action['file']         = strtolower($req_array[1]);
                $action['function']     = $req_array[2];
                $action['vars']         = array_slice($req_array, 3);
                FrontController::dispatch($action['controller'], $action['function'], $_GET, $_POST, $action['vars'], $action['vars']);
                break;
        }
    }
}