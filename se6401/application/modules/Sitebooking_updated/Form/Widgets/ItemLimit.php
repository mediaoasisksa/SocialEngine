<?php

class Sitebooking_Form_Widgets_ItemLimit extends Engine_Form
{
  
  public function init()
  {   
    $this->addElement('Text', 'limit', array(
      'label' => 'Limit',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor()
      ),
      'validators' => array(
              array('Int', true),
              new Engine_Validate_AtLeast(5),
      ),
    ));
    
  }

}
