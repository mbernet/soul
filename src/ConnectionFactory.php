<?php
namespace SoulFramework;

class ConnectionFactory
{
    private static $factory;
    private $db = false;
    private $numRows;

    /**
     * Singleton contstructor
     * @return type
     */
    public static function init()
    {
        if (!self::$factory)
            self::$factory = new ConnectionFactory();
        return self::$factory;
    }

    public function getConnection($config) {
        if (!isset($this->db[$config['name']])) {
            $options = null;
            if(isset($config['options'])) {
                $options = $config['options'];
            }
            $this->db[$config['name']] = new \PDO("{$config['driver']}:host={$config['host']};dbname={$config['database']};charset=utf8;", $config['login'], $config['password'], $options);
        }

        return $this->db[$config['name']];
    }
}