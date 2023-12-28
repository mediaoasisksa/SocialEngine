<?php  
class Sitecourse_Form_Admin_ReportAction extends Engine_Form{

    protected $_field;

    public function init(){
        $this
        ->setTitle('Take Action')
        ->setDescription("What would you like to do with this report?")
        ->setMethod('post')
        ->setAttrib('class', 'global_form_box');

        $this->addElement('Radio', 'action', array(
            'value' =>'1',
            'multiOptions' => array(
                '0' => 'Delete Course',
                '1' => 'Disapprove Course',
                '2' => 'Disable Future Enrollments'
            ),
        ));

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
