<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 201-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Locale.php 6590 2016-01-21 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_View_Helper_VideoPlayerJs extends Engine_View_Helper_Locale
{
  public function videoPlayerJs()
  {
    //GET CORE VERSION
    $coreVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core')->version;

    $checkVersion = Engine_Api::_()->seaocore()->checkVersion($coreVersion, '4.8.10');
    
    $flowplayerJs = empty($checkVersion) ?  'flashembed-1.0.1.pack.js' : 'flowplayer-3.2.13.min.js';
    $this->view->headScript()
      ->appendFile($this->view->layout()->staticBaseUrl . 'externals/flowplayer/' . $flowplayerJs)
      ->appendFile($this->view->layout()->staticBaseUrl . 'externals/html5media/html5media.min.js');
  }

}
