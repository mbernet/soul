<?php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
class Object
{
	protected function log($msg, $file = null, $type = null)
	{
// create a log channel
		if(is_array($msg) || is_object($msg))
		{
			$msg = print_r($msg, true);
		}

		$log = new Logger($file);
		$log->pushHandler(new StreamHandler(LOG_DIR . DS . $file . '.log', Logger::DEBUG));

		$log->debug($msg);



		/*
		if($file == null)
		{
			$file = 'general';
		}

		$log = LOG_DIR . DS . $file . '.log';
		if(is_array($msg) || is_object($msg))
		{
			$msg = print_r($msg, true);
		}

		$entry = date("Y-m-d H:i:s")." # ".$msg."\r\n";
		$fh = fopen($log, 'a');
		if($fh)
		{
			fwrite($fh, $entry);
			fclose($fh);
		}*/
	}

	public function __toString()
	{
		return var_export($this, true);
	}
}