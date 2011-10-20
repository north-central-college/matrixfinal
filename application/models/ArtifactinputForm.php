<?php
class Application_Model_ArtifactinputForm extends Zend_Form
{
   public function __construct($coursedata, $studentid){
      parent::__construct();
      $this->setName('upload');
      $this->setMethod('post');
      $this->setAttrib('enctype', 'multipart/form-data');
     
      $jsparam = 'javascript:confirm("Are you sure?")';
      $action = 'studentartifact/process';
	 
      $this->setAction($action);
      $this->addAttribs(array('onSubmit'=>$jsparam));
	  
      $atitle = new Zend_Form_Element_Text('artifacttitle');
      $atitle->setLabel('Artifact Title')
            ->setRequired(true)
            ->addValidator('NotEmpty');
   
      $adescription = new Zend_Form_Element_Text('artifactdescription');
      $adescription->setLabel('Description')
                  ->setRequired(true)
                  ->addValidator('NotEmpty');

      $course = new Zend_Form_Element_Select('course');
      $course->setName('course')
             ->setLabel('Course')
             ->setRequired(true)
             ->addMultiOptions($coursedata)
             ->addValidator('NotEmpty');

      $ctitle = new Zend_Form_Element_Text('covertitle');
      $ctitle->setLabel('Cover Sheet Title')
            ->setRequired(true)
            ->addValidator('NotEmpty');
   
      $cdescription = new Zend_Form_Element_Text('coverdescription');
      $cdescription->setLabel('Cover Sheet Description')
                  ->setRequired(true)
                  ->addValidator('NotEmpty');

      $afile = new Zend_Form_Element_File('artifactfile');
      
      $afile->setLabel('Artifact File')
           ->setDestination(APPLICATION_PATH . '/uploads/s' . $studentid)
           ->setRequired(true);
      
      $cfile = new Zend_Form_Element_File('coverfile');
      
      $cfile->setLabel('Cover Sheet File')
           ->setDestination(APPLICATION_PATH . '/uploads/s' . $studentid)
           ->setRequired(true);
           
      $submit = new Zend_Form_Element_Submit('submit');
      $submit->setLabel('Upload');
      
      $this->addElements(array($atitle, $adescription, $afile,
                               $ctitle, $cdescription, $cfile,
                               $course, $submit));
    }
}

