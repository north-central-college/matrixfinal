
<?php
class SubmitartifactController extends Zend_Controller_Action
{
		
    public function preDispatch()
    {
		
	$sessionNamespace = new Zend_Session_Namespace();
	$this->view->userInfo = array('userID' => $sessionNamespace->userID,
			  'role' => $sessionNamespace->userRole,
 			  'last_name' => $sessionNamespace->userLName, 
      			  'first_name' => $sessionNamespace->userFName,
        );
		
	}
	
    public function init()
    {
      	$this->studentService = new App_StudentService(); 	
	$this->facultyService = new App_FacultyService();
	$this->view->pagetitle = "Submit Artifact";

    }

    public function indexAction()
    {
    	$this->view->standard_id = $this->_getParam('standard');
	$this->view->artifact_id = $this->_getParam('artifact');
	$this->view->indicator_id = $this->_getParam('indicator');
    	$this->view->facultyinfo = $this->getFaculty();
	
	$param = "standard=" . $this->view->standard_id .
		"&indicator=" . $this->view->indicator_id .
		"&artifact=" . $this->view->artifact_id;
    	$this->view->form = $this->getForm($param);
    }
    
    public function processAction()
    {  // function called after faculty chosen for submit for approval process 
	$this->facultyService = new App_FacultyService();
	$this->view->standard_id = $this->_getParam('standard');
	$this->view->standard_num = $this->_getParam('standardnum');
	$this->view->artifact_id = $this->_getParam('artifact');
	$this->view->indicator_id = $this->_getParam('indicator');
	
	$param = "standard=" . $this->view->standard_id .
		"&indicator=" . $this->view->indicator_id .
		"&artifact=" . $this->view->artifact_id;
	$this->view->facultyinfo = $this->getFaculty();	
	$form = $this->getForm($this->view->facultyinfo, $param);
    
	$request = $this->getRequest();
	if($request->isPost())
      	{
	    $formData = $request->getPost();
		     
	    if ($form->isValid($formData))
	    {
		// getting form data
		$faculty = $formData['faculty'];
	        $this->view->facultyRowSet = $this->facultyService->GetFacultyByName(
					$faculty);
	    	$this->view->faculty_id = $this->view->facultyRowSet[0]['user_id'];
		$this->facultyService->NewArtifactRating($this->view->artifact_id,
						 $this->view->indicator_id,
						 $this->view->faculty_id);
	    }
	}
	$this->_redirect('/indicators?standard=' . $this->view->standard_id)
	           . '&standardnum=' . $this->view->standard_num;
    }
    
    public function getForm($param)
    {
    	return new Application_Model_SelectfacultyForm(
				$this->view->facultyinfo, $param);
    }
    
    public function getFaculty()
    {
	$this->facultyService = new App_FacultyService();
	// get a list of all faculty as an array
    	$rowset = $this->facultyService->GetAllFaculty();
 	   	
    	// get the form
	$facultyNames = array();
	$ctr = 0;
	foreach ($rowset as $row){
	   $name = $row['name'];
	   $facultyNames[$name] = $name;
	   $ctr++;
	}
    	return $facultyNames;
    }
        
    public function errorAction(){
	
    }
}

