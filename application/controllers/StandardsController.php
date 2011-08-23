<?php
class StandardsController extends Zend_Controller_Action
{   protected $studentService;
	
    public function init()
    {
        /* Initialize action controller here */
	$this->view->pagetitle = "Manage Portfolio";
    }
    
    //	establishes studentService and preloads the User Information
    public function preDispatch()
    {   
    	$this->studentService = new App_StudentService(); 	
		
    	$sessionNamespace = new Zend_Session_Namespace();
   	$this->view->userInfo = array('user_id' => $sessionNamespace->userID,
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
    	$this->view->roleInfo = $this->studentService->GetRoleInfo($this->view->userInfo['role']);
	
    	$this->view->standardsInfo = $this->studentService->GetStandardsbyProgram(
					$this->view->userInfo['role']);
    }
}