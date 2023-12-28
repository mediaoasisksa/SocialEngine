<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Global.php 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Classified_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');


    $this->addElement('Text', 'classified_page', array(
      'label' => 'Listings Per Page',
      'description' => 'How many classified listings will be shown per page? (Enter a number between 1 and 999)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('classified.page', 10),
    ));
      $this->addElement('Radio', 'classified_allow_unauthorized', array(
          'label' => 'Make unauthorized classified searchable?',
          'description' => 'Do you want to make a unauthorized classifieds searchable? (If set to no, classifieds that are not authorized for the current user will not be displayed in the classified search results and widgets.)',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('classified.allow.unauthorized',0),
          'multiOptions' => array(
              '1' => 'Yes',
              '0' => 'No',
          ),
      ));
      
      $this->addElement('Radio', 'classified_enable_rating', array(
        'label' => 'Enable Rating',
        'description' => 'Do you want to enable rating for the classifieds on your website?',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('classified.enable.rating', 1),
        'multiOptions' => array(
          '1' => 'Yes',
          '0' => 'No',
        ),
        'onchange' => 'showHideRatingSetting(this.value)', 
      ));
      
      $this->addElement('Text', 'classified_ratingicon', array(
        'label' => 'Font Icon for Rating',
        'description' => 'Enter font icon for rating. You can choose font icon from <a href="https://fontawesome.com/v5/search?m=free&s=solid" target="_blank"> here</a>. Example: fas fa-star',
        'requried' => true,
        'allowEmpty' => false,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('classified.ratingicon', 'fas fa-star'),
      ));
      $this->classified_ratingicon->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
      
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}
