<?php

class Sitecourse_Form_Report extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Report')
      ->setDescription('Do you want to report this?')
      ->setAttrib('class', 'global_form_popup')
      ;

    $this->addElement('Select', 'type', array(
      'label' => 'Type',
      'required' => true,
      'allowEmpty' => false,
      'multiOptions' => array(
        '' => '(select)',
        'spam' => 'Spam',
        'abuse' => 'Abuse',
        'inappropriate' => 'Inappropriate Content',
        'licensed' => 'Licensed Material',
        'other' => 'Other',
      ),
    ));

    $this->addElement('Textarea', 'reason', array(
      'label' => 'Description',
      'required' => true,
      'allowEmpty' => false,
      'filters' => array('StripTags'),
    ));

    $this->addElement('Hidden', 'subject');

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Submit Report',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');

  }
}
