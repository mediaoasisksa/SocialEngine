<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Menus.php 9747 2012-07-26 02:08:08Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Employment_Plugin_Menus
{
  public function canCreateEmployments()
  {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }

    // Must be able to create employment listings
    if( !Engine_Api::_()->authorization()->isAllowed('employment', $viewer, 'create') ) {
      return false;
    }

    return true;
  }

  public function canViewEmployments()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    // Must be able to view employment listings
    if( !Engine_Api::_()->authorization()->isAllowed('employment', $viewer, 'view') ) {
      return false;
    }

    return true;
  }

  public function onMenuInitialize_EmploymentGutterList($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $subject = Engine_Api::_()->core()->getSubject();
    if( $subject instanceof User_Model_User ) {
      $user_id = $subject->getIdentity();
    } else if( $subject instanceof Employment_Model_Employment ) {
      $user_id = $subject->owner_id;
    } else {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['user_id'] = $user_id;
    return $params;
  }

  public function onMenuInitialize_EmploymentGutterCreate($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $employment = Engine_Api::_()->core()->getSubject('employment');

    if( !$employment->isOwner($viewer) ) {
      return false;
    }

    if( !Engine_Api::_()->authorization()->isAllowed('employment', $viewer, 'create') ) {
      return false;
    }

    return true;
  }

  public function onMenuInitialize_EmploymentGutterEdit($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $employment = Engine_Api::_()->core()->getSubject('employment');

    if( !$employment->authorization()->isAllowed($viewer, 'edit') ) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['employment_id'] = $employment->getIdentity();
    return $params;
  }

  public function onMenuInitialize_EmploymentGutterDelete($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $employment = Engine_Api::_()->core()->getSubject('employment');

    if( !$employment->authorization()->isAllowed($viewer, 'delete') ) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['employment_id'] = $employment->getIdentity();
    return $params;
  }
}
