<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Locale.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_View_Helper_MiniMenuPulldownSEAO extends Engine_View_Helper_Locale
{

  protected $_allow_mini_menus = array(
    'core_mini_profile',
    'core_mini_messages',
    'core_mini_update',
    'sitemenu_mini_friend_request',
    'sitemenu_mini_magentocart',
    'sitemenu_mini_currency',
    'sitemenu_mini_cart',
    'sitemenu_mini_notification',
    'sitemenu_mini_friend_request',
    'core_mini_messages',
    'seaocore_mini_friend_request',
  );
  protected $_actionLink = array(
    'href' => false,
    'label' => 'label',
    'options' => array()
  );

  //GET Date TIME Format.
  public function miniMenuPulldownSEAO($menuName, $menu)
  {

    $viewer = Engine_Api::_()->user()->getViewer();
    $data = array();
    $bubbleCount = 0;
    $headerTitle = 'Updates';
    $content = '';
    $bubbleType = '';
    $headerActionLink = $action = false;
    if( $menuName === 'sitemenu_mini_notification' || $menuName === 'core_mini_update' ) {
      $headerTitle = 'Notifications';
      $headerActionLink = false;
      $action = $this->view->url(array('module' => 'seaocore', 'controller' => 'mini-menu', 'action' => 'notification'), 'default', true);
      $bubbleType = 'activity_updates';
      $bubbleCount = Engine_Api::_()->getApi('updates', 'seaocore')->getNewUpdatesCount($viewer);
    }

    if( $menuName == 'sitemenu_mini_currency' ) {
      $headerTitle = false;
      $action = $this->view->url(array('module' => 'sitemulticurrency', 'controller' => 'index', 'action' => 'currency', 'isOtherModule' => 1), 'default', true);
    } else if( $menuName == 'sitemenu_mini_cart' ) {
      $headerTitle = false;
      $action = $this->view->url(array('module' => 'sitestoreproduct', 'controller' => 'product', 'action' => 'get-cart-products', 'isOtherModule' => 1), 'default', true);
    } else if( $menuName == 'sitemenu_mini_magentocart' ) {
      $headerTitle = false;
      $action = $this->view->url(array('module' => 'sitemagento', 'controller' => 'index', 'action' => 'get-magento-cart-products', 'isOtherModule' => 1), 'default', true);
    }
    if( $menuName === 'sitemenu_mini_friend_request' || $menuName === 'seaocore_mini_friend_request' ) {
      $headerTitle = 'Friend Requests';
      $bubbleType = 'friend_request_updates';
      $bubbleCount = Engine_Api::_()->getApi('updates', 'seaocore')->getNewFriendRequestCount($viewer);
      $headerActionLink = false;
      $action = $this->view->url(array('module' => 'seaocore', 'controller' => 'mini-menu', 'action' => 'friend-request', 'showSuggestion' => 0), 'default', true);
    }
    if( $menuName === 'core_mini_settings' ) {
      $headerTitle = 'Account Settings';
      $settingsNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('user_settings', array());
      if( $viewer && $viewer->getIdentity() ) {
        if( 1 === count(Engine_Api::_()->user()->getSuperAdmins()) && 1 === $viewer->level_id ) {
          foreach( $settingsNavigation as $page ) {
            if( $page instanceof Zend_Navigation_Page_Mvc && $page->getAction() == 'delete' ) {
              $settingsNavigation->removePage($page);
            }
          }
        }
      }
      $content = $this->view->navigation()->menu()->setContainer($settingsNavigation)->render();
    }
    if( $menuName === 'core_mini_messages' ) {
      $headerTitle = 'Messages';
      $bubbleType = 'message_updates';
      $headerActionLink = array(
        'href' => $this->view->url(array('action' => 'compose'), 'messages_general'),
        'label' => 'Compose New Message',
        'options' => array(
          'style' => "height:16px;float:right;",
          'class' => 'icon_message_new fa fa-plus',
          'title' => 'Compose New Message',
        )
      );
      $action = $this->view->url(array('module' => 'seaocore', 'controller' => 'mini-menu', 'action' => 'message'), 'default', true);
      $bubbleCount = Engine_Api::_()->getApi('updates', 'seaocore')->getUnreadMessageCount($viewer);
    }
    if( $menuName === 'core_mini_profile' ) {
      $action = false;
      $headerTitle = 'Account & Settings';
      $settingsNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('user_settings', array());
      if( $viewer && $viewer->getIdentity() ) {
        if( 1 === count(Engine_Api::_()->user()->getSuperAdmins()) && 1 === $viewer->level_id ) {
          foreach( $settingsNavigation as $page ) {
            if( $page instanceof Zend_Navigation_Page_Mvc && $page->getAction() == 'delete' ) {
              $settingsNavigation->removePage($page);
            }
          }
        }
      }
      $content = $this->view->partial('_mini-menu-pulldown/profile.tpl', 'seaocore', array(
        'settingsNavigation' => $settingsNavigation,
        )
      );
    }

    return array_merge(array(
      'bubbleCount' => $bubbleCount,
      'bubbleType' => $bubbleType,
      'content' => $content,
      'action' => $action,
      'header' => array(
        'title' => $headerTitle,
        'actionLink' => $headerActionLink,
      )), $data);
  }

}