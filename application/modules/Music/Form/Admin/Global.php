<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Global.php 9909 2013-02-14 05:49:17Z matthew $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Music_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');
    
    // Get The Value
    $values = Engine_Api::_()->getApi('settings', 'core')->music;
    
    
    $this->addElement('Text', 'playlistsPerPage', array(
      'label' => 'Playlists Per Page',
      'description' => 'How many playlists will be shown per page? (Enter a number between 1 and 999)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('music.playlistsPerPage', $values['playlistsperpage']),
    ));    
    $this->addElement('Radio', 'music_enable_rating', array(
      'label' => 'Enable Rating',
      'description' => 'Do you want to enable rating for the musics on your website?',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('music.enable.rating', 1),
      'multiOptions' => array(
        '1' => 'Yes',
        '0' => 'No',
      ),
      'onchange' => 'showHideRatingSetting(this.value)', 
    ));
    
    $this->addElement('Text', 'music_ratingicon', array(
      'label' => 'Font Icon for Rating',
      'description' => 'Enter font icon for rating. You can choose font icon from <a href="https://fontawesome.com/v5/search?m=free&s=solid" target="_blank"> here</a>. Example: fas fa-star',
      'requried' => true,
      'allowEmpty' => false,
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('music.ratingicon', 'fas fa-star'),
    ));
    $this->music_ratingicon->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }

  public function saveValues()
  {
    $values = $this->getValues();
    if (!is_numeric($values['playlistsPerPage'])
           || 0  >= $values['playlistsPerPage']
           || 999 < $values['playlistsPerPage'])
      $values['playlistsPerPage'] = 10;
    Engine_Api::_()->getApi('settings', 'core')
        ->setSetting('music.playlistsperpage', $values['playlistsPerPage']);
    Engine_Api::_()->getApi('settings', 'core')
        ->setSetting('music.enable.rating', $values['music_enable_rating']);

  }
}
