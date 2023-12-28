<?php  

class Sitecourse_Form_Lesson extends Engine_Form{

	public function init(){
		$this
		->setTitle("Add a Text Lesson")
		->setMethod('post')
		->setAttrib('class', 'global_form_box');

		// Element: Lesson title
		$this->addElement('Text', 'title', array(
			'label' => 'Title',
			'description' => 'Lesson Title.',
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

		// description or body for lesson
		$editorOptions = array(
      'html' => (bool) true,
      'mode' => "exact",
      'forced_root_block' => false,
      'force_p_newlines' => false,
      'image_advtab' => false,
      'toolbar1' => "ltr,rtl,undo,redo,removeformat,pastetext,|,code,link",
      'elements' => 'text',
    );

        $this->addElement('TinyMce', 'text', array(
         'label' => 'Text',
         'description' => 'Text you want to add to your Lesson',
         'required' => true,
         'allowEmpty' => false,
         'editorOptions' => $editorOptions,
         'filters' => array(
            new Engine_Filter_Censor(),
            new Engine_Filter_Html(array('AllowedTags' => true))),
     )); 
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
