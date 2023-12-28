<?php  

class Sitecourse_Form_LessonDelete extends Engine_Form{

    public function init(){
        $this
        ->setMethod('post')
        ->setAttrib('class', 'global_form_box')
        ->setTitle('Delete Lesson')
        ->setDescription("Once a lesson is deleted it can't be recoverable. Are you sure you want to delete this lesson?");

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Delete',
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
