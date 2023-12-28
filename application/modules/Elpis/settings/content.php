<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Elpis
 * @copyright  Copyright 2006-2022 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: content.php 2022-06-21
 */

return array(
  array(
    'title' => 'Accessibility Options',
    'description' => 'This widget is used to display the option for accessibility in site header.',
    'category' => 'Elpis',
    'type' => 'widget',
    'name' => 'elpis.menu-top',
  ),
	array(
		'title' => 'Landing Page Popular Blog Entries',
		'description' => 'Displays Popular Blog Entries on the Landing Page.',
		'category' => 'Elpis',
		'type' => 'widget',
		'name' => 'elpis.landing-page-blogs',
		'defaultParams' => array(
				'title' => 'Popular Blog Entries',
		),
		'requirements' => array(
				'no-subject',
		),
		'adminForm' => array(
				'elements' => array(
						array(
								'Radio',
								'popularType',
								array(
										'label' => 'Popular Type',
										'multiOptions' => array(
												'view' => 'Views',
												'comment' => 'Comments',
										),
										'value' => 'comment',
								)
						),
						array(
								'Text',
								'itemCountPerPage',
								array(
										'label' => 'Count (number of items to show)',
								)
						),
				)
		 ),
	),
	array(
    'title' => 'Landing Page Members',
    'description' => 'Displays the members.',
    'category' => 'Elpis',
    'type' => 'widget',
    'name' => 'elpis.landing-page-members',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Popular Members',
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),
);
