<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Elpis
 * @copyright  Copyright 2006-2022 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Global.php 2022-06-21
 */

class Elpis_Form_Admin_Settings_Global extends Engine_Form {

  public function init() {

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->addElement('Radio', 'elpis_changelanding', array(
      'label' => 'Set Landing Page of Elpis Theme',
      'description' => 'Do you want to set the Landing Page from this theme and replace the current Landing page with one of the landing page design from this theme? (If you choose Yes and save changes, then later you can manually make changes in the Landing page from Layout Editor. Back up page of your current landing page will get created with the name "Backup - Landing Page".)',
      'onclick' => 'confirmChangeLandingPage(this.value)',
      'multiOptions' => array(
        '1' => 'Yes, Landing Page Design 1',
        '2' => 'Yes, Landing Page Design 2',
        '0' => 'No',
      ),
      'value' => $settings->getSetting('elpis.changelanding', 0),
    ));

    $this->addElement('MultiCheckbox', 'elpis_headerloggedinoptions', array(
      'label' => 'Header Options for Logged in Members',
      'description' => 'Choose from the below options to be available in the header to logged in members on your website.',
      'multiOptions' => array(
          'search' => 'Search',
          'miniMenu' => 'Mini Menu',
          'mainMenu' =>'Main Menu',
          'logo' =>'Logo',
      ),
      'value' => unserialize($settings->getSetting('elpis.headerloggedinoptions', 'a:4:{i:0;s:6:"search";i:1;s:8:"miniMenu";i:2;s:8:"mainMenu";i:3;s:4:"logo";}')),
    ));

    $this->addElement('MultiCheckbox', 'elpis_headernonloggedinoptions', array(
      'label' => 'Header Options for Non-Logged in Members',
      'description' => 'Choose from the below options to be available in the header to non-logged in members on your website.',
      'multiOptions' => array(
          'search' => 'Search',
          'miniMenu' => 'Mini Menu',
          'mainMenu' =>'Main Menu',
          'logo' =>'Logo',
      ),
      'value' => unserialize($settings->getSetting('elpis.headernonloggedinoptions', 'a:4:{i:0;s:6:"search";i:1;s:8:"miniMenu";i:2;s:8:"mainMenu";i:3;s:4:"logo";}')),
    ));

    $this->addElement('Text', 'theme_widget_radius', array(
      'label' => 'Widget Corner Radius',
      'description' => 'Enter the corner radius of widgets on your website in px.',
      'value' => $settings->getSetting('theme.widget.radius', 10), //Engine_Api::_()->elpis()->getContantValueXML('theme_widget_radius'),
    ));


    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}
