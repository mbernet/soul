<?php
class Controller
{
		protected $params = array();
        protected $layout = 'default';
        protected $generateCache = false;
        
 /**
         *
         * @param Array $get_vars
         * @param Array $post_vars
         * @param Array $url_vars 
         */
        public function setRequest($get_vars, $post_vars, $url_vars)
        {
        	$this->params['get'] = $get_vars;
        	$this->params['post'] = $post_vars;
        	$this->params['url'] = $url_vars;
        }
        
        public function params($type)
        {
        	if($type == 'get' || $type == 'post' || $type == 'url')
        	{
        		return $this->params[$type];
        	}
        	return false;
        }
	
        /**
         *
         * @param type $view the view to render
         * @param type $data data for the view
         */
        protected function render($view, $data = null)
        {
            $this->beforeRender();
            if($this->layout)
            {
                $this->renderWithLayout($view, $data, $this->layout);
            }
            else
            {
                $this->renderView($view, $data);
            }
        }
        
        
        private function renderWithLayout($view, $data, $layout)
        {
        	
            ob_start();
            include('app'.DS.'views'.DS.$view.'.php');
            $view_content = ob_get_clean();
            
            ob_start();
            include('app'.DS.'views'.DS.'layouts'.DS.$layout.'.php');
            if($this->generateCache)
            {
            	//$all_content = ob_get_clean();
            	ActionCache::writeCache($this->name, $this->action);
            }
            ob_flush();
        }
        
        private function renderView($view, $data)
        {
            ob_start();
            include('app'.DS.'views'.DS.$view.'.php');
            ob_flush();
        }
        
        public function manageCache()
        {
        	if (defined('DISABLE_CACHE') && DISABLE_CACHE )
        	{
        		return false;
        	}
        	if(ActionCache::isCached($this->name, $this->action, $this->cacheActions[$this->action]['expiration']))
        	{
        		ActionCache::showCache($this->name, $this->action);
        		echo "<!-- cached -->";
        		return true;
        	}
        	else
        	{
        		$this->generateCache = true;
        	}
        	
        	return false;
        }
        
        public function beforeAction()
        {    
        }
        
        public function afterAction()
        {   
        }
        
        public function beforeRender()
        {   
        }
}