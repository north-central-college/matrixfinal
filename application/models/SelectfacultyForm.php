<?php
class Application_Model_SelectfacultyForm extends Zend_Form
{
     public function __construct($facultydata, $param){
        parent::__construct();
    	$this->setName("selectfaculty");
	$this->setMethod('post');
	
	$jsparam = 'javascript:confirm("Are you sure?")';
        $action = 'submitartifact/process?' . $param;
	 
        $this->setAction($action);
	$this->addAttribs(array('onSubmit'=>$jsparam));
	
	$faculty = new Zend_Form_Element_Radio('faculty');
	$faculty->setRequired(true);
	$faculty->setLabel('Faculty: ')
		->addMultiOptions($facultydata);
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Select');

        $this->addElements(array($faculty, $submit));
    	
    }
}