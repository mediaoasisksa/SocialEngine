<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sescompany_Widget_HeaderController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $defaultoptn = array('search','miniMenu','mainMenu','logo', 'socialshare');
		$loggedinHeaderCondition = $settings->getSetting('sescompany.header.loggedin.options', $defaultoptn);
		$nonloggedinHeaderCondition = $settings->getSetting('sescompany.header.nonloggedin.options',$defaultoptn);
		
		$this->view->social_navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescompany_extra_links_menu');

		$this->view->headerview = Engine_Api::_()->sescompany()->getContantValueXML('company_header_type');
		$this->view->headerFixed = $settings->getSetting('sescompany.header.fixed', 1);
		
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    
    if($viewer_id != 0) {
      if(!in_array('mainMenu',$loggedinHeaderCondition))
        $this->view->show_menu = 0;
      else
        $this->view->show_menu = 1;
			if(!in_array('miniMenu',$loggedinHeaderCondition))
        $this->view->show_mini = 0;
      else
        $this->view->show_mini = 1;
			if(!in_array('logo',$loggedinHeaderCondition))
        $this->view->show_logo = 0;
      else
        $this->view->show_logo = 1;
        
			if(!in_array('socialshare',$loggedinHeaderCondition))
        $this->view->show_socialshare = 0;
      else
        $this->view->show_socialshare = 1;
        
			if(!in_array('search',$loggedinHeaderCondition))
        $this->view->show_search = 0;
      else
        $this->view->show_search = 1;
    } else {
      if(!in_array('mainMenu',$nonloggedinHeaderCondition))
        $this->view->show_menu = 0;
      else
        $this->view->show_menu = 1;
			if(!in_array('miniMenu',$nonloggedinHeaderCondition))
        $this->view->show_mini = 0;
      else
        $this->view->show_mini = 1;
			if(!in_array('logo',$nonloggedinHeaderCondition))
        $this->view->show_logo = 0;
      else
        $this->view->show_logo = 1;
			if(!in_array('socialshare',$nonloggedinHeaderCondition))
        $this->view->show_socialshare = 0;
      else
        $this->view->show_socialshare = 1;
			if(!in_array('search',$nonloggedinHeaderCondition))
        $this->view->show_search = 0;
      else
        $this->view->show_search = 1;	
		}
  }

}
