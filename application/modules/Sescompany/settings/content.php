<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: content.php 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
if(Engine_Api::_()->getDbtable("modules", "core")->isModuleEnabled("sescompany") && Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.pluginactivated')) {

  $results = Engine_Api::_()->getDbtable('banners', 'sescompany')->getBanner(array('fetchAll' => true));
  if (count($results) > 0) {
    foreach ($results as $gallery)
      $arrayGallery[$gallery['banner_id']] = $gallery['banner_name'];
  }

}
return array(
    array(
        'title' => 'SES - The Company & Business - Banner Slideshow',
        'description' => 'Displays banner slideshows as configured by you in the admin panel of this theme. Edit this widget to choose the slideshow to be shown and configure various settings.',
        'category' => 'SES - The Company & Business - Responsive Multi-Purpose Theme',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sescompany.banner-slideshow',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'banner_id',
                    array(
                        'label' => 'Choose the Banner to be shown in this widget.',
                        'multiOptions' => $arrayGallery,
                        'value' => 1,
                    )
                ),
                array(
                    'Select',
                    'full_width',
                    array(
                        'label' => 'Do you want to show this Banner in full width?',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'height',
                    array(
                        'label' => 'Enter the height of this Banner (in pixels).',
                        'value' => 200,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
            ),
        ),
    ),
    array(
			'title' => 'SES - The Company & Business - Responsive Multi-Purpose Theme - Footer Menu',
			'description' => 'This widget shows the site-wide footer menu. Edit this widget to configure various settings.',
			'category' => 'SES - The Company & Business - Responsive Multi-Purpose Theme',
			'type' => 'widget',
			'autoEdit' => true,
			'name' => 'sescompany.menu-footer',
    ),
			array(
			'title' => 'SES - The Company & Business - Responsive Multi-Purpose Theme - Header',
			'description' => 'This widget shows the site-wide header which includes Main Menu, Mini Menu, Site Logo, Search and extra links. You can edit this from the admin panel of this theme.',
			'category' => 'SES - The Company & Business - Responsive Multi-Purpose Theme',
			'type' => 'widget',
			'name' => 'sescompany.header',
			'autoEdit' => false,
		),
		array(
			'title' => 'SES - The Company & Business - Responsive Multi-Purpose Theme - Landing Page',
			'description' => 'This widget shows the site landing page.',
			'category' => 'SES - The Company & Business - Responsive Multi-Purpose Theme',
			'type' => 'widget',
			'name' => 'sescompany.landing-page',
			'autoEdit' => false,
		),
		array(
			'title' => 'SES - The Company & Business - Responsive Multi-Purpose Theme - Banner',
			'description' => 'Shows one of your banners. Requires that you have at least one banner.',
			'category' => 'SES - The Company & Business - Responsive Multi-Purpose Theme',
			'type' => 'widget',
			'name' => 'sescompany.banner',
			'autoEdit' => true,
			'adminForm' => 'Sescompany_Form_Admin_Widget_Banner',
		),
    array(
			'title' => 'SES - The Company & Business - Responsive Multi-Purpose Theme - Login',
			'description' => 'This widget displays login form in a transparent block with an image in background of the page.',
			'category' => 'SES - The Company & Business - Responsive Multi-Purpose Theme',
			'type' => 'widget',
			'name' => 'sescompany.login',
      'autoEdit' => true,
      'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'showlogo',
                    array(
                        'label' => 'Do you want to show logo?.',
                        'multiOptions' => array(
                          '1' => 'Yes',
                          '0' => 'No'
                        
                        ),
                        'value' => 1,
                    )
                ),
            ),
        ),
    ),
  );