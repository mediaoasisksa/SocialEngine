<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Api.php (var) 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Api_Windowlive_Api {

  protected $_authorizedURL = 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize';
  protected $_key;
  protected $_secret;
  protected $_scope;
  protected $_callback;

  public function accessTokenURL() {
    return 'https://login.microsoftonline.com/common/oauth2/v2.0/token';
  }

  public function init($scope, $callback) {
    $this->_key = Engine_Api::_()->getApi('settings', 'core')->getSetting('windowlive.apikey');
    $this->_secret = Engine_Api::_()->getApi('settings', 'core')->getSetting('windowlive.secretkey');
    $this->_scope = $scope;
    $this->_callback = $callback;
  }

  public function getAuthorizeURL($responseType = 'code') {
    $authorizedUrl = $this->_authorizedURL . '?client_id=' . $this->_key . '&response_type=' . $responseType . '&scope=' . $this->_scope . '&redirect_uri=' . $this->_callback;

    return $authorizedUrl;
  }

  public function getAccessToken($oauth_verifier) {

    $postFields = 'client_id=' . $this->_key . '&redirect_uri=' . $this->_callback . '&client_secret=' . $this->_secret . '&code=' . $oauth_verifier . '&grant_type=authorization_code';

    $request = (array) $this->http($this->accessTokenURL(), 'POST', $postFields);
    if (!isset($request['access_token'])) {
      echo Zend_Registry::get('Zend_Translate')->_("There are some problem in processing your request. Please try again after some time.");die;
    }
      
    $token = $request['access_token'];
    return $token;
  }

  public function getContacts($access_token) {

    $response = (array) $this->httprequestforcontact(http_build_query($contactsQueryParams),$access_token);
    $contacts = array();
    if (isset($response['value'])) {
      foreach ($response['value'] as $key => $contact) { 
        if ($contact->emailAddresses[0]->address) {
          $contacts[$key]['contactMail'] = $contact->emailAddresses[0]->address;
          $contacts[$key]['contactName'] = $contact->displayName;
        }
      }
    }
    return $contacts;
  }

  public function http($url, $method, $postfields = NULL) {
    $http_info = array();
    if (!empty($method))
      $header[] = 'Content-type: application/x-www-form-urlencoded';
    $ci = curl_init();
    /* Curl settings */
    //curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
    curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
    curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ci, CURLOPT_HTTPHEADER, array('Expect:'));
    curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE);
    //curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
    if (!empty($method))
      curl_setopt($ci, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ci, CURLOPT_HEADER, FALSE);

    switch ($method) {
      case 'POST':
        curl_setopt($ci, CURLOPT_POST, TRUE);
        if (!empty($postfields)) {
          curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
        }
        break;
    }

    curl_setopt($ci, CURLOPT_URL, $url);
    $response = curl_exec($ci);
    curl_close($ci);
    return json_decode($response);
  }

  public function httprequestforcontact($postfields = NULL,$access_token) {

            $service_url = 'https://graph.microsoft.com/v1.0/me/contacts';
            $curlHeaders = array (
                    'Host: graph.microsoft.com',
                    'Authorization: Bearer '.$access_token,    
            );
            $curl = curl_init($service_url);
            curl_setopt($curl, CURLOPT_HTTPHEADER,$curlHeaders);
            curl_setopt ($curl, CURLOPT_HEADER, false);
            ob_start();
            curl_exec($curl);
            curl_close($curl);
            $response = ob_get_contents();
            ob_end_clean();
            $data=json_decode($response);
            return $data;
  }

}
