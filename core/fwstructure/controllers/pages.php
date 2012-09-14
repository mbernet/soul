<?php
/*Controller test*/
class PagesController extends AppController
{
	public function index()
        {
            $this->render('pages/index', array('1'=>'variable pasada desde controlador'));
        }
        
        public function test()
        {
        	debug($this->params);
        	$values = 'a';
           
            $this->render('pages/index',array('values' => $values));
        }
        
        
       
        
        public function view_no_layout()
        {
        	Registry::init()->model('Video')->getTotalVideos();
            //$this->layout = 'default';
            $this->render('pages/index',array('1'=> 'No layout'));
        }
        
         *  Com es fa servir el registry
}
