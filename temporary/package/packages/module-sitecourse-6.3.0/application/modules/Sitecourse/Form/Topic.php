<?php  

class Sitecourse_Form_Topic extends Engine_Form{

	public function init(){
		$this
		->setTitle("Add a Topic")
		->setMethod('post')
		->setAttrib('class', 'global_form_box');

		// Element: course title
		$this->addElement('Text', 'title', array(
			'description' => 'Enter the title of topic. Each topic may contain multiple lessons.',
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

    	// Buttons
		$this->addElement('Button', 'submit', array(
			'label' => 'Add Topic',
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
