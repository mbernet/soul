<?php
namespace SoulFramework;

use DI\Container;

class FrontController
{
    /**
     * @param $controller
     * @param $action
     * @param $vars_get
     * @param $vars_post
     * @param $vars_uri
     * @param $vars_arg
     *
     * @throws \ReflectionException | \Exception
     */
    public static function dispatch($controller, $action, $vars_get, $vars_post, $vars_uri, $vars_arg)
    {
        if (class_exists($controller)) {
            $rfClass = new \ReflectionClass($controller);

            $namedMethodExists = $rfClass->hasMethod($action) && $rfClass->getMethod($action)->class === $controller;
            $callMethodExists = $rfClass->hasMethod('__call') && $rfClass->getMethod('__call')->class === $controller;

            if ($namedMethodExists || $callMethodExists) {
                /**
                 * @var Controller $controller
                 */

                $controller_inst = self::getDIContainer()->get($controller);
                $controller_inst->name = $controller;
                $controller_inst->action = $action;
                $controller_inst->setRequest($vars_get, $vars_post, $vars_uri, $vars_arg);
                $controller_inst->beforeAction();
                $avoid_call_action = false;
                if (!$avoid_call_action) {
                    $controller_inst->$action();
                }
                $controller_inst->afterAction();
            } else {
                header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
                throw new \Exception("Missing method $action in $controller", 404);
            }
        } else {
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
            throw new \Exception("Controller $controller does not exists", 404);
        }
    }


    /**
     * @return Container
     */
    private static function getDIContainer() {
        $diContainer = Registry::init()->getObject('DiContainer');
        return $diContainer;
    }
}
