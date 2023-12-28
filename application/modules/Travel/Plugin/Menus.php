<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Travel
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    https://socialengine.com/eula
 * @version    $Id: Menus.php 9747 2012-07-26 02:08:08Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Extensions
 * @package    Travel
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    https://socialengine.com/eula
 */
class Travel_Plugin_Menus
{
  public function canCreateTravels()
  {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }

    // Must be able to create travel listings
    if( !Engine_Api::_()->authorization()->isAllowed('travel', $viewer, 'create') ) {
      return false;
    }

    return true;
  }

  public function canViewTravels()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    // Must be able to view travel listings
    if( !Engine_Api::_()->authorization()->isAllowed('travel', $viewer, 'view') ) {
      return false;
    }

    return true;
  }

  public function onMenuInitialize_TravelGutterList($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $subject = Engine_Api::_()->core()->getSubject();
    if( $subject instanceof User_Model_User ) {
      $user_id = $subject->getIdentity();
    } else if( $subject instanceof Travel_Model_Travel ) {
      $user_id = $subject->owner_id;
    } else {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['user_id'] = $user_id;
    return $params;
  }

  public function onMenuInitialize_TravelGutterCreate($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $travel = Engine_Api::_()->core()->getSubject('travel');

    if( !$travel->isOwner($viewer) ) {
      return false;
    }

    if( !Engine_Api::_()->authorization()->isAllowed('travel', $viewer, 'create') ) {
      return false;
    }

    return true;
  }

  public function onMenuInitialize_TravelGutterEdit($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $travel = Engine_Api::_()->core()->getSubject('travel');

    if( !$travel->authorization()->isAllowed($viewer, 'edit') ) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['travel_id'] = $travel->getIdentity();
    return $params;
  }

  public function onMenuInitialize_TravelGutterDelete($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $travel = Engine_Api::_()->core()->getSubject('travel');

    if( !$travel->authorization()->isAllowed($viewer, 'delete') ) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['travel_id'] = $travel->getIdentity();
    return $params;
  }
}
