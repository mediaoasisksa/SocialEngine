<?php

class Sitebooking_Form_Service_Edit extends Sitebooking_Form_Service_Create
{
  protected $_item;

  public function getItem() {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item) {
    $this->_item = $item;
    return $this;
  }

  public function init()
  {
    parent::init();
    $this->setTitle('Edit Service Entry')
      ->setDescription('Edit your service here and then save the changes made.');
        
    $this->submit->setLabel('Save Changes');
  }
}

?>