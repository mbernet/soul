<?php
namespace SoulFramework;

class ConnectionFactory
{
    private static $factory;
    private $db = false;

    /**
     * @return ConnectionFactory
     */
    public static function init()
    {
        if (!self::$factory) {
            self::$factory = new ConnectionFactory();
        }
        return self::$factory;
    }

    /**
     * @param $config
     *
     * @return \PDO
     */
    public function getConnection($config)
    {
        if (!isset($this->db[$config['name']])) {
            $charset = '';
            $options = null;
            if (isset($config['options'])) {
                $options = $config['options'];
            }
            if (strtolower($config['driver']) === 'mysql') {
                $charset = 'charset=utf8;';
            }
            $this->db[$config['name']] = new \PDO("{$config['driver']}:host={$config['host']};dbname={$config['database']};$charset", $config['login'], $config['password'], $options);
        }
        return $this->db[$config['name']];
    }
}
