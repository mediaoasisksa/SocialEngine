<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Travel
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    https://socialengine.com/eula
 * @version    $Id: Album.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Travel
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    https://socialengine.com/eula
 */
class Travel_Model_Album extends Core_Model_Item_Collection
{
  protected $_parent_type = 'travel';

  protected $_owner_type = 'travel';

  protected $_children_types = array('travel_photo');

  protected $_collectible_type = 'travel_photo';

  public function getHref($params = array())
  {
    return $this->getTravel()->getHref($params);
  }

  public function getTravel()
  {
    return $this->getOwner();
  }

  public function getAuthorizationItem()
  {
    return $this->getParent('travel');
  }

  protected function _delete()
  {
    // Delete all child posts
    $photoTable = Engine_Api::_()->getItemTable('travel_photo');
    $photoSelect = $photoTable->select()->where('album_id = ?', $this->getIdentity());
    foreach( $photoTable->fetchAll($photoSelect) as $travelPhoto ) {
      $travelPhoto->delete();
    }

    parent::_delete();
  }
}
