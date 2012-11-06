<?php
class Router
{
    
        protected static $routes;
        protected static $regularRoutes;
        
        static function get_route()
        {
            if(MOD_REWRITE && !isset($_GET['force_route']))
            {
                return Router::mod_rewrite_get_route();
            }
            else
            {
                return Router::regular_get_route();
            }
        }
        
        static function mod_rewrite_get_route()
        {
            $req = $_SERVER['REQUEST_URI'];
            if(substr($_SERVER['REQUEST_URI'], 1, strlen(DIR_ROOT)) == DIR_ROOT)
            {
                $offset = 2;
                if(strlen(DIR_ROOT) == 0)
                {
                    $offset = 1;
                }
                $req = substr($_SERVER['REQUEST_URI'], strlen(DIR_ROOT) + $offset);
                return self::extract_data_route($req);
            }
        
        }
        
        static function regular_get_route()
        {
            if(isset($_GET['r']))
            {
                $request = $_GET['r'];
            }
            else
            {
                $request = DEFAULT_ROUTE;
            }
        
            return self::extract_data_route($request);
           
        }
        
        
        /**
         * Splits the request string and gets wich action has to be executed.
         * It also searches in the self::$routes array searching for a user defined route
         * @param type $req
         * @param type $start_position
         * @return type 
         */
        private static function extract_data_route($req, $start_position = 0)
        {   
        	 
            
             $pos = strpos($req,'?');
             if($pos !== false)
             {
                $req = substr($req, 0, $pos);
             }
             if(empty($req)) $req = DEFAULT_ROUTE;
             $req_array = explode('/', $req);
             if($rfind = self::search_in_routes($req))
             {
                 $action['controller']   = ucfirst($rfind['controller']).'Controller';
                 $action['file']         = strtolower($rfind['controller']);
                 $action['function']     = $rfind['action'];
                 $action['vars']         = Router::get_vars($start_position, $req_array);
                 $action['args']         = array_slice($rfind, 2);
             }
             else if(count($req_array) >= 2)
             {

                 $action['controller']   = ucfirst($req_array[$start_position]).'Controller';
                 $action['file']         = strtolower($req_array[$start_position]);
                 $action['function']     = $req_array[$start_position+1];
                 $action['vars']         = Router::get_vars($start_position, $req_array);
                 $action['args']         = array_slice($action['vars'], 2);
             }
             else
             {

                 throw new Exception('Action not found', E_USER_ERROR);
             }
             return $action;
        }
        
        private static function search_in_routes($req)
        {
        	$dest = self::search_in_normal_routes($req);
        	if($dest !== false)
        	{
        		return $dest;
        	}
        	else
        	{
        		return self::search_in_regular_routes($req);
        	}
        	
        	
        }
        
        private static function search_in_normal_routes($req)
        {
        	if(!empty(self::$routes))
        	{
	            foreach(self::$routes as $route => $details)
	            {
	            	if($req == $route)
					{
					    return $details['dest'];
					}
	            }
        	}
        	return false;
        }
        
        private static function search_in_regular_routes($req)
        {
        	foreach(self::$regularRoutes as $route => $details)
            {
    			$destination = $details['dest'];
        		if(preg_match_all("/$route/", $req, $vars))
        		{
                    
        			if($details['routeParams'])
    				{
	        			$i=1;
	           			foreach($details['routeParams'] as $key => $val)
	           			{
	           					foreach($details['dest'] as $kdest => $value)
		            			{
                                    foreach ($vars as $repo) 
                                    {
                                        $rep = $repo[0];
                                        if(strpos($value, ':') !== false)
                                        {
        		            				$destination[$kdest] = str_replace(':'.$val, $rep, $value);
                                        }
                                       
                                    }
		            			}
	            		}
    				}
    				return $destination;
            	} 	
            }
            return false;
        }
        
        static function get_vars($index, $req)
        {
            $size = count($req);
            $vars = array();
            for($n=$index; $n<$size; $n++)
            {
                if($p = strpos($req[$n], ':'))
                {
                    $vars[substr($req[$n], 0, $p)] = substr($req[$n], $p+1);
                }
                else
                {
                    $vars[] = $req[$n];
                }
            }
            return $vars;
        }
        
        static function getExactPath($path)
        {
        	if(strpos($path, ':') !== false) //Contiene expresion regular
            {
	        	$a = preg_match_all('/\<([^\>]+)\>/', $path, $variables);
	        	if($a > 0)
	            {
	            	$route = $path;
	            	foreach($variables[1] as $expression_pair)
	        		{
	        			$epe = explode(':', $expression_pair);
	        			if(count($epe) == 2)
	        			{
	        				$route = str_replace('<'.$expression_pair.'>', $epe[1], $route);
	        				$params[] = $epe[0];
	        			}
	        			
	        		}
	        		return array($route, $params);
	            }
            }
            return array($path, false);   
        }
        
 
        static function addRegular($path, $dest)
        {
        	$exRoute = self::getExactPath($path);
        	$path = $exRoute[0];
        	$routeParams = $exRoute[1];
        	
        	if(!isset(self::$regularRoutes[$path]))
            {
                if(is_array($dest) && isset($dest['controller']) && isset($dest['action']))
                {
                    self::$regularRoutes[$path] = array('dest' => $dest, 'routeParams' => $routeParams);
                    return true;
                }
                else
                {
                    throw new Exception('Unknown destination', E_USER_NOTICE);
                }
            }
            return false;
        
        }
        
        static function addExact($path, $dest)
        {
        	if(!isset(self::$routes[$path]))
            {
                if(is_array($dest) && isset($dest['controller']) && isset($dest['action']))
                {
                    self::$routes[$path] = array('dest' => $dest);
                    return true;
                }
                else
                {
                    throw new Exception('Unknown destination', E_USER_NOTICE);
                }
            }
        	
        }
}