<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Styling.php 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sescompany_Form_Admin_Styling extends Engine_Form {

  public function init() {
  
  
    $description = $this->getTranslator()->translate('Here, you can configure the color scheme for this theme on your site. Below, there are 9 pre-configured color schemes which you can simply choose and enable on your website.<br>You can also make your own theme by using any existing color scheme or a completely new theme. Making from existing theme will not affect the existing schemes.<br />');

// 	  $moreinfo = $this->getTranslator()->translate('See Google Fonts here: <a href="%1$s" target="_blank">https://fonts.google.com/</a><br />');
//         
//     $moreinfos = $this->getTranslator()->translate('See Web Safe Font Combinations here: <a href="%2$s" target="_blank">https://www.w3schools.com/cssref/css_websafe_fonts.asp</a>');
// 
//     $description = vsprintf($description.$moreinfo.$moreinfos, array('https://fonts.google.com','https://www.w3schools.com/cssref/css_websafe_fonts.asp'));

    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $sescompanyApi = Engine_Api::_()->sescompany();
    $this->setTitle('Manage Color Schemes')
            ->setDescription($description);

    $this->addElement('Radio', 'theme_color', array(
        'label' => 'Color Schemes',
        'multiOptions' => array(
            1 => '<img src="./application/modules/Sescompany/externals/images/color-scheme/1.png" alt="" />',
            2 => '<img src="./application/modules/Sescompany/externals/images/color-scheme/2.png" alt="" />',
            3 => '<img src="./application/modules/Sescompany/externals/images/color-scheme/3.png" alt="" />',
            4 => '<img src="./application/modules/Sescompany/externals/images/color-scheme/4.png" alt="" />',
						6 => '<img src="./application/modules/Sescompany/externals/images/color-scheme/5.png" alt="" />',
						7 => '<img src="./application/modules/Sescompany/externals/images/color-scheme/6.png" alt="" />',
						8 => '<img src="./application/modules/Sescompany/externals/images/color-scheme/7.png" alt="" />',
						9 => '<img src="./application/modules/Sescompany/externals/images/color-scheme/8.png" alt="" />',
						10 => '<img src="./application/modules/Sescompany/externals/images/color-scheme/9.png" alt="" />',
            
						5 => '<img src="./application/modules/Sescompany/externals/images/color-scheme/custom.png" alt="" />',
        ),
        'onclick' => 'changeThemeColor(this.value, "")',
        'escape' => false,
        'value' => $sescompanyApi->getContantValueXML('theme_color'),
    ));

    $this->addElement('Select', 'custom_theme_color', array(
        'label' => 'Custom Theme Color',
        'multiOptions' => array(
            5 => 'New Custom',
            1 => 'Theme - 1',
            2 => 'Theme - 2',
            3 => 'Theme - 3',
            4 => 'Theme - 4',
						6 => 'Theme - 5',
						7 => 'Theme - 6',
						8 => 'Theme - 7',
						9 => 'Theme - 8',
						10 => 'Theme - 9'
						
        ),
        'onclick' => 'changeCustomThemeColor(this.value)',
        'escape' => false,
        'value' => $sescompanyApi->getContantValueXML('custom_theme_color'),
    ));
    $theme_color = $sescompanyApi->getContantValueXML('theme_color');
    if($theme_color == '5') {
    	$company_header_background_color = $settings->getSetting('company.header.background.color');
	    $company_header_border_color = $settings->getSetting('company.header.border.color');
			$company_mainmenu_links_color = $settings->getSetting('company.mainmenu.links.color');
			$company_mainmenu_links_hover_color = $settings->getSetting('company.mainmenu.links.hover.color');
			$company_minimenu_links_color = $settings->getSetting('company.minimenu.linkscolor');
			$company_minimenu_links_hover_color = $settings->getSetting('company.minimenu.links.hover.color');
			$company_header_searchbox_background_color = $settings->getSetting('company.header.searchbox.background.color');
			$company_header_searchbox_text_color = $settings->getSetting('company.header.searchbox.text.color');
			$company_header_searchbox_border_color = $settings->getSetting('company.header.searchbox.border.color');
			$company_footer_background_color = $settings->getSetting('company.footer.background.color');
			$company_footer_border_color = $settings->getSetting('company.footer.border.color');
			$company_footer_text_color = $settings->getSetting('company.footer.text.color');
			$company_footer_links_color = $settings->getSetting('company.footer.links.color');
			$company_footer_links_hover_color = $settings->getSetting('company.footer.links.hover.color');
			$company_theme_color = $settings->getSetting('company.theme.color');
			$company_body_background_color = $settings->getSetting('company.body.background.color');
			$company_font_color = $settings->getSetting('company.fontcolor');
			$company_font_color_light = $settings->getSetting('company.font.color.light');
			$company_heading_color = $settings->getSetting('company.heading.color');
			$company_links_color = $settings->getSetting('company.links.color');
			$company_links_hover_color = $settings->getSetting('company.links.hover.color');
			$company_content_background_color = $settings->getSetting('company.content.background.color');
			$company_content_border_color = $settings->getSetting('company.content.bordercolor');
			$company_form_label_color = $settings->getSetting('company.form.label.color');
			$company_input_background_color = $settings->getSetting('company.input.background.color');
			$company_input_font_color = $settings->getSetting('company.input.font.color');
			$company_input_border_color = $settings->getSetting('company.input.border.color');
			$company_button_background_color = $settings->getSetting('company.button.backgroundcolor');
			$company_button_background_color_hover = $settings->getSetting('company.button.background.color.hover');
			$company_button_border_color = $settings->getSetting('company.button.border.color');
			$company_button_font_color = $settings->getSetting('company.button.font.color');
			$company_button_font_hover_color = $settings->getSetting('company.button.font.hover.color');
			$company_comment_background_color = $settings->getSetting('company.comment.background.color');

    } else {
	    $company_header_background_color = $sescompanyApi->getContantValueXML('company_header_background_color');
	    $company_header_border_color = $sescompanyApi->getContantValueXML('company_header_border_color');
			$company_mainmenu_links_color = $sescompanyApi->getContantValueXML('company_mainmenu_links_color');
			$company_mainmenu_links_hover_color = $sescompanyApi->getContantValueXML('company_mainmenu_links_hover_color');
			$company_minimenu_links_color = $sescompanyApi->getContantValueXML('company_minimenu_links_color');
			$company_minimenu_links_hover_color = $sescompanyApi->getContantValueXML('company_minimenu_links_hover_color');
			$company_header_searchbox_background_color = $sescompanyApi->getContantValueXML('company_header_searchbox_background_color');
			$company_header_searchbox_text_color = $sescompanyApi->getContantValueXML('company_header_searchbox_text_color');
			$company_header_searchbox_border_color = $sescompanyApi->getContantValueXML('company_header_searchbox_border_color');
			$company_footer_background_color = $sescompanyApi->getContantValueXML('company_footer_background_color');
			$company_footer_border_color = $sescompanyApi->getContantValueXML('company_footer_border_color');
			$company_footer_text_color = $sescompanyApi->getContantValueXML('company_footer_text_color');
			$company_footer_links_color = $sescompanyApi->getContantValueXML('company_footer_links_color');
			$company_footer_links_hover_color = $sescompanyApi->getContantValueXML('company_footer_links_hover_color');
			$company_theme_color = $sescompanyApi->getContantValueXML('company_theme_color');
			$company_body_background_color = $sescompanyApi->getContantValueXML('company_body_background_color');
			$company_font_color = $sescompanyApi->getContantValueXML('company_font_color');
			$company_font_color_light = $sescompanyApi->getContantValueXML('company_font_color_light');
			$company_heading_color = $sescompanyApi->getContantValueXML('company_heading_color');
			$company_links_color = $sescompanyApi->getContantValueXML('company_links_color');
			$company_links_hover_color = $sescompanyApi->getContantValueXML('company_links_hover_color');
			$company_content_background_color = $sescompanyApi->getContantValueXML('company_content_background_color');
			$company_content_border_color = $sescompanyApi->getContantValueXML('company_content_border_color');
			$company_form_label_color = $sescompanyApi->getContantValueXML('company_form_label_color');
			$company_input_background_color = $sescompanyApi->getContantValueXML('company_input_background_color');
			$company_input_font_color = $sescompanyApi->getContantValueXML('company_input_font_color');
			$company_input_border_color = $sescompanyApi->getContantValueXML('company_input_border_color');
			$company_button_background_color = $sescompanyApi->getContantValueXML('company_button_background_color');
			$company_button_background_color_hover = $sescompanyApi->getContantValueXML('company_button_background_color_hover');
			$company_button_border_color = $sescompanyApi->getContantValueXML('company_button_border_color');
			$company_button_font_color = $sescompanyApi->getContantValueXML('company_button_font_color');
			$company_button_font_hover_color = $sescompanyApi->getContantValueXML('company_button_font_hover_color');
			$company_comment_background_color = $sescompanyApi->getContantValueXML('company_comment_background_color');
    }

    //Start Header Styling
    $this->addElement('Dummy', 'header_settings', array(
        'label' => 'Header Styling Settings',
    ));
    $this->addElement('Text', "company_header_background_color", array(
        'label' => 'Header Background Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_header_background_color,
    ));

    $this->addElement('Text', "company_header_border_color", array(
        'label' => 'Header Border Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_header_border_color,
    ));

    $this->addElement('Text', "company_mainmenu_links_color", array(
        'label' => 'Main Menu Link Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_mainmenu_links_color,
    ));

    $this->addElement('Text', "company_mainmenu_links_hover_color", array(
        'label' => 'Main Menu Link Hover Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_mainmenu_links_hover_color,
    ));


    $this->addElement('Text', "company_minimenu_links_color", array(
        'label' => 'Mini Menu Link Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_minimenu_links_color,
    ));


    $this->addElement('Text', "company_minimenu_links_hover_color", array(
        'label' => 'Mini Menu Link Hover Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_minimenu_links_hover_color,
    ));

    $this->addElement('Text', "company_header_searchbox_background_color", array(
        'label' => 'Header Searchbox Background Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_header_searchbox_background_color,
    ));

    $this->addElement('Text', "company_header_searchbox_text_color", array(
        'label' => 'Header Searchbox Text Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_header_searchbox_text_color,
    ));
		    $this->addElement('Text', "company_header_searchbox_border_color", array(
        'label' => 'Header Searchbox Border Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_header_searchbox_border_color,
    ));

    $this->addDisplayGroup(array('company_header_background_color', 'company_header_border_color', 'company_mainmenu_links_color', 'company_mainmenu_links_hover_color', 'company_minimenu_links_color', 'company_minimenu_links_hover_color', 'company_header_searchbox_background_color', 'company_header_searchbox_text_color','company_header_searchbox_border_color'), 'header_settings_group', array('disableLoadDefaultDecorators' => true));
    $header_settings_group = $this->getDisplayGroup('header_settings_group');
    $header_settings_group->setDecorators(array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'header_settings_group'))));
    //End Header Styling
    //Start Footer Styling
    $this->addElement('Dummy', 'footer_settings', array(
        'label' => 'Footer Styling Settings',
    ));
    $this->addElement('Text', "company_footer_background_color", array(
        'label' => 'Footer Background Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_footer_background_color,
    ));

    $this->addElement('Text', "company_footer_border_color", array(
        'label' => 'Footer Border Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_footer_border_color,
    ));

    $this->addElement('Text', "company_footer_text_color", array(
        'label' => 'Footer Text Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_footer_text_color,
    ));

    $this->addElement('Text', "company_footer_links_color", array(
        'label' => 'Footer Link Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_footer_links_color,
    ));

    $this->addElement('Text', "company_footer_links_hover_color", array(
        'label' => 'Footer Link Hover Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_footer_links_hover_color,
    ));
    $this->addDisplayGroup(array('company_footer_background_color', 'company_footer_border_color', 'company_footer_text_color', 'company_footer_links_color', 'company_footer_links_hover_color'), 'footer_settings_group', array('disableLoadDefaultDecorators' => true));
    $footer_settings_group = $this->getDisplayGroup('footer_settings_group');
    $footer_settings_group->setDecorators(array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'footer_settings_group'))));
    //End Footer Styling
    //Start Body Styling
    $this->addElement('Dummy', 'body_settings', array(
        'label' => 'Body Styling Settings',
    ));
    $this->addElement('Text', "company_theme_color", array(
        'label' => 'Theme Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_theme_color,
    ));
    
    
    $this->addElement('Text', "company_body_background_color", array(
        'label' => 'Body Background Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_body_background_color,
    ));

    $this->addElement('Text', "company_font_color", array(
        'label' => 'Font Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_font_color,
    ));

    $this->addElement('Text', "company_font_color_light", array(
        'label' => 'Font Light Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_font_color_light,
    ));

    $this->addElement('Text', "company_heading_color", array(
        'label' => 'Heading Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_heading_color,
    ));

    $this->addElement('Text', "company_links_color", array(
        'label' => 'Link Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_links_color,
    ));

    $this->addElement('Text', "company_links_hover_color", array(
        'label' => 'Link Hover Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_links_hover_color,
    ));

    $this->addElement('Text', "company_content_background_color", array(
        'label' => 'Content Background Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_content_background_color,
    ));

    $this->addElement('Text', "company_content_border_color", array(
        'label' => 'Content Border Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_content_border_color,
    ));

    $this->addElement('Text', "company_form_label_color", array(
        'label' => 'Form Label Font Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_form_label_color,
    ));

    $this->addElement('Text', "company_input_background_color", array(
        'label' => 'Input Background Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_input_background_color,
    ));

    $this->addElement('Text', "company_input_font_color", array(
        'label' => 'Input Font Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_input_font_color,
    ));

    $this->addElement('Text', "company_input_border_color", array(
        'label' => 'Input Border Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_input_border_color,
    ));

    $this->addElement('Text', "company_button_background_color", array(
        'label' => 'Button Background Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_button_background_color,
    ));
    $this->addElement('Text', "company_button_background_color_hover", array(
        'label' => 'Button Background Hovor Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_button_background_color_hover,
    ));
    $this->addElement('Text', "company_button_border_color", array(
        'label' => 'Button Border Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_button_border_color,
    ));

    $this->addElement('Text', "company_button_font_color", array(
        'label' => 'Button Font Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_button_font_color,
    ));
    $this->addElement('Text', "company_button_font_hover_color", array(
        'label' => 'Button Font Hover Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_button_font_hover_color,
    ));
    $this->addElement('Text', "company_comment_background_color", array(
        'label' => 'Comments Background Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEScolor',
        'value' => $company_comment_background_color,
    ));

    $this->addDisplayGroup(array('company_theme_color','company_body_background_color', 'company_font_color', 'company_font_color_light', 'company_heading_color', 'company_links_color', 'company_links_hover_color', 'company_content_background_color', 'company_content_border_color', 'company_form_label_color', 'company_input_background_color', 'company_input_font_color', 'company_input_border_color', 'company_button_background_color', 'company_button_background_color_hover', 'company_button_font_color', 'company_button_font_hover_color', 'company_button_border_color', 'company_comment_background_color'), 'body_settings_group', array('disableLoadDefaultDecorators' => true));
    $body_settings_group = $this->getDisplayGroup('body_settings_group');
    $body_settings_group->setDecorators(array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'body_settings_group'))));
    //End Body Styling
    // Add submit button
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}
