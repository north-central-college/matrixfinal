<?php
class StudentartifactController extends Zend_Controller_Action
{

	protected $studentService;
	
    public function init()
    {
       	$this->view->pagetitle = "Upload Artifacts";

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
    
   
    /**
    * JD
    * Handles the setup to display the student artifact page.  
    * Should rely on session variables for the user id. Hard-coded to user 1 for now.
    */  
    
     public function indexAction()
      {
      		// Get all courses to display on form		  	
	        $this->view->courses = $this->getCourses();
		
		// Retrieve the form and assign it to the view
		$this->view->form = $this->getForm();
    		
      }
      public function processAction(){
	 	// Get all courses to display on form		  	
	        $this->view->courses = $this->getCourses();
		// Get our form and validate it
        	$request = $this->getRequest();
    		$form = $this->getForm();      
    		// Get student service for queries
		$this->studentService = new App_StudentService();
			
		if($request->isPost())
      		{
		     $formData = $request->getPost();
		     var_dump($formData);
	             if ($form->isValid($formData)) {
			    
			// getting text data
	                $artifact_title = $formData['title'];
			$description = $formData['description'];
			
			// getting course chosen
			$course_num = $formData['course'];
			$course_id = $this->studentService->GetCourse($course_num);
			// above stmt returns array
			$course_id = $course_id['course_id'];
			
			// getting file data
			$fullFilePath = $form->file->getFileName();
			$path_title = strtok($fullFilePath, ".");
			$media_extension = strtok("\n\t");
		
			// uploading file
			$file = $form->getElement('file');
			try {
			    // upload received file(s)
		            $file->receive();
			} catch (Zend_File_Transfer_Exception $e) {
			    $e->getMessage();
			}
			// getting user id
			$userid = $this->view->userInfo['userID'];
			
			Zend_Debug::dump($artifact_title, '$artifact_title');
	                Zend_Debug::dump($description, '$description');
			Zend_Debug::dump($course_num, '$course_num');
			Zend_Debug::dump($course_id, '$course_id');
			Zend_Debug::dump($userid, '$userid');
	               
			$this->studentService->NewArtifact($artifact_title, $course_id,
				    $description, $media_extension, $userid);
	
		    } else {
	                $form->populate($formData);
	            }
		}
		$this->_helper->redirector('index');
	
      }
      public function getCourses()
      {
	$this->studentService = new App_StudentService();
	// get a list of all courses as an array
    	$rowset = $this->studentService->GetAllCourses();
 	   	
    	$courseNumbers = array('Choose One' => 'Choose One');
	$ctr = 0;
	foreach ($rowset as $row){
	   $cnum = $row['course_number'];
	   $courseNumbers[$cnum] = $cnum;
	   $ctr++;
	}
    		
    	return $courseNumbers;
      }
      
      public function getForm()
    	{
		return new Application_Model_ArtifactinputForm($this->view->courses);
    	}
     /**
     * JD
     * Handles the setup to display the artifact detail page.  Relies on information stored
     * in the URL to get details about the artifact in question.
     */  
     public function artAction()
     {
     	$this->view->singleArtifact = $this->studentService->GetArtifact(
     		$this->getRequest()->getParam('aid'));
     	
     	$this->view->curArt = $this->studentService->GetAllArtifactDetails(
     		$this->view->userInfo['userID'],  
     		$this->getRequest()->getParam('aid'));
     }
    
 
    
     public function inputArtifactAction()
     {
       $request = $this->getRequest();
       
        // Check if we have a POST request
        if (!$request->isPost()) {
            return $this->_helper->redirector('index');
        }
             
        /* don't think we need to do this again
        // Validate username and password for matching criteria
        if (!$form->isValid($request->getPost()))
        {
            // Invalid entries
            $this->view->form = $form;
            return $this->render('index'); // re-render the login form
        }     
       */
        
        // Validate against matrix database
        $artifact_title = $form->getValue('artifact_title');
        $description = $form->getValue('description');
        $media_extention = $form->getValue('media_extention');
        $this->studentService->NewArtifact($artifact_title, $description, $media_extention, $userid);
    }
    
    public function lookUpArtifact()
    {
    	$this->view->user = $this->studentService->GetArtifact(_getParam('artifact_id'));
    }
    
  
    
}

