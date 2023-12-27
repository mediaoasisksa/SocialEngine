<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.Seaocores.com/license/
 * @version    $Id: Core.php 2010-11-18 9:40:21Z Seaocores $
 * @author     SocialEngineAddOns
 */
class Seaocore_Plugin_Core extends Zend_Controller_Plugin_Abstract
{

  public function routeShutdown(Zend_Controller_Request_Abstract $request)
  {
    $lightbox_type = $request->getParam('lightbox_type', null);
    if( $lightbox_type == 'photo' ) {
      $module_name = $request->getModuleName();
      $tab_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
      $request->setModuleName('seaocore');
      $request->setControllerName('photo');
      $request->setActionName('view');
      if( $module_name == 'sitealbum' ) {
        $module_name = 'album';
      }
      $request->setParam("module_name", $module_name);
      $request->setParam("tab", $tab_id);
    }
  }

  public function onRenderLayoutDefault($event)
  {
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    // Below file is included to show font-awesome icons of old version, we will remove it in further upgrades.
    // $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl . 'externals/font-awesome/css/font-awesome.min.css');
    $theme = '';
    $themeArray = $view->layout()->themes;
    if( isset($themeArray[0]) ) {
      $theme = $view->layout()->themes[0];
    }
    if( strpos($theme, 'insignia') !== false ) {
      $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl
        . 'application/modules/Seaocore/externals/styles/themes/insignia.css');
    } else if( strpos($theme, 'serenity') !== false ) {
      $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl
        . 'application/modules/Seaocore/externals/styles/themes/serenity.css');
    }
  }

  public function onCoreCommentDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if( isset($payload->parent_comment_id) && !empty($payload->resource_type) && !empty($payload->resource_id) ) {
      if( Engine_Api::_()->hasItemType($payload->resource_type) && Engine_Api::_()->getItem($payload->resource_type, $payload->resource_id) ) {
        $resource = Engine_Api::_()->getItem($payload->resource_type, $payload->resource_id);
        if( $resource->getType() != 'core_link' ) {
          $replyTable = Engine_Api::_()->getDbtable('comments', 'core');
          $replySelect = $replyTable->select()
            ->where('parent_comment_id = ?', $payload->getIdentity());
          foreach( $replyTable->fetchAll($replySelect) as $reply ) {
            $resource->comments()->removeComment($reply->comment_id);
          }
        }
      }
    }
  }

  public function onActivityCommentDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if( isset($payload->parent_comment_id) && !empty($payload->comment_id) ) {
      $replyTable = Engine_Api::_()->getDbtable('comments', 'activity');
      $replySelect = $replyTable->select()
        ->where('parent_comment_id = ?', $payload->comment_id);
      foreach( $replyTable->fetchAll($replySelect) as $reply ) {
        if( Engine_Api::_()->hasItemType('activity_comment') && Engine_Api::_()->getItem('activity_comment', $reply->comment_id) ) {
          $resource = Engine_Api::_()->getItem('activity_comment', $reply->comment_id);
          $resource->delete();
        }
      }
    }
  }

  public function onRenderLayoutDefaultSimple($event)
  {
    // Forward
    return $this->onRenderLayoutDefault($event);
  }

  public function onRenderLayoutMobileDefault($event)
  {
    // Forward
    return $this->onRenderLayoutDefault($event);
  }

  public function onRenderLayoutMobileDefaultSimple($event)
  {
    // Forward
    return $this->onRenderLayoutDefault($event);
  }

}