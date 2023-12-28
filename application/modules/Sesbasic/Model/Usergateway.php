<?php

class Sesbasic_Model_Usergateway extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = false;

  protected $_modifiedTriggers = false;

  /**
   * @var Engine_Payment_Plugin_Abstract
   */
  protected $_plugin;

  /**
   * Get the payment plugin
   *
   * @return Engine_Payment_Plugin_Abstract
   */
  public function getPlugin($class)
  {
    if( null === $this->_plugin ) {
    ///  $class = 'Sesbasic_Plugin_Gateway_User_PayPal';
      Engine_Loader::loadClass($class);
      $plugin = new $class($this);
      if( !($plugin instanceof Engine_Payment_Plugin_Abstract) ) {
        throw new Engine_Exception(sprintf('Payment plugin "%1$s" must ' .
            'implement Engine_Payment_Plugin_Abstract', $class));
      }
      $this->_plugin = $plugin;
    }
    return $this->_plugin;
  }

  /**
   * Get the payment gateway
   *
   * @return Engine_Payment_Gateway
   */
  public function getGateway($class) {

    return $this->getPlugin($class['plugin'])->getGateway($class['is_sponsorship']);
  }

  /**
   * Get the payment service api
   *
   * @return Zend_Service_Abstract
   */
  public function getService()
  {
    return $this->getPlugin()->getService();
  }
}
