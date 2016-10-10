<?php
namespace SoulFramework;
use PDO;
use PDOStatement;
class Model extends Object
{
    protected static $modelMapper;
    protected static $connection = null;
    private static $connection_name;
    protected $defaultConnection;
    //private $current_statement = null;


    public static function gateway()
    {
        // return $this->$modelMapper;
    }

    public function __construct()
    {
        $this->defaultConnection = \App\Config\DatabaseConfig::$default;
    }

    public function getPrimaryKey($table) {
        $sql = "SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'";
        $rs = $this->query($sql);
        if($rs->rowCount() > 0) {
            return $rs->fetch(PDO::FETCH_OBJ)->Column_name;
        }
        return false;
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
     * @return PDO connection
     */
    public function getCurrentConnection()
    {
        if(self::$connection == null)
        {
            $this->connect($this->defaultConnection);
        }
        return self::$connection;
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
            if(self::$connection == null)
            {
                $this->connect($this->defaultConnection);
            }
            $statement = self::$connection->prepare($sql);
            if(!$statement->execute($input_parameters))
            {
                $errorInfo = implode(' : ', $statement->errorInfo());
                throw new \Exception("SQL ERROR: $errorInfo \r\n $sql");
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
        if(self::$connection == null)
        {
            $this->connect($this->defaultConnection);
        }
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
                    if(isset($data[$key]) && isset($value['function']))
                    {
                        if($this->$value['function']($data[$key]) === false)
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

    protected function insert($table, $data) {
        $table = stripslashes($table);
        $strValues = '';
        $strFields = '';
        $params = $data;
        foreach($data as $key => $value) {
            $key = stripslashes($key);
            $strFields .= "`$key`, ";
            $strValues .= ":$key, ";
        }
        $strValues = rtrim($strValues, ', ');
        $strFields = rtrim($strFields, ', ');
        $sql = "INSERT INTO $table ($strFields) VALUES ($strValues)";

        $rs = $this->prepare($sql);

        foreach($params as $key => $value) {
            $rs->bindValue(":$key", $value);
        }


        if($rs->execute()) {
            return $this->lastInsertId();
        }
        else {
            $errorInfo = $rs->errorInfo();
            throw new SoulException($errorInfo[2]);
        }
    }

    /**
     * @param $table
     * @param $data
     * @param $id
     * @return bool
     * @throws SoulException
     */
    protected function update($table, $data, $id) {

        $strFields = '';
        $params = $data;
        foreach($data as $key => $value) {
            $strFields .= "`$key` = :$key, ";
        }
        $strFields = rtrim($strFields, ', ');
        $primaryKey = $this->getPrimaryKey($table);
        $sql = "UPDATE $table SET $strFields WHERE $primaryKey = :primary_key_id LIMIT 1";


        $rs = $this->prepare($sql);
        foreach($params as $key => $value) {
            $rs->bindValue(":$key", $value);
        }
        $rs->bindValue(':primary_key_id', $id);

        if($rs->execute()) {
            return true;
        }
        else {
            $errorInfo = $rs->errorInfo();
            throw new SoulException($errorInfo[2]);
        }
    }

}


