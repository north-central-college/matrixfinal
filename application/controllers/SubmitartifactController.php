
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
	$this->view->artifact_id = $this->_getParam('artifactid');
	$this->view->indicator_id = $this->_getParam('indicator');
    	$this->view->facultyinfo = $this->getFaculty();
	
	$param = "standard=" . $this->view->standard_id .
		"&indicator=" . $this->view->indicator_id .
		"&artifactid=" . $this->view->artifact_id;
    	$this->view->form = $this->getForm($param);
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

