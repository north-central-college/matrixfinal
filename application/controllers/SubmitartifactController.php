
<?php
class SubmitartifactController extends Zend_Controller_Action
{
		
    public function preDispatch()
	{
		$this->studentService = new App_StudentService(); 	
		$this->facultyService = new App_FacultyService();
		
		$sessionNamespace = new Zend_Session_Namespace();
   		$this->view->userInfo = array('userID' => $sessionNamespace->userID,
   								  'role' => $sessionNamespace->userRole,
  	 							  'last_name' => $sessionNamespace->userLName, 
        							  'first_name' => $sessionNamespace->userFName,
                                );
		
	}
	
    public function init()
    {
        /* Initialize action controller here */
    	
    }

    public function indexAction()
    {
    	$this->view->standard_id = $this->_getParam('standard');
    	
    	// get a list of all faculty as an array
    	$rowset = $this->facultyService->GetAllFaculty();
 	   	
    	// get the form
	$facultyNames = array();
	$ctr = 0;
	foreach ($rowset as $row){
	   $facultyNames[$ctr] = $row['name'];
	   $ctr++;
	}
    	$this->view->facultyinfo = $facultyNames;
	$this->view->form = new Application_Model_SelectfacultyForm($this->view->facultyinfo);
    }
 
    public function processAction()
    {
    	// insert a tuple into artifact rating
    }
}

