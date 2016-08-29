<?php
namespace SoulFramework;
class Request
{
    private  static $methods = array('get', 'post', 'request');
    protected $params;
   
    
    function __construct($get, $post, $req) {
        $this->params['get'] = $get;
        $this->params['post'] = $post;
        $this->params['request'] = $req;
        
    
    }
    
    public function clean()
    {
        $this->raw = $this->params;
        foreach(self::$methods as $meth)
        {
            foreach($this->raw[$meth] as $k => $v)
            {
                $this->params[$meth][$k] = $this->sanitize($v);
            }
        }
        return $this;
    }
    
    public function raw()
    {
        if(isset($this->raw))
        {
            $this->params = $this->raw;
        }
        return $this;
    }
    
    protected function sanitize($value)
    {
        return addslashes($value);
    }
    
    
    
    public function params($method, $key = null)
    {
       if(!isset($this->params[$method]))
       {
            throw new Exception("Method $method does not exists", E_USER_WARNING);
       }
       else if($key == null)
       {
           return $this->params[$method];
       }
       else if(isset($this->params[$method][$key]))
       {
           return $this->params[$method][$key];
       }
       else
       {
           throw new Exception("Key $key in $method does not exists", E_USER_WARNING);
       }
    }
    
    
}