<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Typography.php 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sescompany_Form_Admin_Typography extends Engine_Form {

  public function init() {
  
    $description = $this->getTranslator()->translate('Here, you can configure the font settings in this theme on your website. You can also choose to enable the Google Fonts.<br/>');

	  $moreinfo = $this->getTranslator()->translate('See Google Fonts here: <a href="%1$s" target="_blank">https://fonts.google.com/</a><br />');
        
    $moreinfos = $this->getTranslator()->translate('See Web Safe Font Combinations here: <a href="%2$s" target="_blank">https://www.w3schools.com/cssref/css_websafe_fonts.asp</a>');

    $description = vsprintf($description.$moreinfo.$moreinfos, array('https://fonts.google.com','https://www.w3schools.com/cssref/css_websafe_fonts.asp'));

    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);


    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->setTitle('Manage Fonts / Typography Settings')
            ->setDescription($description);
            
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

    $this->addElement('Select', 'sescompany_googlefonts', array(
      'label' => 'Choose Fonts',
      'description' => 'Choose from below the Fonts which you want to enable in this theme.',
      'multiOptions' => array(
        '0' => 'Web Safe Font Combinations',
        '1' => 'Google Fonts',
      ),
      'onchange' => "usegooglefont(this.value)",
      'value' => $settings->getSetting('sescompany.googlefonts', 0),
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
	  "'Open Sans', sans-serif" => "'Open Sans', sans-serif",
    );

    $this->addElement('Select', 'company_body_fontfamily', array(
      'label' => 'Body - Font Family',
      'description' => "Choose font family for the text under Body Styling.",
      'multiOptions' => $font_array,
      'value' => Engine_Api::_()->sescompany()->getContantValueXML('company_body_fontfamily'),
    ));
    $this->getElement('company_body_fontfamily')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
    
    
    
    $this->addElement('Text', 'company_body_fontsize', array(
      'label' => 'Body - Font Size',
      'description' => 'Enter the font size for the text under Body Styling.',
      'allowEmpty' => false,
      'required' => true,
      'value' => Engine_Api::_()->sescompany()->getContantValueXML('company_body_fontsize'),
    ));
    $this->getElement('company_body_fontsize')->getDecorator('Description')->setOption('escape',false); 
    
    $this->addDisplayGroup(array('company_body_fontfamily', 'company_body_fontsize'), 'company_bodygrp', array('disableLoadDefaultDecorators' => true));
    $company_bodygrp = $this->getDisplayGroup('company_bodygrp');
    $company_bodygrp->setDecorators(array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'company_bodygrp'))));
    
   
    //Google Font work
    $this->addElement('Select', 'company_googlebody_fontfamily', array(
      'label' => 'Body - Font Family',
      'description' => "Choose font family for the text under Body Styling.",
      'multiOptions' => $googleFontArray,
      'value' => Engine_Api::_()->sescompany()->getContantValueXML('company_body_fontfamily'),
    ));
    $this->getElement('company_googlebody_fontfamily')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
    
    $this->addElement('Text', 'company_googlebody_fontsize', array(
      'label' => 'Body - Font Size',
      'description' => 'Enter the font size for the text under Body Styling.',
      'allowEmpty' => false,
      'required' => true,
      'value' => Engine_Api::_()->sescompany()->getContantValueXML('company_body_fontsize'),
    ));
    $this->getElement('company_googlebody_fontsize')->getDecorator('Description')->setOption('escape',false); 
    
    $this->addDisplayGroup(array('company_googlebody_fontfamily', 'company_googlebody_fontsize'), 'company_googlebodygrp', array('disableLoadDefaultDecorators' => true));
    $company_googlebodygrp = $this->getDisplayGroup('company_googlebodygrp');
    $company_googlebodygrp->setDecorators(array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'company_googlebodygrp'))));
    

    $this->addElement('Select', 'company_heading_fontfamily', array(
      'label' => 'Heading - Font Family',
      'description' => "Choose font family for the text under Heading Styling.",
      'multiOptions' => $font_array,
      'value' => Engine_Api::_()->sescompany()->getContantValueXML('company_heading_fontfamily'),
    ));
    $this->getElement('company_heading_fontfamily')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

    $this->addElement('Text', 'company_heading_fontsize', array(
      'label' => 'Heading - Font Size',
      'description' => 'Enter the font size for the text under Heading Styling.',
      'allowEmpty' => false,
      'required' => true,
      'value' => Engine_Api::_()->sescompany()->getContantValueXML('company_heading_fontsize'),
    ));
    $this->getElement('company_heading_fontsize')->getDecorator('Description')->setOption('escape',false); 
    
    $this->addDisplayGroup(array('company_heading_fontfamily', 'company_heading_fontsize'), 'company_headinggrp', array('disableLoadDefaultDecorators' => true));
    $company_headinggrp = $this->getDisplayGroup('company_headinggrp');
    $company_headinggrp->setDecorators(array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'company_headinggrp'))));
    
    
    //Google Font work
    $this->addElement('Select', 'company_googleheading_fontfamily', array(
      'label' => 'Heading - Font Family',
      'description' => "Choose font family for the text under Heading Styling.",
      'multiOptions' => $googleFontArray,
      'value' => Engine_Api::_()->sescompany()->getContantValueXML('company_heading_fontfamily'),
    ));
    $this->getElement('company_googleheading_fontfamily')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
    
    $this->addElement('Text', 'company_googleheading_fontsize', array(
      'label' => 'Heading - Font Size',
      'description' => 'Enter the font size for the text under Heading Styling.',
      'allowEmpty' => false,
      'required' => true,
      'value' => Engine_Api::_()->sescompany()->getContantValueXML('company_heading_fontsize'),
    ));
    $this->getElement('company_googleheading_fontsize')->getDecorator('Description')->setOption('escape',false); 
    
    $this->addDisplayGroup(array('company_googleheading_fontfamily', 'company_googleheading_fontsize'), 'company_googleheadinggrp', array('disableLoadDefaultDecorators' => true));
    $company_googleheadinggrp = $this->getDisplayGroup('company_googleheadinggrp');
    $company_googleheadinggrp->setDecorators(array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'company_googleheadinggrp'))));
    
    

    $this->addElement('Select', 'company_mainmenu_fontfamily', array(
      'label' => 'Main Menu - Font Family',
      'description' => "Choose font family for the text under Main Menu Styling.",
      'multiOptions' => $font_array,
      'value' => Engine_Api::_()->sescompany()->getContantValueXML('company_mainmenu_fontfamily'),
    ));
    $this->getElement('company_mainmenu_fontfamily')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
    
            
    $this->addElement('Text', 'company_mainmenu_fontsize', array(
      'label' => 'Main Menu - Font Size',
      'description' => 'Enter the font size for the text under Main Menu Styling.',
      'allowEmpty' => false,
      'required' => true,
      'value' => Engine_Api::_()->sescompany()->getContantValueXML('company_mainmenu_fontsize'),
    ));
    $this->getElement('company_mainmenu_fontsize')->getDecorator('Description')->setOption('escape',false); 
    
    $this->addDisplayGroup(array('company_mainmenu_fontfamily', 'company_mainmenu_fontsize'), 'company_mainmenugrp', array('disableLoadDefaultDecorators' => true));
    $company_mainmenugrp = $this->getDisplayGroup('company_mainmenugrp');
    $company_mainmenugrp->setDecorators(array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'company_mainmenugrp'))));
    
    //Google Font work
    $this->addElement('Select', 'company_googlemainmenu_fontfamily', array(
      'label' => 'Main Menu - Font Family',
      'description' => "Choose font family for the text under Main Menu Styling.",
      'multiOptions' => $googleFontArray,
      'value' => Engine_Api::_()->sescompany()->getContantValueXML('company_mainmenu_fontfamily'),
    ));
    $this->getElement('company_googlemainmenu_fontfamily')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
    
    $this->addElement('Text', 'company_googlemainmenu_fontsize', array(
      'label' => 'Main Menu - Font Size',
      'description' => 'Enter the font size for the text under Main Menu Styling.',
      'allowEmpty' => false,
      'required' => true,
      'value' => Engine_Api::_()->sescompany()->getContantValueXML('company_mainmenu_fontsize'),
    ));
    $this->getElement('company_googlemainmenu_fontsize')->getDecorator('Description')->setOption('escape',false); 
    
    $this->addDisplayGroup(array('company_googlemainmenu_fontfamily', 'company_googlemainmenu_fontsize'), 'company_googlemainmenugrp', array('disableLoadDefaultDecorators' => true));
    $company_googlemainmenugrp = $this->getDisplayGroup('company_googlemainmenugrp');
    $company_googlemainmenugrp->setDecorators(array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'company_googlemainmenugrp'))));
    
    
    $this->addElement('Select', 'company_tab_fontfamily', array(
      'label' => 'Tab - Font Family',
      'description' => "Choose font family for the text under Tab Styling.",
      'multiOptions' => $font_array,
      'value' => Engine_Api::_()->sescompany()->getContantValueXML('company_tab_fontfamily'),
    ));
    $this->getElement('company_tab_fontfamily')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

    $this->addElement('Text', 'company_tab_fontsize', array(
      'label' => 'Tab - Font Size',
      'description' => 'Enter the font size for the text under Tab Styling.',
      'allowEmpty' => false,
      'required' => true,
      'value' => Engine_Api::_()->sescompany()->getContantValueXML('company_tab_fontsize'),
    ));
    $this->getElement('company_tab_fontsize')->getDecorator('Description')->setOption('escape',false); 
    
    $this->addDisplayGroup(array('company_tab_fontfamily', 'company_tab_fontsize'), 'company_tabgrp', array('disableLoadDefaultDecorators' => true));
    $company_tabgrp = $this->getDisplayGroup('company_tabgrp');
    $company_tabgrp->setDecorators(array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'company_tabgrp'))));
    
    
    //Google Font work
    $this->addElement('Select', 'company_googletab_fontfamily', array(
      'label' => 'Tab - Font Family',
      'description' => "Choose font family for the text under Tab Styling.",
      'multiOptions' => $googleFontArray,
      'value' => Engine_Api::_()->sescompany()->getContantValueXML('company_tab_fontfamily'),
    ));
    $this->getElement('company_googletab_fontfamily')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
    
    $this->addElement('Text', 'company_googletab_fontsize', array(
      'label' => 'Tab - Font Size',
      'description' => 'Enter the font size for the text under Tab Styling.',
      'allowEmpty' => false,
      'required' => true,
      'value' => Engine_Api::_()->sescompany()->getContantValueXML('company_tab_fontsize'),
    ));
    $this->getElement('company_googletab_fontsize')->getDecorator('Description')->setOption('escape',false); 
    
    $this->addDisplayGroup(array('company_googletab_fontfamily', 'company_googletab_fontsize'), 'company_googletabgrp', array('disableLoadDefaultDecorators' => true));
    $company_googletabgrp = $this->getDisplayGroup('company_googletabgrp');
    $company_googletabgrp->setDecorators(array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'company_googletabgrp'))));

    
    // Add submit button
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}
