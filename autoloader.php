<?php
/**
 * 
 */
class Autoloader
{
    
    
	private static $objectTypes = array(
		'Model' => 'models',
		'Controller' => 'controllers',
		'Config' => 'config',
		'Component' => 'components',
		'Helper'	=> 'helpers'
	);
    
    /**
     *
     * @param String $class_name class name to load
     */
    public static function loadFile($class_name, $type, $file)
    {
       if(key_exists($type, self::$objectTypes))
        {
            $directory = "app".DS.self::$objectTypes[$type].DS;
            $file = strtolower($directory.$file.'.php');
            
            if(file_exists($file))
            {
                require($file);
                return true;
            }
            else
            {
                throw new Exception("File $file not found: Error loading $class_name class", E_USER_ERROR);
            }
        }
        else
        {
        		$directory = "app".DS.'classes'.DS;
        		$file = strtolower($directory.$file.'.php');
        		if(file_exists($file))
	            {
	                require($file);
	                return true;
	            }
        }
        throw new Exception("File $file not found: Error loading $class_name class. Unknown type $type", E_USER_ERROR);
    }
    
    public static function autoLoadFile($class_name)
    {
        if($name_type = self::getTypeFromName($class_name))
        {
            return self::loadFile($class_name, $name_type[1], $name_type[0]);
        }
        else
        {
            return self::loadFile($class_name, null, $class_name);
        }
    }
    
    private static function getTypeFromName($class_name)
    {
       $name_array = preg_split('/(?<=\\w)(?=[A-Z])/',$class_name);
       
        if(count($name_array) == 2)
        {
            if(key_exists($name_array[1], self::$objectTypes))
            {
                return $name_array;
            }
        }
        return false;
    }
    
    
}
