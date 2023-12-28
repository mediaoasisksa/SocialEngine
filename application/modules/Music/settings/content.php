<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: content.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
return array(
  array(
      'title' => 'Music Categories',
      'description' => 'Display a list of categories for music playlists.',
      'category' => 'Music',
      'type' => 'widget',
      'name' => 'music.list-categories',
  ),
  array(
    'title' => 'Home Playlist',
    'description' => 'Displays a single selected playlist.',
    'category' => 'Music',
    'type' => 'widget',
    'name' => 'music.home-playlist',
    'autoEdit' => true,
    //'adminForm' => 'Music_Form_Admin_Widget_HomePlaylist',
    'defaultParams' => array(
      'title' => 'Playlist',
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),
  array(
    'title' => 'Profile Music',
    'description' => 'Displays a member\'s music on their profile.',
    'category' => 'Music',
    'type' => 'widget',
    'name' => 'music.profile-music',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Music',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
  array(
    'title' => 'Profile Player',
    'description' => 'Displays a flash player that plays the music the member has selected to play on their profile.',
    'category' => 'Music',
    'type' => 'widget',
    'name' => 'music.profile-player',
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
  array(
    'title' => 'Popular Playlists',
    'description' => 'Displays a list of popular playlists.',
    'category' => 'Music',
    'type' => 'widget',
    'name' => 'music.list-popular-playlists',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Popular Playlists',
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
              'play_count' => 'Most Played',
            ),
            'value' => 'play_count',
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'Recent Playlists',
    'description' => 'Displays a list of recent playlists.',
    'category' => 'Music',
    'type' => 'widget',
    'name' => 'music.list-recent-playlists',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Recent Playlists',
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
    'title' => 'Music Browse Search',
    'description' => 'Displays a search form in the music browse page.',
    'category' => 'Music',
    'type' => 'widget',
    'name' => 'music.browse-search',
    'requirements' => array(
      'no-subject',
    ),
  ),
  array(
    'title' => 'Music Browse Menu',
    'description' => 'Displays a menu in the music browse page.',
    'category' => 'Music',
    'type' => 'widget',
    'name' => 'music.browse-menu',
    'requirements' => array(
      'no-subject',
    ),
  ),
  array(
    'title' => 'Music Browse Quick Menu',
    'description' => 'Displays a small menu in the music browse page.',
    'category' => 'Music',
    'type' => 'widget',
    'name' => 'music.browse-menu-quick',
    'requirements' => array(
      'no-subject',
    ),
  ),
	array(
		'title' => 'Breadcrumb for Music View Page',
		'description' => 'Displays Breadcrumb for Music view page.',
		'category' => 'Music',
		'type' => 'widget',
		'name' => 'music.breadcrumb',
  ),
) ?>
