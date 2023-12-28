<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9831 2012-11-27 20:42:43Z jung $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Core_Widget_AdminMenuMainController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('core_admin_main');

    foreach ($this->view->navigation->getPages() as $key => $page)
    {
      // Code to hide Wibiya from admin panel without removing it from DB
      // ****** START CODE ******
      if ('Settings' == $page->getLabel() )
      {
        foreach($page->getPages() as $page)
        {
          if('menu_core_admin_main_settings core_admin_main_wibiya' == $page->getClass())
          {
            $page->setVisible(false);
          }
        }
      }
      // ****** END CODE ******
      
      if ('Plugins' == $page->getLabel() && 0 == count($page->getPages()))
      {
        $page->setVisible(false);        
      }
      
      if ('Plugins' == $page->getLabel() && Engine_Api::_()->user()->getViewer()->getIdentity() && Engine_Api::_()->user()->getViewer()->level_id == 2 ) {
           
          foreach($page->getPages() as $page)
            { $page->setVisible(false);
              if('menu_core_admin_main_plugins core_admin_main_plugins_sitestore' == $page->getClass() || 'menu_core_admin_main_plugins core_admin_main_plugins_yndynamicform' ==  $page->getClass() )
              {
                //$page->setVisible(true);
              }
              
            }
      }
      
      if (Engine_Api::_()->user()->getViewer()->getIdentity() && Engine_Api::_()->user()->getViewer()->level_id == 2 ) {
          if ('Manage' == $page->getLabel() )
          {
            foreach($page->getPages() as $page)
            {
              if('menu_core_admin_main_manage core_admin_main_manage_packages' == $page->getClass() || 'menu_core_admin_main_manage core_admin_main_manage_levels' ==  $page->getClass() || 'menu_core_admin_main_manage core_admin_main_manage_networks' ==  $page->getClass())
              {
                $page->setVisible(false);
              }
              
              
              
            }
          }
      }
    }

  }
}
