<?php
namespace SoulFramework;
class FrontController
{
	static function dispatch($controller, $action, $vars_get, $vars_post, $vars_uri, $vars_arg)
	{              
		$controller_inst = new $controller();
        if(method_exists($controller_inst,$action) || method_exists($controller_inst, '__call'))
        {
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
        else
        {
                throw new \Exception("Missing method $action in $controller", 404);
        }
	}
}
