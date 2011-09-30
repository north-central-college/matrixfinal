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
        		$this->view->pagetitle = "Faculty Landing";
			$this->facultyService = new App_FacultyService();
			
			$rowset = $this->facultyService->GetPendingArtifacts(
				$this->view->userInfo['userID'],  'submitted_timestamp', 2);
			$this->view->pending = $rowset->toArray();
			
			$rowset = $this->facultyService->GetReviewedArtifacts(
			   	  $this->view->userInfo['userID'], 'submitted_timestamp', 3);
			$this->view->reviewed = $rowset->toArray();
			
		
		}
        	else 	//user is a student
        	{
			if ($sessionNamespace->userRole == 'U'){ // user is an undergraduate
				$this->view->pagetitle = "Undergraduate Student Landing";
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
			else{	// user is a graduate student
				
				$this->view->pagetitle = "Graduate Student Landing";
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
	}
	 public function openartifactAction(){
		//This will open a file chosen by the user.
		                  
	 	$this->view->artifact_id = $this->_getParam('artifact');
		// Get student service for queries
		$this->studentService = new App_StudentService();
		$result = $this->studentService->GetArtifact($this->view->artifact_id);
		$file = "uploads/s" . $result->student_id . "/" .
			$result->filename . "." . $result->media_extension;
		
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		ob_clean();
		flush();
		readfile($file);
		exit;
      }
      public function openreflectiveAction(){
		//This will open a file chosen by the user.
		                  
	 	$this->view->reflective_id = $this->_getParam('reflective');
		// Get student service for queries
		$this->studentService = new App_StudentService();
		$result = $this->studentService->GetReflectiveStatement($this->view->reflective_id);
		$file = "uploads/s" . $result->student_id . "/" .
			$result->filename . "." . $result->media_extension;
		
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		ob_clean();
		flush();
		readfile($file);
		exit;
      }
      public function standardsAction()
	{
	    $this->_helper->redirector('index', 'student');
	}
}

