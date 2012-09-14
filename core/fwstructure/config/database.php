<?php
class DatabaseConfig {

    public static $default = array(
        'name'      => 'default',
        'driver'    => 'mysql',
        'host' 	    => 'localhost',
        'port'      => '3306',
        'login'     => 'login',
        'password'  => '',
        'database'  => '',
        'encoding'  => 'utf8'
    );
    
    public static $master = array(
        'name'      => 'master',
        'driver'    => 'mysql',
        'host' 	    => 'localhost',
        'port'      => '3306',
        'login'     => '',
        'password'  => '',
        'database'  => '',
        'encoding'  => 'utf8'
    );
    
    const num_slaves = 3;
}
