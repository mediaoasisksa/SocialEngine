<?php  

class Sitecourse_Form_Courseoverview extends Engine_Form{
	public function init(){
		$this->setTitle('Overview')
		->setDescription("Edit the Overview for your Course using the editor below, and then click the “Save Overview” button to save changes.")
		->setAttrib('name', 'sitcourse_courseoverview');

		$editorOptions = array(
			'html' => true,
			// 'mode' => "exact",
			// 'forced_root_block' => false,
			// 'force_p_newlines' => false,
			'image_advtab' => false,
			'toolbar1' => "ltr,rtl,undo,redo,removeformat,pastetext,|,code,link",
			'elements' => 'overview',
		);

		$this->addElement('TinyMce', 'overview', array(
			'editorOptions' => $editorOptions,
			'filters' => array(
				new Engine_Filter_Censor(),
				new Engine_Filter_Html(array('AllowedTags' => true))),
		)); 
		// Element: submit
		$this->addElement('Button', 'submit', array(
			'label' => 'Save Overview',
			'type' => 'submit',
		));
	}
}
?>
