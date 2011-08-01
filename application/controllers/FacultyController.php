<?php

class FacultyController extends Zend_Controller_Action
{
	protected $facultyService;  
	
	public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {    	
	$sessionNamespace = new Zend_Session_Namespace();
	
   	$this->view->userInfo = array('userID' => $sessionNamespace->userID,
   				  'role' => $sessionNamespace->userRole,
  				  'last_name' => $sessionNamespace->userLName, 
				  'first_name' => $sessionNamespace->userFName,
                                );
                                
    	$facultyService = new App_FacultyService();                           
		
	$rowset = $facultyService->GetArtifactsForFacultyIDWithOrderAndStatus(
		$this->view->userInfo['userID'],  'submitted_timestamp', 2);              
	$this->view->user = $rowset->toArray();   
	
    }

}

