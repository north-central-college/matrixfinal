<?php
class Application_Model_SelectfacultyForm extends Zend_Form
{
     public function __construct($facultydata){
        parent::__construct();
    	$this->setMethod('post');
    	$this->setAction('/process');
	$faculty = new Zend_Form_Element_Radio('faculty');
	$faculty->setRequired(true);
	$faculty->setLabel('Faculty: ')
		->addMultiOptions($facultydata);
	$this->addElement($faculty);
    }
}