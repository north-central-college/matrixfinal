<script type="text/javascript">
function show_confirm(good, bad, msg)
	{
	   var r=confirm("Are you sure?\n" + msg);
	   if (r==true)
	   {
		//return true;
		window.location = "http://localhost/mpfinal/public/" + good;
           }
	   else
	   {    //return false;
		window.location = "http://localhost/mpfinal/public/" + bad;
           }
	}
</script>

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
    	$this->view->standard_num = $this->_getParam('standardnum');
	$this->view->indRowSet = $this->studentService->GetIndicatorsbyStandard($this->view->standard_id);
	
	$this->view->indartRowSet = $this->studentService->GetAllIndicatorsArtifactsbyStandard(
    		$this->view->userInfo['userID'], $this->view->standard_id);
	
    	var_dump($this->view->indartRowSet);
    }
    
    
    public function removeAction()
    {   // remove artifact link action
	$this->studentService = new App_StudentService();
	$this->facultyService = new App_FacultyService();
	
	$this->view->standard_id = $this->_getParam('standard');
	$this->view->standard_num = $this->_getParam('standardnum');
	$this->view->artifact_id = $this->_getParam('artifact');
	$this->view->indicator_id = $this->_getParam('indicator');
	$this->view->indicator_num = $this->_getParam('indicatornum');
	$this->view->artifact_indicator_rating_id = $this->_getParam('air');
	$this->view->facultyRowSet = $this->facultyService->GetFacultyByName(
					$this->_getParam('faculty'));
	
    	$this->view->faculty_id = $this->view->facultyRowSet[0]['user_id'];
	$this->studentService->RemoveArtifactIndicatorLink($this->view->artifact_indicator_rating_id);
	$this->_redirect('/indicators?standard=' . $this->view->standard_id);
   }
    public function linkAction()
    {  // function called after artifact chosen to be linked
	$this->studentService = new App_StudentService();
	$this->view->standard_id = $this->_getParam('standard');
	$this->view->standard_num = $this->_getParam('standardnum');
	$this->view->artifact_id = $this->_getParam('artifact');
	$this->view->indicator_id = $this->_getParam('indicator');
	$this->view->cover_id = $this->_getParam('cover');
	$this->studentService->NewArtifactIndicator(
		$this->view->artifact_id, $this->view->indicator_id, $this->view->cover_id);
	$this->_redirect('/indicators?standard=' . $this->view->standard_id)
	           . '&standardnum=' . $this->view->standard_num;
    }
}

