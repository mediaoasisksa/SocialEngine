<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: styling.tpl 2017-06-10 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>

<?php $settings = Engine_Api::_()->getApi('settings', 'core');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jscolor/jscolor.js');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jquery.min.js');
?>

<script>
hashSign = '#';
</script>
<?php include APPLICATION_PATH .  '/application/modules/Sescompany/views/scripts/dismiss_message.tpl';?>
<div class='clear'>
  <div class='settings sesbasic_admin_form sescompany_themes_form'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>
<script>

  window.addEvent('domready', function() {
    changeThemeColor("<?php echo Engine_Api::_()->sescompany()->getContantValueXML('theme_color'); ?>", '');
  });
  
  function changeCustomThemeColor(value) {
	  changeThemeColor(value, 'custom');
  }


	function changeThemeColor(value, custom) {
	
	  if(custom == '' && (value == 1 || value == 2 || value == 3 || value == 4 || value == 6 || value == 7 || value == 8 || value == 9 || value == 10)) {
	    if($('common_settings-wrapper'))
				$('common_settings-wrapper').style.display = 'none';
		  if($('header_settings-wrapper'))
				$('header_settings-wrapper').style.display = 'none';
	    if($('footer_settings-wrapper'))
				$('footer_settings-wrapper').style.display = 'none';
		  if($('body_settings-wrapper'))
				$('body_settings-wrapper').style.display = 'none';
		  if($('general_settings_group'))
			  $('general_settings_group').style.display = 'none';
			if($('header_settings_group'))
			  $('header_settings_group').style.display = 'none';
			if($('footer_settings_group'))
			  $('footer_settings_group').style.display = 'none';
			if($('body_settings_group'))
			  $('body_settings_group').style.display = 'none';
	    if($('custom_theme_color-wrapper'))
				$('custom_theme_color-wrapper').style.display = 'none';
	  } else if(custom == '' && value == 5) {
	    
	    if($('custom_theme_color-wrapper'))
				$('custom_theme_color-wrapper').style.display = 'block';
		  changeCustomThemeColor(5);
	  } else if(custom == 'custom') {
		  if($('common_settings-wrapper'))
				$('common_settings-wrapper').style.display = 'block';
		  if($('header_settings-wrapper'))
				$('header_settings-wrapper').style.display = 'block';
	    if($('footer_settings-wrapper'))
				$('footer_settings-wrapper').style.display = 'block';
			if($('body_settings-wrapper'))
				$('body_settings-wrapper').style.display = 'block';
		  if($('general_settings_group'))
			  $('general_settings_group').style.display = 'block';
			if($('header_settings_group'))
			  $('header_settings_group').style.display = 'block';
			if($('footer_settings_group'))
			  $('footer_settings_group').style.display = 'block';
			if($('body_settings_group'))
			  $('body_settings_group').style.display = 'block';
	  }


		if(value == 1) {
			console.log(document.getElementById('company_theme_color'));
      //Theme Base Styling
      if($('company_theme_color')) {
        $('company_theme_color').value = '#CC0821';
        //document.getElementById('company_theme_color').color.fromString('#000');
      }
			
			//Body Styling
			if($('company_body_background_color')) {
				$('company_body_background_color').value = '#eeeeee';
				//document.getElementById('company_body_background_color').color.fromString('#eeeeee');
			}
			if($('company_font_color')) {
				$('company_font_color').value = '#000';
				//document.getElementById('company_font_color').color.fromString('#000');
			}
			if($('company_font_color_light')) {
				$('company_font_color_light').value = '#424242';
				//document.getElementById('company_font_color_light').color.fromString('#424242');
			}
			
			if($('company_heading_color')) {
				$('company_heading_color').value = '#000';
				//document.getElementById('company_heading_color').color.fromString('#000');
			}
			if($('company_links_color')) {
				$('company_links_color').value = '#292929';
				//document.getElementById('company_links_color').color.fromString('#292929');
			}
			if($('company_links_hover_color')) {
				$('company_links_hover_color').value = '#000';
				//document.getElementById('company_links_hover_color').color.fromString('#000');
			}
			if($('company_content_background_color')) {
				$('company_content_background_color').value = '#fff';
				//document.getElementById('company_content_background_color').color.fromString('#fff');
			}
			if($('company_content_border_color')) {
				$('company_content_border_color').value = '#E7E7E7';
				//document.getElementById('company_content_border_color').color.fromString('#E7E7E7');
			}
			if($('company_form_label_color')) {
				$('company_form_label_color').value = '#000';
				//document.getElementById('company_form_label_color').color.fromString('#000');
			}
			if($('company_input_background_color')) {
				$('company_input_background_color').value = '#fff';
				//document.getElementById('company_input_background_color').color.fromString('#fff');
			}
			if($('company_input_font_color')) {
				$('company_input_font_color').value = '#000';
				//document.getElementById('company_input_font_color').color.fromString('#000');
			}
			if($('company_input_border_color')) {
				$('company_input_border_color').value = '#E7E7E7';
				//document.getElementById('company_input_border_color').color.fromString('#E7E7E7');
			}
			if($('company_button_background_color')) {
				$('company_button_background_color').value = '#CC0821';
				//document.getElementById('company_button_background_color').color.fromString('#000');
			}
			if($('company_button_background_color_hover')) {
				$('company_button_background_color_hover').value = '#CC0821'; //document.getElementById('company_button_background_color_hover').color.fromString('#1e1e1e');
			}
			if($('company_button_border_color')) {
				$('company_button_border_color').value = '#CC0821'; //document.getElementById('company_button_background_color_hover').color.fromString('#000');
			}
			if($('company_button_font_color')) {
				$('company_button_font_color').value = '#fff';
				//document.getElementById('company_button_font_color').color.fromString('#fff');
			}
			if($('company_button_font_hover_color')) {
				$('company_button_font_hover_color').value = '#fff';
				//document.getElementById('company_button_font_hover_color').color.fromString('#fff');
			}
			if($('company_comment_background_color')) {
				$('company_comment_background_color').value = '#f6f7f9';
				//document.getElementById('company_comment_background_color').color.fromString('#f6f7f9');
			}
			//Body Styling
			
			//Header Styling
			if($('company_header_background_color')) {
				$('company_header_background_color').value = '#fff';  
				//document.getElementById('company_header_background_color').color.fromString('#fff');
			}
			if($('company_header_border_color')) {
				$('company_header_border_color').value = '#eeeeee';
				//document.getElementById('company_header_border_color').color.fromString('#eeeeee');
			}
			if($('company_menu_logo_top_space')) {
				$('company_menu_logo_top_space').value = '10px';
			}
			if($('company_mainmenu_links_color')) {
				$('company_mainmenu_links_color').value = '#FFFFFF';
				//document.getElementById('company_mainmenu_links_color').color.fromString('#1c1c1c');
			}
			if($('company_mainmenu_links_hover_color')) {
				$('company_mainmenu_links_hover_color').value = '#FFF';
				//document.getElementById('company_mainmenu_links_hover_color').color.fromString('#000');
			}
			if($('company_minimenu_links_color')) {
				$('company_minimenu_links_color').value = '#292929';
				//document.getElementById('company_minimenu_links_color').color.fromString('#292929');
			}
			if($('company_minimenu_links_hover_color')) {
				$('company_minimenu_links_hover_color').value = '#CC0821';
				//document.getElementById('company_minimenu_links_hover_color').color.fromString('#000');
			}
			if($('company_header_searchbox_background_color')) {
				$('company_header_searchbox_background_color').value = '#ebeeee'; //document.getElementById('company_header_searchbox_background_color').color.fromString('#fff');
			}
			if($('company_header_searchbox_text_color')) {
				$('company_header_searchbox_text_color').value = '#767676';
				//document.getElementById('company_header_searchbox_text_color').color.fromString('#636363');
			}
			if($('company_header_searchbox_border_color')) {
				$('company_header_searchbox_border_color').value = '#E7E7E7';
				//document.getElementById('company_header_searchbox_border_color').color.fromString('#E7E7E7');
			}
			//Header Styling
			
			//Footer Styling
			if($('company_footer_background_color')) {
				$('company_footer_background_color').value = '#222';
				//document.getElementById('company_footer_background_color').color.fromString('#222');
			}
			if($('company_footer_border_color')) {
				$('company_footer_border_color').value = '#E7E7E7';
				//document.getElementById('company_footer_border_color').color.fromString('#E7E7E7');
			}
			if($('company_footer_text_color')) {
				$('company_footer_text_color').value = '#767676';
				//document.getElementById('company_footer_text_color').color.fromString('#FFFFFF');
			}
			if($('company_footer_links_color')) {
				$('company_footer_links_color').value = '#767676';
				//document.getElementById('company_footer_links_color').color.fromString('#FFFFFF');
			}
			if($('company_footer_links_hover_color')) {
				$('company_footer_links_hover_color').value = '#FFFFFF';
				//document.getElementById('company_footer_links_hover_color').color.fromString('#FFFFFF');
			}
			//Footer Styling
		} 
		else if(value == 2) {
			//Theme Base Styling
			if($('company_theme_color')) {
				$('company_theme_color').value = '#3A6EE8';
				//document.getElementById('company_theme_color').color.fromString('#0288D1');
			}
			//Theme Base Styling
			
			//Body Styling
			if($('company_body_background_color')) {
				$('company_body_background_color').value = '#EEEEEE';
				//document.getElementById('company_body_background_color').color.fromString('#F6F9FC');
			}
			if($('company_font_color')) {
				$('company_font_color').value = '#424242';
				//document.getElementById('company_font_color').color.fromString('#424242');
			}
			if($('company_font_color_light')) {
				$('company_font_color_light').value = '#424242';
				//document.getElementById('company_font_color_light').color.fromString('#424242');
			}
			
			if($('company_heading_color')) {
				$('company_heading_color').value = '#000';
				//document.getElementById('company_heading_color').color.fromString('#000');
			}
			if($('company_links_color')) {
				$('company_links_color').value = '#202020';
				//document.getElementById('company_links_color').color.fromString('#202020');
			}
			if($('company_links_hover_color')) {
				$('company_links_hover_color').value = '#3A6EE8';
				//document.getElementById('company_links_hover_color').color.fromString('#0288D1');
			}
			if($('company_content_background_color')) {
				$('company_content_background_color').value = '#fff';
				//document.getElementById('company_content_background_color').color.fromString('#fff');
			}
			if($('company_content_border_color')) {
				$('company_content_border_color').value = '#ebecee';
				//document.getElementById('company_content_border_color').color.fromString('#ebecee');
			}
			if($('company_form_label_color')) {
				$('company_form_label_color').value = '#5a5a5a';
				//document.getElementById('company_form_label_color').color.fromString('#5a5a5a');
			}
			if($('company_input_background_color')) {
				$('company_input_background_color').value = '#f5f5f5';
				//document.getElementById('company_input_background_color').color.fromString('#f5f5f5');
			}
			if($('company_input_font_color')) {
				$('company_input_font_color').value = '#5a5a5a';
				//document.getElementById('company_input_font_color').color.fromString('#5a5a5a');
			}
			if($('company_input_border_color')) {
				$('company_input_border_color').value = '#cacaca';
				//document.getElementById('company_input_border_color').color.fromString('#cacaca');
			}
			if($('company_button_background_color')) {
				$('company_button_background_color').value = '#3A6EE8';
				//document.getElementById('company_button_background_color').color.fromString('#0288D1');
			}
			if($('company_button_background_color_hover')) {
				$('company_button_background_color_hover').value = '#3A6EE8'; //document.getElementById('company_button_background_color_hover').color.fromString('#0097e9');
			}
			if($('company_button_border_color')) {
				$('company_button_border_color').value = '#3A6EE8'; //document.getElementById('company_button_background_color_hover').color.fromString('#0288D1');
			}
			if($('company_button_font_color')) {
				$('company_button_font_color').value = '#fff';
				//document.getElementById('company_button_font_color').color.fromString('#fff');
			}
			if($('company_button_font_hover_color')) {
				$('company_button_font_hover_color').value = '#fff';
				//document.getElementById('company_button_font_hover_color').color.fromString('#fff');
			}
			if($('company_comment_background_color')) {
				$('company_comment_background_color').value = '#f6f7f9';
				//document.getElementById('company_comment_background_color').color.fromString('#f6f7f9');
			}
			//Body Styling
			
			//Header Styling
			if($('company_header_background_color')) {
				$('company_header_background_color').value = '#fff';
				//document.getElementById('company_header_background_color').color.fromString('#fff');
			}
			if($('company_header_border_color')) {
				$('company_header_border_color').value = '#eeeeee';
				//document.getElementById('company_header_border_color').color.fromString('#eeeeee');
			}
			if($('company_menu_logo_top_space')) {
				$('company_menu_logo_top_space').value = '10px';
			}
			if($('company_mainmenu_links_color')) {
				$('company_mainmenu_links_color').value = '#FFF';
				//document.getElementById('company_mainmenu_links_color').color.fromString('#1c1c1c');
			}
			if($('company_mainmenu_links_hover_color')) {
				$('company_mainmenu_links_hover_color').value = '#FFF';
				//document.getElementById('company_mainmenu_links_hover_color').color.fromString('#0288D1');
			}
			if($('company_minimenu_links_color')) {
				$('company_minimenu_links_color').value = '#424242';
				//document.getElementById('company_minimenu_links_color').color.fromString('#424242');
			}
			if($('company_minimenu_links_hover_color')) {
				$('company_minimenu_links_hover_color').value = '#3A6EE8';
				//document.getElementById('company_minimenu_links_hover_color').color.fromString('#0288D1');
			}
			if($('company_header_searchbox_background_color')) {
				$('company_header_searchbox_background_color').value = '#ebeeee'; //document.getElementById('company_header_searchbox_background_color').color.fromString('#fff');
			}
			if($('company_header_searchbox_text_color')) {
				$('company_header_searchbox_text_color').value = '#767676';
				//document.getElementById('company_header_searchbox_text_color').color.fromString('#636363');
			}
			if($('company_header_searchbox_border_color')) {
				$('company_header_searchbox_border_color').value = '#E7E7E7';
				//document.getElementById('company_header_searchbox_border_color').color.fromString('#E7E7E7');
			}
			//Header Styling
			
			//Footer Styling
			if($('company_footer_background_color')) {
				$('company_footer_background_color').value = '#222';
				//document.getElementById('company_footer_background_color').color.fromString('#222');
			}
			if($('company_footer_border_color')) {
				$('company_footer_border_color').value = '#3A6EE8';
				//document.getElementById('company_footer_border_color').color.fromString('#0288D1');
			}
			if($('company_footer_text_color')) {
				$('company_footer_text_color').value = '#767676';
				//document.getElementById('company_footer_text_color').color.fromString('#767676');
			}
			if($('company_footer_links_color')) {
				$('company_footer_links_color').value = '#767676';
				//document.getElementById('company_footer_links_color').color.fromString('#767676');
			}
			if($('company_footer_links_hover_color')) {
				$('company_footer_links_hover_color').value = '#ffffff';
				//document.getElementById('company_footer_links_hover_color').color.fromString('#ffffff');
			}
			//Footer Styling
		} 
			else if(value == 3) {
			//Theme Base Styling
			if($('company_theme_color')) {
				$('company_theme_color').value = '#fc7f0c';
				//document.getElementById('company_theme_color').color.fromString('#0288D1');
			}
			//Theme Base Styling
			
			//Body Styling
			if($('company_body_background_color')) {
				$('company_body_background_color').value = '#191a1c';
				//document.getElementById('company_body_background_color').color.fromString('#F6F9FC');
			}
			if($('company_font_color')) {
				$('company_font_color').value = '#989898';
				//document.getElementById('company_font_color').color.fromString('#424242');
			}
			if($('company_font_color_light')) {
				$('company_font_color_light').value = '#989898';
				//document.getElementById('company_font_color_light').color.fromString('#424242');
			}
			
			if($('company_heading_color')) {
				$('company_heading_color').value = '#989898';
				//document.getElementById('company_heading_color').color.fromString('#000');
			}
			if($('company_links_color')) {
				$('company_links_color').value = '#989898';
				//document.getElementById('company_links_color').color.fromString('#202020');
			}
			if($('company_links_hover_color')) {
				$('company_links_hover_color').value = '#fc7f0c';
				//document.getElementById('company_links_hover_color').color.fromString('#0288D1');
			}
			if($('company_content_background_color')) {
				$('company_content_background_color').value = '#252525';
				//document.getElementById('company_content_background_color').color.fromString('#fff');
			}
			if($('company_content_border_color')) {
				$('company_content_border_color').value = '#4a4a4a';
				//document.getElementById('company_content_border_color').color.fromString('#ebecee');
			}
			if($('company_form_label_color')) {
				$('company_form_label_color').value = '#989898';
				//document.getElementById('company_form_label_color').color.fromString('#5a5a5a');
			}
			if($('company_input_background_color')) {
				$('company_input_background_color').value = '#252525';
				//document.getElementById('company_input_background_color').color.fromString('#f5f5f5');
			}
			if($('company_input_font_color')) {
				$('company_input_font_color').value = '#989898';
				//document.getElementById('company_input_font_color').color.fromString('#5a5a5a');
			}
			if($('company_input_border_color')) {
				$('company_input_border_color').value = '#cacaca';
				//document.getElementById('company_input_border_color').color.fromString('#cacaca');
			}
			if($('company_button_background_color')) {
				$('company_button_background_color').value = '#fc7f0c';
				//document.getElementById('company_button_background_color').color.fromString('#0288D1');
			}
			if($('company_button_background_color_hover')) {
				$('company_button_background_color_hover').value = '#fc7f0c'; //document.getElementById('company_button_background_color_hover').color.fromString('#0097e9');
			}
			if($('company_button_border_color')) {
				$('company_button_border_color').value = '#fc7f0c'; //document.getElementById('company_button_background_color_hover').color.fromString('#0288D1');
			}
			if($('company_button_font_color')) {
				$('company_button_font_color').value = '#4a4a4a';
				//document.getElementById('company_button_font_color').color.fromString('#fff');
			}
			if($('company_button_font_hover_color')) {
				$('company_button_font_hover_color').value = '#4a4a4a';
				//document.getElementById('company_button_font_hover_color').color.fromString('#fff');
			}
			if($('company_comment_background_color')) {
				$('company_comment_background_color').value = '#f6f7f9';
				//document.getElementById('company_comment_background_color').color.fromString('#f6f7f9');
			}
			//Body Styling
			
			//Header Styling
			if($('company_header_background_color')) {
				$('company_header_background_color').value = '#252525';
				//document.getElementById('company_header_background_color').color.fromString('#fff');
			}
			if($('company_header_border_color')) {
				$('company_header_border_color').value = '#eeeeee';
				//document.getElementById('company_header_border_color').color.fromString('#eeeeee');
			}
			if($('company_menu_logo_top_space')) {
				$('company_menu_logo_top_space').value = '10px';
			}
			if($('company_mainmenu_links_color')) {
				$('company_mainmenu_links_color').value = '#222';
				//document.getElementById('company_mainmenu_links_color').color.fromString('#1c1c1c');
			}
			if($('company_mainmenu_links_hover_color')) {
				$('company_mainmenu_links_hover_color').value = '#FFF';
				//document.getElementById('company_mainmenu_links_hover_color').color.fromString('#0288D1');
			}
			if($('company_minimenu_links_color')) {
				$('company_minimenu_links_color').value = '#989898';
				//document.getElementById('company_minimenu_links_color').color.fromString('#424242');
			}
			if($('company_minimenu_links_hover_color')) {
				$('company_minimenu_links_hover_color').value = '#fc7f0c';
				//document.getElementById('company_minimenu_links_hover_color').color.fromString('#0288D1');
			}
			if($('company_header_searchbox_background_color')) {
				$('company_header_searchbox_background_color').value = '#ebeeee'; //document.getElementById('company_header_searchbox_background_color').color.fromString('#fff');
			}
			if($('company_header_searchbox_text_color')) {
				$('company_header_searchbox_text_color').value = '#767676';
				//document.getElementById('company_header_searchbox_text_color').color.fromString('#636363');
			}
			if($('company_header_searchbox_border_color')) {
				$('company_header_searchbox_border_color').value = '#E7E7E7';
				//document.getElementById('company_header_searchbox_border_color').color.fromString('#E7E7E7');
			}
			//Header Styling
			
			//Footer Styling
			if($('company_footer_background_color')) {
				$('company_footer_background_color').value = '#222';
				//document.getElementById('company_footer_background_color').color.fromString('#222');
			}
			if($('company_footer_border_color')) {
				$('company_footer_border_color').value = '#fc7f0c';
				//document.getElementById('company_footer_border_color').color.fromString('#0288D1');
			}
			if($('company_footer_text_color')) {
				$('company_footer_text_color').value = '#767676';
				//document.getElementById('company_footer_text_color').color.fromString('#767676');
			}
			if($('company_footer_links_color')) {
				$('company_footer_links_color').value = '#767676';
				//document.getElementById('company_footer_links_color').color.fromString('#767676');
			}
			if($('company_footer_links_hover_color')) {
				$('company_footer_links_hover_color').value = '#ffffff';
				//document.getElementById('company_footer_links_hover_color').color.fromString('#ffffff');
			}
			//Footer Styling
		} 
		else if(value == 4) {
			//Theme Base Styling
			if($('company_theme_color')) {
				$('company_theme_color').value = '#03A9F4';
				//document.getElementById('company_theme_color').color.fromString('#0288D1');
			}
			//Theme Base Styling
			
			//Body Styling
			if($('company_body_background_color')) {
				$('company_body_background_color').value = '#EEEEEE';
				//document.getElementById('company_body_background_color').color.fromString('#F6F9FC');
			}
			if($('company_font_color')) {
				$('company_font_color').value = '#424242';
				//document.getElementById('company_font_color').color.fromString('#424242');
			}
			if($('company_font_color_light')) {
				$('company_font_color_light').value = '#424242';
				//document.getElementById('company_font_color_light').color.fromString('#424242');
			}
			
			if($('company_heading_color')) {
				$('company_heading_color').value = '#000';
				//document.getElementById('company_heading_color').color.fromString('#000');
			}
			if($('company_links_color')) {
				$('company_links_color').value = '#202020';
				//document.getElementById('company_links_color').color.fromString('#202020');
			}
			if($('company_links_hover_color')) {
				$('company_links_hover_color').value = '#03A9F4';
				//document.getElementById('company_links_hover_color').color.fromString('#0288D1');
			}
			if($('company_content_background_color')) {
				$('company_content_background_color').value = '#fff';
				//document.getElementById('company_content_background_color').color.fromString('#fff');
			}
			if($('company_content_border_color')) {
				$('company_content_border_color').value = '#ebecee';
				//document.getElementById('company_content_border_color').color.fromString('#ebecee');
			}
			if($('company_form_label_color')) {
				$('company_form_label_color').value = '#5a5a5a';
				//document.getElementById('company_form_label_color').color.fromString('#5a5a5a');
			}
			if($('company_input_background_color')) {
				$('company_input_background_color').value = '#f5f5f5';
				//document.getElementById('company_input_background_color').color.fromString('#f5f5f5');
			}
			if($('company_input_font_color')) {
				$('company_input_font_color').value = '#5a5a5a';
				//document.getElementById('company_input_font_color').color.fromString('#5a5a5a');
			}
			if($('company_input_border_color')) {
				$('company_input_border_color').value = '#cacaca';
				//document.getElementById('company_input_border_color').color.fromString('#cacaca');
			}
			if($('company_button_background_color')) {
				$('company_button_background_color').value = '#03A9F4';
				//document.getElementById('company_button_background_color').color.fromString('#0288D1');
			}
			if($('company_button_background_color_hover')) {
				$('company_button_background_color_hover').value = '#03A9F4'; //document.getElementById('company_button_background_color_hover').color.fromString('#0097e9');
			}
			if($('company_button_border_color')) {
				$('company_button_border_color').value = '#03A9F4'; //document.getElementById('company_button_background_color_hover').color.fromString('#0288D1');
			}
			if($('company_button_font_color')) {
				$('company_button_font_color').value = '#fff';
				//document.getElementById('company_button_font_color').color.fromString('#fff');
			}
			if($('company_button_font_hover_color')) {
				$('company_button_font_hover_color').value = '#fff';
				//document.getElementById('company_button_font_hover_color').color.fromString('#fff');
			}
			if($('company_comment_background_color')) {
				$('company_comment_background_color').value = '#f6f7f9';
				//document.getElementById('company_comment_background_color').color.fromString('#f6f7f9');
			}
			//Body Styling
			
			//Header Styling
			if($('company_header_background_color')) {
				$('company_header_background_color').value = '#fff';
				//document.getElementById('company_header_background_color').color.fromString('#fff');
			}
			if($('company_header_border_color')) {
				$('company_header_border_color').value = '#eeeeee';
				//document.getElementById('company_header_border_color').color.fromString('#eeeeee');
			}
			if($('company_menu_logo_top_space')) {
				$('company_menu_logo_top_space').value = '10px';
			}
			if($('company_mainmenu_links_color')) {
				$('company_mainmenu_links_color').value = '#FFF';
				//document.getElementById('company_mainmenu_links_color').color.fromString('#1c1c1c');
			}
			if($('company_mainmenu_links_hover_color')) {
				$('company_mainmenu_links_hover_color').value = '#FFF';
				//document.getElementById('company_mainmenu_links_hover_color').color.fromString('#0288D1');
			}
			if($('company_minimenu_links_color')) {
				$('company_minimenu_links_color').value = '#424242';
				//document.getElementById('company_minimenu_links_color').color.fromString('#424242');
			}
			if($('company_minimenu_links_hover_color')) {
				$('company_minimenu_links_hover_color').value = '#03A9F4';
				//document.getElementById('company_minimenu_links_hover_color').color.fromString('#0288D1');
			}
			if($('company_header_searchbox_background_color')) {
				$('company_header_searchbox_background_color').value = '#ebeeee'; //document.getElementById('company_header_searchbox_background_color').color.fromString('#fff');
			}
			if($('company_header_searchbox_text_color')) {
				$('company_header_searchbox_text_color').value = '#767676';
				//document.getElementById('company_header_searchbox_text_color').color.fromString('#636363');
			}
			if($('company_header_searchbox_border_color')) {
				$('company_header_searchbox_border_color').value = '#E7E7E7';
				//document.getElementById('company_header_searchbox_border_color').color.fromString('#E7E7E7');
			}
			//Header Styling
			
			//Footer Styling
			if($('company_footer_background_color')) {
				$('company_footer_background_color').value = '#222';
				//document.getElementById('company_footer_background_color').color.fromString('#222');
			}
			if($('company_footer_border_color')) {
				$('company_footer_border_color').value = '#03A9F4';
				//document.getElementById('company_footer_border_color').color.fromString('#0288D1');
			}
			if($('company_footer_text_color')) {
				$('company_footer_text_color').value = '#767676';
				//document.getElementById('company_footer_text_color').color.fromString('#767676');
			}
			if($('company_footer_links_color')) {
				$('company_footer_links_color').value = '#767676';
				//document.getElementById('company_footer_links_color').color.fromString('#767676');
			}
			if($('company_footer_links_hover_color')) {
				$('company_footer_links_hover_color').value = '#ffffff';
				//document.getElementById('company_footer_links_hover_color').color.fromString('#ffffff');
			}
			//Footer Styling
		} 
		else if(value == 6) {
			//Theme Base Styling
			if($('company_theme_color')) {
				$('company_theme_color').value = '#e91e63';
				//document.getElementById('company_theme_color').color.fromString('#0288D1');
			}
			//Theme Base Styling
			
			//Body Styling
			if($('company_body_background_color')) {
				$('company_body_background_color').value = '#EEEEEE';
				//document.getElementById('company_body_background_color').color.fromString('#F6F9FC');
			}
			if($('company_font_color')) {
				$('company_font_color').value = '#424242';
				//document.getElementById('company_font_color').color.fromString('#424242');
			}
			if($('company_font_color_light')) {
				$('company_font_color_light').value = '#424242';
				//document.getElementById('company_font_color_light').color.fromString('#424242');
			}
			
			if($('company_heading_color')) {
				$('company_heading_color').value = '#000';
				//document.getElementById('company_heading_color').color.fromString('#000');
			}
			if($('company_links_color')) {
				$('company_links_color').value = '#202020';
				//document.getElementById('company_links_color').color.fromString('#202020');
			}
			if($('company_links_hover_color')) {
				$('company_links_hover_color').value = '#e91e63';
				//document.getElementById('company_links_hover_color').color.fromString('#0288D1');
			}
			if($('company_content_background_color')) {
				$('company_content_background_color').value = '#fff';
				//document.getElementById('company_content_background_color').color.fromString('#fff');
			}
			if($('company_content_border_color')) {
				$('company_content_border_color').value = '#ebecee';
				//document.getElementById('company_content_border_color').color.fromString('#ebecee');
			}
			if($('company_form_label_color')) {
				$('company_form_label_color').value = '#5a5a5a';
				//document.getElementById('company_form_label_color').color.fromString('#5a5a5a');
			}
			if($('company_input_background_color')) {
				$('company_input_background_color').value = '#f5f5f5';
				//document.getElementById('company_input_background_color').color.fromString('#f5f5f5');
			}
			if($('company_input_font_color')) {
				$('company_input_font_color').value = '#5a5a5a';
				//document.getElementById('company_input_font_color').color.fromString('#5a5a5a');
			}
			if($('company_input_border_color')) {
				$('company_input_border_color').value = '#cacaca';
				//document.getElementById('company_input_border_color').color.fromString('#cacaca');
			}
			if($('company_button_background_color')) {
				$('company_button_background_color').value = '#e91e63';
				//document.getElementById('company_button_background_color').color.fromString('#0288D1');
			}
			if($('company_button_background_color_hover')) {
				$('company_button_background_color_hover').value = '#e91e63'; //document.getElementById('company_button_background_color_hover').color.fromString('#0097e9');
			}
			if($('company_button_border_color')) {
				$('company_button_border_color').value = '#e91e63'; //document.getElementById('company_button_background_color_hover').color.fromString('#0288D1');
			}
			if($('company_button_font_color')) {
				$('company_button_font_color').value = '#fff';
				//document.getElementById('company_button_font_color').color.fromString('#fff');
			}
			if($('company_button_font_hover_color')) {
				$('company_button_font_hover_color').value = '#fff';
				//document.getElementById('company_button_font_hover_color').color.fromString('#fff');
			}
			if($('company_comment_background_color')) {
				$('company_comment_background_color').value = '#f6f7f9';
				//document.getElementById('company_comment_background_color').color.fromString('#f6f7f9');
			}
			//Body Styling
			
			//Header Styling
			if($('company_header_background_color')) {
				$('company_header_background_color').value = '#fff';
				//document.getElementById('company_header_background_color').color.fromString('#fff');
			}
			if($('company_header_border_color')) {
				$('company_header_border_color').value = '#eeeeee';
				//document.getElementById('company_header_border_color').color.fromString('#eeeeee');
			}
			if($('company_menu_logo_top_space')) {
				$('company_menu_logo_top_space').value = '10px';
			}
			if($('company_mainmenu_links_color')) {
				$('company_mainmenu_links_color').value = '#FFF';
				//document.getElementById('company_mainmenu_links_color').color.fromString('#1c1c1c');
			}
			if($('company_mainmenu_links_hover_color')) {
				$('company_mainmenu_links_hover_color').value = '#FFF';
				//document.getElementById('company_mainmenu_links_hover_color').color.fromString('#0288D1');
			}
			if($('company_minimenu_links_color')) {
				$('company_minimenu_links_color').value = '#424242';
				//document.getElementById('company_minimenu_links_color').color.fromString('#424242');
			}
			if($('company_minimenu_links_hover_color')) {
				$('company_minimenu_links_hover_color').value = '#e91e63';
				//document.getElementById('company_minimenu_links_hover_color').color.fromString('#0288D1');
			}
			if($('company_header_searchbox_background_color')) {
				$('company_header_searchbox_background_color').value = '#ebeeee'; //document.getElementById('company_header_searchbox_background_color').color.fromString('#fff');
			}
			if($('company_header_searchbox_text_color')) {
				$('company_header_searchbox_text_color').value = '#767676';
				//document.getElementById('company_header_searchbox_text_color').color.fromString('#636363');
			}
			if($('company_header_searchbox_border_color')) {
				$('company_header_searchbox_border_color').value = '#E7E7E7';
				//document.getElementById('company_header_searchbox_border_color').color.fromString('#E7E7E7');
			}
			//Header Styling
			
			//Footer Styling
			if($('company_footer_background_color')) {
				$('company_footer_background_color').value = '#222';
				//document.getElementById('company_footer_background_color').color.fromString('#222');
			}
			if($('company_footer_border_color')) {
				$('company_footer_border_color').value = '#e91e63';
				//document.getElementById('company_footer_border_color').color.fromString('#0288D1');
			}
			if($('company_footer_text_color')) {
				$('company_footer_text_color').value = '#767676';
				//document.getElementById('company_footer_text_color').color.fromString('#767676');
			}
			if($('company_footer_links_color')) {
				$('company_footer_links_color').value = '#767676';
				//document.getElementById('company_footer_links_color').color.fromString('#767676');
			}
			if($('company_footer_links_hover_color')) {
				$('company_footer_links_hover_color').value = '#ffffff';
				//document.getElementById('company_footer_links_hover_color').color.fromString('#ffffff');
			}
			//Footer Styling
		} 
     else if(value == 7) {
			//Theme Base Styling
			if($('company_theme_color')) {
				$('company_theme_color').value = '#21c789';
				//document.getElementById('company_theme_color').color.fromString('#0288D1');
			}
			//Theme Base Styling
			
			//Body Styling
			if($('company_body_background_color')) {
				$('company_body_background_color').value = '#EEEEEE';
				//document.getElementById('company_body_background_color').color.fromString('#F6F9FC');
			}
			if($('company_font_color')) {
				$('company_font_color').value = '#424242';
				//document.getElementById('company_font_color').color.fromString('#424242');
			}
			if($('company_font_color_light')) {
				$('company_font_color_light').value = '#424242';
				//document.getElementById('company_font_color_light').color.fromString('#424242');
			}
			
			if($('company_heading_color')) {
				$('company_heading_color').value = '#000';
				//document.getElementById('company_heading_color').color.fromString('#000');
			}
			if($('company_links_color')) {
				$('company_links_color').value = '#202020';
				//document.getElementById('company_links_color').color.fromString('#202020');
			}
			if($('company_links_hover_color')) {
				$('company_links_hover_color').value = '#21c789';
				//document.getElementById('company_links_hover_color').color.fromString('#0288D1');
			}
			if($('company_content_background_color')) {
				$('company_content_background_color').value = '#fff';
				//document.getElementById('company_content_background_color').color.fromString('#fff');
			}
			if($('company_content_border_color')) {
				$('company_content_border_color').value = '#ebecee';
				//document.getElementById('company_content_border_color').color.fromString('#ebecee');
			}
			if($('company_form_label_color')) {
				$('company_form_label_color').value = '#5a5a5a';
				//document.getElementById('company_form_label_color').color.fromString('#5a5a5a');
			}
			if($('company_input_background_color')) {
				$('company_input_background_color').value = '#f5f5f5';
				//document.getElementById('company_input_background_color').color.fromString('#f5f5f5');
			}
			if($('company_input_font_color')) {
				$('company_input_font_color').value = '#5a5a5a';
				//document.getElementById('company_input_font_color').color.fromString('#5a5a5a');
			}
			if($('company_input_border_color')) {
				$('company_input_border_color').value = '#cacaca';
				//document.getElementById('company_input_border_color').color.fromString('#cacaca');
			}
			if($('company_button_background_color')) {
				$('company_button_background_color').value = '#21c789';
				//document.getElementById('company_button_background_color').color.fromString('#0288D1');
			}
			if($('company_button_background_color_hover')) {
				$('company_button_background_color_hover').value = '#21c789'; //document.getElementById('company_button_background_color_hover').color.fromString('#0097e9');
			}
			if($('company_button_border_color')) {
				$('company_button_border_color').value = '#21c789'; //document.getElementById('company_button_background_color_hover').color.fromString('#0288D1');
			}
			if($('company_button_font_color')) {
				$('company_button_font_color').value = '#fff';
				//document.getElementById('company_button_font_color').color.fromString('#fff');
			}
			if($('company_button_font_hover_color')) {
				$('company_button_font_hover_color').value = '#fff';
				//document.getElementById('company_button_font_hover_color').color.fromString('#fff');
			}
			if($('company_comment_background_color')) {
				$('company_comment_background_color').value = '#f6f7f9';
				//document.getElementById('company_comment_background_color').color.fromString('#f6f7f9');
			}
			//Body Styling
			
			//Header Styling
			if($('company_header_background_color')) {
				$('company_header_background_color').value = '#fff';
				//document.getElementById('company_header_background_color').color.fromString('#fff');
			}
			if($('company_header_border_color')) {
				$('company_header_border_color').value = '#eeeeee';
				//document.getElementById('company_header_border_color').color.fromString('#eeeeee');
			}
			if($('company_menu_logo_top_space')) {
				$('company_menu_logo_top_space').value = '10px';
			}
			if($('company_mainmenu_links_color')) {
				$('company_mainmenu_links_color').value = '#FFF';
				//document.getElementById('company_mainmenu_links_color').color.fromString('#1c1c1c');
			}
			if($('company_mainmenu_links_hover_color')) {
				$('company_mainmenu_links_hover_color').value = '#FFF';
				//document.getElementById('company_mainmenu_links_hover_color').color.fromString('#0288D1');
			}
			if($('company_minimenu_links_color')) {
				$('company_minimenu_links_color').value = '#424242';
				//document.getElementById('company_minimenu_links_color').color.fromString('#424242');
			}
			if($('company_minimenu_links_hover_color')) {
				$('company_minimenu_links_hover_color').value = '#21c789';
				//document.getElementById('company_minimenu_links_hover_color').color.fromString('#0288D1');
			}
		if($('company_header_searchbox_background_color')) {
				$('company_header_searchbox_background_color').value = '#ebeeee'; //document.getElementById('company_header_searchbox_background_color').color.fromString('#fff');
			}
			if($('company_header_searchbox_text_color')) {
				$('company_header_searchbox_text_color').value = '#767676';
				//document.getElementById('company_header_searchbox_text_color').color.fromString('#636363');
			}
			if($('company_header_searchbox_border_color')) {
				$('company_header_searchbox_border_color').value = '#E7E7E7';
				//document.getElementById('company_header_searchbox_border_color').color.fromString('#E7E7E7');
			}
			//Header Styling
			
			//Footer Styling
			if($('company_footer_background_color')) {
				$('company_footer_background_color').value = '#222';
				//document.getElementById('company_footer_background_color').color.fromString('#222');
			}
			if($('company_footer_border_color')) {

				$('company_footer_border_color').value = '#21c789';
				//document.getElementById('company_footer_border_color').color.fromString('#0288D1');
			}
			if($('company_footer_text_color')) {
				$('company_footer_text_color').value = '#767676';
				//document.getElementById('company_footer_text_color').color.fromString('#767676');
			}
			if($('company_footer_links_color')) {
				$('company_footer_links_color').value = '#767676';
				//document.getElementById('company_footer_links_color').color.fromString('#767676');
			}
			if($('company_footer_links_hover_color')) {
				$('company_footer_links_hover_color').value = '#ffffff';
				//document.getElementById('company_footer_links_hover_color').color.fromString('#ffffff');
			}
			//Footer Styling
		} 
      else if(value == 8) {
			//Theme Base Styling
			if($('company_theme_color')) {
				$('company_theme_color').value = '#8bc34a';
				//document.getElementById('company_theme_color').color.fromString('#0288D1');
			}
			//Theme Base Styling
			
			//Body Styling
			if($('company_body_background_color')) {
				$('company_body_background_color').value = '#EEEEEE';
				//document.getElementById('company_body_background_color').color.fromString('#F6F9FC');
			}
			if($('company_font_color')) {
				$('company_font_color').value = '#424242';
				//document.getElementById('company_font_color').color.fromString('#424242');
			}
			if($('company_font_color_light')) {
				$('company_font_color_light').value = '#424242';
				//document.getElementById('company_font_color_light').color.fromString('#424242');
			}
			
			if($('company_heading_color')) {
				$('company_heading_color').value = '#000';
				//document.getElementById('company_heading_color').color.fromString('#000');
			}
			if($('company_links_color')) {
				$('company_links_color').value = '#202020';
				//document.getElementById('company_links_color').color.fromString('#202020');
			}
			if($('company_links_hover_color')) {
				$('company_links_hover_color').value = '#8bc34a';
				//document.getElementById('company_links_hover_color').color.fromString('#0288D1');
			}
			if($('company_content_background_color')) {
				$('company_content_background_color').value = '#fff';
				//document.getElementById('company_content_background_color').color.fromString('#fff');
			}
			if($('company_content_border_color')) {
				$('company_content_border_color').value = '#ebecee';
				//document.getElementById('company_content_border_color').color.fromString('#ebecee');
			}
			if($('company_form_label_color')) {
				$('company_form_label_color').value = '#5a5a5a';
				//document.getElementById('company_form_label_color').color.fromString('#5a5a5a');
			}
			if($('company_input_background_color')) {
				$('company_input_background_color').value = '#f5f5f5';
				//document.getElementById('company_input_background_color').color.fromString('#f5f5f5');
			}
			if($('company_input_font_color')) {
				$('company_input_font_color').value = '#5a5a5a';
				//document.getElementById('company_input_font_color').color.fromString('#5a5a5a');
			}
			if($('company_input_border_color')) {
				$('company_input_border_color').value = '#cacaca';
				//document.getElementById('company_input_border_color').color.fromString('#cacaca');
			}
			if($('company_button_background_color')) {
				$('company_button_background_color').value = '#8bc34a';
				//document.getElementById('company_button_background_color').color.fromString('#0288D1');
			}
			if($('company_button_background_color_hover')) {
				$('company_button_background_color_hover').value = '#8bc34a'; //document.getElementById('company_button_background_color_hover').color.fromString('#0097e9');
			}
			if($('company_button_border_color')) {
				$('company_button_border_color').value = '#8bc34a'; //document.getElementById('company_button_background_color_hover').color.fromString('#0288D1');
			}
			if($('company_button_font_color')) {
				$('company_button_font_color').value = '#fff';
				//document.getElementById('company_button_font_color').color.fromString('#fff');
			}
			if($('company_button_font_hover_color')) {
				$('company_button_font_hover_color').value = '#fff';
				//document.getElementById('company_button_font_hover_color').color.fromString('#fff');
			}
			if($('company_comment_background_color')) {
				$('company_comment_background_color').value = '#f6f7f9';
				//document.getElementById('company_comment_background_color').color.fromString('#f6f7f9');
			}
			//Body Styling
			
			//Header Styling
			if($('company_header_background_color')) {
				$('company_header_background_color').value = '#fff';
				//document.getElementById('company_header_background_color').color.fromString('#fff');
			}
			if($('company_header_border_color')) {
				$('company_header_border_color').value = '#eeeeee';
				//document.getElementById('company_header_border_color').color.fromString('#eeeeee');
			}
			if($('company_menu_logo_top_space')) {
				$('company_menu_logo_top_space').value = '10px';
			}
			if($('company_mainmenu_links_color')) {
				$('company_mainmenu_links_color').value = '#FFF';
				//document.getElementById('company_mainmenu_links_color').color.fromString('#1c1c1c');
			}
			if($('company_mainmenu_links_hover_color')) {
				$('company_mainmenu_links_hover_color').value = '#FFF';
				//document.getElementById('company_mainmenu_links_hover_color').color.fromString('#0288D1');
			}
			if($('company_minimenu_links_color')) {
				$('company_minimenu_links_color').value = '#424242';
				//document.getElementById('company_minimenu_links_color').color.fromString('#424242');
			}
			if($('company_minimenu_links_hover_color')) {
				$('company_minimenu_links_hover_color').value = '#8bc34a';
				//document.getElementById('company_minimenu_links_hover_color').color.fromString('#0288D1');
			}
		if($('company_header_searchbox_background_color')) {
				$('company_header_searchbox_background_color').value = '#ebeeee'; //document.getElementById('company_header_searchbox_background_color').color.fromString('#fff');
			}
			if($('company_header_searchbox_text_color')) {
				$('company_header_searchbox_text_color').value = '#767676';
				//document.getElementById('company_header_searchbox_text_color').color.fromString('#636363');
			}
			if($('company_header_searchbox_border_color')) {
				$('company_header_searchbox_border_color').value = '#E7E7E7';
				//document.getElementById('company_header_searchbox_border_color').color.fromString('#E7E7E7');
			}
			//Header Styling
			
			//Footer Styling
			if($('company_footer_background_color')) {
				$('company_footer_background_color').value = '#222';
				//document.getElementById('company_footer_background_color').color.fromString('#222');
			}
			if($('company_footer_border_color')) {
				$('company_footer_border_color').value = '#8bc34a';
				//document.getElementById('company_footer_border_color').color.fromString('#0288D1');
			}
			if($('company_footer_text_color')) {
				$('company_footer_text_color').value = '#767676';
				//document.getElementById('company_footer_text_color').color.fromString('#767676');
			}
			if($('company_footer_links_color')) {
				$('company_footer_links_color').value = '#767676';
				//document.getElementById('company_footer_links_color').color.fromString('#767676');
			}
			if($('company_footer_links_hover_color')) {
				$('company_footer_links_hover_color').value = '#ffffff';
				//document.getElementById('company_footer_links_hover_color').color.fromString('#ffffff');
			}
			//Footer Styling
		} 
		 else if(value == 9) {
			//Theme Base Styling
			if($('company_theme_color')) {
				$('company_theme_color').value = '#274584';
				//document.getElementById('company_theme_color').color.fromString('#0288D1');
			}
			//Theme Base Styling
			
			//Body Styling
			if($('company_body_background_color')) {
				$('company_body_background_color').value = '#EEEEEE';
				//document.getElementById('company_body_background_color').color.fromString('#F6F9FC');
			}
			if($('company_font_color')) {
				$('company_font_color').value = '#424242';
				//document.getElementById('company_font_color').color.fromString('#424242');
			}
			if($('company_font_color_light')) {
				$('company_font_color_light').value = '#424242';
				//document.getElementById('company_font_color_light').color.fromString('#424242');
			}
			
			if($('company_heading_color')) {
				$('company_heading_color').value = '#000';
				//document.getElementById('company_heading_color').color.fromString('#000');
			}
			if($('company_links_color')) {
				$('company_links_color').value = '#202020';
				//document.getElementById('company_links_color').color.fromString('#202020');
			}
			if($('company_links_hover_color')) {
				$('company_links_hover_color').value = '#274584';
				//document.getElementById('company_links_hover_color').color.fromString('#0288D1');
			}
			if($('company_content_background_color')) {
				$('company_content_background_color').value = '#fff';
				//document.getElementById('company_content_background_color').color.fromString('#fff');
			}
			if($('company_content_border_color')) {
				$('company_content_border_color').value = '#ebecee';
				//document.getElementById('company_content_border_color').color.fromString('#ebecee');
			}
			if($('company_form_label_color')) {
				$('company_form_label_color').value = '#5a5a5a';
				//document.getElementById('company_form_label_color').color.fromString('#5a5a5a');
			}
			if($('company_input_background_color')) {
				$('company_input_background_color').value = '#f5f5f5';
				//document.getElementById('company_input_background_color').color.fromString('#f5f5f5');
			}
			if($('company_input_font_color')) {
				$('company_input_font_color').value = '#5a5a5a';
				//document.getElementById('company_input_font_color').color.fromString('#5a5a5a');
			}
			if($('company_input_border_color')) {
				$('company_input_border_color').value = '#cacaca';
				//document.getElementById('company_input_border_color').color.fromString('#cacaca');
			}
			if($('company_button_background_color')) {
				$('company_button_background_color').value = '#274584';
				//document.getElementById('company_button_background_color').color.fromString('#0288D1');
			}
			if($('company_button_background_color_hover')) {
				$('company_button_background_color_hover').value = '#274584'; //document.getElementById('company_button_background_color_hover').color.fromString('#0097e9');
			}
			if($('company_button_border_color')) {
				$('company_button_border_color').value = '#274584'; //document.getElementById('company_button_background_color_hover').color.fromString('#0288D1');
			}
			if($('company_button_font_color')) {
				$('company_button_font_color').value = '#fff';
				//document.getElementById('company_button_font_color').color.fromString('#fff');
			}
			if($('company_button_font_hover_color')) {
				$('company_button_font_hover_color').value = '#fff';
				//document.getElementById('company_button_font_hover_color').color.fromString('#fff');
			}
			if($('company_comment_background_color')) {
				$('company_comment_background_color').value = '#f6f7f9';
				//document.getElementById('company_comment_background_color').color.fromString('#f6f7f9');
			}
			//Body Styling
			
			//Header Styling
			if($('company_header_background_color')) {
				$('company_header_background_color').value = '#fff';
				//document.getElementById('company_header_background_color').color.fromString('#fff');
			}
			if($('company_header_border_color')) {
				$('company_header_border_color').value = '#eeeeee';
				//document.getElementById('company_header_border_color').color.fromString('#eeeeee');
			}
			if($('company_menu_logo_top_space')) {
				$('company_menu_logo_top_space').value = '10px';
			}
			if($('company_mainmenu_links_color')) {
				$('company_mainmenu_links_color').value = '#FFF';
				//document.getElementById('company_mainmenu_links_color').color.fromString('#1c1c1c');
			}
			if($('company_mainmenu_links_hover_color')) {
				$('company_mainmenu_links_hover_color').value = '#FFF';
				//document.getElementById('company_mainmenu_links_hover_color').color.fromString('#0288D1');
			}
			if($('company_minimenu_links_color')) {
				$('company_minimenu_links_color').value = '#424242';
				//document.getElementById('company_minimenu_links_color').color.fromString('#424242');
			}
			if($('company_minimenu_links_hover_color')) {
				$('company_minimenu_links_hover_color').value = '#274584';
				//document.getElementById('company_minimenu_links_hover_color').color.fromString('#0288D1');
			}
			if($('company_header_searchbox_background_color')) {
				$('company_header_searchbox_background_color').value = '#ebeeee'; //document.getElementById('company_header_searchbox_background_color').color.fromString('#fff');
			}
			if($('company_header_searchbox_text_color')) {
				$('company_header_searchbox_text_color').value = '#767676';
				//document.getElementById('company_header_searchbox_text_color').color.fromString('#636363');
			}
			if($('company_header_searchbox_border_color')) {
				$('company_header_searchbox_border_color').value = '#E7E7E7';
				//document.getElementById('company_header_searchbox_border_color').color.fromString('#E7E7E7');
			}
			//Header Styling
			
			//Footer Styling
			if($('company_footer_background_color')) {
				$('company_footer_background_color').value = '#222';
				//document.getElementById('company_footer_background_color').color.fromString('#222');
			}
			if($('company_footer_border_color')) {
				$('company_footer_border_color').value = '#274584';
				//document.getElementById('company_footer_border_color').color.fromString('#0288D1');
			}
			if($('company_footer_text_color')) {
				$('company_footer_text_color').value = '#767676';
				//document.getElementById('company_footer_text_color').color.fromString('#767676');
			}
			if($('company_footer_links_color')) {
				$('company_footer_links_color').value = '#767676';
				//document.getElementById('company_footer_links_color').color.fromString('#767676');
			}
			if($('company_footer_links_hover_color')) {
				$('company_footer_links_hover_color').value = '#ffffff';
				//document.getElementById('company_footer_links_hover_color').color.fromString('#ffffff');
			}
			//Footer Styling
		} 
		 else if(value == 10) {
			//Theme Base Styling
			if($('company_theme_color')) {
				$('company_theme_color').value = '#00adc7';
				//document.getElementById('company_theme_color').color.fromString('#0288D1');
			}
			//Theme Base Styling
			
			//Body Styling
			if($('company_body_background_color')) {
				$('company_body_background_color').value = '#EEEEEE';
				//document.getElementById('company_body_background_color').color.fromString('#F6F9FC');
			}
			if($('company_font_color')) {
				$('company_font_color').value = '#424242';
				//document.getElementById('company_font_color').color.fromString('#424242');
			}
			if($('company_font_color_light')) {
				$('company_font_color_light').value = '#424242';
				//document.getElementById('company_font_color_light').color.fromString('#424242');
			}
			
			if($('company_heading_color')) {
				$('company_heading_color').value = '#000';
				//document.getElementById('company_heading_color').color.fromString('#000');
			}
			if($('company_links_color')) {
				$('company_links_color').value = '#202020';
				//document.getElementById('company_links_color').color.fromString('#202020');
			}
			if($('company_links_hover_color')) {
				$('company_links_hover_color').value = '#00adc7';
				//document.getElementById('company_links_hover_color').color.fromString('#0288D1');
			}
			if($('company_content_background_color')) {
				$('company_content_background_color').value = '#fff';
				//document.getElementById('company_content_background_color').color.fromString('#fff');
			}
			if($('company_content_border_color')) {
				$('company_content_border_color').value = '#ebecee';
				//document.getElementById('company_content_border_color').color.fromString('#ebecee');
			}
			if($('company_form_label_color')) {
				$('company_form_label_color').value = '#5a5a5a';
				//document.getElementById('company_form_label_color').color.fromString('#5a5a5a');
			}
			if($('company_input_background_color')) {
				$('company_input_background_color').value = '#f5f5f5';
				//document.getElementById('company_input_background_color').color.fromString('#f5f5f5');
			}
			if($('company_input_font_color')) {
				$('company_input_font_color').value = '#5a5a5a';
				//document.getElementById('company_input_font_color').color.fromString('#5a5a5a');
			}
			if($('company_input_border_color')) {
				$('company_input_border_color').value = '#cacaca';
				//document.getElementById('company_input_border_color').color.fromString('#cacaca');
			}
			if($('company_button_background_color')) {
				$('company_button_background_color').value = '#00adc7';
				//document.getElementById('company_button_background_color').color.fromString('#0288D1');
			}
			if($('company_button_background_color_hover')) {
				$('company_button_background_color_hover').value = '#00adc7'; //document.getElementById('company_button_background_color_hover').color.fromString('#0097e9');
			}
			if($('company_button_border_color')) {
				$('company_button_border_color').value = '#00adc7'; //document.getElementById('company_button_background_color_hover').color.fromString('#0288D1');
			}
			if($('company_button_font_color')) {
				$('company_button_font_color').value = '#fff';
				//document.getElementById('company_button_font_color').color.fromString('#fff');
			}
			if($('company_button_font_hover_color')) {
				$('company_button_font_hover_color').value = '#fff';
				//document.getElementById('company_button_font_hover_color').color.fromString('#fff');
			}
			if($('company_comment_background_color')) {
				$('company_comment_background_color').value = '#f6f7f9';
				//document.getElementById('company_comment_background_color').color.fromString('#f6f7f9');
			}
			//Body Styling
			
			//Header Styling
			if($('company_header_background_color')) {
				$('company_header_background_color').value = '#fff';
				//document.getElementById('company_header_background_color').color.fromString('#fff');
			}
			if($('company_header_border_color')) {
				$('company_header_border_color').value = '#eeeeee';
				//document.getElementById('company_header_border_color').color.fromString('#eeeeee');
			}
			if($('company_menu_logo_top_space')) {
				$('company_menu_logo_top_space').value = '10px';
			}
			if($('company_mainmenu_links_color')) {
				$('company_mainmenu_links_color').value = '#FFF';
				//document.getElementById('company_mainmenu_links_color').color.fromString('#1c1c1c');
			}
			if($('company_mainmenu_links_hover_color')) {
				$('company_mainmenu_links_hover_color').value = '#FFF';
				//document.getElementById('company_mainmenu_links_hover_color').color.fromString('#0288D1');
			}
			if($('company_minimenu_links_color')) {
				$('company_minimenu_links_color').value = '#424242';
				//document.getElementById('company_minimenu_links_color').color.fromString('#424242');
			}
			if($('company_minimenu_links_hover_color')) {
				$('company_minimenu_links_hover_color').value = '#00adc7';
				//document.getElementById('company_minimenu_links_hover_color').color.fromString('#0288D1');
			}
		  if($('company_header_searchbox_background_color')) {
				$('company_header_searchbox_background_color').value = '#ebeeee'; //document.getElementById('company_header_searchbox_background_color').color.fromString('#fff');
			}
			if($('company_header_searchbox_text_color')) {
				$('company_header_searchbox_text_color').value = '#767676';
				//document.getElementById('company_header_searchbox_text_color').color.fromString('#636363');
			}
			if($('company_header_searchbox_border_color')) {
				$('company_header_searchbox_border_color').value = '#E7E7E7';
				//document.getElementById('company_header_searchbox_border_color').color.fromString('#E7E7E7');
			}
			//Header Styling
			
			//Footer Styling
			if($('company_footer_background_color')) {
				$('company_footer_background_color').value = '#222';
				//document.getElementById('company_footer_background_color').color.fromString('#222');
			}
			if($('company_footer_border_color')) {
				$('company_footer_border_color').value = '#00adc7';
				//document.getElementById('company_footer_border_color').color.fromString('#0288D1');
			}
			if($('company_footer_text_color')) {
				$('company_footer_text_color').value = '#767676';
				//document.getElementById('company_footer_text_color').color.fromString('#767676');
			}
			if($('company_footer_links_color')) {
				$('company_footer_links_color').value = '#767676';
				//document.getElementById('company_footer_links_color').color.fromString('#767676');
			}
			if($('company_footer_links_hover_color')) {
				$('company_footer_links_hover_color').value = '#ffffff';
				//document.getElementById('company_footer_links_hover_color').color.fromString('#ffffff');
			}
			//Footer Styling
		} 
    else if(value == 5) {
      //Theme Base Styling
      if($('company_theme_color')) {
        $('company_theme_color').value = '<?php echo $settings->getSetting('company.theme.color') ?>';
        document.getElementById('company_theme_color').color.fromString('<?php echo $settings->getSetting('company.theme.color') ?>')   ;
      }
      //Theme Base Styling
      //Body Styling
      if($('company_body_background_color')) {
        $('company_body_background_color').value = '<?php echo $settings->getSetting('company.body.background.color') ?>';
        document.getElementById('company_body_background_color').color.fromString('<?php echo $settings->getSetting('company.body.background.color') ?>');
      }
      if($('company_font_color')) {
        $('company_font_color').value = '<?php echo $settings->getSetting('company.fontcolor') ?>';
        document.getElementById('company_font_color').color.fromString('<?php echo $settings->getSetting('company.fontcolor') ?>');
      }
      if($('company_font_color_light')) {
        $('company_font_color_light').value = '<?php echo $settings->getSetting('company.font.color.light') ?>';
        document.getElementById('company_font_color_light').color.fromString('<?php echo $settings->getSetting('company.font.color.light') ?>');
      }
      if($('company_heading_color')) {
        $('company_heading_color').value = '<?php echo $settings->getSetting('company.heading.color') ?>';
        document.getElementById('company_heading_color').color.fromString('<?php echo $settings->getSetting('company.heading.color') ?>');
      }
      if($('company_links_color')) {
        $('company_links_color').value = '<?php echo $settings->getSetting('company.links.color') ?>';
        document.getElementById('company_links_color').color.fromString('<?php echo $settings->getSetting('company.links.color') ?>');
      }
      if($('company_links_hover_color')) {
        $('company_links_hover_color').value = '<?php echo $settings->getSetting('company.links.color.hover') ?>';
        document.getElementById('company_links_hover_color').color.fromString('<?php echo $settings->getSetting('company.links.color.hover') ?>');
      }
      if($('company_content_background_color')) {
        $('company_content_background_color').value = '<?php echo $settings->getSetting('company.content.background.color') ?>';
        document.getElementById('company_content_background_color').color.fromString('<?php echo $settings->getSetting('company.content.background.color') ?>');
      }
      if($('company_content_border_color')) {
        $('company_content_border_color').value = '<?php echo $settings->getSetting('company.content.bordercolor') ?>';
        document.getElementById('company_content_border_color').color.fromString('<?php echo $settings->getSetting('company.content.bordercolor') ?>');
      }
      if($('company_form_label_color')) {
        $('company_input_font_color').value = '<?php echo $settings->getSetting('company.form.label.color') ?>';
        document.getElementById('company_form_label_color').color.fromString('<?php echo $settings->getSetting('company.form.label.color') ?>');
      }
      if($('company_input_background_color')) {
        $('company_input_background_color').value = '<?php echo $settings->getSetting('company.input.background.color') ?>';
        document.getElementById('company_input_background_color').color.fromString('<?php echo $settings->getSetting('company.input.background.color') ?>');
      }
      if($('company_input_font_color')) {
        $('company_input_font_color').value = '<?php echo $settings->getSetting('company.input.font.color') ?>';
        document.getElementById('company_input_font_color').color.fromString('<?php echo $settings->getSetting('company.input.font.color') ?>');
      }
      if($('company_input_border_color')) {
        $('company_input_border_color').value = '<?php echo $settings->getSetting('company.input.border.color') ?>';
        document.getElementById('company_input_border_color').color.fromString('<?php echo $settings->getSetting('company.input.border.color') ?>');
      }
      if($('company_button_background_color')) {
        $('company_button_background_color').value = '<?php echo $settings->getSetting('company.button.backgroundcolor') ?>';
        document.getElementById('company_button_background_color').color.fromString('<?php echo $settings->getSetting('company.button.backgroundcolor') ?>');
      }
      if($('company_button_background_color_hover')) {
        $('company_button_background_color_hover').value = '<?php echo $settings->getSetting('company.button.background.color.hover') ?>'; 
        document.getElementById('company_button_background_color_hover').color.fromString('<?php echo $settings->getSetting('company.button.background.color.hover') ?>');
      }
      if($('company_button_font_color')) {
        $('company_button_font_color').value = '<?php echo $settings->getSetting('company.button.font.color') ?>';
        document.getElementById('company_button_font_color').color.fromString('<?php echo $settings->getSetting('company.button.font.color') ?>');
      }
      if($('company_button_font_hover_color')) {
        $('company_button_font_color').value = '<?php echo $settings->getSetting('company.button.font.hover.color') ?>';
        document.getElementById('company_button_font_hover_color').color.fromString('<?php echo $settings->getSetting('company.button.font.hover.color') ?>');
      }
      if($('company_comment_background_color')) {
        $('company_comment_background_color').value = '<?php echo $settings->getSetting('company.comment.background.color') ?>';
        document.getElementById('company_comment_background_color').color.fromString('<?php echo $settings->getSetting('company.comment.background.color') ?>');
      }
      if($('company_button_border_color')) {
        $('company_button_background_color_hover').value = '<?php echo $settings->getSetting('company.button.border.color') ?>'; 
        document.getElementById('company_button_border_color').color.fromString('<?php echo $settings->getSetting('company.button.border.color') ?>');
      }
      //Body Styling
      //Header Styling
      if($('company_header_background_color')) {
        $('company_header_background_color').value = '<?php echo $settings->getSetting('company.header.background.color') ?>';
        document.getElementById('company_header_background_color').color.fromString('<?php echo $settings->getSetting('company.header.background.color') ?>');
      }
      if($('company_header_border_color')) {
        $('company_header_border_color').value = '<?php echo $settings->getSetting('company.header.border.color') ?>';
        document.getElementById('company_header_border_color').color.fromString('<?php echo $settings->getSetting('company.header.border.color') ?>');
      }
      if($('company_menu_logo_top_space')) {
        $('company_menu_logo_top_space').value = '10px';
      }
      if($('company_mainmenu_links_color')) {
        $('company_mainmenu_links_color').value = '<?php echo $settings->getSetting('company.mainmenu.links.color') ?>';
        document.getElementById('company_mainmenu_links_color').color.fromString('<?php echo $settings->getSetting('company.mainmenu.links.color') ?>');
      }
      if($('company_mainmenu_links_hover_color')) {
        $('company_mainmenu_links_hover_color').value = '<?php echo $settings->getSetting('company.mainmenu.links.color.hover') ?>';
        document.getElementById('company_mainmenu_links_hover_color').color.fromString('<?php echo $settings->getSetting('company.mainmenu.links.color.hover') ?>');
      }
      if($('company_minimenu_links_color')) {
        $('company_minimenu_links_color').value = '<?php echo $settings->getSetting('company.minimenu.linkscolor') ?>';
        document.getElementById('company_minimenu_links_color').color.fromString('<?php echo $settings->getSetting('company.minimenu.linkscolor') ?>');
      }
      if($('company_minimenu_links_hover_color')) {
        $('company_minimenu_links_hover_color').value = '<?php echo $settings->getSetting('company.minimenu.links.color.hover') ?>';
        document.getElementById('company_minimenu_links_hover_color').color.fromString('<?php echo $settings->getSetting('company.minimenu.links.color.hover') ?>');
      }
      if($('company_header_searchbox_background_color')) {
        $('company_header_searchbox_background_color').value = '<?php echo $settings->getSetting('company.header.searchbox.background.color') ?>'; 
        document.getElementById('company_header_searchbox_background_color').color.fromString('<?php echo $settings->getSetting('company.header.searchbox.background.color') ?>');
      }
      if($('company_header_searchbox_text_color')) {
        $('company_header_searchbox_text_color').value = '<?php echo $settings->getSetting('company.header.searchbox.text.color') ?>';
        document.getElementById('company_header_searchbox_text_color').color.fromString('<?php echo $settings->getSetting('company.header.searchbox.text.color') ?>');
      }
			if($('company_header_searchbox_border_color')) {
        $('company_header_searchbox_border_color').value = '<?php echo $settings->getSetting('company.header.searchbox.border.color') ?>';
        document.getElementById('company_header_searchbox_border_color').color.fromString('<?php echo $settings->getSetting('company.header.searchbox.border.color') ?>');
      }
      //Header Styling
      //Footer Styling
      if($('company_footer_background_color')) {
        $('company_footer_background_color').value = '<?php echo $settings->getSetting('company.footer.background.color') ?>';
        document.getElementById('company_footer_background_color').color.fromString('<?php echo $settings->getSetting('company.footer.background.color') ?>');
      }
      if($('company_footer_border_color')) {
        $('company_footer_border_color').value = '<?php echo $settings->getSetting('company.footer.border.color') ?>';
        document.getElementById('company_footer_border_color').color.fromString('<?php echo $settings->getSetting('company.footer.border.color') ?>');
      }
      if($('company_footer_text_color')) {
        $('company_footer_text_color').value = '<?php echo $settings->getSetting('company.footer.text.color') ?>';
        document.getElementById('company_footer_text_color').color.fromString('<?php echo $settings->getSetting('company.footer.text.color') ?>');
      }
      if($('company_footer_links_color')) {
        $('company_footer_links_color').value = '<?php echo $settings->getSetting('company.footer.links.color') ?>';
        document.getElementById('company_footer_links_color').color.fromString('<?php echo $settings->getSetting('company.footer.links.color') ?>');
      }
      if($('company_footer_links_hover_color')) {
        $('company_footer_links_hover_color').value = '<?php echo $settings->getSetting('company.footer.links.hover.color') ?>';
        document.getElementById('company_footer_links_hover_color').color.fromString('<?php echo $settings->getSetting('company.footer.links.hover.color') ?>');
      }
      //Footer Styling
    }
	}
</script>