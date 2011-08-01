<?php
class ArtifactsbyindicatorController extends Zend_Controller_Action
{
 	
    protected $studentService;
    protected $user = 2;
	
	
	
	
	
	
    public function preDispatch()
    {
		$this->studentService = new App_StudentService();
		
    }
	
    public function init()
    {
        /* Initialize action controller here */
    	
    }

    public function indexAction()
    {
    	$this->view->ind = $this->_getParam('indicator');
    	
    	$indicator_num = 
$this->studentService->GetIndicatorNumbyIndicatorId($this->view->ind);
		$this->view->indicatornum = $indicator_num;
				
		$artifacts = 
$this->studentService->GetArtifactsbyIndicator($this->view->ind, $this->user);
		$this->view->artifacts = $artifacts;
		
		var_dump($artifacts);
    	   	
    }
   }

