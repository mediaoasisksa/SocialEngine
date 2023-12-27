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
class Seaocore_Installer extends Engine_Package_Installer_Module
{

  function onInstall()
  {
    $db = $this->getDb();
    $db->query("INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `enabled`, `custom`, `order`) VALUES ('seaocore_mini_friend_request', 'seaocore', 'Friend Requests', 'Seaocore_Plugin_Menus', '', 'core_mini', '1', '0', '3');");

    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_modules')->where( "name = 'core'" )->where('enabled = ?', 1);
    $coreModule = $select->query()->fetchObject();
    if ( !empty($coreModule) && isset($coreModule->version) ) {
      $result = $this->checkVersion(  $coreModule->version, '6.0.0' );
      if ( $result != 0 ) {
        // make database changes now
        $select = new Zend_Db_Select($db);
        $result = $select->from( 'engine4_core_menuitems', array('id', 'params') )->query()->fetchAll();
        if ( !empty($result) ) {
          foreach( $result as $singleResult ) {
            if ( !empty($singleResult) ) {
              if ( !empty($singleResult['params']) ) {
                try {
                  $decodeJsonData = json_decode($singleResult['params'], true);
                  if ( !empty($decodeJsonData['icon']) && strpos( $decodeJsonData['icon'], 'fa fa-') === false && strpos( $decodeJsonData['icon'], 'fa-') !== false ) {
                    $decodeJsonData['icon'] = 'fa ' . $decodeJsonData['icon'];
                    $decodeJsonData = json_encode($decodeJsonData);
                    $db->query( "UPDATE `engine4_core_menuitems` SET params = '$decodeJsonData' WHERE id = ".$singleResult['id'] . ";");
                  }
                } catch (Exception $e) {
                  die(" Exception " . $e);
                }
              }
            }
          }
        }
      }else{

          return $this->_error('<div class="global_form"><div><div>THe version is not compatiable with versions lower than 6.0.0 of Social Engine please contact support.</div></div></div>');
      }
    }
    parent::onInstall();
  }

  private function checkVersion($databaseVersion, $checkDependancyVersion)
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
