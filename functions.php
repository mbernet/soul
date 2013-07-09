<?php

function __($text)
{
	if(empty($text))
		return '';
	else
		return gettext($text);
}

function debug($obj)
{
	if(DEBUG)
	{
	    echo "<pre>";
	    var_dump($obj);
	    echo "</pre>";
	}
    
}

function auto_version($file)
{
	if(strpos($file, '/') !== 0 || !file_exists($_SERVER['DOCUMENT_ROOT'] . $file))
	return $file;
	
	$asset = CURRENT_ASSET;
	return preg_replace('{\\.([^./]+)$}', ".$asset.\$1", $file);
}

function version_file($file)
{
	return CURRENT_ASSET;
}