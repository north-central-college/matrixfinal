<?php
class StandardsController extends Zend_Controller_Action
{   protected $studentService;
	
    public function init()
    {
        /* Initialize action controller here */
    }
    
    //	establishes studentService and preloads the User Information
    public function preDispatch()
    {   
    	$this->studentService = new App_StudentService(); 	
		
    	$sessionNamespace = new Zend_Session_Namespace();
   		$this->view->userInfo = array('userID' => $sessionNamespace->userID,
   									  'role' => $sessionNamespace->userRole,
  	 								  'last_name' => $sessionNamespace->userLName, 
        							  'first_name' => $sessionNamespace->userFName,
                                );
        // Create the Artifacts table
		$rowset = $this->studentService->GetAllArtifacts($sessionNamespace->userID);        
        $this->view->artifactinfo = $rowset;
    }
    public function indexAction()
    {
    	$this->view->last_name = $this->view->userInfo['last_name'];
    	$this->view->first_name = $this->view->userInfo['first_name'];
    	$this->view->role = $this->studentService->GetRoleInfo($this->view->userInfo['role']);
    	$this->view->stand = $this->studentService->GetStandardsbyProgram(
    			$this->view->userInfo['role']);
    }
}