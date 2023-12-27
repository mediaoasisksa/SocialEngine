<?php


class Sitecourse_Form_Admin_Manage_Edit extends Engine_Form {

  public function init() {

    $this
            ->setTitle('Edit Page')
            ->setDescription('Edit the Page below, and then click "Save Changes". You can change various parameters like payment status, approval status, etc.');

    // Element: title_dummy
    $this->addElement('Dummy', 'title_dummy', array(
        'label' => 'Title',
    ));
    $this->getElement('title_dummy')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));

    if (Engine_Api::_()->sitecourse()->hasPackageEnable()) {

      // Element: package_title
      $this->addElement('Dummy', 'package_title', array(
          'label' => 'Package',
      ));
      $this->getElement('package_title')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));
      // Element: status
      $this->addElement('select', 'status', array(
          'label' => 'Payment Status',
          'multiOptions' => array(
              'initial' => 'No',
              'active' => 'Yes',
              'pending' => 'Pending',
              'overdue' => 'Overdue',
              'refunded' => 'Refunded',
              'cancelled' => 'Cancelled',
          ),
      ));
    }

    // Element: status_page
    $this->addElement('select', 'status_page', array(
        'label' => 'Status',
    ));



    // Element: closed
    $this->addElement('select', 'closed', array(
        'label' => 'Closed',
        'multiOptions' => array("0" => "No", "1" => "Yes"),
        'value' => array("0"),
    ));

    // Element: featured
    $this->addElement('select', 'featured', array(
        'label' => 'Featured',
        'multiOptions' => array("0" => "No", "1" => "Yes"),
        'value' => array("0"),
    ));

    // Element: sponsored
    $this->addElement('select', 'sponsored', array(
        'label' => 'Sponsored',
        'multiOptions' => array("0" => "No", "1" => "Yes"),
        'value' => array("0"),
    ));

    // Element: execute
    $this->addElement('Button', 'execute', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper'),
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'prependText' => ' or ',
        'ignore' => true,
        'link' => true,
        'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index', 'id' => null)),
        'decorators' => array('ViewHelper'),
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array('execute', 'cancel'), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper',
        )
    ));
  }

}

?>