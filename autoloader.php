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
		'Helper'	=> 'helpers',
        'Resource'  => 'resources'
	);
    
    /**
     *
     * @param String $class_name class name to load
     */
    public static function loadFile($class_name, $type, $file)
    {
       if(array_key_exists($type, self::$objectTypes))
        {

	        $fileToSearch = strtolower("app".DS.self::$objectTypes[$type].DS.$file.'.php');

	        if(file_exists($fileToSearch))
	        {
		        require($fileToSearch);
		        return true;
	        }
	        else if($type == 'Controller')
	        {
		        foreach(Paths::$controllers as $dir)
		        {
			        $directory = "app".DS.self::$objectTypes[$type].DS.$dir.DS;
			        $fileToSearch = strtolower($directory.$file.'.php');
			        if(file_exists($fileToSearch))
			        {
				        require($fileToSearch);
				        return true;
			        }
		        }
		        throw new Exception("File $file not found: Error loading $class_name class. <br>Look at config/controllers.php and define your paths", 404);
	        }
        }
        else
        {
        		$directory = "app".DS.'classes'.DS;
        		$fileToSearch = strtolower($directory.$file.'.php');

        		if(file_exists($fileToSearch))
	            {
	                require($fileToSearch);
	                return true;
	            }
        }
    }
    
    public static function autoLoadFile($class_name)
    {
        if($name_type = self::getTypeFromName($class_name))
        {
            return self::loadFile($class_name, $name_type[1], Autoloader::underscore($name_type[0]));
        }
        else
        {
            return self::loadFile($class_name, null, Autoloader::underscore($class_name));
        }
    }
    
    private static function getTypeFromName($class_name)
    {

       $name_array = preg_split('/(?<=\\w)(?=[A-Z])/',$class_name);
       $numCamel = count($name_array);
        if($numCamel >= 2)
        {

            if(array_key_exists($name_array[$numCamel-1], self::$objectTypes))
            {
                $type =  $name_array[$numCamel-1];
                unset($name_array[$numCamel-1]);
                $name = implode('', $name_array);
                return array(0 => $name, 1 => $type );
            }
        }
        return false;
    }

    private static function underscore($name)
    {
        $result = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
        return $result;
    }
}
