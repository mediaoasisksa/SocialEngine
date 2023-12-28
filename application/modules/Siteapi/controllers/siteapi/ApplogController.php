<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_AppLogController extends Core_Controller_Action_Standard {

    /*
     *  Logging Application Error 
     */
    public function writeAction() {
      $data = $this->_getAllParams();
      $isTestMode = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteapi.app.mode.' . _CLIENT_TYPE, 0);
      if(!empty($isTestMode) && !empty($data)) {
        $logArray['error'] = true;
        $logArray['error_type'] = "APP Issue";
        $logArray['message'] = $data['message'];
        $logArray['error_code'] = $data['error_code'];
        $logArray['status_code'] = 400;
        $logArray['time'] = date('c');
        $applog_dir = APPLICATION_PATH."/temporary/log";
        if (!is_dir($applog_dir)) {
          mkdir($applog_dir);
        }
        chmod($applog_dir, 0777);

        $logFile = 'Seao-' . (_CLIENT_TYPE == 'ios' ? 'iOS' : 'Android');
        $applog_file = $applog_dir. '/' . $logFile . '.log';
        if(!is_file($applog_file)){
          touch($applog_file);
        }
        chmod($applog_file, 0777);
        $logFileData = file_get_contents($applog_file);
        $logFileDataArray = !empty($logFileData) ? Zend_Json::decode($logFileData, true) : array();
        unset($logArray['body']);
        $logFileDataArray[] = $logArray;
        file_put_contents($applog_file, Zend_Json::encode($logFileDataArray));
      }
     return $this->_helper->json(array('status' => true));
   }
}
