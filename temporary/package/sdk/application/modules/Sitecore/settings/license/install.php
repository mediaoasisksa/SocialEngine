<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */



class Sitecore_License_Installer extends Engine_Package_Installer_Module
{

  protected $_needToEmptyFiles = array(
    '/application/modules/#MODULE#/settings/my.sql'
  );
  protected $_installConfig = array(
    'sku' => '',
  );
  protected $_deependencyVersion = array(
  );

  public function onPreInstall()
  {
    $errorMsg = $this->_checkDeependency();
    if( !empty($errorMsg) ) {
      $this->_error($errorMsg);
      return;
    }
    $targetPackage = $this->getOperation()->getTargetPackage();
    $installConfig = array_filter($this->_installConfig);
    $this->_installConfig = array_merge(array(
//      'sku' => $targetPackage->getSku(),
      'name' => $targetPackage->getName(),
      'directory' => ucfirst($targetPackage->getName()),
      'version' => $targetPackage->getVersion(),
      'category' => 'plugin',
      'title' => $targetPackage->getTitle(),
      'description' => $targetPackage->getDescription(),
      'seaoCoreVersion' => '4.9.4',
      'baseFileName' => 'NewSeaoProduct',
      ), $installConfig);
    $PRODUCT_TYPE = $this->_installConfig['sku'];
    $PLUGIN_TITLE = $this->_installConfig['directory'];
    $PLUGIN_VERSION = $this->_installConfig['version'];
    $PLUGIN_CATEGORY = $this->_installConfig['category'];
    $PRODUCT_DESCRIPTION = $this->_installConfig['description'];
    $PRODUCT_TITLE = $this->_installConfig['title'];
    $SocialEngineAddOns_version = $this->_installConfig['seaoCoreVersion'];
    $_BASE_FILE_NAME = !empty($this->_installConfig['baseFileName']) ? $this->_installConfig['baseFileName'] : '';
    include_once APPLICATION_PATH . '/application/modules/Sitecore/settings/license/license3.php';
    parent::onPreInstall();
  }

  public function onPostInstall()
  {
    $this->_doAfterInstall();
  }

  protected function _doAfterInstall()
  {
    $domain = $_SERVER['HTTP_HOST'];
    $lan1 = strpos($domain, '192.168.');
    $lan2 = strpos($domain, '127.0.');
    if( $domain == 'localhost' || strpos($domain, 'localhost:') !== false || $lan1 !== false || $lan2 !== false ) {
      return;
    }
    foreach( $this->_needToEmptyFiles as $file ) {
      $file = APPLICATION_PATH . str_replace('#MODULE#', $this->_installConfig['directory'], $file);
      if( file_exists($file) ) {
        try {
          file_put_contents($file, '');
        } catch( Exception $e ) {
          
        }
      }
    }
  }

  protected function _checkDeependency()
  {
    return $this->_checkDeependencyVersion();
  }

  protected function _checkDeependencyVersion()
  {
    $db = $this->getDb();

    $errorMsg = '';
    $finalModules = $getResultArray = array();
    $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

    $modArray = $this->_deependencyVersion;

    foreach( $modArray as $key => $value ) {
      $isMod = $db->query("SELECT * FROM  `engine4_core_modules` WHERE  `name` LIKE  '" . $key . "'")->fetch();
      if( !empty($isMod) && !empty($isMod['version']) ) {
        $isModSupport = $this->_compareDependancyVersion($isMod['version'], $value);
        if( empty($isModSupport) ) {
          $finalModules['modName'] = $key;
          $finalModules['title'] = $isMod['title'];
          $finalModules['versionRequired'] = $value;
          $finalModules['versionUse'] = $isMod['version'];
          $getResultArray[] = $finalModules;
        }
      }
    }

    foreach( $getResultArray as $modArray ) {
      $errorMsg .= '<div class="tip"><span>Note: Your website does not have the latest version of "' . $modArray['title'] . '". Please upgrade "' . $modArray['title'] . '" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with this plugin.<br/> Please <a href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
    }
    return $errorMsg;
  }

  private function _compareDependancyVersion($databaseVersion, $checkDependancyVersion)
  {
    $f = $databaseVersion;
    $s = $checkDependancyVersion;
    if( strcasecmp($f, $s) == 0 )
      return -1;

    $fArr = explode(".", $f);
    $sArr = explode('.', $s);
    if( count($fArr) <= count($sArr) )
      $count = count($fArr);
    else
      $count = count($sArr);

    for( $i = 0; $i < $count; $i++ ) {
      $fValue = $fArr[$i];
      $sValue = $sArr[$i];
      if( is_numeric($fValue) && is_numeric($sValue) ) {
        if( $fValue > $sValue )
          return 1;
        elseif( $fValue < $sValue )
          return 0;
        else {
          if( ($i + 1) == $count ) {
            return -1;
          } else
            continue;
        }
      }
      elseif( is_string($fValue) && is_numeric($sValue) ) {
        $fsArr = explode("p", $fValue);

        if( $fsArr[0] > $sValue )
          return 1;
        elseif( $fsArr[0] < $sValue )
          return 0;
        else {
          return 1;
        }
      } elseif( is_numeric($fValue) && is_string($sValue) ) {
        $ssArr = explode("p", $sValue);

        if( $fValue > $ssArr[0] )
          return 1;
        elseif( $fValue < $ssArr[0] )
          return 0;
        else {
          return 0;
        }
      } elseif( is_string($fValue) && is_string($sValue) ) {
        $fsArr = explode("p", $fValue);
        $ssArr = explode("p", $sValue);
        if( $fsArr[0] > $ssArr[0] )
          return 1;
        elseif( $fsArr[0] < $ssArr[0] )
          return 0;
        else {
          if( $fsArr[1] > $ssArr[1] )
            return 1;
          elseif( $fsArr[1] < $ssArr[1] )
            return 0;
          else {
            return -1;
          }
        }
      }
    }
  }

}

?>
