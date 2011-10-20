
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
    {   // get rating values to populate form
	$this->view->ratinginfo = $this->getRatings();
	
	// retrieve ids to pass to form - will need to process
    	$this->view->artifact_id = $this->_getParam('aid');
	$this->view->artifact_indicator_rating_id = $this->_getParam('airid');
	$this->view->cover_id = $this->_getParam('csid');
	$this->view->cover_rating_id = $this->_getParam('csrid');
	
	$param = "aid=" . $this->view->artifact_id .
		"&airid=" . $this->view->artifact_indicator_rating_id .
		"&csid=" . $this->view->cover_id .
		"&csrid=" . $this->view->cover_rating_id;
	
    	$this->view->form = $this->getForm($param);
    }
    public function getForm($param)
    {
    	return new Application_Model_SelectratingForm($this->view->ratinginfo, $param);
    }
    public function processAction()
    {  // function called after rating chosen - need to update rating info
	
	$this->facultyService = new App_FacultyService();
	
	// get passed params
    	$this->view->artifact_id = $this->_getParam('aid');
	$this->view->artifact_indicator_rating_id = $this->_getParam('airid');
	$this->view->cover_id = $this->_getParam('csid');
	$this->view->cover_rating_id = $this->_getParam('csrid');
	
	$param = "aid=" . $this->view->artifact_id .
		"&airid=" . $this->view->artifact_indicator_rating_id .
		"&csid=" . $this->view->cover_id .
		"&csrid=" . $this->view->cover_rating_id;
	
	$this->view->ratinginfo = $this->getRatings();
	
	$form = $this->getForm($this->view->ratinginfo, $param);
    
	$request = $this->getRequest();
	if($request->isPost())
      	{
	    $formData = $request->getPost();
		     
	    if ($form->isValid($formData))
	    {
		// getting form data
		$artifact_rating = $formData['artrating'];
		$reflective_rating = $formData['refrating'];
		$artifact_rating_comment = $formData['acomment'];
		$reflective_rating_comment = $formData['rcomment'];
		$this->facultyService->UpdateRatingForArtifactAndCover(
		    $artifact_rating, $artifact_rating_comment,
		    $reflective_rating, $reflective_rating_comment,
		    $this->view->artifact_indicator_rating_id);
	    }
	}
	$this->_redirect('/landing');
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
	   $desc = $row['rating_description'];
	   $ratingNames[$code] = $code . ": " . $desc;
	   $ctr++;
	}
    	return $ratingNames;
    }
        
    public function errorAction(){
	
    }
}

