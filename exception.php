<?php
class SoulException extends Exception
{
	static function catchException($exception)
	{
		if($exception->getCode() == 404) {
			if(class_exists('StaticController')) {
				$controller = new StaticController();
				if(method_exists($controller, 'error404')) {
					$controller->error404($exception);
				}
			}
		}
		else {
			if(DEBUG || php_sapi_name() == 'cli')
			{
				$message['code'] = $exception->getCode();
				$message['Message'] = $exception->getMessage();
				$message['File'] = $exception->getFile();
				$message['line'] = $exception->getLine();

				if( php_sapi_name() != 'cli' )
				{
					self::showError($message);
				}
				else
				{
					self::showConsoleError($message);
				}
			}
			else
			{
				header($_SERVER['SERVER_PROTOCOL'] . ' ' . '500'. ' '. 'Internal Server Error', true, 500);
				if(defined('ERROR_VIEW')) {
					$message['code'] = $exception->getCode();
					$message['Message'] = $exception->getMessage();
					$message['File'] = $exception->getFile();
					$message['line'] = $exception->getLine();
					include('app'.DS.'views'.DS.ERROR_VIEW.'.php');
				}

			}
		}
	}
	static function showError($message)
	{
		if($_SERVER['HTTP_ACCEPT'] == 'application/json') {
			header($_SERVER['SERVER_PROTOCOL'] . ' ' . '500'. ' '. 'Internal Server Error', true, 500);
			echo json_encode(
				[
					'code'      => $message['code'],
					'message'   =>  $message['Message'],
					'file'      => $message['File'],
					'line'      => $message['line']
				]
			);
		}
		else {
			header($_SERVER['SERVER_PROTOCOL'] . ' ' . '500'. ' '. 'Internal Server Error', true, 500);
			echo "<h1> Error {$message['code']}</h1>";
			echo "<strong>{$message['Message']}</strong> ";
			echo "<br /><strong>file:</strong> {$message['File']} ";
			echo "<br /><strong>line:</strong> {$message['line']} ";
		}
	}

	static function showConsoleError($message)
	{
		echo "\r\n Error {$message['code']}";
		echo "\r\n{$message['Message']}";
		echo "\r\nfile:</strong> {$message['File']}";
		echo "\r\nline:</strong> {$message['line']}\r\n";
	}
}