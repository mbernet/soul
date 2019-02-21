<?php
namespace SoulFramework;
/**
 * Class Registry
 * @package SoulFramework
 */
class Registry
{

    /**
     * @var array
     */
    private static $objects = array();

    /**
     * @var array
     */
    private static $settings = array();


    /**
     * @var Registry
     */
    private static $instance;

    /**
     * @var array
     */
    private static $objectTypes = array(
        'model' => 'models',
        'controller' => 'controllers'
    );



    private function __construct()
    {
    }

    /**
     * @return Registry
     */
    public static function init()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Registry();
        }
        return self::$instance;
    }

    /**
     * @throws \Exception
     */
    public function __clone()
    {
        throw new \Exception('Cloning the registry is not permitted', E_USER_ERROR);
    }

    /**
     * @param $object
     *
     * @return object
     * @throws \Exception
     */
    public function get($object)
    {
        if ($instance = self::getObject($object)) {
            return $instance;
        } elseif (class_exists($object)) {
            $instance = new $object();
            $obj = self::$objects[ $object ] = $instance;
            return $obj;
        } else {
            throw new \Exception("Cannot load $object", E_USER_ERROR);
        }
    }

    /**
     * @param $name
     *
     * @return object | Model
     * @throws \Exception
     */
    public function model($name)
    {
        return self::get($name.'Model');
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return object
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        if (array_key_exists($name, self::$objectTypes)) {
            return self::get($arguments[0]);
        } else {
            throw new \Exception("Method $name not found", E_USER_ERROR);
        }
    }



    /**
     * Stores an object in the registry
     * @param Object $object instance of the object
     * @param String $key the key for the array
     * @return void
     */
    public function storeObject($object, $key)
    {
        self::$objects[ $key ] = $object;
    }

    /**
     * @param $key
     *
     * @return bool|mixed
     */
    public function getObject($key)
    {
        if (isset(self::$objects[ $key ]) && is_object(self::$objects[ $key ])) {
            return self::$objects[ $key ];
        }
        return false;
    }

    /**
     * Stores settings in the registry
     * @param String $data
     * @param String $key the key for the array
     * @return void
     */
    public function storeSetting($data, $key)
    {
        self::$settings[ $key ] = $data;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function getSetting($key)
    {
        return self::$settings[ $key ];
    }


    /**
     * @return array
     */
    public function getAllObjects()
    {
        return self::$objects;
    }
}
