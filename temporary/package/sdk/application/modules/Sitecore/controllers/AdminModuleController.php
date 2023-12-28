<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminController.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecore_AdminModuleController extends Core_Controller_Action_Admin
{

  public function indexAction()
  {
    ini_set('max_execution_time', 300);
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
    $this->_setParam('plugin_title', @base64_decode($this->_getParam('plugin_title', null)));
    include_once APPLICATION_PATH . '/application/modules/Sitecore/controllers/license/license1.php';

    $session = new Zend_Session_Namespace('PURCHASED_Licences');
    if( !empty($session->licences[$product_type]['license']) ) {
      foreach( $form->getElements() as $key => $element ) {
        if( $element->getType() == 'Engine_Form_Element_Text' ) {
          $licenseElement = $element;
          break;
        }
      }
      $licenseElement->setValue($session->licences[$product_type]['license']);
    }
  }

  private function _doWeHaveOldDocument()
  {
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

    $errorMsg = '';
    $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

    $modArray = array(
      'document' => '4.8.12'
    );

    $finalModules = array();
    foreach( $modArray as $key => $value ) {
      $select = new Zend_Db_Select($db);
      $select->from('engine4_core_modules')
        ->where('name = ?', "$key")
        ->where('enabled = ?', 1);
      $isModEnabled = $select->query()->fetchObject();
      if( !empty($isModEnabled) ) {
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_modules', array('title', 'version'))
          ->where('name = ?', "$key")
          ->where('enabled = ?', 1);
        $getModVersion = $select->query()->fetchObject();

//				$isModSupport = strcasecmp($getModVersion->version, $value);
        $running_version = $getModVersion->version;
        $product_version = $value;
        $shouldUpgrade = false;
        if( !empty($running_version) && !empty($product_version) ) {
          $temp_running_verion_2 = $temp_product_verion_2 = 0;
          if( strstr($product_version, "p") ) {
            $temp_starting_product_version_array = @explode("p", $product_version);
            $temp_product_verion_1 = $temp_starting_product_version_array[0];
            $temp_product_verion_2 = $temp_starting_product_version_array[1];
          } else {
            $temp_product_verion_1 = $product_version;
          }
          $temp_product_verion_1 = @str_replace(".", "", $temp_product_verion_1);


          if( strstr($running_version, "p") ) {
            $temp_starting_running_version_array = @explode("p", $running_version);
            $temp_running_verion_1 = $temp_starting_running_version_array[0];
            $temp_running_verion_2 = $temp_starting_running_version_array[1];
          } else {
            $temp_running_verion_1 = $running_version;
          }
          $temp_running_verion_1 = @str_replace(".", "", $temp_running_verion_1);


          if( ($temp_running_verion_1 < $temp_product_verion_1) || (($temp_running_verion_1 == $temp_product_verion_1) && ($temp_running_verion_2 < $temp_product_verion_2)) ) {
            $shouldUpgrade = true;
          }
        }

        if( !empty($shouldUpgrade) ) {
          $finalModules[$key] = $getModVersion->title;
        }
      }
    }

    foreach( $finalModules as $modArray ) {
      $errorMsg .= '<div class="tip"><span style="background-color: #da5252;color:#FFFFFF;">Note: You do not have the latest version of the "' . $modArray . '". Please upgrade "' . $modArray . '" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "' . $modArray . '".<br/> Please <a class="" href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
    }

    return $errorMsg;
  }

}