<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright 2021 SocialEngine
 * @license    http://www.socialengine.com/license/
 */

/**
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright 2021 SocialEngine
 * @license    http://www.socialengine.com/license/
 */
class Employment_Form_Custom_Fields extends Fields_Form_Standard
{
  public $_error = array();

  protected $_name = 'fields';

  protected $_elementsBelongTo = 'fields';

  public function init()
  { 
    // custom employment fields
    if( !$this->_item ) {
      $employment_item = new Employment_Model_Employment(array());
      $this->setItem($employment_item);
    }
    parent::init();

    $this->removeElement('submit');
  }

  public function loadDefaultDecorators()
  {
    if( $this->loadDefaultDecoratorsIsDisabled() )
    {
      return;
    }

    $decorators = $this->getDecorators();
    if( empty($decorators) )
    {
      $this
        ->addDecorator('FormElements')
        ; //->addDecorator($decorator);
    }
  }
}
