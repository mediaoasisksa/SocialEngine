<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Elpis
 * @copyright  Copyright 2006-2022 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Bootstrap.php 2022-06-21
 */

class Elpis_Bootstrap extends Engine_Application_Bootstrap_Abstract {

	public function __construct($application) {

    parent::__construct($application);
    $front = Zend_Controller_Front::getInstance();
    $front->registerPlugin(new Elpis_Plugin_Core);
	}
}
