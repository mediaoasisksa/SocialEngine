<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Bootstrap extends Engine_Application_Bootstrap_Abstract
{

  public function __construct($application)
  {

    parent::__construct($application);
    $this->initViewHelperPath();

    $headScript = new Zend_View_Helper_HeadScript();
    if( Zend_Registry::isRegistered('StaticBaseUrl') ) {
      $headScript->appendFile(Zend_Registry::get('StaticBaseUrl')
        . 'application/modules/Seaocore/externals/scripts/core.js');
    } else {
      $headScript->appendFile('application/modules/Seaocore/externals/scripts/core.js');
    }
  }

  protected function _initFrontController()
  {
    $this->initActionHelperPath();
    Zend_Controller_Action_HelperBroker::addHelper(new Seaocore_Controller_Action_Helper_Seaocorehelper());
    /*  $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
      $view->addHelperPath(APPLICATION_PATH . '/application/modules/Seaocore/View/Helper', 'Seaocore_View_Helper'); */
    $this->initViewHelperPath();
    $front = Zend_Controller_Front::getInstance();
    $front->registerPlugin(new Seaocore_Plugin_Core);
    Engine_API::_()->seaocore()->setDefaultConstant();
  }

}
?>
