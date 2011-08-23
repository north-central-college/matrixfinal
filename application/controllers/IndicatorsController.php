<?php
class IndicatorsController extends Zend_Controller_Action
{
    public function preDispatch()
    {
	$this->studentService = new App_StudentService(); 	
	$sessionNamespace = new Zend_Session_Namespace();
   	$this->view->userInfo = array('userID' => $sessionNamespace->userID,
   		  'role' => $sessionNamespace->userRole,
  		  'last_name' => $sessionNamespace->userLName, 
		  'first_name' => $sessionNamespace->userFName,
                 );
    }
	
    public function init()
    {
       	$this->view->pagetitle = "View Indicators";
    }

    public function indexAction()
    {
	$this->view->standard_id = $this->_getParam('standard');
    	
	$this->view->indRowSet = $this->studentService->GetIndicatorsbyStandard($this->view->standard_id);
	
	$this->view->indartRowSet = $this->studentService->GetAllIndicatorsArtifactsbyStandard(
    		$this->view->userInfo['userID'], $this->view->standard_id);
    	
    }
    
    public function processAction()
    {
	$this->facultyService = new App_FacultyService();
	$this->view->standard_id = $this->_getParam('standard');
	$this->view->artifact_id = $this->_getParam('artifactid');
	$this->view->indicator_id = $this->_getParam('indicator');
	$this->view->facultyRowSet = $this->facultyService->GetFacultyByName(
					$this->_getParam('faculty'));
	
    	$this->view->faculty_id = $this->view->facultyRowSet[0]['user_id'];
	$this->facultyService->NewArtifactRating($this->view->artifact_id,
						 $this->view->indicator_id,
						 $this->view->faculty_id);
	$this->_redirect('/indicators?standard=' . $this->view->standard_id);
    }
    public function removeAction()
    {
	$this->studentService = new App_StudentService();
	$this->facultyService = new App_FacultyService();
	
	$this->view->standard_id = $this->_getParam('standard');
	$this->view->artifact_id = $this->_getParam('artifactid');
	$this->view->indicator_id = $this->_getParam('indicator');
	$this->view->facultyRowSet = $this->facultyService->GetFacultyByName(
					$this->_getParam('faculty'));
	
    	$this->view->faculty_id = $this->view->facultyRowSet[0]['user_id'];
	$this->studentService->RemoveArtifactIndicatorLink($this->view->artifact_id,
						 $this->view->indicator_id);
	$this->_redirect('/indicators?standard=' . $this->view->standard_id);
	
   }
}

