<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Team.php 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sescompany_Model_Team extends Core_Model_Item_Abstract {

  protected $_searchTriggers = false;
  
  public function getFilePath($item = 'thumb_icon') {
  
    $file = Engine_Api::_()->getItem('storage_file', $this->{$item});
    if ($file)
      return $file->map();
  }
}
