<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Playlists.php 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Music_Model_DbTable_Playlists extends Engine_Db_Table
{
  protected $_rowClass = 'Music_Model_Playlist';

  public function getSpecialPlaylist(User_Model_User $user, $type)
  {
    $allowedTypes = array('profile', 'wall', 'message');
    if( !engine_in_array($type, $allowedTypes) ) {
      throw new Album_Model_Exception('Unknown special album type');
    }
    //$typeIndex = array_search($type, $allowedTypes);

    $select = $this->select()
        ->where('owner_type = ?', $user->getType())
        ->where('owner_id = ?', $user->getIdentity())
        ->where('special = ?', $type)
        ->order('playlist_id ASC')
        ->limit(1);

    $playlist = $this->fetchRow($select);

    // Create if it doesn't exist yet
    if( null === $playlist ) {
      $translate = Zend_Registry::get('Zend_Translate');

      $playlist = $this->createRow();
      $playlist->owner_type = 'user';
      $playlist->owner_id = $user->getIdentity();
      $playlist->special = $type;

      if( $type == 'message' ) {
        $playlist->title = $translate->_('_MUSIC_MESSAGE_PLAYLIST');
        $playlist->search = 0;
      } else {
        $playlist->title = $translate->_('_MUSIC_DEFAULT_PLAYLIST');
        $playlist->search = 1;
      }

      $playlist->save();

      // Authorizations
      if( $type != 'message' ) {
        $auth = Engine_Api::_()->authorization()->context;
        $auth->setAllowed($playlist, 'everyone', 'view',    true);
        $auth->setAllowed($playlist, 'everyone', 'comment', true);
      }
    }

    return $playlist;
  }
  
  public function isMusicExists($category_id, $categoryType = 'category_id') {
    return $this->select()
            ->from($this->info('name'), 'playlist_id')
            ->where($categoryType . ' = ?', $category_id)
            ->query()
            ->fetchColumn();
  }
}
