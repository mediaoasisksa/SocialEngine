<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Mailtemplate.php 6590 2012-06-20 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_View_Helper_Mailtemplate extends Zend_View_Helper_Abstract {

  public function mailtemplate($data = array()) {

    $template_id = 0;
    if(isset($data['template_id'])) {
			$template_id = $data['template_id'];
    }  
   
    $mailTemplate_id = 0;
    if(isset($data['mailtemplate_id'])) {
			$mailTemplate_id = $data['mailtemplate_id'];
    } 
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
		$publishUrl = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'home'), 'user_general', true);

		$emailNotificationUrl = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $view->baseUrl().'/members/settings/notifications';

		$siteTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemailtemplates.site.title', Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1)); 

		$description = $view->translate("<p><span style='color: #92999c; font-size: x-small; font-family: arial,helvetica,sans-serif;'>If you are a member of <a href='%s' target='_blank' style='text-decoration:none;'>$siteTitle</a>, and would like to choose which emails you receive from us, then please <a href='$emailNotificationUrl' style='text-decoration:none;' target='_blank'>click here</a>.</span><br><span style='color: #92999c; font-size: x-small; font-family: arial,helvetica,sans-serif;'> To continue receiving our emails, please add us to your address book or safe list.</span></p>");

    if(empty($template_id)) {
      //MAKE QUERY
			$sitemailtemplateTable = Engine_Api::_()->getDbTable('templates','sitemailtemplates');
			$tablesitemailtemplatesName = $sitemailtemplateTable->info('name');
			$select = $sitemailtemplateTable->select()->where('active_template = ?',1);
      $resultTemplate = $sitemailtemplateTable->fetchRow($select)->toArray();
    }
    else {
			$resultTemplate = Engine_Api::_()->getItem('sitemailtemplates_templates', $template_id)->toArray();
    }

    //GET THE CURRENT LANGUAGE
    
    $locale = $view->locale()->getLocale()->__toString();
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $column = 'email_signature_'.$locale;
		$languageColumn = $db->query("SHOW COLUMNS FROM engine4_sitemailtemplates_mailtemplates LIKE '$column'")->fetch();
    
    if(!empty($languageColumn)) {
      $signature = $column;
    }
    else {
      $signature = 'email_signature_en';
    }
    
    if(!empty($mailTemplate_id)) {
       $tableMailtemplate = Engine_Api::_()->getDbtable('mailtemplates', 'sitemailtemplates');
      $tableMailtemplateName = $tableMailtemplate->info('name');
      $sitemailtemplate_id = $tableMailtemplate->select()->from($tableMailtemplateName, 'sitemailtemplate_id')->where('mailtemplate_id =?', $mailTemplate_id)->query()->fetchColumn();
      $templateObject = Engine_Api::_()->getItem('sitemailtemplates_mail_template', $sitemailtemplate_id);
			$coretemplateObject = Engine_Api::_()->getItem('core_mail_template', $mailTemplate_id);

			if(empty($templateObject->show_signature) && empty($templateObject->$signature) && ($coretemplateObject->type != 'SITEMAILTEMPLATES_CONTACTS_EMAIL_NOTIFICATION')) {
				$textFooter = sprintf($description, $publishUrl);
			}
			elseif($coretemplateObject->type != 'SITEMAILTEMPLATES_CONTACTS_EMAIL_NOTIFICATION') {
				$textFooter = $templateObject->$signature;
			}
      else {
        $textFooter = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemailtemplates.footer1', $description);
      }
    }
    else {
      $textFooter = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemailtemplates.footer1', $description);
    }
   
    $siteUrl = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'home'), 'user_general', true);

    $data_html = array('bodyHtmlTemplate' => $data['bodyHtmlTemplate'],'siteUrl' => $siteUrl,'textofFooter' => $textFooter);
    $final_data = array_merge($resultTemplate,$data_html);

    $siteTitle = '';
    $bodyContent = '';
    $headerContent = '';
    $siteUrl = $final_data['siteUrl'];
    $site_title = $final_data['site_title'];
    if(!empty($final_data['img_path'])) {
    $logo_photo = $final_data['img_path'];
    }
    else {
      $logo_photo = 'application/modules/Sitemailtemplates/externals/images/web.png';
    }

    $upload_image = explode('/',$logo_photo);
    $encoded_image = rawurlencode($upload_image[2]);
    if($upload_image[0] == 'application' && $upload_image[1] == 'modules' && $upload_image[2] == 'Sitemailtemplates' && $upload_image[3] == 'externals') {
      $path = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST']. $view->baseUrl(). '/'.$logo_photo;
    }
    else {
      $path = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST']. $view->baseUrl(). '/'.'public/admin'.'/'.$encoded_image;
    }

    $bodyContent .= '<div style="text-align:center;overflow:hidden;">';
    if($final_data['show_icon'] && $final_data['sitelogo_location'] == 'body') {
      $bodyContent .= '<div style="float:' .$final_data['sitelogo_position']. '"><a href="' . $siteUrl . '" target="_blank"><img src="'.$path.'" style="max-width:800px;vertical-align: middle;" border="0" /></a></div>';
    }
  
    if($final_data['show_title'] && $final_data['sitetitle_location'] == 'body') {
      $bodyContent .= '<div style="margin:0 10px;float:' .$final_data['sitetitle_position']. ';font-family:' .$final_data['sitetitle_fontfamily']. ';font-size:' .$final_data['sitetitle_fontsize']. 'px;"><a href="' .$siteUrl .'" target="_blank" style="text-decoration:none; color:' . $final_data['header_titlecolor']. ';font-weight:bold;">' .$site_title. '</a></div>';
    }
  
    if($final_data['show_tagline'] && $final_data['tagline_location'] == 'body') {
      $bodyContent .= '<div style="margin:0 10px;float:' .$final_data['tagline_position']. ';font-family:' .$final_data['tagline_fontfamily']. ';font-size:' .$final_data['tagline_fontsize']. 'px;color:' .$final_data['header_tagcolor']. ';">' .$final_data['tagline_title']. '</div>';
    
    }

    $bodyContent .= '</div>';

    if($final_data['tagline_location'] == 'above_header') {
      $headerContent .= '<tr><td style="text-align:center;"><div style="margin:0 10px 5px;float:' .$final_data['tagline_position']. ';font-family:' .$final_data['tagline_fontfamily']. ';font-size:' .$final_data['tagline_fontsize']. 'px;color:' .$final_data['header_tagcolor']. ';">' .$final_data['tagline_title']. '</div></td></tr>';
    }

    $description = Zend_Registry::get('Zend_Translate')->_("<p><span style='color: #92999C;'>If you are a member of&nbsp;  <a href='%s' target='_parent'>$site_title</a> and do not want to receive these emails from us in the future, then visit your account settings to manage email notifications. To continue receiving our emails, please add us to your address book or safe list.</span></p>");
    $description= sprintf($description, $siteUrl);

    if($final_data['show_icon'] && $final_data['sitelogo_location'] == 'header') {
      
      //$path_img = 'http://' . $_SERVER['HTTP_HOST'] . $logo_photo;
      if (!empty($path)) {
        if($final_data['show_title'] &&$final_data['sitetitle_location'] == 'header') {
        $siteTitle .= '<div style="float:' .$final_data['sitelogo_position']. '"><a href="' . $siteUrl . '" target="_blank"><img src="'.$path.'" style="max-width:800px;vertical-align: middle;" border="0" /></a></div>';
        }
        else {
          $siteTitle .= '<div style="float:' .$final_data['sitelogo_position']. '"><a href="' . $siteUrl . '" target="_blank"><img alt="'.$site_title.'" src="'.$path.'" style="max-height:800px;vertical-align: middle;" border="0" /></a></div>';
        }
      }
    }
    if($final_data['show_title'] && $final_data['sitetitle_location'] == 'header') {
      $siteTitle .= '<div style="margin:0 10px;float:' .$final_data['sitetitle_position']. ';font-family:' .$final_data['sitetitle_fontfamily']. ';font-size:' .$final_data['sitetitle_fontsize']. 'px;"><a href="' .$siteUrl. '" target="_blank" style="text-decoration:none; color:' . $final_data['header_titlecolor']. ';font-weight:bold;">' .$site_title. '</a></div>';
    }
    if($final_data['show_tagline'] && $final_data['tagline_location'] == 'header') {
      $siteTitle .= '<div style="margin:0 10px;float:' .$final_data['tagline_position']. ';font-family:' .$final_data['tagline_fontfamily']. ';font-size:' .$final_data['tagline_fontsize']. 'px;color:' .$final_data['header_tagcolor']. ';">' .$final_data['tagline_title']. '</div>';
    }


    if(($final_data['show_title'] && $final_data['sitetitle_location'] == 'header') || ($final_data['show_icon'] && $final_data['sitelogo_location'] == 'header') || ($final_data['show_tagline'] && $final_data['tagline_location'] == 'header')) {
      $headerContent .= '<tr><td style="background-color:' .$final_data['header_bgcol']. ';padding:' .$final_data['header_outpadding']. 'px;vertical-align:middle;text-align:center"> ' .$siteTitle. '</td></tr>' ;
    }

    $html = $bodyContent.$final_data['bodyHtmlTemplate'];
    return $bodyHtmlTemplate = '<table border="0" cellpadding="10" cellspacing="0"><tbody><tr><td bgcolor="' .$final_data['body_outerbgcol'] .'"><table border="0" cellpadding="0" cellspacing="0" align="center" style="width:100%;"><tbody>'.$headerContent.'<tr><td colspan="0" bgcolor="' .$final_data['body_innerbgcol']. '" style="font-family:Arial, Helvetica, sans-serif;border-bottom:' .$final_data['footer_bottomwidth']. 'px solid ' .$final_data['footer_bottomcol']. ';border-left:' .$final_data['lr_bottomwidth']. 'px solid ' .$final_data['lr_bordercolor']. ';border-right:' .$final_data['lr_bottomwidth']. 'px solid ' .$final_data['lr_bordercolor']. ';border-top:' .$final_data['header_bottomwidth']. 'px solid ' .$final_data['header_bottomcolor']. ';font-size:12px;padding:10px;" valign="top">'.$html.'</td></tr><tr><td height="5px"></td></tr><tr><td style="background-color:' . $final_data['signature_bgcol'] . ';font-size:12px;padding:8px 15px;">' .$final_data['textofFooter'] .'</td></tr></tbody></table></td></tr></tbody></table>';
  }
}