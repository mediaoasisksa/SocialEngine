<?php

require_once realpath(dirname(__FILE__)) . '/seaocore_install.php';
class SiteModule_Installer extends Sitecore_License_Installer
{

  protected $_urls = array();

  public function init()
  {

  }

  public function onPreInstall()
  {
    $errorMsg = $this->_checkDeependencyVersion();
    if( !empty($errorMsg) ) {
      $this->_error($errorMsg);
      return;
    }
    parent::onPreInstall();
  }

  public function onInstall()
  {
    parent::onInstall();
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
      } else {
        if ($key == 'seaocore')
          $plugin = "SocialApps.tech Core Plugin";
        if($key == "siteevent")
          $plugin_title = "Advanced Events Plugin";
        elseif($key == "siteeventticket")
          $plugin_title = "Advanced Events - Events Booking, Tickets Selling & Paid Events Extension";
        elseif($key == "siteapi")
          $plugin_title = "SocialEngine REST API Plugin";
        elseif($key == "sitebooking")
          $plugin_title = "Services Booking & Appointments Plugin";
        elseif($key == "siteandroidapp")
          $plugin_title = "Android Mobile Application Plugin";

        $errorMsg .= '<div class="global_form"><div><div>The '.$plugin_title.' is not installed on your site. Please download the latest version of this plugin from your Client Area on <a href="https://socialapps.tech/" target="_blank">SocialApps.tech</a>.</div></div></div>';
      }
    }

    foreach( $getResultArray as $modArray ) {
      $errorMsg .= '<div class="tip"><span>Note: Your website does not have the latest version of "' . $modArray['title'] . '". Please upgrade "' . $modArray['title'] . '" on your website to the latest version available in your SocialApps.tech Client Area to enable its integration with this plugin.<br/> Please <a href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
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