<?php  

class Sitecourse_Form_Announcement extends Engine_Form {

	public function init(){
		$this
		->setMethod('post')
		->setAttrib('class', 'global_form_box')
		->setTitle('Create');
		
		$this->addElement('Text','title',array(
			'label' => 'Title',			
			'allowEmpty' => false,
			'required' => true,
			'maxlength' => '64',
			'filters' => array(
				new Engine_Filter_Censor(),
				'StripTags',
				new Engine_Filter_StringLength(array('max' => '64'))
			),
			'autofocus' => 'autofocus'
		));

		$this->addElement('Text','body',array(
			'label' => 'Body',
			'allowEmpty' => false,
			'required' => true,
			'maxlength' => '256',
			'filters' => array(
				new Engine_Filter_Censor(),
				'StripTags',
				new Engine_Filter_StringLength(array('max' => '256'))
			),
			'autofocus' => 'autofocus'
		));

		$this->addElement('Date','start_date',array(
			'label' => 'Start Date',
			'allowEmpty' => false,
			'required' => true,
		));

		$this->addElement('Date','end_date',array(
			'label' => 'End Date',
			'allowEmpty' => false,
			'required' => true,
		));
		$this->addElement('Checkbox','enable',array(
			'label' => 'Enable the Announcement',
			'checked' => true,
		));

        // Buttons
		$this->addElement('Button', 'submit', array(
			'label' => 'Create Announcement',
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
