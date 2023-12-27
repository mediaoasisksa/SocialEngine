<?php 

class Sitecourse_Form_Disapprove extends Engine_Form{

	public function init(){
		$this
		->setMethod('post')
		->setAttrib('class', 'global_form_box')
		->setTitle('Dis-Approve Course');

		$this->addElement('Text', 'reason', array(
			'label' => 'Reason',
			'description' => 'Enter the reason for the action',
			'allowEmpty' => false,
			'required' => true,
			'maxlength' => '300',
			'filters' => array(
				new Engine_Filter_Censor(),
				'StripTags',
				new Engine_Filter_StringLength(array('max' => '300'))
			),
			'autofocus' => 'autofocus',
		));

    	// Buttons
		$this->addElement('Button', 'submit', array(
			'label' => 'Dis-Approve',
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
