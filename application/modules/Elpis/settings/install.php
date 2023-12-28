<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Elpis
 * @copyright  Copyright 2006-2022 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: install.php 2022-06-21
 */

class Elpis_Installer extends Engine_Package_Installer_Module {

  public function onInstall() {
    $db = $this->getDb();
    if($this->_databaseOperationType != 'upgrade') {
      $default_constants = array(
        'theme_color'  => '1',
        'custom_theme_color'  => '1',
        'theme_widget_radius' => '10',
        'contrast_mode' => 'dark_mode',
        'elpis_header_background_color'  => '#ffffff',
        'elpis_mainmenu_background_color'  => '#ffffff',
        'elpis_mainmenu_links_color'  => '#002646',
        'elpis_mainmenu_links_hover_color'  => '#FFFFFF',
        'elpis_mainmenu_links_hover_background_color'  => '#2491EB',
        'elpis_minimenu_links_color'  => '#FFFFFF',
        'elpis_minimenu_link_active_color'  => '#FFFFFF',
				'elpis_minimenu_links_background_color' => '#2491eb',
				'elpis_minimenu_signup_background_color' => '#002646',
				'elpis_minimenu_signup_font_color' => '#FFFFFF',
        'elpis_footer_background_color'  => '#FFFFFF',
        'elpis_footer_font_color'  => '#676767',
        'elpis_footer_links_color'  => '#676767',
        'elpis_footer_border_color'  => '#e4e4e4',
        'elpis_theme_color'  => '#2491eb',
        'elpis_body_background_color'  => '#eff4fb',
        'elpis_font_color'  => '#5f727f',
        'elpis_font_color_light'  => '#808D97',
        'elpis_links_color'  => '#444f5d',
        'elpis_links_hover_color'  => '#03598f',
        'elpis_headline_color'  => '#1c2735',
        'elpis_border_color'  => '#e2e4e6',
        'elpis_box_background_color'  => '#FFFFFF',
        'elpis_form_label_color'  => '#455B6B',
        'elpis_input_background_color'  => '#fff',
        'elpis_input_font_color'  => '#5f727f',
        'elpis_input_border_color'  => '#d7d8da',
        'elpis_button_background_color'  => '#2491eb',
        'elpis_button_background_color_hover'  => '#59b4ff',
        'elpis_button_font_color'  => '#FFFFFF',
        'elpis_button_border_color'  => '#2491eb',
        'elpis_comments_background_color'  => '#fff',
  			'elpis_body_fontfamily' => '"Lato"',
        'elpis_heading_fontfamily' => '"Lato"',
        'elpis_mainmenu_fontfamily' => '"Lato"',
        'elpis_tab_fontfamily' => '"Lato"',
      );
      $this->readWriteXML('', '', $default_constants);
      
      // landing page
      $select = new Zend_Db_Select($db);
      $select
          ->from('engine4_core_pages')
          ->where('name = ?', 'core_index_index')
          ->limit(1);
      $pageId = $select->query()->fetchObject()->page_id;
      
      $select = new Zend_Db_Select($db);
      $select
          ->from('engine4_core_content')
          ->where('page_id = ?', $pageId)
          ->where('type = ?', 'widget')
          ->where('name = ?', 'core.landing-page-banner');
      $info = $select->query()->fetch();
      if(!empty($info) ) {
        // Insert banner
        $db->insert('engine4_core_banners', array(
          'name' => 'core',
          'module' => 'core',
          'title' => 'Welcome to our Network',
          'body' => 'We offer everyone the opportunity to engage and discover the world with fun and positivity.',
          'photo_id' => 0,
          'params' => '{"label":"Join","uri":"signup"}',
          'custom' => 0
        ));
        $bannerId = $db->lastInsertId();
        if( $bannerId ) {
          $db->query('UPDATE `engine4_core_content` SET `params` = \'{"bannerId":"'.$bannerId.'","height":"550","title":"","nomobile":"0","name":"core.landing-page-banner"}\' WHERE `engine4_core_content`.`name` = "core.landing-page-banner" AND `engine4_core_content`.`page_id` = "3";');
        }
      }
    }
    parent::onInstall();
  }
  
  public function onDisable() {

    $db = $this->getDb();

    $db->query("UPDATE  `engine4_core_themes` SET  `active` =  '0' WHERE  `engine4_core_themes`.`name` ='elpis' LIMIT 1");
    $db->query("UPDATE  `engine4_core_themes` SET  `active` =  '1' WHERE  `engine4_core_themes`.`name` ='insignia' LIMIT 1");
    parent::onDisable();
  }

  function onEnable() {

    $db = $this->getDb();

    //Theme Enabled and disabled
    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_themes', 'name')
            ->where('active = ?', 1)
            ->limit(1);
    $themeActive = $select->query()->fetch();
    if($themeActive) {
      $db->query("UPDATE  `engine4_core_themes` SET  `active` =  '0' WHERE  `engine4_core_themes`.`name` ='".$themeActive['name']."' LIMIT 1");
      $db->query("UPDATE  `engine4_core_themes` SET  `active` =  '1' WHERE  `engine4_core_themes`.`name` ='elpis' LIMIT 1");
    }
    parent::onEnable();
  }
  
  function readWriteXML($keys, $value, $default_constants = null) {
    $filePath = APPLICATION_PATH . "/application/settings/constants.xml";
    $results = simplexml_load_file($filePath);

    if (!empty($keys) && !empty($value)) {
        $contactsThemeArray = array($keys => $value);
    } elseif (!empty($keys)) {
        $contactsThemeArray = array($keys => '');
    } elseif ($default_constants) {
        $contactsThemeArray = $default_constants;
    }

    foreach ($contactsThemeArray as $key => $value) {
      $xmlNodes = $results->xpath('/root/constant[name="' . $key . '"]');
      $nodeName = @$xmlNodes[0];
      $params = json_decode(json_encode($nodeName));
      $paramsVal = @$params->value;
      if ($paramsVal && $paramsVal != '' && $paramsVal != null) {
          $nodeName->value = $value;
      } else {
          $entry = $results->addChild('constant');
          $entry->addChild('name', $key);
          $entry->addChild('value', $value);
      }
    }
    return $results->asXML($filePath);
  }
}
