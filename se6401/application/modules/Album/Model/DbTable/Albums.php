<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Albums.php 10264 2014-06-06 22:08:42Z lucas $
 * @author     Sami
 */

/**
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Album_Model_DbTable_Albums extends Core_Model_Item_DbTable_Abstract
{
    protected $_rowClass = 'Album_Model_Album';

    public function getSpecialAlbum(User_Model_User $user, $type)
    {
        $select = $this->select()
            ->where('owner_type = ?', $user->getType())
            ->where('owner_id = ?', $user->getIdentity())
            ->where('type = ?', $type)
            ->order('album_id ASC')
            ->limit(1);

        $album = $this->fetchRow($select);

        // Create wall photos album if it doesn't exist yet
        if( null === $album ) {
            $translate = Zend_Registry::get('Zend_Translate');

            $album = $this->createRow();
            $album->owner_type = 'user';
            $album->owner_id = $user->getIdentity();
            if(engine_in_array($type,array('event','group'))){
                $album->title = $translate->_(ucfirst($type) . ' Wall Photos');
            } else {
                $album->title = $translate->_(ucfirst($type) . ' Photos');
            }
            $album->type = $type;

            $settings = Engine_Api::_()->getApi('settings', 'core');
            $album->search = (int) $settings->getSetting('album_searchable', 0);
            if( $type == 'message' ) {
                $album->search = 0;
            }

            $album->save();

            // Authorizations
            if( $type != 'message' ) {
                $auth = Engine_Api::_()->authorization()->context;
                $auth->setAllowed($album, 'everyone', 'view',    true);
                $auth->setAllowed($album, 'everyone', 'comment', true);
            }
        }

        return $album;
    }

    public function getAlbumSelect($options = array())
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewerId = $viewer->getIdentity();
        if( !empty($options['owner']) ) {
            $owner = $options['owner'];
            $ownerId = $owner->getIdentity();
        }
        $excludedLevels = array(1, 2, 3);   // level_id of Superadmin,Admin & Moderator
        $isOwnerOrAdmin = false;
        if( !empty($viewerId)
            && ((isset($ownerId) && ($ownerId == $viewerId))
                || engine_in_array($viewer->level_id, $excludedLevels)) ) {
            $isOwnerOrAdmin = true;
        }
        
        $tableAlbumName = $this->info('name');
        $tablePhotoName = Engine_Api::_()->getItemTable('album_photo')->info('name');
        
        $select = $this->select()
                      ->from($tableAlbumName)
                      ->setIntegrityCheck(false)
                      ->joinRight($tablePhotoName, "$tablePhotoName.album_id = $tableAlbumName.album_id",null)
                      ->where($tableAlbumName.'.album_id <> ?', 0)
                      ->group($tablePhotoName.'.album_id');

        if( !empty($options['search']) && is_numeric($options['search']) && !$isOwnerOrAdmin ) {
            $select->where($tableAlbumName.'.search = ?', $options['search']);
        }
        $select = Engine_Api::_()->network()->getNetworkSelect($this->info('name'), $select);
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('album.allow.unauthorized', 0)){
            return $select;
        }
        if( !empty($owner) && $owner instanceof Core_Model_Item_Abstract ) {

            $select->where($tableAlbumName.'.owner_type = ?', $owner->getType())
                  ->where($tableAlbumName.'.owner_id = ?', $ownerId)
                  ->where($tableAlbumName.'.type IS NULL OR '.$tableAlbumName.'.type NOT IN(?)',array("group","event"))
                  ->order($tableAlbumName.'.modified_date DESC');

            if( $isOwnerOrAdmin ) {
                return $select;
            }

            $isOwnerViewerLinked = true;

            if( $viewer->getIdentity() ) {
                $restrictedPrivacy = array('owner');

                $ownerFriendsIds = $owner->membership()->getMembersIds();
                if( !engine_in_array($viewerId, $ownerFriendsIds) ) {
                    array_push($restrictedPrivacy, 'owner_member');

                    $friendsOfFriendsIds = array();
                    foreach( $ownerFriendsIds as $friendId ) {
                        $friend = Engine_Api::_()->getItem('user', $friendId);
                        $friendMembersIds = $friend->membership()->getMembersIds();
                        $friendsOfFriendsIds = array_merge($friendsOfFriendsIds, $friendMembersIds);
                    }

                    if( !engine_in_array($viewerId, $friendsOfFriendsIds) ) {
                        array_push($restrictedPrivacy, 'owner_member_member');

                        $netMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
                        $viewerNetwork = $netMembershipTable->getMembershipsOfIds($viewer);
                        $ownerNetwork = $netMembershipTable->getMembershipsOfIds($owner);
                        if( !array_intersect($viewerNetwork, $ownerNetwork) ) {
                            $isOwnerViewerLinked = false;
                        }
                    }
                }
                if( $isOwnerViewerLinked ) {
                    $select->where($tableAlbumName.".view_privacy NOT IN (?)", $restrictedPrivacy);
                    return $select;
                }
            }

            $select->where($tableAlbumName.".view_privacy = ?", 'everyone');
        }

        return $select;
    }

    public function getAlbumPaginator($options = array())
    {
        return Zend_Paginator::factory($this->getAlbumSelect($options));
    }
    
    public function isAlbumExists($category_id, $categoryType = 'category_id') {
      return $this->select()
              ->from($this->info('name'), 'album_id')
              ->where($categoryType . ' = ?', $category_id)
              ->query()
              ->fetchColumn();
    }
}
