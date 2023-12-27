<?php

class Sitebooking_Model_Meta extends Core_Model_Item_Abstract {

  protected $_parent_type = 'meta';
  protected $_searchColumns = array('field_id', 'label');
  protected $_parent_is_owner = true;

}
