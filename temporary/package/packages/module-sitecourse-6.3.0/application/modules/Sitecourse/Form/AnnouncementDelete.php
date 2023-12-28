<?php  

class Sitecourse_Form_AnnouncementDelete extends Engine_Form{

    public function init(){
        $this
        ->setMethod('post')
        ->setAttrib('class', 'global_form_box')
        ->setTitle("Delete Announcement")
        ->setDescription("Are You Sure You Want To Delete The Announcement?");

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


?>
