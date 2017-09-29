<?php
if(!function_exists('debug')) {
	function debug($obj)
	{
		if (DEBUG) {
			$bt = debug_backtrace();
			$caller = array_shift($bt);
			if ($_SERVER['HTTP_ACCEPT'] == 'application/json') {
				echo "\r\n**************************************************************\n";
				echo $caller['file'] . ':' . $caller['line'] . "\n\n";
				print_r($obj);
				echo "\r\n**************************************************************\n";
			} else {
				echo "<pre>";
				echo $caller['file'] . ':' . $caller['line'] . "\n";
				print_r($obj);
				echo "</pre>";
			}
		}

	}
}


if(!function_exists('auto_version')) {

	function auto_version($file)
	{
		if (strpos($file, '/') !== 0 || !file_exists($_SERVER['DOCUMENT_ROOT'] . '/public/' . $file))
			return $file;

		$asset = CURRENT_ASSET;
		return preg_replace('{\\.([^./]+)$}', ".$asset.\$1", $file);
	}
}

if(!function_exists('version_file')) {

	function version_file($file)
	{
		return CURRENT_ASSET;
	}
}
