<?php  

class Sitecourse_Form_Doclesson extends Engine_Form{

	public function init(){
		$this
		->setMethod('post')
		->setTitle("Add a Document Lesson")
		->setAttrib('enctype', 'multipart/form-data')
		->setAttrib('class', 'global_form_box');

		// Element: Lesson title
		$this->addElement('Text', 'title', array(
			'label' => 'Title',
			'description' => 'Lesson Title',
			'allowEmpty' => false,
			'required' => true,
			'maxlength' => '63',
			'filters' => array(
				new Engine_Filter_Censor(),
				'StripTags',
				new Engine_Filter_StringLength(array('max' => '63'))
			),
			'autofocus' => 'autofocus',
		));

		 $this->addElement('File','filename', array(
            'label' => 'Choose the document',
            'required' => true,
          ));
         $this->filename->addValidator('Extension', false, 'doc,docx,pdf,txt');
		
    	// Buttons
		$this->addElement('Button', 'submit', array(
			'label' => 'Add Lesson',
			'type' => 'submit',
			'ignore' => true,
			'decorators' => array('ViewHelper')
		));

		$this->addElement('Cancel', 'cancel', array(
			'label' => 'cancel',
			'link' => true,
			'prependText' => ' or ',
			'href' => '',
			'onClick'=> 'javascript:parent.Smoothbox.close();',
			'decorators' => array(
				'ViewHelper'
			)
		));
		$this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
		$button_group = $this->getDisplayGroup('buttons');
	}
}


?>
