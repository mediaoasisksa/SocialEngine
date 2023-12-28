<?php 
class Sitebooking_Form_ServiceProvider_Overview extends Engine_Form
{
  public $_error = array();

  public function init()
  { 
  	$this->setTitle('Write Long Description')
      ->setAttrib('name', 'provider_overview');
    $user = Engine_Api::_()->user()->getViewer();
    $userLevel = Engine_Api::_()->user()->getViewer()->level_id;
   

    $this->addElement('TinyMce', 'overview', array(
      'disableLoadDefaultDecorators' => true,
      'required' => true,
      'allowEmpty' => false,
      'decorators' => array(
        'ViewHelper'
      ),
      'filters' => array(
        new Engine_Filter_Censor()),
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Submit',
      'type' => 'submit',
    ));
  }
}