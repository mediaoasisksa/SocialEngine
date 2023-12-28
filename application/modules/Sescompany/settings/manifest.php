<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: manifest.php 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

return array(
	'package' => array(
			'type' => 'module',
			'name' => 'sescompany',
			'version' => '4.10.3p1',
			'path' => 'application/modules/Sescompany',
			'title' => 'SES - The Company & Business - Responsive Multi-Purpose Theme',
			'description' => 'SES - The Company & Business - Responsive Multi-Purpose Theme',
			'author' => '<a href="http://www.socialenginesolutions.com" style="text-decoration:underline;" target="_blank">SocialEngineSolutions</a>',
			'actions' => array(
					'install',
					'upgrade',
					'refresh',
					'enable',
					'disable',
			),
			'callback' => array(
					'path' => 'application/modules/Sescompany/settings/install.php',
					'class' => 'Sescompany_Installer',
			),
			'directories' =>
			array(
        'application/modules/Sescompany',
        'application/themes/sescompany',
			),
			'files' => array(
					'application/languages/en/sescompany.csv',
					'public/admin/blank.png',
					'public/admin/background_img2.jpg',
					'public/admin/bg_f.jpg',
					'public/admin/background_img1.jpg',
					'public/admin/drft.jpg',
					'public/admin/whychooseus_bg.jpg',
					'public/admin/contact_bg.jpg',
					'public/admin/contact_main_img.jpg',
					'public/admin/team_bg.jpg',
					'public/admin/login-bg.jpg',
					'public/admin/logo_business.png',
					'public/admin/Our-Clients.jpg',
			),
	),
	// Hooks ---------------------------------------------------------------------
	'hooks' => array(
		array(
			'event' => 'onRenderLayoutDefault',
			'resource' => 'Sescompany_Plugin_Core'
		),
		array(
			'event' => 'onRenderLayoutDefaultSimple',
			'resource' => 'Sescompany_Plugin_Core'
		),
	),
	// Items ---------------------------------------------------------------------
	'items' => array(
		'sescompany_slide', 'sescompany_banner','sescompany_managesearchoptions', 'sescompany_testimonial','sescompany_client','sescompany_feature', 'sescompany_slide', 'sescompany_counter', 'sescompany_about','sescompany_team','sescompany_bannerslide'
	),
);