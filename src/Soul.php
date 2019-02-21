<?php
namespace SoulFramework;

use FastRoute\Dispatcher;

class Soul
{
    protected $appDirName = 'app';
    public function __construct($appDirName)
    {
        $this->appDirName = $appDirName;
    }

    public function init()
    {
        set_exception_handler(array('SoulFramework\SoulException', 'catchException'));

        $dispatcher = \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $r) {
            require($this->appDirName.'/Config/routes.php');
        });

        // Fetch method and URI from somewhere
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        if ($httpMethod == 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, DELETE, PUT, PATCH, OPTIONS');
            if (defined('ALLOWED_HEADERS')) {
                header('Access-Control-Allow-Headers: '.ALLOWED_HEADERS);
            } else {
                header('Access-Control-Allow-Headers: Content-Type, ApiKey, Authorization');
            }

            header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK', true, 200);
            exit();
        }
        // Strip query string (?foo=bar) and decode URI
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
        switch ($routeInfo[0]) {
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new SoulException("Method not allowed");
                break;
            case Dispatcher::FOUND:
                FrontController::dispatch('App\Controller\\'.$routeInfo[1]['controller'], $routeInfo[1]['action'], $_GET, $_POST, $routeInfo[2], array_merge($routeInfo[1], $routeInfo[2]));
                break;
            case Dispatcher::NOT_FOUND:
                $req_array = explode('/', $uri);
                if (count($req_array) > 2) {
                    $action['controller']   = 'App\Controller\\'.str_replace('_', '', ucwords($req_array[1])).'Controller';
                    $action['file']         = strtolower($req_array[1]);
                    $action['function']     = $req_array[2];
                    $action['vars']         = array_slice($req_array, 3);
                    FrontController::dispatch($action['controller'], $action['function'], $_GET, $_POST, $action['vars'], $action['vars']);
                } else {
                    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
                    throw new SoulException('Page not found', 404);
                }
                break;
        }
    }
}
