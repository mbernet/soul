<?php
/**
 * The Registry object
 * Implements the Registry and Singleton design patterns
 *
 * @version 0.1
 * @author Michael Peacock
 */
namespace SoulFramework;
class Registry {

	/**
	 * Our array of objects
	 * @access private
	 */
	private static $objects = array();

	/**
	 * Our array of settings
	 * @access private
	 */
	private static $settings = array();


	/**
	 * The instance of the registry
	 * @access private
	 */
	private static $instance;

	private static $objectTypes = array(
		'model' => 'models',
		'controller' => 'controllers'
	);


	/**
	 * Private constructor to prevent it being created directly
	 * @access private
	 */

	private function __construct()
	{
	}

	/**
	 * singleton method used to access the object
	 * @access public
	 * @return
	 */
	public static function init()
	{
		if( !isset( self::$instance ) )
		{
			$obj = __CLASS__;
			self::$instance = new $obj;
		}

		return self::$instance;
	}

	/**
	 * prevent cloning of the object: issues an E_USER_ERROR if this is attempted
	 */
	public function __clone()
	{
		throw new \Exception( 'Cloning the registry is not permitted', E_USER_ERROR );
	}

	/**
	 *
	 * @param String $object class name
	 * @param String $type style
	 * @return Object
	 */
	public function get($object)
	{
		if($instance = self::getObject($object))
		{
			return $instance;
		}
		else if(class_exists($object))
		{
			$instance = new $object();
			$obj = self::$objects[ $object ] = $instance;
			return $obj;
		}
		else
		{
			throw new \Exception("Cannot load $object", E_USER_ERROR);
		}

	}

	public function model($name)
	{
		return self::get($name.'Model');
	}

	public function __call($name, $arguments)
	{
		if(key_exists($name, self::$objectTypes))
		{
			return self::get($arguments[0]);
		}
		else
		{
			throw new \Exception("Method $name not found", E_USER_ERROR);
		}
	}



	/**
	 * Stores an object in the registry
	 * @param Object $object instance of the object
	 * @param String $key the key for the array
	 * @return void
	 */
	public function storeObject( $object, $key )
	{

		self::$objects[ $key ] = $object;
	}

	/**
	 * Gets an object from the registry
	 * @param String $key the array key
	 * @return object
	 */
	public function getObject( $key )
	{
		if( isset(self::$objects[ $key ]) && is_object ( self::$objects[ $key ] ) )
		{
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
	public function storeSetting( $data, $key )
	{
		self::$settings[ $key ] = $data;
	}

	/**
	 * Gets a setting from the registry
	 * @param String $key the key in the array
	 * @return void
	 */
	public function getSetting( $key )
	{
		return self::$settings[ $key ];
	}


	public function getAllObjects()
	{
		return self::$objects;
	}
}