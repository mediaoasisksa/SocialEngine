<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Bizlist
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Album.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Bizlist
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Bizlist_Model_Album extends Core_Model_Item_Collection
{
  protected $_parent_type = 'bizlist';

  protected $_owner_type = 'bizlist';

  protected $_children_types = array('bizlist_photo');

  protected $_collectible_type = 'bizlist_photo';

  public function getHref($params = array())
  {
    return $this->getBizlist()->getHref($params);
  }

  public function getBizlist()
  {
    return $this->getOwner();
  }

  public function getAuthorizationItem()
  {
    return $this->getParent('bizlist');
  }

  protected function _delete()
  {
    // Delete all child posts
    $photoTable = Engine_Api::_()->getItemTable('bizlist_photo');
    $photoSelect = $photoTable->select()->where('album_id = ?', $this->getIdentity());
    foreach( $photoTable->fetchAll($photoSelect) as $bizlistPhoto ) {
      $bizlistPhoto->delete();
    }

    parent::_delete();
  }
}
