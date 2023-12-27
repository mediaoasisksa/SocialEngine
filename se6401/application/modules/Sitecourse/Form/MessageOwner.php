<?php  

class Sitecourse_Form_MessageOwner extends Engine_Form{

    public function init(){
        $this
        ->setMethod('post')
        ->setTitle('Contact Course Owner')
        ->setDescription('Create your message with the form given below. Your message will be sent to the owner of this Course.')
        ->setAttrib('class', 'global_form_box');

        $this->addElement('Text', 'title', array(
            'label' => 'Subject',
            'allowEmpty' => true,
            'required' => false,
            'maxlength' => '63',
            'filters' => array(
                new Engine_Filter_Censor(),
                'StripTags',
                new Engine_Filter_StringLength(array('max' => '63'))
            ),
            'autofocus' => 'autofocus',
        ));

        $this->addElement('Textarea', 'body', array(
            'label' => 'Message',
            'description' => 'Lesson Title',
            'allowEmpty' => false,
            'required' => true,
            'maxlength' => '256',
            'filters' => array(
                new Engine_Filter_Censor(),
                'StripTags',
                new Engine_Filter_StringLength(array('max' => '256'))
            ),
            'autofocus' => 'autofocus',
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Send Message',
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
