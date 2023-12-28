<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9806 2012-10-30 23:54:12Z matthew $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Customtheme_Widget_HeaderTipController extends Engine_Content_Widget_Abstract{
  public function indexAction(){
    $this->view->data = $this->_getParam('data');
    
       $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    if (!Engine_Api::_()->core()->hasSubject('user')) {
      $this->view->user = $user = $viewer;
    } else {
      $this->view->user = $user = Engine_Api::_()->core()->getSubject('user');
    }

    if (!$user->getIdentity()) {
      return $this->setNoRender();
    }
    
    if($viewer->getIdentity() != $user->getIdentity()) {
        return $this->setNoRender();
    }
    
    if($viewer->level_id != 6) {
       return $this->setNoRender(); 
    }
    
    if($viewer->level_id == 6) {
        $this->view->approved = 0;
        $tokenTable = Engine_Api::_()->getItemTable('token');
        $tokenTableName = $tokenTable->info('name');
        
         //MAKE QUERY
        $select = $tokenTable->select()
                ->from($tokenTableName, array('user_id'));
    
        $select = $select->where('user_id = ?', $viewer->getIdentity())
                ->limit(1);
        $row = $tokenTable->fetchRow($select);
        
        if($row && $row->toArray()) {
            $this->view->approved = 1;
        }
    } 
  }
}
