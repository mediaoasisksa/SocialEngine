<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Travel
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    https://socialengine.com/eula
 * @version    $Id: Core.php 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Travel
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    https://socialengine.com/eula
 */

class Travel_Plugin_Core extends Zend_Controller_Plugin_Abstract
{
  public function routeShutdown(Zend_Controller_Request_Abstract $request) {
  
    if (substr($request->getPathInfo(), 1, 6) == "admin/") {
      return;
    }
    $moduleName = $request->getModuleName();
    $controllerName = $request->getControllerName();
    $actionName = $request->getActionName();
    if($moduleName == 'travel' && $controllerName == 'index' && $actionName == 'index' && engine_count($_GET) == 1) {
      $request->setModuleName('travel');
      $request->setControllerName('index');
      $request->setActionName('index');
      $request->setParams(array('closed' => 0));
    }
  }
  
  public function onStatistics($event)
  {
    $table   = Engine_Api::_()->getDbTable('travels', 'travel');
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), 'COUNT(*) AS count');
    $event->addResponse($select->query()->fetchColumn(0), 'travel');
  }

  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if( $payload instanceof User_Model_User ) {
      // Delete travels
      $travelTable = Engine_Api::_()->getDbtable('travels', 'travel');
      $travelSelect = $travelTable->select()->where('owner_id = ?', $payload->getIdentity());
      foreach( $travelTable->fetchAll($travelSelect) as $travel ) {
        $travel->delete();
      }
      // delete images and albums as well
    }
  }
}
