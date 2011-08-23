<?php
class Application_Model_SelectfacultyForm extends Zend_Form
{
     public function __construct($facultydata, $param){
        parent::__construct();
    	$this->setName("selectfaculty");
	$this->setMethod('post');
    	$param = 'javascript:getData("' . $param . '")';
        $this->setAction($param);
	$faculty = new Zend_Form_Element_Radio('faculty');
	$faculty->setRequired(true);
	$faculty->setLabel('Faculty: ')
		->addMultiOptions($facultydata);
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Select');

        $this->addElements(array($faculty, $submit));
    	
    }
}