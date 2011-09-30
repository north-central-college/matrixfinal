<?php
class ArtifactlinkController extends Zend_Controller_Action
{
	

	
	public function preDispatch()
	{
	    $this->studentService = new App_StudentService(); 	
		
            $sessionNamespace = new Zend_Session_Namespace();
            $this->view->userInfo = array('userID' => $sessionNamespace->userID,
   					  'role' => $sessionNamespace->userRole,
  					  'last_name' => $sessionNamespace->userLName, 
        				  'first_name' => $sessionNamespace->userFName,
                                    );
		
	}
	
    public function init()
    {
	$this->view->pagetitle = "Link Artifact";
				
        /* Initialize action controller here */
    	
    }

    public function indexAction()
    {
	// need to get the standard and indicator from URL and all user information
    	$this->view->standard_id = $this->_getParam('standard');
	$this->view->standard_num = $this->_getParam('standardnum');
	$this->view->indicator_id = $this->_getParam('indicator');
    	$this->view->indicator_num = $this->_getParam('indicatornum');
	
    	$this->view->artRowSet =
		$this->studentService
		     ->GetAllArtifactsNotLinkedtoIndicator($this->view->userInfo['userID'],
							   $this->view->standard_id,
							   $this->view->indicator_id);

    }
}

