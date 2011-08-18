<?php

class LandingController extends Zend_Controller_Action
{
	protected $facultyService;  
	protected $studentService;
	
	// object to hold user and role information from Zend_Session_Namespace
	protected $userInfo;
	
	public function init()
	{
	        
	}

	public function indexAction()
	{    	
		$sessionNamespace = new Zend_Session_Namespace();
	
		$this->view->userInfo = array('userID' => $sessionNamespace->userID,
					  'role' => $sessionNamespace->userRole,
					  'last_name' => $sessionNamespace->userLName, 
					  'first_name' => $sessionNamespace->userFName,
		                        );
                                
		if ($sessionNamespace->userRole == 'F')  //user is a faculty
		{
        		
			$this->facultyService = new App_FacultyService();                           
			$rowset = $facultyService->GetArtifactsForFacultyIDWithOrderAndStatus(
				$this->view->userInfo['userID'],  'submitted_timestamp', 2);              
			$this->view->user = $rowset->toArray();
			$this->view->pagetitle = "Faculty Landing";
	
		}
        	else 	//user is student
        	{
			$this->view->pagetitle = "Student Landing";
			$this->studentService = new App_StudentService();
		        //assign to roledesc value of GetRoleName function
			$userprogram = $this->studentService->GetRoleInfo($sessionNamespace->userRole);
			//create variable roledesc for index.phtml to use under current program
			$this->view->roledesc = $userprogram;
			
			//assign to uploadrows value from getUploads function
			//uploadrows holds 5 latest upload artifacts unlinked
			$uploadrows = $this->studentService->GetUploads($sessionNamespace->userID);
				//create variable uploads for index.phtml to use under recent uploads
			$this->view->uploads = $uploadrows;
			
			//assign to links value from GetLinkedArtifacts function
			//uploadrows holds 5 latest linked artifacts
			$linkedrows = $this->studentService->GetLinkedArtifacts($sessionNamespace->userID);
			//create variable links for index.phtml to use under linked artifacts
			$this->view->links = $linkedrows;
			
			//assign to submit value from GetSubmittedArtifacts function
			//uploadrows holds 5 latest submitted artifacts
			$submittedrows = $this->studentService->GetSubmittedArtifacts($sessionNamespace->userID);
			//create variable submit for index.phtml to use under recent submissions
			$this->view->submit = $submittedrows;
			
				 //assign to evaluated value from GetEvaluatedArtifacts function
			//uploadrows holds 5 latest evaluated artifacts
			$evalrows = $this->studentService->GetEvaluatedArtifacts($sessionNamespace->userID);
			//create variable evaluated for index.phtml to use under recent feedback
			$this->view->evaluated = $evalrows;
		}
	}
	public function standardsAction()
	{
	    $this->_helper->redirector('index', 'student');
	}
}

