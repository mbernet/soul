<?php
/*TODO*/
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
        $bt = debug_backtrace();
        $caller = array_shift($bt);
	    echo "<pre>";
        echo $caller['file'].':'.$caller['line']."\n";
	    print_r($obj);
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
