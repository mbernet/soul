<?php
namespace SoulFramework;
class FrontController
{
	static function dispatch($controller, $action, $vars_get, $vars_post, $vars_uri, $vars_arg)
    {
        if(class_exists($controller)) {
                $reflectionClass = new \ReflectionClass($controller);
                if($reflectionClass->hasMethod($action) && $reflectionClass->getMethod($action)->class === $controller) {
                        $controller_inst = new $controller();
                        $controller_inst->name = $controller;
                        $controller_inst->action = $action;
                        $controller_inst->setRequest($vars_get, $vars_post, $vars_uri, $vars_arg);
                        $controller_inst->beforeAction();
                        $avoid_call_action = false;
                        if(!$avoid_call_action) {
                                $controller_inst->$action();
                        }
                        $controller_inst->afterAction();
                }
                else {
                    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
                    throw new \Exception("Missing method $action in $controller", 404);
                }
        }
        else
        {
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
            throw new \Exception("Controller $controller does not exists", 404);
        }
	}
}
