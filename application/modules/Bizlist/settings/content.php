<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Bizlist
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: content.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
return array(
    array(
        'title' => 'Breadcrumb for Business View Page',
        'description' => 'Displays Breadcrumb for business view page.',
        'category' => 'Businesses',
        'type' => 'widget',
        'name' => 'bizlist.breadcrumb',
    ),
    array(
        'title' => 'Businesses Hashtag Search',
        'description' => 'Displays businesses on hashtag results page.',
        'category' => 'Businesses',
        'type' => 'widget',
        'autoEdit' => true,
        'defaultParams' => array(
            'title' => 'Businesses',
            'titleCount' => true,
        ),
        'isPaginated' => true,
        'name' => 'bizlist.hashtag-search-results',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Profile Businesses',
        'description' => 'Displays a member\'s businesses on their profile.',
        'category' => 'Businesses',
        'type' => 'widget',
        'name' => 'bizlist.profile-businesses',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Businesses',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
    array(
        'title' => 'Popular Businesses',
        'description' => 'Displays a list of most viewed businesses.',
        'category' => 'Businesses',
        'type' => 'widget',
        'name' => 'bizlist.list-popular-businesses',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Popular Businesses',
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
            )
        ),
    ),
    array(
        'title' => 'Recent Businesses',
        'description' => 'Displays a list of recently posted businesses.',
        'category' => 'Businesses',
        'type' => 'widget',
        'name' => 'bizlist.list-recent-businesses',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Recent Businesses',
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
            )
        ),
    ),

    array(
        'title' => 'Business Browse Search',
        'description' => 'Displays a search form in the business browse page.',
        'category' => 'Businesses',
        'type' => 'widget',
        'name' => 'bizlist.browse-search',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Business Browse Menu',
        'description' => 'Displays a menu in the business browse page.',
        'category' => 'Businesses',
        'type' => 'widget',
        'name' => 'bizlist.browse-menu',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Business Browse Quick Menu',
        'description' => 'Displays a small menu in the poll browse page.',
        'category' => 'Businesses',
        'type' => 'widget',
        'name' => 'bizlist.browse-menu-quick',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Business Categories',
        'description' => 'Display a list of categories for businesses.',
        'category' => 'Businesses',
        'type' => 'widget',
        'name' => 'bizlist.list-categories',
    ),
);
