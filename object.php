<?php
class Object
{
	protected function log($msg, $file = null)
	{
		if($file == null)
		{
			$file = 'general';
		}

		$log = LOG_DIR . DS . $file . '.log';
		if(is_array($msg))
		{
			$msg = print_r($msg, true);
		}

		$entry = date("Y-m-d H:i:s")." # ".$msg."\r\n";
		$fh = fopen($log, 'a');
		if($fh)
		{
			fwrite($fh, $entry);
			fclose($fh);
		}
	}

	public function __toString()
	{
		return var_export($this, true);
	}
}