<?php
class Application_Model_SelectratingForm extends Zend_Form
{
     public function __construct($ratingdata, $param){
        parent::__construct();
	  $this->setName("selectrating");
	  $this->setMethod('post');
	  $this->setAttrib('enctype', 'multipart/form-data'); 
	  // getRatings method appears in facultyevaluate/index.phtml
	  // this method grabs the chosen ratings
	  // calling javascript loses form data, so either need to pass
	  // comments via URL (bad idea too long), or force setAction method
	  // to process without a box to double check
	  $jsparam = 'javascript:getRatings("' . $param . '")';
	  //$this->setAction($jsparam);
	  $this->setAction("localhost/mpfinal/public/facultyevaluate/process?" + $param);
	  $this->setAttrib('onSubmit', $jsparam);
	  $ratings1 = new Zend_Form_Element_Radio('artrating');
	  $ratings1->setRequired(true);
	  $ratings1->setLabel('Artifact Rating: ')
		  ->addMultiOptions($ratingdata);
  
	  $acomment = new Zend_Form_Element_Text('acomment');
	  $acomment->setLabel('Comment')
		    ->setName('acomment')
		    ->setRequired(true)
		    ->addValidator('NotEmpty');
  
		  
	  $ratings2 = new Zend_Form_Element_Radio('refrating');
	  $ratings2->setRequired(true);
	  $ratings2->setLabel('Reflective Statement Rating: ')
		  ->addMultiOptions($ratingdata);

	  $rcomment = new Zend_Form_Element_Text('rcomment');
	  $rcomment->setLabel('Comment')
		    ->setName('rcomment')
		    ->setRequired(true)
		    ->addValidator('NotEmpty');
  
	  $submit = new Zend_Form_Element_Submit('submit');
	  $submit->setLabel('Select');
	         
	  $this->addElements(array($ratings1, $acomment, $ratings2, $rcomment, $submit));
    	
    }
}