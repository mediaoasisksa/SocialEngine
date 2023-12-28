<?php

/**
 * SocialEngine
 *
 * @copyright  Copyright 2006-2017 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Seaocore_Service_Curl extends Zend_Service_Abstract
{

  private $url;
  private $token;

  public function __construct()
  {
    
  }

  public function setURl($url)
  {
    $this->url = $url;
    return $this;
  }

  public function setToken($token)
  {
    $this->token = $token;
    return $this;
  }

  public function get($options = array())
  {
    return $this->call(Zend_Http_Client::GET, $options);
  }

  public function post($options = array())
  {
    return $this->call(Zend_Http_Client::POST, $options);
  }

  private function call($method, $options = array())
  {

    $uri = $this->url;

    try {
      $client = $this->getHttpClient()
        ->resetParameters()
        ->setMethod($method)
        ->setConfig(array(
          'timeout' => 60,
          'adapter' => 'Zend_Http_Client_Adapter_Curl',
          'curloptions' => array(
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        )))
        ->setUri($uri);

      if( $this->token ) {
        $client->setHeaders('X-Auth-Token', $this->token);
      }

      switch( $method ) {
        case Zend_Http_Client::GET:
          $client->setParameterGet($options);
          break;
        case Zend_Http_Client::POST:
          $client->setParameterPost($options);
          break;
      }

      $response = $client->request();
      $responseData = Zend_Json::decode($response->getBody(), Zend_Json::TYPE_ARRAY);
    } catch( Exception $e ) {
      if( Zend_Registry::isRegistered('Zend_Log') && ($log = Zend_Registry::get('Zend_Log')) instanceof Zend_Log ) {
        $log->log($e->__toString(), Zend_Log::CRIT);
      }
      return false;
    }


    if( isset($responseData['error']) ) {
      if( $responseData['message'] == '404 Not Found' ) {
        return false;
      }
      throw new Exception($responseData['message']);
    }

    return $responseData;
  }

}