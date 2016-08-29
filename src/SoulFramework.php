<?php
namespace SoulFramework;

class Soul {

    function __construct() {

    }

    public function init() {
        set_exception_handler(array('SoulException', 'catchException'));
        $array_uri = Router::get_route();
        FrontController::dispatch($array_uri['controller'],$array_uri['function'], $array_uri['file'], $_GET, $_POST, $array_uri['vars'], $array_uri['args']);
    }
}