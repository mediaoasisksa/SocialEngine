<?php  

class Sitecourse_Form_Targetstudents extends Engine_Form{

	public function init(){
		$this->setTitle('Target Your Students')
		->setDescription("Edit the details using the editor, and then click on the 'Submit' button to save changes.");
        $user = Engine_Api::_()->user()->getViewer();
        $userLevel = Engine_Api::_()->user()->getViewer()->level_id;

		// Elements 

      
$editorOptions = array(
      'html' => (bool) true,
      'mode' => "exact",
      'forced_root_block' => false,
      'force_p_newlines' => false,
      'image_advtab' => false,
      'toolbar1' => "ltr,rtl,undo,redo,removeformat,pastetext,|,code,link",
      'elements' => 'course_benefits,prerequisites,about_instructor',
    );
        $this->addElement('TinyMce', 'course_benefits', array(
         'label' => 'Benefits',
         'description' => 'What will your students learn in this course?',
         'editorOptions' => $editorOptions,
         'filters' => array(
            new Engine_Filter_Censor(),
            new Engine_Filter_Html(array('AllowedTags' => true))),
     )); 

        //$this->course_benefits->getDecorator('Description')->setOption('placement', 'append');

        $this->addElement('TinyMce', 'prerequisites', array(
         'label' => 'Requirements',
         'description' => 'Are there any requirements or prerequisites for this course?',
         'editorOptions' => $editorOptions,
         'filters' => array(
            new Engine_Filter_Censor(),
            new Engine_Filter_Html(array('AllowedTags' => $allowedHtml))),
     ));

        
        $this->addElement('TinyMce', 'about_instructor', array(
         'label' => 'About Instructor',
         'description' => 'Add the details about the Instructor.',
         'editorOptions' => $editorOptions,
         'filters' => array(
            new Engine_Filter_Censor(),
            new Engine_Filter_Html(array('AllowedTags' => $allowedHtml))),
     ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Submit',
            'type' => 'submit',
        ));


    }

}
