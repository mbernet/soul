<?php
class AppModel extends Model
{
    protected function beforeQuery($statement) {
        $updates = array(
                'CREATE', 'DELETE', 'DROP',
                'INSERT', 'UPDATE'
            );
            if (preg_match('/^(' . implode('|', $updates) .')/i', trim($statement))) 
            {
                $this->connect(DatabaseConfig::$master);
            }
            else
            {
                $this->connect($this->balancer());
            }
            return true;
    }
    
    private function balancer()
    {
    	return DatabaseConfig::$master;
        $slave = mt_rand(0, DatabaseConfig::num_slaves-1);
    }
}
