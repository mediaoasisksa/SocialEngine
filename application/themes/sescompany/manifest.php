<?php

/**
 * SocialEngine
 *
 * @category   Application_Theme
 * @package    Responsive Company Theme
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: manifest.php 2014-08-02 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
return array (
  'package' => 
  array (
    'type' => 'theme',
    'name' => 'SES - The Company & Business - Responsive Multi-Purpose Theme',
    'version' => '4.10.3',
    'path' => 'application/themes/sescompany',
    'repository' => 'socialenginesolutions.com',
    'title' => '<span style="color:#DDDDDD">Responsive Company Theme</span>',
    'thumb' => 'company_theme.jpg',
    'author' => '<a href="http://socialenginesolutions.com/" target="_blank" title="Visit our website!">SocialEngineSolutions</a>',
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'remove',
    ),
    'callback' => 
    array (
      'class' => 'Engine_Package_Installer_Theme',
    ),
    'directories' => 
    array (
      0 => 'application/themes/sescompany',
    ),
    'description' => '',
  ),
  'files' => 
  array (
    0 => 'theme.css',
    1 => 'constants.css',
		2 => 'media-queries.css',
		3 => 'sescompany-custom.css',
  ),  
	'nophoto' => array(
    'user' => array(
      'thumb_icon' => 'application/themes/sescompany/images/nophoto_user_thumb_icon.png',
      'thumb_profile' => 'application/themes/sescompany/images/nophoto_user_thumb_profile.png',
    )
	)
); 