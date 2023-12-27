<?php

class Sitebooking_Form_Admin_Categories_Edit extends Sitebooking_Form_Admin_Categories_Add {

  public function init() {
    parent::init();

    $this->submit->setLabel('Save Changes');
  }

}