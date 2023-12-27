<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Global.php 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class CustomTheme_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');

      $this->addElement('Text', 'customtheme_height', array(
        'label' => 'Enter the height for each block [Please enter in px]',
        //'description' => 'Enter the height for each block',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('customtheme.height', 300),
      ));

      $this->addElement('Text', 'customtheme_width', array(
        'label' => 'Enter the height for each block [Please enter in %]',
        //'description' => 'Enter the height for each block',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('customtheme.width', 33.3),
      ));
  

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}