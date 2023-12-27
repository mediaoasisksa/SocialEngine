<?php

class Sitebooking_Form_ServiceProvider_Contact extends Engine_Form
{
  public $_error = array();

  public function init()
  {   
    $this->setTitle('Provider Contact Details')
      ->setDescription('Save your details here.')
      ->setAttrib('name', 'provider_contact_datails');

    $this->addElement('Text', 'email', array(
      'label' => 'Email',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '63'))
      ),
      'validators' => array(
        array('EmailAddress', true),
        // array('StringLength', false, array(5, 18)),
      ),
      'autofocus' => 'autofocus',
    ));

    $this->addElement('Text', 'website', array(
      'label' => 'Website',
      'allowEmpty' => false,
      'required' => false,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '127'))
      ),
      'autofocus' => 'autofocus',
    ));

    $this->addElement('Text', 'telephone_no', array(
      'label' => "Telephone No",
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
      'validators' => array(
        array('NotEmpty', true),
        array('Regex', true, array('/^[+|1-9][0-9]{1,4}[-|1-9][0-9]{4,18}$/')),
        // array('StringLength', false, array(5, 18)),
      ),
      'description' => 'This field accept phone no. in following formats (+91-1234567890 or +911234567890 or 1234567890)'
    ));
    $this->telephone_no->getDecorator('description')->setOptions(array('placemeent' => 'PREPEND', "ESCAPE" => false));

    $this->telephone_no->getValidator('Regex')->setMessage('Invalid Format, please enter valid telephone number.');
    $this->telephone_no->getValidator('NotEmpty')->setMessage('Please complete this field - it is required.', 'isEmpty');


    // // Element: submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Submit',
      'type' => 'submit',
    ));

    
  }  
}
?>