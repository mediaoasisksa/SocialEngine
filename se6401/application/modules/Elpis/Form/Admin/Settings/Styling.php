<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Elpis
 * @copyright  Copyright 2006-2022 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Styling.php 2022-06-21
 */

class Elpis_Form_Admin_Settings_Styling extends Engine_Form {

  public function init() {

    $description = "Here, you can manage the color schemes of your website. <br /><div class='tip'><span>Once you switch color schemes or make any changes to the new color schemes you added, please change the mode of your website from Production to Development. This has to be done everytime, and you can switch to production instantly or as soon you are done configuring the color scheme of your website.</span></div>";
    
    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
    $this->setTitle('Manage Color Schemes')
        ->setDescription($description);

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $api = Engine_Api::_()->elpis();
    $contrast_mode = $api->getContantValueXML('contrast_mode') ? $api->getContantValueXML('contrast_mode') : 'dark_mode';
    $this->addElement('Radio', 'contrast_mode', array(
      'label' => 'Contrast Mode?',
      'description' => 'Choose the Contrast mode for the accessibility widget on your website. You can choose Dark Mode or Light Mode as per the default theme on your website.',
      'multiOptions' => array(
        'light_mode' => 'Light Mode',
        'dark_mode' => 'Dark Mode'
      ),
      'value'=>$contrast_mode
    ));

    $customThemes = Engine_Api::_()->getDbTable('customthemes', 'elpis')->getCustomThemes(array('all' => 1));
    foreach($customThemes as $customTheme) {
      if(engine_in_array($customTheme['theme_id'], array(1,2,3))) {
        $themeOptions[$customTheme['theme_id']] = '<img src="./application/modules/Elpis/externals/images/color-scheme/'.$customTheme['theme_id'].'.png" alt="" />';
      } else {
        $themeOptions[$customTheme['theme_id']] = '<img src="./application/modules/Elpis/externals/images/color-scheme/custom.png" alt="" /> <span class="custom_theme_name">'. $customTheme->name.'</span>';
      }
    }

    $this->addElement('Radio', 'theme_color', array(
      'label' => 'Color Schemes',
      'multiOptions' => $themeOptions,
      'onclick' => 'changeThemeColor(this.value, "")',
      'escape' => false,
      'value' => $api->getContantValueXML('theme_color'),
    ));

    $this->addElement('dummy', 'custom_themes', array(
      'decorators' => array(array('ViewScript', array(
        'viewScript' => 'application/modules/Elpis/views/scripts/custom_themes.tpl',
        'class' => 'form element',
      )))
    ));

    $theme_color = $api->getContantValueXML('theme_color');
    if($theme_color == '5') {
      $elpis_header_background_color = $settings->getSetting('elpis.header.background.color');
      $elpis_mainmenu_background_color = $settings->getSetting('elpis.mainmenu.background.color');
      $elpis_mainmenu_links_color = $settings->getSetting('elpis.mainmenu.link.color');
      $elpis_mainmenu_links_hover_color = $settings->getSetting('elpis.mainmenu.link.hover.color');
			$elpis_mainmenu_links_hover_background_color = $settings->getSetting('elpis.mainmenu.links.hover.background.color');
      $elpis_minimenu_links_color = $settings->getSetting('elpis.minimenu.link.color');
      $elpis_minimenu_link_active_color = $settings->getSetting('elpis.minimenu.link.active.color');
			$elpis_minimenu_links_background_color = $settings->getSetting('elpis.minimenu.links.background.color');
			$elpis_minimenu_signup_background_color = $settings->getSetting('elpis.minimenu.signup.background.color');
			$elpis_minimenu_signup_font_color = $settings->getSetting('elpis.minimenu.signup.font.color');
      $elpis_footer_background_color = $settings->getSetting('elpis.footer.background.color');
      $elpis_footer_font_color = $settings->getSetting('elpis.footer.font.color');
      $elpis_footer_links_color = $settings->getSetting('elpis.footer.links.color');
      $elpis_footer_border_color = $settings->getSetting('elpis.footer.border.color');
      $elpis_theme_color = $settings->getSetting('elpis.theme.color');
      $elpis_body_background_color = $settings->getSetting('elpis.body.background.color');
      $elpis_font_color = $settings->getSetting('elpis.font.color');
      $elpis_font_color_light = $settings->getSetting('elpis.font.color.light');
      $elpis_links_color = $settings->getSetting('elpis.links.color');
      $elpis_links_hover_color = $settings->getSetting('elpis.links.hover.color');
      $elpis_headline_color = $settings->getSetting('elpis.headline.color');
      $elpis_border_color = $settings->getSetting('elpis.border.color');
      $elpis_box_background_color = $settings->getSetting('elpis.box.background.color');
      $elpis_form_label_color = $settings->getSetting('elpis.form.label.color');
      $elpis_input_background_color = $settings->getSetting('elpis.input.background.color');
      $elpis_input_font_color = $settings->getSetting('elpis.input.font.color');
      $elpis_input_border_color = $settings->getSetting('elpis.input.border.colors');
      $elpis_button_background_color = $settings->getSetting('elpis.button.background.color');
      $elpis_button_background_color_hover = $settings->getSetting('elpis.button.background.color.hover');
      $elpis_button_font_color = $settings->getSetting('elpis.button.font.color');
      $elpis_button_border_color = $settings->getSetting('elpis.button.border.color');
      $elpis_comments_background_color = $settings->getSetting('elpis.comments.background.color');
    } else {

      $elpis_header_background_color = $api->getContantValueXML('elpis_header_background_color');
      $elpis_mainmenu_background_color = $api->getContantValueXML('elpis_mainmenu_background_color');
      $elpis_mainmenu_links_color = $api->getContantValueXML('elpis_mainmenu_links_color');
      $elpis_mainmenu_links_hover_color = $api->getContantValueXML('elpis_mainmenu_links_hover_color');
      $elpis_minimenu_links_color = $api->getContantValueXML('elpis_minimenu_links_color');
      $elpis_minimenu_link_active_color = $api->getContantValueXML('elpis_minimenu_link_active_color');
			$elpis_minimenu_signup_background_color = $api->getContantValueXML('elpis_minimenu_signup_background_color');
			$elpis_minimenu_signup_font_color = $api->getContantValueXML('elpis_minimenu_signup_font_color');
			$elpis_minimenu_links_background_color = $api->getContantValueXML('elpis_minimenu_links_background_color');
			$elpis_mainmenu_links_hover_background_color = $api->getContantValueXML('elpis_mainmenu_links_hover_background_color');
      $elpis_footer_background_color = $api->getContantValueXML('elpis_footer_background_color');
      $elpis_footer_font_color = $api->getContantValueXML('elpis_footer_font_color');
      $elpis_footer_links_color = $api->getContantValueXML('elpis_footer_links_color');
      $elpis_footer_border_color = $api->getContantValueXML('elpis_footer_border_color');
      $elpis_theme_color = $api->getContantValueXML('elpis_theme_color');
      $elpis_body_background_color = $api->getContantValueXML('elpis_body_background_color');
      $elpis_font_color = $api->getContantValueXML('elpis_font_color');
      $elpis_font_color_light = $api->getContantValueXML('elpis_font_color_light');
      $elpis_links_color = $api->getContantValueXML('elpis_links_color');
      $elpis_links_hover_color = $api->getContantValueXML('elpis_links_hover_color');
      $elpis_headline_color = $api->getContantValueXML('elpis_headline_color');
      $elpis_border_color = $api->getContantValueXML('elpis_border_color');
      $elpis_box_background_color = $api->getContantValueXML('elpis_box_background_color');
      $elpis_form_label_color = $api->getContantValueXML('elpis_form_label_color');
      $elpis_input_background_color = $api->getContantValueXML('elpis_input_background_color');
      $elpis_input_font_color = $api->getContantValueXML('elpis_input_font_color');
      $elpis_input_border_color = $api->getContantValueXML('elpis_input_border_color');
      $elpis_button_background_color = $api->getContantValueXML('elpis_button_background_color');
      $elpis_button_background_color_hover = $api->getContantValueXML('elpis_button_background_color_hover');
      $elpis_button_font_color = $api->getContantValueXML('elpis_button_font_color');
      $elpis_button_border_color = $api->getContantValueXML('elpis_button_border_color');
      $elpis_comments_background_color = $api->getContantValueXML('elpis_comments_background_color');
    }

    $this->addElement('Dummy', 'header_settings', array(
        'label' => 'Header Styling Settings',
    ));
    $this->addElement('Text', "elpis_header_background_color", array(
        'label' => 'Header Background Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_header_background_color,
    ));

    $this->addElement('Text', "elpis_mainmenu_background_color", array(
        'label' => 'Main Menu Background Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_mainmenu_background_color,
    ));

    $this->addElement('Text', "elpis_mainmenu_links_color", array(
        'label' => 'Main Menu Link Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_mainmenu_links_color,
    ));

    $this->addElement('Text', "elpis_mainmenu_links_hover_color", array(
        'label' => 'Main Menu Link Hover Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_mainmenu_links_hover_color,
    ));
    $this->addElement('Text', "elpis_mainmenu_links_hover_background_color", array(
        'label' => 'Main Menu Link Hover Background Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_mainmenu_links_hover_background_color,
    ));

    $this->addElement('Text', "elpis_minimenu_links_color", array(
        'label' => 'Mini Menu Link Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_minimenu_links_color,
    ));

		$this->addElement('Text', "elpis_minimenu_links_background_color", array(
        'label' => 'Mini Menu Links Background Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_minimenu_links_background_color,
    ));
		
		$this->addElement('Text', "elpis_minimenu_signup_background_color", array(
        'label' => 'Mini Menu Signup Background Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_minimenu_signup_background_color,
    ));
		
		$this->addElement('Text', "elpis_minimenu_signup_font_color", array(
        'label' => 'Mini Menu Signup Font Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_minimenu_signup_font_color,
    ));

    $this->addDisplayGroup(array('elpis_header_background_color', 'elpis_mainmenu_background_color', 'elpis_mainmenu_links_color', 'elpis_mainmenu_links_hover_color', 'elpis_mainmenu_links_hover_background_color', 'elpis_minimenu_links_color', 'elpis_minimenu_links_background_color', 'elpis_minimenu_signup_background_color', 'elpis_minimenu_signup_font_color'), 'header_settings_group', array('disableLoadDefaultDecorators' => true));
    $header_settings_group = $this->getDisplayGroup('header_settings_group');
    $header_settings_group->setDecorators(array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'header_settings_group'))));

    $this->addElement('Dummy', 'footer_settings', array(
        'label' => 'Footer Styling Settings',
    ));
    $this->addElement('Text', "elpis_footer_background_color", array(
        'label' => 'Footer Background Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_footer_background_color,
    ));

    $this->addElement('Text', "elpis_footer_font_color", array(
        'label' => 'Footer Font Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_footer_font_color,
    ));

    $this->addElement('Text', "elpis_footer_links_color", array(
        'label' => 'Footer Link Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_footer_links_color,
    ));

    $this->addElement('Text', "elpis_footer_border_color", array(
        'label' => 'Footer Border Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_footer_border_color,
    ));
    $this->addDisplayGroup(array('elpis_footer_background_color', 'elpis_footer_font_color', 'elpis_footer_links_color', 'elpis_footer_border_color'), 'footer_settings_group', array('disableLoadDefaultDecorators' => true));
    $footer_settings_group = $this->getDisplayGroup('footer_settings_group');
    $footer_settings_group->setDecorators(array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'footer_settings_group'))));

    $this->addElement('Dummy', 'body_settings', array(
        'label' => 'Body Styling Settings',
    ));
    $this->addElement('Text', "elpis_theme_color", array(
        'label' => 'Theme Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_theme_color,
    ));

    $this->addElement('Text', "elpis_body_background_color", array(
        'label' => 'Body Background Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_body_background_color,
    ));

    $this->addElement('Text', "elpis_font_color", array(
        'label' => 'Font Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_font_color,
    ));

    $this->addElement('Text', "elpis_font_color_light", array(
        'label' => 'Font Light Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_font_color_light,
    ));

    $this->addElement('Text', "elpis_links_color", array(
      'label' => 'Link Color',
      'allowEmpty' => false,
      'required' => true,
      'class' => 'SEcolor',
      'value' => $elpis_links_color,
    ));

    $this->addElement('Text', "elpis_links_hover_color", array(
        'label' => 'Link Hover Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_links_hover_color,
    ));

    $this->addElement('Text', "elpis_headline_color", array(
        'label' => 'Headline Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_headline_color,
    ));

    $this->addElement('Text', "elpis_border_color", array(
        'label' => 'Border Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_border_color,
    ));
    $this->addElement('Text', "elpis_box_background_color", array(
        'label' => 'Box Background Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_box_background_color,
    ));

    $this->addElement('Text', "elpis_form_label_color", array(
        'label' => 'Form Label Font Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_form_label_color,
    ));

    $this->addElement('Text', "elpis_input_background_color", array(
        'label' => 'Input Background Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_input_background_color,
    ));

    $this->addElement('Text', "elpis_input_font_color", array(
        'label' => 'Input Font Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_input_font_color,
    ));

    $this->addElement('Text', "elpis_input_border_color", array(
        'label' => 'Input Border Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_input_border_color,
    ));

    $this->addElement('Text', "elpis_button_background_color", array(
        'label' => 'Button Background Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_button_background_color,
    ));
    $this->addElement('Text', "elpis_button_background_color_hover", array(
        'label' => 'Button Background Hovor Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_button_background_color_hover,
    ));

    $this->addElement('Text', "elpis_button_font_color", array(
        'label' => 'Button Font Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_button_font_color,
    ));
    $this->addElement('Text', "elpis_button_border_color", array(
        'label' => 'Button Border Color',
        'allowEmpty' => false,
        'required' => true,
        'class' => 'SEcolor',
        'value' => $elpis_button_border_color,
    ));
    $this->addElement('Text', "elpis_comments_background_color", array(
      'label' => 'Comments Background Color',
      'allowEmpty' => false,
      'required' => true,
      'class' => 'SEcolor',
      'value' => $elpis_comments_background_color,
    ));

    $this->addDisplayGroup(array('elpis_theme_color','elpis_body_background_color', 'elpis_font_color', 'elpis_font_color_light', 'elpis_links_color', 'elpis_links_hover_color','elpis_headline_color', 'elpis_border_color', 'elpis_box_background_color', 'elpis_form_label_color', 'elpis_input_background_color', 'elpis_input_font_color', 'elpis_input_border_color', 'elpis_button_background_color', 'elpis_button_background_color_hover', 'elpis_button_font_color', 'elpis_button_border_color', 'elpis_dashboard_list_background_color_hover', 'elpis_dashboard_list_border_color', 'elpis_dashboard_font_color', 'elpis_dashboard_link_color', 'elpis_comments_background_color'), 'body_settings_group', array('disableLoadDefaultDecorators' => true));
    $body_settings_group = $this->getDisplayGroup('body_settings_group');
    $body_settings_group->setDecorators(array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'body_settings_group'))));

    $this->addElement('Button', 'save', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));
//     $this->addElement('Button', 'submit', array(
//         'label' => 'Save as Draft',
//         'type' => 'submit',
//         'ignore' => true,
//         'decorators' => array('ViewHelper')
//     ));
//     $this->addDisplayGroup(array('save', 'submit'), 'buttons');
  }
}
