<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Global.php 9802 2012-10-20 16:56:13Z pamela $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Group_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');
      
    $this->addElement('Text', 'group_page', array(
      'label' => 'Groups Per Page',
      'description' => 'How many groups will be shown per page?',
      'value' => 12,
    ));
    
   // Create Elements
    $bbcode = new Engine_Form_Element_Radio('bbcode');
    $bbcode
      ->addMultiOptions(array(
        1 => 'Yes, members can use BBCode tags.',
        0 => 'No, do not let members use BBCode.'
      ));
    $bbcode->setValue(1);
    $bbcode->setLabel("Enable BBCode");

    $html = new Engine_Form_Element_Radio('html');

    $html
      ->addMultiOptions(array(
        1 => 'Yes, members can use HTML in their posts.',
        0 => 'No, strip HTML from posts.'
      ));
    $html->setValue(0);
    $html->setLabel("Enable HTML");

    // Add elements
    $this->addElements(array(
      $bbcode,
      $html
    ));
      $this->addElement('Radio', 'group_allow_unauthorized', array(
          'label' => 'Make unauthorized groups searchable?',
          'description' => 'Do you want to make a unauthorized groups searchable? (If set to no, groups that are not authorized for the current user will not be displayed in the group search results and widgets.)',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('group.allow.unauthorized',0),
          'multiOptions' => array(
              '1' => 'Yes',
              '0' => 'No',
          ),
      ));
      $this->addElement('Radio', 'group_enable_rating', array(
        'label' => 'Enable Rating',
        'description' => 'Do you want to enable rating for the groups on your website?',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('group.enable.rating', 1),
        'multiOptions' => array(
          '1' => 'Yes',
          '0' => 'No',
        ),
        'onchange' => 'showHideRatingSetting(this.value)', 
      ));
      
      $this->addElement('Text', 'group_ratingicon', array(
        'label' => 'Font Icon for Rating',
        'description' => 'Enter font icon for rating. You can choose font icon from <a href="https://fontawesome.com/v5/search?m=free&s=solid" target="_blank"> here</a>. Example: fas fa-star',
        'requried' => true,
        'allowEmpty' => false,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('group.ratingicon', 'fas fa-star'),
      ));
      $this->group_ratingicon->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}
