<?php
namespace SoulFramework;
use PDO;
use PDOStatement;
class Model extends SoulObject
{
    protected static $modelMapper;
    protected static $connection = null;
    private static $connection_name;
    protected $defaultConnection;

    function __construct() {
        $this->defaultConnection = \App\Config\DatabaseConfig::$default;
    }

    protected function getPrimaryKey($table) {
        $sql = "SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'";
        $rs = $this->query($sql);
        if($rs->rowCount() > 0) {
            return $rs->fetch(PDO::FETCH_OBJ)->Column_name;
        }
        return false;
    }

    /**
     * @param $sql
     *
     * @return bool
     */
    protected function beforeQuery($sql)
    {
        return true;
    }


    /**
     *
     * @param type $config
     */
    protected function connect($config)
    {
        self::$connection_name = $config['name'];
        self::$connection = ConnectionFactory::init()->getConnection($config);
    }

    /**
     * @return mixed
     */
    protected function getCurrentConnectionName()
    {
        return self::$connection_name;
    }

    /**
     * @return PDO connection
     */
    protected function getCurrentConnection()
    {
        if(self::$connection == null)
        {
            $this->connect($this->defaultConnection);
        }
        return self::$connection;
    }

    /**
     * @param $sql
     * @param null $input_parameters
     *
     * @return PDOStatement | bool
     * @throws \Exception
     */
    protected function query($sql, $input_parameters = null)
    {
        if($this->beforeQuery($sql))
        {
            if(self::$connection == null) {
                $this->connect($this->defaultConnection);
            }
            $statement = self::$connection->prepare($sql);
            if(!empty($input_parameters)) {
                foreach ($input_parameters as $key => $input) {
                    switch (gettype($input)) {
                        case 'integer':
                            $paramType = PDO::PARAM_INT;
                            break;
                        case 'boolean':
                            $paramType = PDO::PARAM_BOOL;
                            break;
                        default:
                            $paramType = PDO::PARAM_STR;
                            break;
                    }
                    $statement->bindValue($key, $input, $paramType);
                }
            }
            if(!$statement->execute())
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
    protected function prepare($sql)
    {
        if(self::$connection == null)
        {
            $this->connect($this->defaultConnection);
        }
        return self::$connection->prepare($sql);
    }

    /**
     * @return mixed
     */
    public function lastInsertId()
    {
        return self::$connection->lastInsertId();
    }

    /**
     * @return bool
     */
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


    /**
     * @param $data
     *
     * @return array|bool
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

    /**
     * @param $table
     * @param $data
     *
     * @return mixed
     * @throws SoulException
     */
    protected function insert($table, $data) {
        $table = stripslashes($table);
        $strValues = '';
        $strFields = '';
        $params = $data;
        foreach($data as $key => $value) {
            $key = stripslashes($key);
            $strFields .= "`$key`, ";
            $strValues .= ":".str_replace('-', '_', $key).", ";
        }
        $strValues = rtrim($strValues, ', ');
        $strFields = rtrim($strFields, ', ');
        $sql = "INSERT INTO $table ($strFields) VALUES ($strValues)";

        $rs = $this->prepare($sql);

        foreach($params as $key => $value) {
            $key = str_replace('-', '_', $key);
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


