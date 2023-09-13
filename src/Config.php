<?php
namespace SoulFramework;
#[AllowDynamicProperties]
class Config
{
    private static $instance;
    private $config = array();
    private function __construct()
    {
    }
    
    public function read($key)
    {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }
        return false;
    }
    
    public function write($key, $value)
    {
        $this->config[$key] = $value;
    }
    public static function get()
    {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
    }
    
    public function __clone()
    {
        throw new \Exception('Clone is not allowed.', E_USER_ERROR);
    }
}
