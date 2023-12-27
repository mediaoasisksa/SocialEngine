<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	  Sitepage
 * @copyright  Copyright 2017-2018 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: install.php 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
$moduleInfo = include realpath(dirname(__FILE__)) . '/manifest.php';

$path = APPLICATION_PATH . '/application/modules/Sitecore/settings/license/install.php';
if( empty($moduleInfo['package']['sku']) && file_exists($path) ) {
  require_once $path;
} else if( !class_exists('Sitecore_License_Installer')) {

  class Sitecore_License_Installer extends Engine_Package_Installer_Module
  {

    protected $_needToEmptyFiles = array(
      '/application/modules/#MODULE#/settings/my.sql'
    );

    public function onPreInstall()
    {
      $moduleInfo = include realpath(dirname(__FILE__)) . '/manifest.php';
      if( empty($moduleInfo['package']['sku']) ) {
        return $this->checkForSeaoCorePlugin();
      }
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

    private function checkForSeaoCorePlugin()
    {
      $db = $this->getDb();
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_modules')
        ->where('name = ?', 'seaocore');
      $seaoCoreModule = $select->query()->fetchObject();
      if( empty($seaoCoreModule) ) {
        return $this->_error('<div class="global_form"><div><div>The SocialEngineAddOns Core Plugin is not installed on your site. Please download the latest version of this FREE plugin from your Client Area on <a href="http://www.socialengineaddons.com" target="_blank">SocialEngineAddOns</a>.</div></div></div>');
      }
      if( !$seaoCoreModule->enabled ) {
        return $this->_error('<div class="global_form"><div><div>The SocialEngineAddOns Core Plugin is not enabled on your site. Please enable it or download the latest version of this FREE plugin from your Client Area on <a href="http://www.socialengineaddons.com" target="_blank">SocialEngineAddOns</a>.</div></div></div>');
      }
      $SocialEngineAddOns_version = '4.9.4p6';
      return $this->_error('<div class="global_form"><div><div> The version of the SocialEngineAddOns Core Plugin on your website is less than the minimum required version: ' . $SocialEngineAddOns_version . '. Please download the latest version of this FREE plugin from your Client Area on <a href="http://www.socialengineaddons.com" target="_blank">SocialEngineAddOns</a> and upgrade it on your website.</div></div></div>');
    }

  }

}
?>
