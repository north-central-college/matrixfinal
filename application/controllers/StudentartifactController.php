<?php
class StudentartifactController extends Zend_Controller_Action
{

	protected $studentService;
	
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
    
   
    /**
    * JD
    * Handles the setup to display the student artifact page.  
    * Should rely on session variables for the user id. Hard-coded to user 1 for now.
    */  
    
     public function indexAction()
      {
      				  	
        	// Retrieve the form and assign it to the view
			$this->view->form = $this->getForm();
			
			
      }

      public function processAction()
       {
      		// check if login data entered
    		$request = $this->getRequest();
    	
     	    // If we don't have a POST request, go back to login 
       	 	if (!$request->isPost()) {
            	   return $this->_helper->redirector('studentartifact');
        	}

        	// Get our form and validate it
        	$form = $this->getForm();      
    		if($this->_request->isPost())
      		{
      		    $formData = $this->_request->getPost();
	            if ($form->isValid($formData)) {
	
	                // success - do something with the uploaded file
	                $uploadedData = $form->getValues();
	                $fullFilePath = $form->file->getFileName();
	                Zend_Debug::dump($uploadedData, '$uploadedData');
	                Zend_Debug::dump($fullFilePath, '$fullFilePath');
	
	                echo "done";
	                exit;
	
	            } else {
	                $form->populate($formData);
	            }
            }
            
            $this->view->form = $form;
            
            $rowset = $this->studentService->GetAllArtifacts($this->view->userInfo['userID']);        
        	$this->view->artifactinfo = $rowset;
      }    

      public function getForm()
    	{
    		return new Application_Model_ArtifactinputForm(array(
			/* don't think next two lines are needed */
    		// 'action' => 'login/process',  
    		// 'method' => 'post',
    		));
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

