<?php
class SoulException
{
	static function catchException($exception)
	{
		if(DEBUG)
		{
			$message['code'] = $exception->getCode();
			$message['Message'] = $exception->getMessage();
			$message['File'] = $exception->getFile();
			$message['line'] = $exception->getLine();
			self::showError($message);
		}
		else
		{
			header('HTTP/1.0 404 Not Found');
		}
	}
	static function showError($message)
	{
		echo "<h1> Error {$message['code']}</h1>";
		echo "<strong>{$message['Message']}</strong> ";
		echo "<br /><strong>file:</strong> {$message['File']} ";
		echo "<br /><strong>line:</strong> {$message['line']} ";
	}
}