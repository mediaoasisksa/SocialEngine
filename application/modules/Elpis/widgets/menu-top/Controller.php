<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Elpis
 * @copyright  Copyright 2006-2022 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 2022-06-21
 */
class Elpis_Widget_MenuTopController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
   		$api = Engine_Api::_()->elpis();
	   	$api = Engine_Api::_()->elpis();
	    $this->view->contrast_mode = $api->getContantValueXML('contrast_mode') ? $api->getContantValueXML('contrast_mode') : 'dark_mode';
  }
}
