<?php
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
        if (!isset($this->db[$config['name']]))
        {
            $this->db[$config['name']] = new PDO("{$config['driver']}:host={$config['host']};dbname={$config['database']};charset=utf8;", $config['login'], $config['password']);
        }
        
        return $this->db[$config['name']];
    }
}


class ModelMapper
{
    
}

class Model extends Object
{
    protected static $modelMapper;
    protected static $connection = null;
    private static $connection_name;
    //private $current_statement = null;


    public static function gateway()
    {
        return $this->$modelMapper;
    }
    
    public function __construct()
    {
        if(self::$connection == null)
        {
            $this->connect(DatabaseConfig::$default);
        }
    }
    
    /**
     *
     * @param type $statement
     * @return type 
     */
    protected function beforeQuery($sql)
    {
        return true;
    }


    /**
     *
     * @param type $config 
     */
    public function connect($config)
    {
        self::$connection_name = $config['name'];
        self::$connection = ConnectionFactory::init()->getConnection($config);
    }
    
    public function getCurrentConnectionName()
    {
        return self::$connection_name;
    }
    
    /**
     *
     * @param string $sql
     * @return PDOStatement statement 
     */
    public function query($sql, $input_parameters = null)
    {
        if($this->beforeQuery($sql))
        {
            $statement = self::$connection->prepare($sql);
            if(!$statement->execute($input_parameters))
            {
                 $errorInfo = implode(' : ', $statement->errorInfo());
                 throw new Exception("SQL ERROR: $errorInfo \r\n $sql");
            }
            return $statement;
        }
        return false;
    }

	/**
	 * @param string $sql
	 * @return PDOStatement statement
	 */
	public function prepare($sql)
	{
		return self::$connection->prepare($sql);
	}
    public function lastInsertId()
    {
        return self::$connection->lastInsertId();
    }

    protected function rules()
    {
        return false;
    }

    protected function validationRules()
    {
        return array(
                        'url'    =>  '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \?=.-]*)*\/?$/',
                        'email'  => '/^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$/',
                        'username' => '/^[a-z0-9_-]{3,15}$/'
                    );
    }

    

    /*
    * Check $data with validationRules()
    *
    */
    public function validate($data)
    {
        $rules = $this->rules();
        $regex = $this->validationRules();
        $errors = array();
        if($rules)
        {
            foreach($rules as $key => $value)
            {
                if(isset($rules[$key]))
                {
                    
                    if($value['required'] && empty($data[$key]))
                    {
                        $errors[$key] = 'FIELD_EMPTY';
                    }
                    if(isset($data[$key]) && isset($value['rule']) && isset($regex[$value['rule']]))
                    {
                        if(!preg_match($regex[$value['rule']], $data[$key]))
                        {
                            $errors[$key] = 'NOT_MATCH';
                        }
                    }
                }
            }
        }
        if(empty($errors))
            return true;
        return $errors;
    }
    
}


