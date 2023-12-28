<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: content.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
return array(
    array(
        'title' => 'Breadcrumb for Classified View Page',
        'description' => 'Displays Breadcrumb for classified view page.',
        'category' => 'Classifieds',
        'type' => 'widget',
        'name' => 'classified.breadcrumb',
    ),
    array(
        'title' => 'Classifieds Hashtag Search',
        'description' => 'Displays classifieds on hashtag results page.',
        'category' => 'Classifieds',
        'type' => 'widget',
        'autoEdit' => true,
        'defaultParams' => array(
            'title' => 'Classifieds',
            'titleCount' => true,
        ),
        'isPaginated' => true,
        'name' => 'classified.hashtag-search-results',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Profile Classifieds',
        'description' => 'Displays a member\'s classifieds on their profile.',
        'category' => 'Classifieds',
        'type' => 'widget',
        'name' => 'classified.profile-classifieds',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Classifieds',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
    array(
      'title' => 'Popular Classifieds',
      'description' => 'Displays a list of most viewed classifieds.',
      'category' => 'Classifieds',
      'type' => 'widget',
      'name' => 'classified.list-popular-classifieds',
      'isPaginated' => true,
      'defaultParams' => array(
          'title' => 'Popular Classifieds',
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
        'title' => 'Recent Classifieds',
        'description' => 'Displays a list of recently posted classifieds.',
        'category' => 'Classifieds',
        'type' => 'widget',
        'name' => 'classified.list-recent-classifieds',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Recent Classifieds',
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
        'title' => 'Classified Browse Search',
        'description' => 'Displays a search form in the classified browse page.',
        'category' => 'Classifieds',
        'type' => 'widget',
        'name' => 'classified.browse-search',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Classified Browse Menu',
        'description' => 'Displays a menu in the classified browse page.',
        'category' => 'Classifieds',
        'type' => 'widget',
        'name' => 'classified.browse-menu',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Classified Browse Quick Menu',
        'description' => 'Displays a small menu in the classified browse page.',
        'category' => 'Classifieds',
        'type' => 'widget',
        'name' => 'classified.browse-menu-quick',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Classified Categories',
        'description' => 'Display a list of categories for classifieds.',
        'category' => 'Classifieds',
        'type' => 'widget',
        'name' => 'classified.list-categories',
    ),
) ?>
