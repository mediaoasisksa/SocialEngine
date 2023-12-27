<?php

class Seaocore_Widget_MenuMiniController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    Zend_Registry::set('Seaocore_Widget_MenuMiniController_Render', 1);
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->navigation = $navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('core_mini');
    Zend_Registry::set('Seaocore_Widget_MenuMiniController_Render', 0);
//    $count = count($navigation);
//    foreach( $navigation->getPages() as $item ) {
//      $item->setOrder(--$count);
//    }

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->notificationOnly = $request->getParam('notificationOnly', false);
    $this->view->updateSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.notificationupdate');
    $this->view->showIcons = $this->_getParam('show_icons', 1);



    $this->view->changeMyLocation = $this->_getParam('changeMyLocation', 0);
    $this->view->changeMyLocationPosition = $this->_getParam('changeMyLocationPosition', 0);
    $this->view->locationbox_width = $this->_getParam('sitemenu_location_box_width', 275);
    $this->view->pullDownMiniMenus = array(
      'core_mini_profile',
      'core_mini_messages',
      'sitemenu_mini_friend_request',
      'sitemenu_mini_magentocart',
      'sitemenu_mini_currency',
      'sitemenu_mini_cart',
      'sitemenu_mini_notification',
      'sitemenu_mini_friend_request',
      'core_mini_messages',
      'core_mini_update',
      'seaocore_mini_friend_request',
      'core_mini_settings',
    );
  }

}