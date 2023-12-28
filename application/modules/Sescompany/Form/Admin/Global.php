<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Global.php 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sescompany_Form_Admin_Global extends Engine_Form {

  public function init() {

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->setTitle('Global Settings')
            ->setDescription('These settings affect all members in your community.');

		$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
		$supportTicket = '<a href="http://www.socialenginesolutions.com/tickets" target="_blank">Support Ticket</a>';
		$sesSite = '<a href="http://www.socialenginesolutions.com" target="_blank">SocialEngineSolutions website</a>';
		$descriptionLicense = sprintf('Enter your license key that is provided to you when you purchased this plugin. If you do not know your license key, please drop us a line from the %s section on %s. (Key Format: XXXX-XXXX-XXXX-XXXX)',$supportTicket,$sesSite);
		$this->addElement('Text', "sescompany_licensekey", array(
      'label' => 'Enter License key',
      'description' => $descriptionLicense,
      'allowEmpty' => false,
      'required' => true,
      'value' => $settings->getSetting('sescompany.licensekey'),
		));
		$this->getElement('sescompany_licensekey')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

    if ($settings->getSetting('sescompany.pluginactivated')) {
			//UPLOAD PHOTO URL
			$upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sescompany', 'controller' => 'manage', 'action' => "upload-photo"), 'admin_default', true);

			$allowed_html = 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr';

			$editorOptions = array(
					'upload_url' => $upload_url,
					'html' => (bool) $allowed_html,
			);

			if (!empty($upload_url)) {
				$editorOptions['plugins'] = array(
						'table', 'fullscreen', 'media', 'preview', 'paste',
						'code', 'image', 'textcolor', 'jbimages', 'link'
				);

				$editorOptions['toolbar1'] = array(
						'undo', 'redo', 'removeformat', 'pastetext', '|', 'code',
						'media', 'image', 'jbimages', 'link', 'fullscreen',
						'preview'
				);
			}
			if (!$settings->getSetting('sescompany.layout.enable', 0)) {
				$this->addElement('Radio', 'sescompany_layout_enable', array(
						'label' => 'Set Company Theme Landing Page',
						'description' => 'Do you want to set Company Landing page for your site? [Note: If you choose Yes, then your current settings will be overwritten by the Company Landing page and changes will not be recoverable.]',
						'multiOptions' => array(
								1 => 'Yes',
								0 => 'No'
						),
						'value' => $settings->getSetting('sescompany.layout.enable', 0),
				));
			}
			
      $this->addElement('Text', "company_main_width", array(
        'label' => 'Theme Width',
        'allowEmpty' => false,
        'required' => true,
        'value' => Engine_Api::_()->sescompany()->getContantValueXML('company_main_width'),
      ));
			
      $this->addElement('Text', "company_left_columns_width", array(
        'label' => 'Left Column Width',
        'description' => "Enter the left column width of the website. This will affect all the pages on your website.",
        'allowEmpty' => false,
        'required' => true,
        'value' => Engine_Api::_()->sescompany()->getContantValueXML('company_left_columns_width'),
      ));

      $this->addElement('Text', "company_right_columns_width", array(
        'label' => 'Right Column Width',
        'description' => 'Enter the right column width of the website. This will affect all the pages on your website.',
        'allowEmpty' => false,
        'required' => true,
        'value' => Engine_Api::_()->sescompany()->getContantValueXML('company_right_columns_width'),
      ));
      
      $this->addElement('Select', "company_mobilehideleftrightcolumn", array(
        'label' => 'Hide Left / Right Column from Mobile',
        'description' => 'Do you want to hide left / right from mobile device?',
        'allowEmpty' => false,
        'required' => true,
        'multiOptions' => array(
            '1' => 'Yes',
            '0' => "No",
        ),
        'value' => Engine_Api::_()->sescompany()->getContantValueXML('company_mobilehideleftrightcolumn'),
      ));
			
      $banner_options[] = '';
      $path = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
      foreach ($path as $file) {
        if ($file->isDot() || !$file->isFile())
          continue;
        $base_name = basename($file->getFilename());
        if (!($pos = strrpos($base_name, '.')))
          continue;
        $extension = strtolower(ltrim(substr($base_name, $pos), '.'));
        if (!in_array($extension, array('gif', 'jpg', 'jpeg', 'png')))
          continue;
        $banner_options['public/admin/' . $base_name] = $base_name;
      }
      $fileLink = $view->baseUrl() . '/admin/files/';
      $this->addElement('Select', 'company_body_background_image', array(
          'label' => 'Body Background Image',
          'description' => 'Choose from below the body background image for your website. [Note: You can add a new photo from the "File & Media Manager" section from here: <a href="' . $fileLink . '" target="_blank">File & Media Manager</a>.]',
          'multiOptions' => $banner_options,
          'escape' => false,
          'value' => Engine_Api::_()->sescompany()->getContantValueXML('company_body_background_image'),
      ));
      $this->company_body_background_image->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
			
      $this->addElement('Select', "sescompany_responsive_layout", array(
          'label' => 'Enable Responsive CSS',
          'description' => 'Do you want to enable the responsive css for your website? If you select Yes, then the website will automatically adopt the device screen size.',
          'allowEmpty' => false,
          'required' => true,
          'multiOptions' => array(
              '1' => 'Yes',
              '2' => "No",
          ),
          'value' => $settings->getSetting('sescompany.responsive.layout', 1),
      ));
      
				
			$this->addElement('Dummy', 'popup_settings', array(
					'label' => 'Sign In & Sign Up Popup Settings',
			));
			$this->addElement('Select', 'sescompany_popupsign', array(
					'label' => 'Enable Popup for Sign In & Sign Up',
					'description' => 'Do you want to enable popup for Sign In and Sign Up? If you select No, then users will be redirected to the login and signup pages when they will click respective options in the Mini Menu.',
					'multiOptions'=>array('1'=>'Yes','0'=>'No'),
						'onclick' => 'showPopup(this.value);',
					'value' => $settings->getSetting('sescompany.popupsign', '1'),
			));		
		
			$this->addElement('Select', 'sescompany_popup_enable', array(
					'label' => 'Open Sign In Popup Automatically',
					'description' => 'Do you want the login popup to be displayed automatically when non-logged in users visit your website?',
					'multiOptions' => array(
							1 => 'Yes',
							0 => 'No'
					),
					'onclick' => 'loginsignupvisiablity(this.value);',
					'value' => $settings->getSetting('sescompany.popup.enable', 1),
			));

			$this->addElement('Text', 'sescompany_popup_day', array(
					'label' => 'Sign In Popup Visibility',
					'description' => 'After how many days will the login popup be visible to non-logged in users once closed? [Enter ‘0’, if you want login popup to be visible each time non-logged in users visit your website.]',
					'value' => $settings->getSetting('sescompany.popup.day', 5),
			));
			
      $this->addElement('Select', 'sescompany_popupfixed', array(
          'label' => 'Allow to Close Sign In Popup',
          'description' => 'Do you want to allow users to close the sign in and sign up popup? If you choose No, then users will not able to close the popup once opened and they have to forcefully login / signup to get into your community.',
          'multiOptions' => array(
              1 => 'No, do not allow to close popup',
              0 => 'Yes, allow to close popup'
          ),
          'value' => $settings->getSetting('sescompany.popupfixed', 0),
      ));
      
      $this->addElement('Select', 'sescompany_loginbackgroundimage', array(
        'label' => 'Sign In Page Background Image',
        'description' => 'Choose from below the sign in page background image for your website. [Note: You can add a new photo from the "File & Media Manager" section from here: <a href="' . $fileLink . '" target="_blank">File & Media Manager</a>.]',
        'multiOptions' => $banner_options,
        'escape' => false,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.loginbackgroundimage', ''),
      ));
      $this->sescompany_loginbackgroundimage->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));



			if (!$settings->getSetting('sescompany.layout.enable', 0)) {
				$this->addElement('Radio', 'sescompany_layout_enable', array(
						'label' => 'Set Company Theme Landing Page',
						'description' => 'Do you want to set Company Landing page for your site? [Note: If you choose Yes, then your current settings will be overwritten by the Company Landing page and changes will not be recoverable.]',
						'multiOptions' => array(
								1 => 'Yes',
								0 => 'No'
						),
						'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.layout.enable', 0),
				));
			}

			// Add submit button
			$this->addElement('Button', 'submit', array(
					'label' => 'Save Changes',
					'type' => 'submit',
					'ignore' => true
			));
    } else {
    
			if (!$settings->getSetting('sescompany.layout.enable', 0)) {
				$this->addElement('Radio', 'sescompany_layout_enable', array(
						'label' => 'Set Company Theme Landing Page',
						'description' => 'Do you want to set Company Landing page for your site? [Note: If you choose Yes, then your current settings will be overwritten by the Company Landing page and changes will not be recoverable.]',
						'multiOptions' => array(
								1 => 'Yes',
								0 => 'No'
						),
						'value' => $settings->getSetting('sescompany.layout.enable', 0),
				));
			}
			
      //Add submit button
      $this->addElement('Button', 'submit', array(
          'label' => 'Activate your plugin',
          'type' => 'submit',
          'ignore' => true
      ));
    }
  }
}