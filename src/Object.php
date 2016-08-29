<?php
namespace SoulFramework;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
class Object
{
	protected function log($msg, $file = null, $type = null)
	{
		$context = [];
		if(is_array($msg) || is_object($msg))
		{
			$context = $msg;
			$msg = '';
		}

		$log = new Logger($file);
		$log->pushHandler(new StreamHandler(LOG_DIR . DS . $file . '.log', Logger::DEBUG));

		$log->debug($msg, $context);
	}

	public function __toString()
	{
		return var_export($this, true);
	}
}