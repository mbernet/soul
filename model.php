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
            $this->db[$config['name']] = new PDO("{$config['driver']}:host={$config['host']};dbname={$config['database']}", $config['login'], $config['password']);
        }
        
        return $this->db[$config['name']];
    }
}


class ModelMapper
{
    
}

class Model
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
            $statement->execute($input_parameters);
            return $statement;
        }
        return false;
    }
    
}


