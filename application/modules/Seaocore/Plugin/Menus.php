<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Plugin_Menus
{

  public function onMenuInitialize_SeaocoreAdminMainInfotooltip($row)
  {
    return Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity');
  }

  public function onMenuInitialize_SeaocoreAdminMainLightbox($row)
  {
    return Engine_Api::_()->hasModuleBootstrap('sitecore') || Engine_Api::_()->hasModuleBootstrap('advancedactivity') || Engine_Api::_()->hasModuleBootstrap('sitealbum');
  }

  public function onMenuInitialize_SeaocoreMiniFriendRequest($row)
  {
    $shouldRender = $this->shouldSeaoMiniMenuRender();
    if( empty($shouldRender) ) {
      return;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    if( $viewer->getIdentity() ) {
      return array(
        'label' => $row->label,
        'name' => $row->name,
        'uri' => 'javascript:void(0)',
        'action' => 'friend-request',
      );
    }

    return false;
  }
  
  public function onMenuInitialize_SeaocoreMiniHome($row)
  {
    $shouldRender = $this->shouldSeaoMiniMenuRender();
    if( empty($shouldRender) ) {
      return;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    return !!$viewer->getIdentity();
  }

  //CHECKS THAT ADVANCED MINI MENU IS PLACED OR NOT IN THE HEADER
  private function shouldSeaoMiniMenuRender()
  {
    if (Zend_Registry::isRegistered('Seaocore_Widget_MenuMiniController_Render')) {
      return Zend_Registry::get('Seaocore_Widget_MenuMiniController_Render');
    }
    $pagesTable = Engine_Api::_()->getDbtable('pages', 'core');
    $headerPageId = $pagesTable->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'header')
      ->limit(1)
      ->query()
      ->fetchColumn();

    if( !empty($headerPageId) ) {
      $contentTable = Engine_Api::_()->getDbtable('content', 'core');
      $isMiniMenuExist = $contentTable->select()
        ->from('engine4_core_content', 'content_id')
        ->where('page_id = ?', $headerPageId)
        ->where('name IN (?)', ['seaocore.menu-mini', 'vertical.header'])
        ->limit(1)
        ->query()
        ->fetchColumn();

      if( !empty($isMiniMenuExist) ) {
        return TRUE;
      }
    }

    return FALSE;
  }

}