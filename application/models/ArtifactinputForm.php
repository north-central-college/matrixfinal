<?php
class Application_Model_ArtifactinputForm extends Zend_Form
{
   public function __construct($options = null){
      parent::__construct($options);
      $this->setName('upload');
      $this->setAction("");
      $this->setAttrib('enctype', 'multipart/form-data');


     $title = new Zend_Form_Element_Text('title');
     $title->setLabel('Artifact Title')
                  ->setRequired(true)
                  ->addValidator('NotEmpty');

     $description = new Zend_Form_Element_Text('description');
     $description->setLabel('Personal Memo')
                  ->setRequired(true)
                  ->addValidator('NotEmpty');

     $file = new Zend_Form_Element_File('file');
     $file->setLabel('File')
           ->setDestination(APPLICATION_PATH . '/uploads')
           ->setRequired(true);

     $submit = new Zend_Form_Element_Submit('submit');
     $submit->setLabel('Upload');

     $this->addElements(array($title, $description, $file, $submit));
    }
}

