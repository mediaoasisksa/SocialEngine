<?php

class Sitebooking_Form_Service_Review extends Engine_Form
{
  public function init()
    {  
    $this->addElement('Textarea', 'review', array(
        'label' => 'Review',
        'allowEmpty' => false,
        'required' => true,
        'description' => 'Please give your review.',
        'filters' => array(
          new Engine_Filter_Censor(),
        )
      ));
      // $this->description->getDecorator("review")->setOption("placement", "append");

      // Element: submit
      $this->addElement('Button', 'submit', array(
        'label' => 'Post Review',
        'type' => 'submit',
        'value' => 'submit'
      ));

  }
}