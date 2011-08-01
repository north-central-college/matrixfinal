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
        /* Initialize action controller here */
    	
    }

    public function indexAction()
    {
    	$this->view->standard_id = $this->_getParam('standard');
    	
    	$this->view->indRowSet = $this->studentService->GetIndicatorsbyStandard($this->view->standard_id);

    	$this->view->indartRowSet = $this->studentService->GetAllIndicatorsArtifactsbyStandard(
    			$this->view->userInfo['userID'], $this->view->standard_id);
    }
}

