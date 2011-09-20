<?php
class Application_Model_SelectratingForm extends Zend_Form
{
     public function __construct($ratingdata, $param){
        parent::__construct();
    	$this->setName("selectrating");
	$this->setMethod('post');
	// getRatings method appears in facultyevaluate/index.phtml 
    	$param = 'javascript:getRatings("' . $param . '")';
        $this->setAction($param);
	$ratings = new Zend_Form_Element_Radio('Ratings');
	$ratings->setRequired(true);
	$ratings->setLabel('Rating: ')
		->addMultiOptions($ratingdata);
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Select');

        $this->addElements(array($ratings, $submit));
    	
    }
}