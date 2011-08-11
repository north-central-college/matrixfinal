<?php
class Application_Model_SelectfacultyForm extends Zend_Form
{
    public function init()
    {
	    $this->setMethod('post');
    	$this->setAction('user/process');
	    $faculty = new Zend_Form_Element_Radio('faculty');
	    $faculty->setLabel('Faculty: ')
		    ->addMultiOptions($this->view->facultyinfo);
    }
}