<?php

class Sescompany_Widget_BannerController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    
    $this->view->height = $this->_getParam('height', '300px');
    $this->view->fullwidth = $this->_getParam('fullwidth', 1);
    
    // Get banner
    $bannerId = $this->_getParam('banner_id', 0);
    $this->view->banner = $banner = Engine_Api::_()->getDbtable('banners', 'core')->getBanner($bannerId);
    if( !$banner ) {
      return $this->setNoRender();
    }
  }
}
