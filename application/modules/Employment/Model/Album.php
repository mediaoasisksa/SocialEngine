<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Album.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Employment_Model_Album extends Core_Model_Item_Collection
{
  protected $_parent_type = 'employment';

  protected $_owner_type = 'employment';

  protected $_children_types = array('employment_photo');

  protected $_collectible_type = 'employment_photo';

  public function getHref($params = array())
  {
    return $this->getEmployment()->getHref($params);
  }

  public function getEmployment()
  {
    return $this->getOwner();
  }

  public function getAuthorizationItem()
  {
    return $this->getParent('employment');
  }

  protected function _delete()
  {
    // Delete all child posts
    $photoTable = Engine_Api::_()->getItemTable('employment_photo');
    $photoSelect = $photoTable->select()->where('album_id = ?', $this->getIdentity());
    foreach( $photoTable->fetchAll($photoSelect) as $employmentPhoto ) {
      $employmentPhoto->delete();
    }

    parent::_delete();
  }
}
