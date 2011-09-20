
<?php
class FacultyevaluateController extends Zend_Controller_Action
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
	$this->view->pagetitle = "Faculty Evaluation";

    }

    public function indexAction()
    {
    	$this->view->artifact_id = $this->_getParam('artifact');
	$this->view->indicator_id = $this->_getParam('indicator');
    	$this->view->ratinginfo = $this->getRatings();
	
	$param = "indicator=" . $this->view->indicator_id .
		"artifact=" . $this->view->artifact_id;
	
    	$this->view->form = $this->getForm($param);
    }
    public function index2Action()
    {
    	$this->view->reflective_statement_id = $this->_getParam('reflective');
	$this->view->ratinginfo = $this->getRatings();
	
	$param = "reflective=" . $this->view->reflective_statement_id;
	
    	$this->view->form = $this->getForm($param);
    }
    public function getForm($param)
    {
    	return new Application_Model_SelectratingForm($this->view->ratinginfo, $param);
    }
    public function processAction()
    {  // function called after rating chosen for submit for approval process 
	$this->facultyService = new App_FacultyService();
	$this->view->standard_id = $this->_getParam('standard');
	$this->view->standard_num = $this->_getParam('standardnum');
	$this->view->artifact_id = $this->_getParam('artifact');
	$this->view->indicator_id = $this->_getParam('indicator');
	$this->view->facultyRowSet = $this->facultyService->GetFacultyByName(
					$this->_getParam('faculty'));
	
    	$this->view->faculty_id = $this->view->facultyRowSet[0]['user_id'];
	$this->facultyService->NewArtifactRating($this->view->artifact_id,
						 $this->view->indicator_id,
						 $this->view->faculty_id);
	$this->_redirect('/indicators?standard=' . $this->view->standard_id)
	           . '&standardnum=' . $this->view->standard_num;
    }
    public function getRatings()
    {
	$this->facultyService = new App_FacultyService();
	// get a list of all ratings as an array
    	$rowset = $this->facultyService->GetRatings();
 	   	
    	// get the form
	$ratingNames = array();
	$ctr = 0;
	foreach ($rowset as $row){
	   $code = $row['rating_code'];
	   $desc = $row['description'];
	   $ratingNames[$code] = $code . ": " . $desc;
	   $ctr++;
	}
    	return $ratingNames;
    }
        
    public function errorAction(){
	
    }
}

