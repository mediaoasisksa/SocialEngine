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
class Customtheme_Widget_MemberProfileMentorServices3Controller extends Engine_Content_Widget_Abstract
{
 
  public function indexAction()
  {
      
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
    
    $this->view->approved = 1;
    // if($viewer->level_id == 6) {
    //     $tokenTable = Engine_Api::_()->getItemTable('token');
    //     $tokenTableName = $tokenTable->info('name');
        
    //      //MAKE QUERY
    //     $select = $tokenTable->select()
    //             ->from($tokenTableName, array('user_id'));
    
    //     $select = $select->where('user_id = ?', $viewer->getIdentity())
    //             ->limit(1);
    //     $row = $tokenTable->fetchRow($select);
        
    //     if($row && $row->toArray()) {
    //         $this->view->approved = 1;
    //     }
    // } else {
    //     $this->view->approved = 1;
    // }
    
    
    $params = array();
    $params['status'] = "1";
    $params['approved'] = "1";
    $params['type'] = "2";
    $params['user_id'] = $viewer->getIdentity();
    $params['limit'] = 1;
    $sql = Engine_Api::_()->getItemTable('sitebooking_ser')->getServicesSelect($params);
    $dataMentor = Engine_Api::_()->getItemTable('sitebooking_ser')->fetchRow($sql);
    $this->view->dataMentor = '';
    if($dataMentor) {
        $this->view->dataMentor = $dataMentor = $dataMentor->toArray();
        $values = array();
    $values['status'] = "1";
    $values['approved'] = "1";
    $values['type'] = "2";
    $values['user_id'] = $user->getIdentity();
    $sql = Engine_Api::_()->getItemTable('sitebooking_ser')->getServicesSelect($values);
    
    $this->view->paginator = $paginator = Zend_Paginator::factory($sql);

    if( $this->_getParam('page') )
    {
      $paginator->setCurrentPageNumber($this->_getParam('page'));
    }
     
    $items_per_page = 100; 

    $this->view->paginator = $paginator->setItemCountPerPage($items_per_page);
    } else {
        $values = array();
    $values['status'] = "1";
    $values['approved'] = "1";
    $values['type'] = "2";
    //$values['user_id'] = $user->getIdentity();
    $sql = Engine_Api::_()->getItemTable('sitebooking_ser')->getServicesSelect($values);
    
    $this->view->paginator = $paginator = Zend_Paginator::factory($sql);

    if( $this->_getParam('page') )
    {
      $paginator->setCurrentPageNumber($this->_getParam('page'));
    }
     
    $items_per_page = 100; 

    $this->view->paginator = $paginator->setItemCountPerPage($items_per_page);
    }
    if($dataMentor) {
        if($this->view->dataMentor['owner_id'] != $user->getIdentity()) {
            return $this->setNoRender();
        }
    }
    
    //print_r($this->view->dataMentor);die;
  }

}
