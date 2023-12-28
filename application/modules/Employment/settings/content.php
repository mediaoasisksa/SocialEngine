<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: content.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
return array(
    array(
        'title' => 'Breadcrumb for Employment View Page',
        'description' => 'Displays Breadcrumb for employment view page.',
        'category' => 'Employment',
        'type' => 'widget',
        'name' => 'employment.breadcrumb',
    ),
    array(
        'title' => 'Employment Hashtag Search',
        'description' => 'Displays employment listings on hashtag results page.',
        'category' => 'Employment',
        'type' => 'widget',
        'autoEdit' => true,
        'defaultParams' => array(
            'title' => 'Employment',
            'titleCount' => true,
        ),
        'isPaginated' => true,
        'name' => 'employment.hashtag-search-results',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Profile Employment',
        'description' => 'Displays a member\'s employment listings on their profile.',
        'category' => 'Employment',
        'type' => 'widget',
        'name' => 'employment.profile-employments',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Employment',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
    array(
        'title' => 'Popular Employment Listings',
        'description' => 'Displays a list of most viewed employment listings.',
        'category' => 'Employment',
        'type' => 'widget',
        'name' => 'employment.list-popular-employments',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Popular Employment Listings',
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
        'title' => 'Recent Employment Listings',
        'description' => 'Displays a list of recently posted employment listings.',
        'category' => 'Employment',
        'type' => 'widget',
        'name' => 'employment.list-recent-employments',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Recent Employment Listings',
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
        'title' => 'Employment Browse Search',
        'description' => 'Displays a search form in the employment browse page.',
        'category' => 'Employment',
        'type' => 'widget',
        'name' => 'employment.browse-search',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Employment Browse Menu',
        'description' => 'Displays a menu in the employment browse page.',
        'category' => 'Employment',
        'type' => 'widget',
        'name' => 'employment.browse-menu',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Employment Browse Quick Menu',
        'description' => 'Displays a small menu in the employment browse page.',
        'category' => 'Employment',
        'type' => 'widget',
        'name' => 'employment.browse-menu-quick',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Employment Categories',
        'description' => 'Display a list of categories for employment listings.',
        'category' => 'Employment',
        'type' => 'widget',
        'name' => 'employment.list-categories',
    ),
) ?>
