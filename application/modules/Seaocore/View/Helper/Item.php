<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Item.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Seaocore_View_Helper_Item extends Core_View_Helper_Item
{
  public function item($type, $identity)
  {
    if (!Engine_Api::_()->hasItemType($type)) {
      return;
    }
    return parent::item($type, $identity);
  }
}
