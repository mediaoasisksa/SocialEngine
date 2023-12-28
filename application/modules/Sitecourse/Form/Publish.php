<?php 

class Sitecourse_Form_Publish extends Engine_Form{

	public function init(){
		$this
		->setMethod('post')
		->setAttrib('class', 'global_form_box')
		->setDescription('Are you sure you want to submit your course for admin approval.You will be notified about your course approval/disapproval');
    	// Buttons
		$this->addElement('Button', 'submit', array(
			'label' => 'Submit',
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