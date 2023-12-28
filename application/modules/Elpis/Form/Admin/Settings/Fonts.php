<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Elpis
 * @copyright  Copyright 2006-2022 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Fonts.php 2022-06-21
 */

class Elpis_Form_Admin_Settings_Fonts extends Engine_Form {

  public function init() {

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->setTitle('Manage Fonts')
        ->setDescription('Here, you can configure the font settings for this theme on your website. You can also choose to enable the Google Fonts.');

    $url = "https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyDczHMCNc0JCmJACM86C7L8yYdF9sTvz1A";
    
    $ch = curl_init();
  
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $data = curl_exec($ch);
    curl_close($ch);

    $results = json_decode($data,true);
    
    $googleFontArray = array();
    foreach($results['items'] as $re) {
      $googleFontArray['"'.$re["family"].'"'] = $re['family'];
    }

    $this->addElement('Select', 'elpis_googlefonts', array(
      'label' => 'Choose Fonts',
      'description' => 'Choose from below the Fonts which you want to enable in this theme.',
      'multiOptions' => array(
        '0' => 'Web Safe Font Combinations',
        '1' => 'Google Fonts',
      ),
      'onchange' => "usegooglefont(this.value)",
      'value' => $settings->getSetting('elpis.googlefonts', 1),
    ));
    
    $font_array = array(
      'Georgia, serif' => 'Georgia, serif',
      '"Palatino Linotype", "Book Antiqua", Palatino, serif' => '"Palatino Linotype", "Book Antiqua", Palatino, serif',
      '"Times New Roman", Times, serif' => '"Times New Roman", Times, serif',
      'Arial, Helvetica, sans-serif' => 'Arial, Helvetica, sans-serif',
      '"Arial Black", Gadget, sans-serif' => '"Arial Black", Gadget, sans-serif',
      '"Comic Sans MS", cursive, sans-serif' => '"Comic Sans MS", cursive, sans-serif',
      'Impact, Charcoal, sans-serif' => 'Impact, Charcoal, sans-serif',
      '"Lucida Sans Unicode", "Lucida Grande", sans-serif' => '"Lucida Sans Unicode", "Lucida Grande", sans-serif',
      'Tahoma, Geneva, sans-serif' => 'Tahoma, Geneva, sans-serif',
      '"Trebuchet MS", Helvetica, sans-serif' => '"Trebuchet MS", Helvetica, sans-serif',
      'Verdana, Geneva, sans-serif' => 'Verdana, Geneva, sans-serif',
      '"Courier New", Courier, monospace' => '"Courier New", Courier, monospace',
      '"Lucida Console", Monaco, monospace' => '"Lucida Console", Monaco, monospace',
    );
    
    
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $link = '<a href="http://www.w3schools.com/cssref/css_websafe_fonts.asp" target="_blank">here</a>.';
    $bodyDes = sprintf('You can see the web safe fonts %s',$link);
    $headingDes = sprintf('You can see the web safe fonts %s',$link);
    $mainmenuDes = sprintf('You can see the web safe fonts %s',$link);
    $tabDes = sprintf('You can see the web safe fonts %s',$link);
    
    //Google Font Work
    $link = '<a href="https://www.google.com/fonts" target="_blank">here</a>.';
    $bodygoogleDes = sprintf('You can see the google fonts %s',$link);
    $headinggoogleDes = sprintf('You can see the google fonts %s',$link);
    $mainmenugoogleDes = sprintf('You can see the google fonts %s',$link);
    $tabgoogleDes = sprintf('You can see the google fonts %s',$link);
    
    //Body Settings

    $this->addElement('Select', 'elpis_body_fontfamily', array(
      'label' => 'Body - Font Family',
      'description' => "Choose font family for the text under Body Styling.",
      'multiOptions' => $font_array,
      'value' => Engine_Api::_()->elpis()->getContantValueXML('elpis_body_fontfamily'),
    ));
    $this->getElement('elpis_body_fontfamily')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
    
    $this->addDisplayGroup(array('elpis_body_fontfamily'), 'elpis_bodygrp', array('disableLoadDefaultDecorators' => true));
    $elpis_bodygrp = $this->getDisplayGroup('elpis_bodygrp');
    $elpis_bodygrp->setDecorators(array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'elpis_bodygrp'))));

    //Google Font work
    $this->addElement('Select', 'elpis_googlebody_fontfamily', array(
      'label' => 'Body - Font Family',
      'description' => "Choose font family for the text under Body Styling.",
      'multiOptions' => $googleFontArray,
      'value' => Engine_Api::_()->elpis()->getContantValueXML('elpis_body_fontfamily'),
    ));
    $this->getElement('elpis_googlebody_fontfamily')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
    
    $this->addDisplayGroup(array('elpis_googlebody_fontfamily'), 'elpis_googlebodygrp', array('disableLoadDefaultDecorators' => true));
    $elpis_googlebodygrp = $this->getDisplayGroup('elpis_googlebodygrp');
    $elpis_googlebodygrp->setDecorators(array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'elpis_googlebodygrp'))));

    //Heading Settings
    $this->addElement('Select', 'elpis_heading_fontfamily', array(
      'label' => 'Heading - Font Family',
      'description' => "Choose font family for the text under Heading Styling.",
      'multiOptions' => $font_array,
      'value' => Engine_Api::_()->elpis()->getContantValueXML('elpis_heading_fontfamily'),
    ));
    $this->getElement('elpis_heading_fontfamily')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
    
    $this->addDisplayGroup(array('elpis_heading_fontfamily'), 'elpis_headinggrp', array('disableLoadDefaultDecorators' => true));
    $elpis_headinggrp = $this->getDisplayGroup('elpis_headinggrp');
    $elpis_headinggrp->setDecorators(array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'elpis_headinggrp'))));
    
    
    //Google Font work
    $this->addElement('Select', 'elpis_googleheading_fontfamily', array(
      'label' => 'Heading - Font Family',
      'description' => "Choose font family for the text under Heading Styling.",
      'multiOptions' => $googleFontArray,
      'value' => Engine_Api::_()->elpis()->getContantValueXML('elpis_heading_fontfamily'),
    ));
    $this->getElement('elpis_googleheading_fontfamily')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
    
    $this->addDisplayGroup(array('elpis_googleheading_fontfamily'), 'elpis_googleheadinggrp', array('disableLoadDefaultDecorators' => true));
    $elpis_googleheadinggrp = $this->getDisplayGroup('elpis_googleheadinggrp');
    $elpis_googleheadinggrp->setDecorators(array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'elpis_googleheadinggrp'))));

   //Main Menu Settings
     $this->addElement('Select', 'elpis_mainmenu_fontfamily', array(
       'label' => 'Main Menu - Font Family',
       'description' => "Choose font family for the text under Main Menu Styling.",
       'multiOptions' => $font_array,
       'value' => Engine_Api::_()->elpis()->getContantValueXML('elpis_mainmenu_fontfamily'),
     ));
     $this->getElement('elpis_mainmenu_fontfamily')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
     
     $this->addDisplayGroup(array('elpis_mainmenu_fontfamily'), 'elpis_mainmenugrp', array('disableLoadDefaultDecorators' => true));
     $elpis_mainmenugrp = $this->getDisplayGroup('elpis_mainmenugrp');
     $elpis_mainmenugrp->setDecorators(array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'elpis_mainmenugrp'))));
    
     //Google Font work
     $this->addElement('Select', 'elpis_googlemainmenu_fontfamily', array(
       'label' => 'Main Menu - Font Family',
       'description' => "Choose font family for the text under Main Menu Styling.",
       'multiOptions' => $googleFontArray,
       'value' => Engine_Api::_()->elpis()->getContantValueXML('elpis_mainmenu_fontfamily'),
     ));
     $this->getElement('elpis_googlemainmenu_fontfamily')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
     
     $this->addDisplayGroup(array('elpis_googlemainmenu_fontfamily'), 'elpis_googlemainmenugrp', array('disableLoadDefaultDecorators' => true));
     $elpis_googlemainmenugrp = $this->getDisplayGroup('elpis_googlemainmenugrp');
     $elpis_googlemainmenugrp->setDecorators(array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'elpis_googlemainmenugrp'))));

    //Tab Settings
    $this->addElement('Select', 'elpis_tab_fontfamily', array(
      'label' => 'Tab - Font Family',
      'description' => "Choose font family for the text under Tab Styling.",
      'multiOptions' => $font_array,
      'value' => Engine_Api::_()->elpis()->getContantValueXML('elpis_tab_fontfamily'),
    ));
    $this->getElement('elpis_tab_fontfamily')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
    
    $this->addDisplayGroup(array('elpis_tab_fontfamily'), 'elpis_tabgrp', array('disableLoadDefaultDecorators' => true));
    $elpis_tabgrp = $this->getDisplayGroup('elpis_tabgrp');
    $elpis_tabgrp->setDecorators(array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'elpis_tabgrp'))));
    
    
    //Google Font work
    $this->addElement('Select', 'elpis_googletab_fontfamily', array(
      'label' => 'Tab - Font Family',
      'description' => "Choose font family for the text under Tab Styling.",
      'multiOptions' => $googleFontArray,
      'value' => Engine_Api::_()->elpis()->getContantValueXML('elpis_tab_fontfamily'),
    ));
    $this->getElement('elpis_googletab_fontfamily')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
    
    $this->addDisplayGroup(array('elpis_googletab_fontfamily'), 'elpis_googletabgrp', array('disableLoadDefaultDecorators' => true));
    $elpis_googletabgrp = $this->getDisplayGroup('elpis_googletabgrp');
    $elpis_googletabgrp->setDecorators(array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'elpis_googletabgrp'))));

    // Add submit button
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }
}
