<?php

class Sitecore_IndexController extends Core_Controller_Action_Standard
{

  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !empty($viewer) && !empty($viewer->level_id) ) {
      $level_id = $viewer->level_id;
      if( !$this->_helper->requireUser()->isValid() )
        return;
    }
    $product_type = $this->_getParam('type');
    if( ($level_id != 1) || (empty($product_type)) ) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    $type = $this->_getParam('type', null);
    if( strstr($type, "sitevideoview") || strstr($type, "sitetagcheckin") || strstr($type, "siteestore") || strstr($type, "sitereview") ) {
      $this->_setParam('plugin_title', @base64_decode($this->_getParam('plugin_title', null)));
    }
    echo "dsgsgsddhd";
    die;
    include_once APPLICATION_PATH . '/application/modules/Sitecore/controllers/license/license1.php';
  }

}