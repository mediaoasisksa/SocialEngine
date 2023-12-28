<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Travel
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    https://socialengine.com/eula
 * @version    $Id: content.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
return array(
		array(
        'title' => 'Breadcrumb for Travel Listings View Page',
        'description' => 'Displays Breadcrumb for Travel Listings view page.',
        'category' => 'Travel Listings',
        'type' => 'widget',
        'name' => 'travel.breadcrumb',
    ),
    array(
        'title' => 'Travel Listings Hashtag Search',
        'description' => 'Displays travel listings on hashtag results page.',
        'category' => 'Travel Listings',
        'type' => 'widget',
        'autoEdit' => true,
        'defaultParams' => array(
            'title' => 'Travel Listings',
            'titleCount' => true,
        ),
        'isPaginated' => true,
        'name' => 'travel.hashtag-search-results',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Profile Travel Listings',
        'description' => 'Displays a member\'s travel listings on their profile.',
        'category' => 'Travel Listings',
        'type' => 'widget',
        'name' => 'travel.profile-travels',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Travel Listings',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
    array(
        'title' => 'Popular Travel Listings',
        'description' => 'Displays a list of most popular travel listings.',
        'category' => 'Travel Listings',
        'type' => 'widget',
        'name' => 'travel.list-popular-travels',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Popular Travel Listings',
        ),
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                  'Select',
                  'popularType',
                  array(
                    'label' => 'Popular Type',
                    'multiOptions' => array(
                      'creation_date' => 'Recently Created',
                      'modified_date' => 'Recently Modified',
                      'like_count' => 'Most Liked',
                      'view_count' => 'Most Viewed',
                      'comment_count' => 'Most Commented',
                    ),
                    'value' => 'view_count',
                  )
                ),
                array(
                    'Radio',
                    'trAlign',
                    array(
                        'label' => 'Vertical or Horizontal?',
                        'multiOptions' => array(
                            0 => 'Vertical',
                            1 => 'Horizontal',
                        )
                    )
                ),
                array(
                    'Text',
                    'trDesLength',
                    array(
                        'label' => 'Travel listing text length',
                        'description' => 'Enter the character length of the travel listing text to show (default: 300)',
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Recent Travel Listings',
        'description' => 'Displays a list of recently posted travel listings.',
        'category' => 'Travel Listings',
        'type' => 'widget',
        'name' => 'travel.list-recent-travels',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Recent Travel Listings',
        ),
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'recentType',
                    array(
                        'label' => 'Recent Type',
                        'multiOptions' => array(
                            'creation' => 'Creation Date',
                            'modified' => 'Modified Date',
                        ),
                        'value' => 'creation',
                    )
                ),
                array(
                    'Radio',
                    'trAlign',
                    array(
                        'label' => 'Vertical or Horizontal?',
                        'multiOptions' => array(
                            0 => 'Vertical',
                            1 => 'Horizontal',
                        )
                    )
                ),
                array(
                    'Text',
                    'trDesLength',
                    array(
                        'label' => 'Travel listing text length',
                        'description' => 'Enter the character length of the travel listing text to show (default: 300)',
                    )
                ),
            )
        ),
    ),

    array(
        'title' => 'Travel Browse Search',
        'description' => 'Displays a search form in the travel browse page.',
        'category' => 'Travel Listings',
        'type' => 'widget',
        'name' => 'travel.browse-search',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Travel Browse Menu',
        'description' => 'Displays a menu in the travel browse page.',
        'category' => 'Travel Listings',
        'type' => 'widget',
        'name' => 'travel.browse-menu',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Travel Categories',
        'description' => 'Display a list of categories for travel listings.',
        'category' => 'Travel Listings',
        'type' => 'widget',
        'name' => 'travel.list-categories',
    ),
) ?>
