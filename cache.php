<?php
class ActionCache
{
	static function isCached($controller, $action, $expiration)
	{
		
		$filename = strtolower(CACHE_DIR. '/'. $controller.'-'.$action.'-'.self::hashRequest().'.tmp');
	
		if(file_exists($filename))
		{
			
			if(time() - $expiration < filemtime($filename))
			{
				return true;
			}
			else
			{
				unlink($filename);
			}
		}
		return false;		
	}
	
	static function showCache($controller, $action)
	{
		$filename = strtolower(CACHE_DIR. '/'. $controller.'-'.$action.'-'.self::hashRequest().'.tmp');
		include($filename);
	}
	
	static function writeCache($controller, $action)
	{
		$filename = strtolower(CACHE_DIR. '/'. $controller.'-'.$action.'-'.self::hashRequest().'.tmp');
		$fp = fopen($filename, 'w');
		fwrite($fp, ob_get_contents());
		fclose($fp);
		ob_end_flush(); 
	}
	
	static function hashRequest()
	{
		$req = implode('', $_GET) . implode('', $_POST) . $_SERVER['PHP_SELF'];
		return md5($req);
	}
}
