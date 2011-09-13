<?php
class Application_Model_ArtifactinputForm extends Zend_Form
{
   public function __construct($coursedata, $studentid){
      parent::__construct();
      $this->setName('upload');
      $this->setAttrib('enctype', 'multipart/form-data');
     // $this->setAction('javascript:getData()');  process will lose form data
     //    if this javascript code executes
      $this->setAction('studentartifact/process');
      //$this->setAction('studentartifact');
      
      $title = new Zend_Form_Element_Text('title');
      $title->setLabel('Artifact Title')
               ->setRequired(true)
               ->addValidator('NotEmpty');
   
      $description = new Zend_Form_Element_Text('description');
      $description->setLabel('Description')
                  ->setRequired(true)
                  ->addValidator('NotEmpty');

      $course = new Zend_Form_Element_Select('course');
      $course->setName('course')
             ->setLabel('Course')
             ->setRequired(true)
             ->addMultiOptions($coursedata)
             ->addValidator('NotEmpty');

      $file = new Zend_Form_Element_File('file');
      
      $file->setLabel('File')
           ->setDestination(APPLICATION_PATH . '/uploads/s' . $studentid)
           ->setRequired(true);
           
      $submit = new Zend_Form_Element_Submit('submit');
      $submit->setLabel('Upload');
      //$submit->setAttrib('disabled', 'disabled');
      
     $this->addElements(array($title, $description, $course, $file, $submit));
    }
}

