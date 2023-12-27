<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: General.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Core_Form_Admin_Settings_General extends Engine_Form
{
  public function init()
  {

    $description = $this->getTranslator()->translate(
        'These settings affect your entire community and all your members. <br>');

	$settings = Engine_Api::_()->getApi('settings', 'core');

	if( $settings->getSetting('user.support.links', 0) == 1 ) {
	  $moreinfo = $this->getTranslator()->translate(
        'More Info: <a href="%1$s" target="_blank"> KB Article</a>');
	} else {
	  $moreinfo = $this->getTranslator()->translate(
        '');
	}

    $description = vsprintf($description.$moreinfo, array(
      'https://community.socialengine.com/blogs/597/24/general-settings',
    ));

	// Decorators
    $this->loadDefaultDecorators();
	$this->getDecorator('Description')->setOption('escape', false);

    // Set form attributes
    $this->setTitle('General Settings');
    $this->setDescription($description);

    // init site maintenance mode
    $this->addElement('Radio', 'maintenance_mode', array(
      'label' => 'Maintenance Mode',
      'description' => 'CORE_FORM_ADMIN_SETTINGS_GENERAL_DESCRIPTION',
      'required' => true,
      'multiOptions' => array(
        0 => 'Online',
        1 => 'Offline (Maintenance Mode)',
      ),
    ));

    // init site maintenance code
    $this->addElement('Text', 'maintenance_code', array(
      'label' => 'Maintenance Mode Code',
      'description' => 'If empty, a password will be randomly generated.',
      'filters' => array(
        'StringTrim',
      ),
    ));
    $this->maintenance_code->getDecorator('Description')->setOption('placement', 'append');

    // init site title
    $this->addElement('Text', 'site_title', array(
      'label' => 'Site Title',
      'description' => 'CORE_FORM_ADMIN_SETTINGS_GENERAL_SITETITLE_DESCRIPTION'
    ));
    $this->site_title->getDecorator('Description')->setOption('placement', 'append');

    // init site description
    $this->addElement('Textarea', 'site_description', array(
      'label' => 'Site Description',
      'description' => 'CORE_FORM_ADMIN_SETTINGS_GENERAL_SITEDESCRIPTION_DESCRIPTION'
    ));
    $this->site_description->getDecorator('Description')->setOption('placement', 'append');


    // init site keywords
    $this->addElement('Textarea', 'site_keywords', array(
      'label' => 'Site Keywords',
      'description' => 'CORE_FORM_ADMIN_SETTINGS_GENERAL_SITEKEYWORDS_DESCRIPTION'
    ));
    $this->site_keywords->getDecorator('Description')->setOption('placement', 'append');


      $this->addElement('Text', 'site_password_reset', array(
          'label' => 'Require Password Reset',
          'description' => 'CORE_FORM_ADMIN_SETTINGS_GENERAL_PASSWORD_RESET',
          'value' => 0,
          'validators' => array(
              array('Int', true)
          ),
      ));
      $this->site_password_reset->getDecorator('Description')->setOption('placement', 'append');

    // init site script
    /*
    $this->addElement('Textarea', 'site_script', array(
      'label' => 'Site Script Header',
      'description' => 'CORE_FORM_ADMIN_SETTINGS_GENERAL_SITESCRIPT_DESCRIPTION'
    ));
    $this->site_script->getDecorator('Description')->setOption('placement', 'append');
    */

    // init profile
    $this->addElement('Radio', 'profile', array(
      'label' => 'Member Profiles',
      'multiOptions' => array(
        1 => 'Yes, give the public access.',
        0 => 'No, visitors must sign in to view member profiles.'
      )
    ));

    $this->addElement('Radio', 'browse', array(
      'label' => 'Browse Members Page',
      'required' => true,
      'multiOptions' => array(
        1 => 'Yes, give the public access.',
        0 => 'No, visitors must sign in to view the browse members page.'
      )
    ));

    $this->addElement('Radio', 'search', array(
      'label' => 'Search Page',
      'required' => true,
      'multiOptions' => array(
        1 => 'Yes, give the public access.',
        0 => 'No, visitors must sign in to view the search page.'
      )
    ));

    $this->addElement('Radio', 'portal', array(
      'label' => 'Portal Page',
      'required' => true,
      'multiOptions' => array(
        1 => 'Yes, give the public access.',
        0 => 'No, visitors must sign in to view the main portal page. '
          . '( Setting it to \'No\' will disallow visitors from viewing Landing page and instead redirect them to '
          . 'Login page, but to restrict visitor access to individual modules such as Albums, Blogs, etc through '
          . 'main menu, you\'ll need to adjust Member Level Settings of each module for public users.)'
      )
    ));

    // Get available files
    $banner_options = array('' => '');
    $files = Engine_Api::_()->getDbTable('files', 'core')->getFiles(array('fetchAll' => 1, 'extension' => array('gif', 'jpg', 'jpeg', 'png', 'webp')));
    foreach( $files as $file ) {
      $banner_options[$file->storage_path] = $file->name;
    }

    $this->addElement('Select', 'landingimage', array(
      'label' => 'Login Form Image',
      'description' => 'Choose from below the image that you want to show with the login form on your website.',
      'multiOptions' => $banner_options,
    ));

    $this->addElement('Radio', 'enableloginlogs', array(
      'label' => 'Enable Login Logs',
      'description' => "Do you want to enable login logs when members log in to your website? If you choose yes then the login entry will save in the database.",
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No',
      ),
      'onchange' => "loginLogs(this.value);",
    ));
    
		$this->addElement('Text', 'logincrondays', array(
			'label' => 'Cron Job Schedule to Clear Login Logs',
			'description' => 'Enter the number of days login logs will be stored before clearing.',
			'required' => true,
		));

    $this->addElement('Select', 'notificationupdate', array(
      'label' => 'Notification Update Frequency',
      'description' => 'ACTIVITY_FORM_ADMIN_SETTINGS_GENERAL_NOTIFICATIONUPDATE_DESCRIPTION',
      'value' => 120000,
      'multiOptions' => array(
        30000  => 'ACTIVITY_FORUM_ADMIN_SETTINGS_GENERAL_LIVEUPDATE_OPTION1',
        60000  => 'ACTIVITY_FORUM_ADMIN_SETTINGS_GENERAL_LIVEUPDATE_OPTION2',
        120000 => "ACTIVITY_FORUM_ADMIN_SETTINGS_GENERAL_LIVEUPDATE_OPTION3",
        0      => 'ACTIVITY_FORUM_ADMIN_SETTINGS_GENERAL_LIVEUPDATE_OPTION4'
      )
    ));

    $translate = Zend_Registry::get('Zend_Translate');
    $this->addElement('Text', 'staticBaseUrl', array(
      'label' => 'Static File Base URL',
      'description' => sprintf($translate->translate('The base URL for ' .
          'static files (such as JavaScript and CSS files). Used to ' .
          'implement CDN hosting of static files through services such ' .
          'as Cloudfront.')),
      'filters' => array(
        'StringTrim',
      ),
    ));
    $this->getElement('staticBaseUrl')->getDecorator('Description')
        ->setOption('escape', false)
        ->setOption('placement', 'append');
    $this->getElement('staticBaseUrl')->getDecorator('Label')
        ->setOption('escape', false)
        ->setOptSuffix(sprintf(
        '<a class="admin help" href="%1$s" target="_blank"> </a>',
        'https://community.socialengine.com/blogs/597/123/how-to-use-the-cloud-storage-feature'));

    $this->addElement('Text', 'analytics', array(
      'label' => 'Measurement ID',
      'description' => 'Enter the Website Profile ID to use Google Analytics.',
      'filters' => array(
        'StringTrim',
      ),
    ));
    $this->getElement('analytics')->getDecorator('Description')
        ->setOption('escape', false)
        ->setOption('placement', 'append');
    $this->getElement('analytics')->getDecorator('Label')
        ->setOption('escape', false)
        ->setOptSuffix(sprintf(
        '<a class="admin help" href="%1$s" target="_blank"> </a>',
        'https://support.google.com/analytics/answer/9306384?hl=en'));

    // scripts/styles
    $this->addElement('Textarea', 'includes', array(
      'label' => 'Head Scripts/Styles',
      'description' => 'Anything entered into the box below will be included ' .
          'at the bottom of the <head> tag. If you want to include a script ' .
          'or stylesheet, be sure to use the <script> or <link> tag.'
    ));

    $kbText = '';
    $links = array('http://www.addthis.com/');
    if( $settings->getSetting('user.support.links', 0) == 1 ) {
      $kbText = ' More Info: <a href="%2$s" target="_blank">KB Article</a>';
      $links[] = 'https://community.socialengine.com/blogs/597/96/add-this';
    }
    // Social share code
    $description = vsprintf('Below you can enter the code generated from: '.
          '<a href="%1$s" target="_blank">http://www.addthis.com</a> '.
          'for displaying social sharing buttons. Leaving this field empty will '.
          'not display those buttons.' . $kbText,
          $links);
    $this->addElement('Textarea', 'social_code', array(
      'label' => 'Social Share Block Code',
      'description' => $description,
    ));
    $this->social_code->getDecorator('Description')->setOption('escape', false);

    $Favicons = array(''=>'');
    $files = Engine_Api::_()->getDbTable('files', 'core')->getFiles(array('fetchAll' => 1, 'extension' => array('ico')));
    foreach( $files as $file ) {
      $Favicons[$file->storage_path] = $file->name;
    }
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $fileLink = $view->baseUrl() . '/admin/files/';

    if (engine_count($Favicons) > 1) {
      $this->addElement('Select', 'site_favicon', array(
          'label' => 'Favicon Upload',
          'description' => 'Choose from the below icons for your website. [Note: You can add new icons from the "File & Media Manager" section from here:  <a target="_blank" href="' . $fileLink . '">File & Media Manager</a> and upload icons with extension ‘.ico’ only.]',
          'multiOptions' => $Favicons,
      ));
    } else {
      $description = "<div class='tip'><span>" . 'There are currently no icons in the File & Media Manager with ".ico" extension. So Firstly upload the icons from the  "Appearance" >> "<a target="_blank" href="' . $fileLink . '">File & Media Manager</a>" section.' . "</span></div>";
      $this->addElement('Dummy', 'site_favicon', array(
          'label' => 'Favicon Upload',
          'description' => $description,
          'value'=> 0,
      ));
    }
    $this->site_favicon->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

    $this->addElement('Radio', 'sell_info', array(
      'label' => "'Do Not Sell Info' Setting",
      'description' => "Do you want to show the 'Do Not Sell Info' settings in the Footer of the Website?",
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No'
      ),
      'value'=>1
    ));

    // init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
}
