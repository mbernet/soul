<?php
class Router
{
    
        protected static $routes;
        
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
                $req = substr($_SERVER['REQUEST_URI'], strlen(DIR_ROOT) + 2);
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
        	 if(empty($req)) $req = DEFAULT_ROUTE;
             $req_array = explode('/', $req);
             
             
             if($rfind = self::search_in_routes($req))
             {
                 $action['controller']   = ucfirst($rfind['controller']).'Controller';
                 $action['file']         = strtolower($rfind['controller']);
                 $action['function']     = $rfind['action'];
                 $action['vars']         = Router::get_vars($start_position, $req_array);
             }
             else if(count($req_array) >= 2)
             {
                 $action['controller']   = ucfirst($req_array[$start_position]).'Controller';
                 $action['file']         = strtolower($req_array[$start_position]);
                 $action['function']     = $req_array[$start_position+1];
                 $action['vars']         = Router::get_vars($start_position, $req_array);
             }
             else
             {
                 trigger_error('Action not found', E_USER_ERROR);
             }
             return $action;
        }
        
        private static function search_in_routes($req)
        {
        	if(empty(self::$routes))
        	{
        		return false;
        	}
            foreach(self::$routes as $route => $destination)
            {
                if(substr($req, 0, strlen($route)) == $route)
                {
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
        
        static function add($path, $dest)
        {
            if(!isset(self::$routes[$path]))
            {
                if(is_array($dest) && isset($dest['controller']) && isset($dest['action']))
                {
                    self::$routes[$path] = $dest;
                    return true;
                }
                else
                {
                    trigger_error('Unknown destination', E_USER_NOTICE);
                }
            }
            return false;
        }
}