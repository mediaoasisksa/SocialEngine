<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Bizlist
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Menus.php 9747 2012-07-26 02:08:08Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Extensions
 * @package    Bizlist
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Bizlist_Plugin_Menus
{
  public function canCreateBizlists()
  {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }

    // Must be able to create businesses
    if( !Engine_Api::_()->authorization()->isAllowed('bizlist', $viewer, 'create') ) {
      return false;
    }

    return true;
  }

  public function canViewBizlists()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    // Must be able to view businesses
    if( !Engine_Api::_()->authorization()->isAllowed('bizlist', $viewer, 'view') ) {
      return false;
    }

    return true;
  }

  public function onMenuInitialize_BizlistGutterList($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $subject = Engine_Api::_()->core()->getSubject();
    if( $subject instanceof User_Model_User ) {
      $user_id = $subject->getIdentity();
    } else if( $subject instanceof Bizlist_Model_Bizlist ) {
      $user_id = $subject->owner_id;
    } else {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['user_id'] = $user_id;
    return $params;
  }

  public function onMenuInitialize_BizlistGutterCreate($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $bizlist = Engine_Api::_()->core()->getSubject('bizlist');

    if( !$bizlist->isOwner($viewer) ) {
      return false;
    }

    if( !Engine_Api::_()->authorization()->isAllowed('bizlist', $viewer, 'create') ) {
      return false;
    }

    return true;
  }

  public function onMenuInitialize_BizlistGutterEdit($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $bizlist = Engine_Api::_()->core()->getSubject('bizlist');

    if( !$bizlist->authorization()->isAllowed($viewer, 'edit') ) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['bizlist_id'] = $bizlist->getIdentity();
    return $params;
  }

  public function onMenuInitialize_BizlistGutterDelete($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $bizlist = Engine_Api::_()->core()->getSubject('bizlist');

    if( !$bizlist->authorization()->isAllowed($viewer, 'delete') ) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['bizlist_id'] = $bizlist->getIdentity();
    return $params;
  }
}
