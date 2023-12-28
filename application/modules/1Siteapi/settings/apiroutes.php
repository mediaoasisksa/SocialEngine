<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    apiroutes.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
// Add Suggestion Plugin APIs Routes.
$routes['suggestion'] = array(
    'suggestion_general' => array(
        'route' => 'suggestions/:action/*',
        'defaults' => array(
            'module' => 'suggestion',
            'controller' => 'index',
            'action' => 'suggestion-listing',
        ),
        'reqs' => array(
            'action' => '(suggestion-listing|remove|send-invite|remove-notification|suggest-to-friend)'
        ),
    )
);

// Add Blog Pugin APIs Routes.
$routes['blog'] = array(
    'blog_general' => array(
        'route' => 'blogs/:action/*',
        'defaults' => array(
            'module' => 'blog',
            'controller' => 'index',
            'action' => 'browse',
        ),
        'reqs' => array(
            'action' => '(index|create|manage|style|tag|upload-photo|browse|member-info|search-form|category|subscribe|unsubscribe)',
        ),
    ),
    'blog_specific' => array(
        'route' => 'blogs/:action/:blog_id/*',
        'defaults' => array(
            'module' => 'blog',
            'controller' => 'index',
            'action' => 'index',
        ),
        'reqs' => array(
            'blog_id' => '\d+',
            'action' => '(deluete|edit|view)',
        ),
    )
);

// Add Classified Pugin APIs Routes.
$routes['classified'] = array(
    'classified_general' => array(
        'route' => 'classifieds/:action/*',
        'defaults' => array(
            'module' => 'classified',
            'controller' => 'index',
            'action' => 'index',
        ),
        'reqs' => array(
            'action' => '(index|manage|create|search-form|view)',
        ),
    ),
    'classified_specific' => array(
        'route' => 'classifieds/:action/:classified_id/*',
        'defaults' => array(
            'module' => 'classified',
            'controller' => 'index',
            'action' => 'index',
        ),
        'reqs' => array(
            'classified_id' => '\d+',
            'action' => '(delete|edit|close|success|view)',
        ),
    ),
    'classified_list_photos_specific' => array(
        'route' => 'classifieds/photo/:action/:classified_id/*',
        'defaults' => array(
            'module' => 'classified',
            'controller' => 'photo',
            'action' => 'list',
        ),
        'reqs' => array(
            'photo_id' => '\d+',
            'action' => '(list|upload|make-photo-cover)',
        ),
    ),
    'classified_photos_specific' => array(
        'route' => 'classifieds/photo/:action/:photo_id/*',
        'defaults' => array(
            'module' => 'classified',
            'controller' => 'photo',
            'action' => 'view',
        ),
        'reqs' => array(
            'photo_id' => '\d+',
            'action' => '(view|remove)',
        ),
    )
);

// Add Group Pugin APIs Routes.
$routes['group'] = array(
    'group_general' => array(
        'route' => 'groups/:action/*',
        'defaults' => array(
            'module' => 'group',
            'controller' => 'index',
            'action' => 'browse',
        ),
        'reqs' => array(
            'action' => '(browse|create|list|manage|search-form|view|edit|delete)',
        )
    ),
    'group_api_specific' => array(
        'route' => 'groups/:action/:group_id/*',
        'defaults' => array(
            'module' => 'group',
            'controller' => 'index',
            'action' => 'view',
        ),
        'reqs' => array(
            'group_id' => '\d+',
            'action' => '(view|edit|delete)',
        ),
    ),
    'group_members_general' => array(
        'route' => 'groups/member/:action/:group_id/*',
        'defaults' => array(
            'module' => 'group',
            'controller' => 'member',
            'action' => 'list',
        ),
        'reqs' => array(
            'action' => '(list|invite|accept|ignore|leave|join|request|request-cancel|remove|edit|approve|promote|demote)',
        )
    ),
    'group_list_photos_specific' => array(
        'route' => 'groups/photo/:action/:group_id/*',
        'defaults' => array(
            'module' => 'group',
            'controller' => 'photo',
            'action' => 'view',
        ),
        'reqs' => array(
            'photo_id' => '\d+',
            'action' => '(list)',
        ),
    ),
    'group_photos_specific' => array(
        'route' => 'groups/photo/:action/:photo_id/*',
        'defaults' => array(
            'module' => 'group',
            'controller' => 'photo',
            'action' => 'view',
        ),
        'reqs' => array(
            'photo_id' => '\d+',
            'action' => '(view|edit|delete)',
        ),
    )
);

// Add Event Pugin APIs Routes.
$routes['event'] = array(
    'event_general' => array(
        'route' => 'events/:action/*',
        'defaults' => array(
            'module' => 'event',
            'controller' => 'index',
            'action' => 'browse',
        ),
        'reqs' => array(
            'action' => '(browse|create|list|manage|search-form|view|edit|delete)',
        )
    ),
    'event_api_specific' => array(
        'route' => 'events/:action/:event_id/*',
        'defaults' => array(
            'module' => 'event',
            'controller' => 'index',
            'action' => 'view',
        ),
        'reqs' => array(
            'event_id' => '\d+',
            'action' => '(view|edit|delete)',
        ),
    ),
    'event_members_general' => array(
        'route' => 'events/member/:action/:event_id/*',
        'defaults' => array(
            'module' => 'event',
            'controller' => 'member',
            'action' => 'list',
        ),
        'reqs' => array(
            'action' => '(list|invite|accept|ignore|leave|join|request|request-cancel|remove|edit|approve|promote|demote|cancel)',
        )
    ),
    'event_list_photos_specific' => array(
        'route' => 'events/photo/:action/:event_id/*',
        'defaults' => array(
            'module' => 'event',
            'controller' => 'photo',
            'action' => 'view',
        ),
        'reqs' => array(
            'photo_id' => '\d+',
            'action' => '(list)',
        ),
    ),
    'event_photo_list' => array(
        'route' => 'events/photos/lists/:event_id/*',
        'defaults' => array(
            'module' => 'event',
            'controller' => 'photo',
            'action' => 'list',
        ),
    ),
    'event_photos_specific' => array(
        'route' => 'events/photo/:action/:photo_id/*',
        'defaults' => array(
            'module' => 'event',
            'controller' => 'photo',
            'action' => 'view',
        ),
        'reqs' => array(
            'photo_id' => '\d+',
            'action' => '(view|edit|delete)',
        ),
    )
);

// Add Video Pugin APIs Routes.
$routes['video'] = array(
    'video_general' => array(
        'route' => 'videos/:action/*',
        'defaults' => array(
            'module' => 'video',
            'controller' => 'index',
            'action' => 'browse',
        ),
        'reqs' => array(
            'action' => '(index|browse|create|list|manage|search-form|view|edit|rate|delete)',
        )
    ),
    'video_siteapi_view' => array(
        'route' => 'videos/:action/:video_id/*',
        'defaults' => array(
            'module' => 'video',
            'controller' => 'index',
            'action' => 'view',
        ),
        'reqs' => array(
            'action' => '(view|edit|rate|delete)',
        )
    )
);

// Add Music Pugin APIs Routes.
$routes['music'] = array(
    'music_general' => array(
        'route' => 'music/:action/*',
        'defaults' => array(
            'module' => 'music',
            'controller' => 'index',
            'action' => 'browse',
        ),
        'reqs' => array(
            'action' => '(index|browse|manage|create|search-form)',
        ),
    ),
    'music_siteapi_view' => array(
        'route' => 'music/playlist/:action/:playlist_id/*',
        'defaults' => array(
            'module' => 'music',
            'controller' => 'playlist',
            'action' => 'view',
        ),
        'reqs' => array(
            'action' => '(view|edit|delete)',
        )
    )
);

// Add Poll Pugin APIs Routes.
$routes['poll'] = array(
    'poll_general' => array(
        'route' => 'polls/:action/*',
        'defaults' => array(
            'module' => 'poll',
            'controller' => 'index',
            'action' => 'browse',
        ),
        'reqs' => array(
            'action' => '(index|browse|manage|create|search-form)',
        ),
    )
);

// Add Advancedactivity Pugin APIs Routes.
$routes['advancedactivity'] = array(
    'advancedactivity_index_general' => array(
        'route' => 'advancedactivity/:action/*',
        'defaults' => array(
            'module' => 'advancedactivity',
            'controller' => 'index',
            'action' => 'comment',
        ),
        'reqs' => array(
            'action' => '(edit-feed|comment|update-save-feed|delete|update-commentable|update-shareable|like|unlike|add-comment-notifications|compose-upload|send-like-notitfication)',
        ),
    ),
    'advancedactivity_feed_general' => array(
        'route' => 'advancedactivity/feeds/:action/*',
        'defaults' => array(
            'module' => 'advancedactivity',
            'controller' => 'feed',
            'action' => 'index',
        ),
        'reqs' => array(
            'action' => '(index|post|hide-item|un-hide-item|likes-comments|attach-link)',
        ),
    ),
    'advancedactivity_friends_general' => array(
        'route' => 'advancedactivity/friends/:action/*',
        'defaults' => array(
            'module' => 'advancedactivity',
            'controller' => 'friends',
            'action' => 'suggest',
        ),
        'reqs' => array(
            'action' => '(suggest|suggest-tag)',
        ),
    )
);

// Add Notification / Friend Requests / Share and Activity Pugin APIs Routes.
$routes['activity'] = array(
    'notification_api_general' => array(
        'route' => 'notifications/:action/*',
        'defaults' => array(
            'module' => 'activity',
            'controller' => 'index',
            'action' => 'notifications',
        ),
        'reqs' => array(
            'action' => '(notifications|friend-request|new-updates|hide|markread)',
        ),
    ),
    'activity_feed_general' => array(
        'route' => 'activity/:action/*',
        'defaults' => array(
            'module' => 'activity',
            'controller' => 'feed',
            'action' => 'index',
        ),
        'reqs' => array(
            'action' => '(index|post|delete|like|unlike|comment|likes-comments|tag-friend)',
        ),
    ),
    'activity_share' => array(
        'route' => 'activity/share/*',
        'defaults' => array(
            'module' => 'activity',
            'controller' => 'index',
            'action' => 'share',
        )
    ),
    'api_hide_all_notifications' => array(
        'route' => 'notifications/markallread/*',
        'defaults' => array(
            'module' => 'activity',
            'controller' => 'index',
            'action' => 'hide',
        )
    )
);

// Add Album Pugin APIs Routes.
$routes['album'] = array(
    'album_general' => array(
        'route' => 'albums/:action/*',
        'defaults' => array(
            'module' => 'album',
            'controller' => 'index',
            'action' => 'browse'
        ),
        'reqs' => array(
            'action' => '(browse|create|list|manage|upload|upload-photo|search-form|view-album|view-content-album)'
        ),
    ),
    'album_list_photos_specific' => array(
        'route' => 'albums/photo/:action/:album_id/*',
        'defaults' => array(
            'module' => 'album',
            'controller' => 'photo',
            'action' => 'list',
        ),
        'reqs' => array(
            'photo_id' => '\d+',
            'action' => '(list|album-cover)',
        ),
    ),
    'album_photos_specific' => array(
        'route' => 'albums/photo/:action/:photo_id/*',
        'defaults' => array(
            'module' => 'album',
            'controller' => 'photo',
            'action' => 'view',
        ),
        'reqs' => array(
            'photo_id' => '\d+',
            'action' => '(view|edit|delete|rotate|flip)',
        ),
    ),
    'sitealbum_entry_view' => array(
        'route' => 'advancedalbums/:slug/:album_id/*',
        'defaults' => array(
            'module' => 'album',
            'controller' => 'album',
            'action' => 'view',
            'slug' => ''
        ),
        'reqs' => array(
            'action' => '(view)',
        ),
    ),
    'sitealbum_extended' => array(
        'route' => 'advancedalbums/photo/:action/*',
        'defaults' => array(
            'module' => 'album',
            'controller' => 'photo',
            'action' => 'view',
        )
    )
);

// Add Core Pugin APIs Routes.
$routes['core'] = array(
    'help_general' => array(
        'route' => 'help/:action/*',
        'defaults' => array(
            'module' => 'core',
            'controller' => 'help',
            'action' => 'privacy',
        ),
        'reqs' => array(
            'action' => '(privacy|terms|contact)',
        ),
    ),
    'api_global_search' => array(
        'route' => 'search/*',
        'defaults' => array(
            'module' => 'core',
            'controller' => 'search',
            'action' => 'index',
        )
    ),
    'api_report_create' => array(
        'route' => 'report/create/*',
        'defaults' => array(
            'module' => 'core',
            'controller' => 'index',
            'action' => 'report-create',
        )
    ),
    'api_enabled_modules' => array(
        'route' => 'get-enabled-modules/*',
        'defaults' => array(
            'module' => 'core',
            'controller' => 'index',
            'action' => 'get-enabled-modules',
        )
    ),
    'api_banners' => array(
        'route' => 'get-banner/*',
        'defaults' => array(
            'module' => 'core',
            'controller' => 'index',
            'action' => 'get-banners',
        )
    ),
    'api_dashboard_menus' => array(
        'route' => 'get-dashboard-menus/*',
        'defaults' => array(
            'module' => 'core',
            'controller' => 'index',
            'action' => 'get-dashboard-menus',
        )
    ),
    
    'api_version_upgrade_popup' => array(
        'route' => 'get-new-version/*',
        'defaults' => array(
            'module' => 'core',
            'controller' => 'index',
            'action' => 'get-new-version',
        )
    ),
    
    'api_location_suggest' => array(
        'route' => 'location-suggest/*',
        'defaults' => array(
            'module' => 'core',
            'controller' => 'index',
            'action' => 'location-suggest',
        )
    ),
    'api_browse_as_guest' => array(
        'route' => 'browse-as-guest/*',
        'defaults' => array(
            'module' => 'core',
            'controller' => 'index',
            'action' => 'browse-as-guest',
        )
    ),
    'api_user-account-menu' => array(
        'route' => 'get-user-account-menu/*',
        'defaults' => array(
            'module' => 'core',
            'controller' => 'index',
            'action' => 'get-user-account-menu',
        )
    ),
    'api_default_language' => array(
        'route' => 'get-default-language/*',
        'defaults' => array(
            'module' => 'core',
            'controller' => 'index',
            'action' => 'get-default-language',
        )
    ),
   'get_language_data' => array(
        'route' => 'get-language-data/*',
        'defaults' => array(
            'module' => 'core',
            'controller' => 'index',
            'action' => 'get-language-data',
        )
    ),
    'get_auction_details' => array(
        'route' => 'get-auctions/*',
        'defaults' => array(
            'module' => 'core',
            'controller' => 'index',
            'action' => 'get-auctions',
        )
    ),
    'default_api_likes_comments' => array(
        'route' => 'likes-comments/*',
        'defaults' => array(
            'module' => 'core',
            'controller' => 'index',
            'action' => 'likes-comments',
        )
    ),
    'default_api_albums_reactions' => array(
        'route' => 'albums_reactions/*',
        'defaults' => array(
            'module' => 'core',
            'controller' => 'index',
            'action' => 'albums',
        )
    ),
    'default_api_like' => array(
        'route' => 'like/*',
        'defaults' => array(
            'module' => 'core',
            'controller' => 'index',
            'action' => 'like',
        )
    ),
    'default_api_notification' => array(
        'route' => 'send-notification/*',
        'defaults' => array(
            'module' => 'core',
            'controller' => 'index',
            'action' => 'send-notification',
        )
    ),
    'default_api_unlike' => array(
        'route' => 'unlike/*',
        'defaults' => array(
            'module' => 'core',
            'controller' => 'index',
            'action' => 'unlike',
        )
    ),
    'default_api_sitevideoenabled' => array(
        'route' => 'is-sitevideo-plugin-enabled/*',
        'defaults' => array(
            'module' => 'core',
            'controller' => 'index',
            'action' => 'is-sitevideo-plugin-enabled',
        )
    ),
    'default_api_comment_create' => array(
        'route' => 'comment-create/*',
        'defaults' => array(
            'module' => 'core',
            'controller' => 'index',
            'action' => 'comment-create',
        )
    ),
    'default_api_comment_create_notification' => array(
        'route' => 'add-comment-notifications/*',
        'defaults' => array(
            'module' => 'core',
            'controller' => 'index',
            'action' => 'add-comment-notifications',
        )
    ),
    'default_api_comment_delete' => array(
        'route' => 'comment-delete/*',
        'defaults' => array(
            'module' => 'core',
            'controller' => 'index',
            'action' => 'comment-delete',
        )
    ),
    'tags_general' => array(
        'route' => 'tags/:action/*',
        'defaults' => array(
            'module' => 'core',
            'controller' => 'tag',
            'action' => 'add',
        ),
        'reqs' => array(
            'action' => '(add|remove|suggest)',
        ),
    ),
    'videos_data_general' => array(
        'route' => 'videosgeneral/*',
        'defaults' => array(
            'module' => 'activity',
            'controller' => 'video',
            'action' => 'browse',
        )
    ),
    'video_data_general' => array(
        'route' => 'videogeneral/:action/*',
        'defaults' => array(
            'module' => 'activity',
            'controller' => 'video',
            'action' => 'view',
        ),
        'reqs' => array(
            'action' => '(view|edit|rating|delete|featured|highlight)'
        ),
    ),
);

$routes['sitetagcheckin'] = array(
    'api_checkin_location' => array(
        'route' => 'sitetagcheckin/suggest/*',
        'defaults' => array(
            'module' => 'sitetagcheckin',
            'controller' => 'index',
            'action' => 'suggest',
        )
    ),
    'api_checkin_content_count' => array(
        'route' => 'sitetagcheckin/checkin-count/*',
        'defaults' => array(
            'module' => 'sitetagcheckin',
            'controller' => 'index',
            'action' => 'content-checkin',
        )
    ),
    'api_checkin_content' => array(
        'route' => 'sitetagcheckin/content-checkin/*',
        'defaults' => array(
            'module' => 'sitetagcheckin',
            'controller' => 'index',
            'action' => 'check-in',
        )
    )
);

$routes['communityad'] = array(
    'communityad_integration' => array(
        'route' => 'communityads/index/remove-ad/*',
        'defaults' => array(
            'module' => 'communityad',
            'controller' => 'index',
            'action' => 'remove-ad',
          
        )
    ),
    'communityad_integration_browse' => array(
        'route' => 'communityads/index/index/*',
        'defaults' => array(
            'module' => 'communityad',
            'controller' => 'index',
            'action' => 'index',
           
        )
    ),
    'communityad_integration_specific' => array(
        'route' => 'communityads/update-click-count/:userad_id/*',
        'defaults' => array(
            'module' => 'communityad',
            'controller' => 'index',
            'action' => 'update-click-count',
            'reqs' => array(
               'userad_id' => '\d+',
            ),
        )
    )
);

// Add User Pugin APIs Routes.
$routes['user'] = array(
    'api_forgot_password' => array(
        'route' => 'forgot-password/*',
        'defaults' => array(
            'module' => 'user',
            'controller' => 'auth',
            'action' => 'forgot',
        )
    ),
    'api_user_profile' => array(
        'route' => 'user/get-friend-list/*',
        'defaults' => array(
            'module' => 'user',
            'controller' => 'profile',
            'action' => 'get-friend-list',
        )
    ),
    'api_user_remove' => array(
        'route' => 'user/remove-profile-photo/:user_id*',
        'defaults' => array(
            'module' => 'user',
            'controller' => 'profile',
            'action' => 'remove-profile-photo',
        )
    ),
    'user_links_general' => array(
        'route' => 'user/:action/*',
        'defaults' => array(
            'module' => 'user',
            'controller' => 'friends',
            'action' => 'add',
        ),
        'reqs' => array(
            'photo_id' => '\d+',
            'action' => '(add|cancel|confirm|reject|ignore|remove|list-create|list-delete|list-add|list-remove|suggest)',
        ),
    ),
    'user_block_general' => array(
        'route' => 'block/:action/*',
        'defaults' => array(
            'module' => 'user',
            'controller' => 'block',
            'action' => 'add',
        ),
        'reqs' => array(
            'photo_id' => '\d+',
            'action' => '(add|remove)',
        ),
    ),
    'default_api_user_profile' => array(
        'route' => 'user/profile/*',
        'defaults' => array(
            'module' => 'user',
            'controller' => 'profile',
            'action' => 'index',
        )
    ),
    'user_general' => array(
        'route' => 'members/:action/*',
        'defaults' => array(
            'module' => 'user',
            'controller' => 'index',
            'action' => 'browse'
        ),
        'reqs' => array(
            'action' => '(home|browse|search-form|get-contact-list-members||get-quick-count)',
        )
    ),
    'api_user_subscription' => array(
        'route' => 'members/settings/subscriptions/*',
        'defaults' => array(
            'module' => 'user',
            'controller' => 'settings',
            'action' => 'subscription',
        )
    ),
    'api_set_ios_subscription' => array(
        'route' => 'user/set-iosuser-subscription/*',
        'defaults' => array(
            'module' => 'user',
            'controller' => 'auth',
            'action' => 'set-user-subscription',
        )
    ),
    'api_upgrade_ios_subscription' => array(
        'route' => 'user/upgrade-subscription/*',
        'defaults' => array(
            'module' => 'user',
            'controller' => 'auth',
            'action' => 'upgrade-subscription',
        )
    ),
    'update_fcm_token' => array(
        'route' => 'user/update-fcm-token/*',
        'defaults' => array(
            'module' => 'user',
            'controller' => 'auth',
            'action' => 'update-fcm-token',
        )
    ),
);


$routes['messages'] = array(
    'messages_general' => array(
        'route' => 'messages/:action/*',
        'defaults' => array(
            'module' => 'messages',
            'controller' => 'messages',
            'action' => 'index',
        ),
        'reqs' => array(
            'action' => '(index|mark-message-read-unread|inbox|search|outbox|delete|view|compose)',
        )
    )
);
$routes['sitereview'] = array(
    'sitereview_general_listtype' => array(
        'route' => 'listings/:action/*',
        'defaults' => array(
            'module' => 'sitereview',
            'controller' => 'index',
            'action' => 'index',
        ),
        'reqs' => array(
            'action' => '(cities|listingtypes|home|categories|search-form|index|manage|create|get-search-listings|map|packages|upgrade-package)',
        )
    ),
    'sitereview_specific_listtype' => array(
        'route' => 'listing/:action/:listing_id/*',
        'defaults' => array(
            'module' => 'sitereview',
            'controller' => 'index',
            'action' => 'view',
        ),
        'reqs' => array(
            'action' => '(view|messageowner|tellafriend|print|delete|publish|close|edit|claim-listing|add|remove|where-to-buy|apply-now)',
            'listing_id' => '\d+',
        )
    ),
    'sitereview_video_general_listtype' => array(
        'route' => 'listings/video/:action/:listing_id/*',
        'defaults' => array(
            'module' => 'sitereview',
            'controller' => 'video',
            'action' => 'index'
        ),
        'reqs' => array(
            'action' => '(index|create)',
            'listing_id' => '\d+',
        )
    ),
    'sitereview_videospecific_listtype' => array(
        'route' => 'listings/video/:action/:listing_id/*',
        'defaults' => array(
            'module' => 'sitereview',
            'controller' => 'video',
            'action' => 'view',
        ),
        'reqs' => array(
            'action' => '(rate|delete|edit|view)',
        )
    ),
    'sitereview_photoalbumupload_listtype' => array(
        'route' => 'listings/photo/:listing_id/*',
        'defaults' => array(
            'module' => 'sitereview',
            'controller' => 'photo',
            'action' => 'list',
        ),
        'reqs' => array(
//            'action' => '(upload)',
            'listing_id' => '\d+',
        ),
    ),
    'sitereview_image_specific_listtype' => array(
        'route' => 'listings/photo/:action/:listing_id/*',
        'defaults' => array(
            'module' => 'sitereview',
            'controller' => 'photo',
            'action' => 'view',
        ),
        'reqs' => array(
            'action' => '(view|delete)',
            'listing_id' => '\d+',
        ),
    ),
    'sitereview_photo_extended_listtype' => array(
        'route' => 'listings/photo/edit/:listing_id/*',
        'defaults' => array(
            'module' => 'sitereview',
            'controller' => 'photo',
            'action' => 'edit',
        ),
        'reqs' => array(
            'listing_id' => '\d+',
        )
    ),
    'sitereview_user_general_listtype' => array(
        'route' => 'listings/review/:action/:listing_id/*',
        'defaults' => array(
            'module' => 'sitereview',
            'controller' => 'review',
        ),
        'reqs' => array(
            'listing_id' => '\d+',
            'action' => '(create|edit|view|update|reply|helpful|email|delete)'
        ),
    ),
    'sitereview_review_browse_listtype' => array(
        'route' => 'listings/reviews/*',
        'defaults' => array(
            'module' => 'sitereview',
            'controller' => 'review',
            'action' => 'browse',
        ),
    ),
    'sitereview_wishlist_specific' => array(
        'route' => 'listings/wishlist/:action/:wishlist_id/*',
        'defaults' => array(
            'module' => 'sitereview',
            'controller' => 'wishlist',
            'action' => 'edit',
        ),
        'reqs' => array(
            'wishlist_id' => '\d+',
            'action' => '(edit|delete|follow)',
        ),
    ),
    'sitereview_wishlist_view' => array(
        'route' => 'listings/wishlist/:wishlist_id/*',
        'defaults' => array(
            'module' => 'sitereview',
            'controller' => 'wishlist',
            'action' => 'profile',
        ),
        'reqs' => array(
            'wishlist_id' => '\d+',
        )
    ),
    'sitereview_wishlist_general' => array(
        'route' => 'listings/wishlist/:action/*',
        'defaults' => array(
            'module' => 'sitereview',
            'controller' => 'wishlist',
            'action' => 'browse'
        ),
        'reqs' => array(
            'action' => '(browse|search-form|create|add|remove|print|tell-a-friend|message-owner)',
        ),
    ),
);
$slug_plural = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.slugplural', 'event-items');

//ADD siteeventrepeat-siteevent Extension API Routes
$routes['siteeventrepeat'] = array(
    'siteeventrepeat_general' => array(
        'route' => 'siteeventrepeat/:action/:event_id/*',
        'defaults' => array(
            'module' => 'siteeventrepeat',
            'controller' => 'index',
            'action' => 'index',
        ),
        'reqs' => array(
            'event_id' => '\d+',
            'action' => '(index|info)',
        )
    )
);

// Sitestore routes
$routes['sitestore'] = array(
    'sitestore_general' => array(
        'route' => 'sitestore/:action/*',
        'defaults' => array(
            'module' => 'sitestore',
            'controller' => 'index',
            'action' => 'browse',
        ),
        'reqs' => array(
            'action' => '(browse|search-form|manage)',
        ),
    ),
    'sitestore_specific' => array(
        'route' => 'sitestore/:action/:store_id/*',
        'defaults' => array(
            'module' => 'sitestore',
            'controller' => 'store',
            'action' => 'view',
        ), 
        'reqs' => array(
            'action' => '(delete-slider-photo|upload-slideshow-album-photo|all-branch|add-branch|edit-branch|delete-branch|view|close|delete|information|overview|album|offers|tellafriend|invite|section|edit|add-shipping-method|edit-shipping-method|shipping-methods)',
        ),
    ), //sitestore/album/10/
    'sitestore_cart' => array(
        'route' => 'sitestore/cart/:action/*',
        'defaults' => array(
            'module' => 'sitestore',
            'controller' => 'cart',
            'action' => 'view'
        ),
        'reqs' => array(
            'action' => '(view|update-quantity|empty|apply-coupon|remove-store-products|merge|cartcount)',
        ),
    ),
    'sitestore_delete_product' => array(
        'route' => 'sitestore/cart/:action/:cartproduct_id/*',
        'defaults' => array(
            'module' => 'sitestore',
            'controller' => 'cart',
            'action' => 'delete'
        ),
        'reqs' => array(
            'action' => '(delete-product)',
        ),
    ),
    'sitestore_checkout' => array(
        'route' => 'sitestore/checkout/:action/*',
        'defaults' => array(
            'module' => 'sitestore',
            'controller' => 'checkout',
            'action' => 'address'
        ),
        'reqs' => array(
            'action' => '(address|states|shipping|payment|validating-order)',
        ),
    ),
    'sitestore_manageorder' => array(
        'route' => 'sitestore/orders/:action/*',
        'defaults' => array(
            'module' => 'sitestore',
            'controller' => 'manageorder',
            'action' => 'index'
        ),
        'reqs' => array(
            'action' => '(index|downloadable-files|search-form|increment-download|download)',
        ),
    ),
    'sitestore_manageorder_specific' => array(
        'route' => 'sitestore/orders/:action/:order_id/*',
        'defaults' => array(
            'module' => 'sitestore',
            'controller' => 'manageorder',
            'action' => 'view'
        ),
        'reqs' => array(
            'action' => '(view|comment|order-ship|reorder|cancel)',
        ),
    ),
    'sitestore_photos' => array(
        'route' => 'sitestore/photos/:action/:store_id/*',
        'defaults' => array(
            'module' => 'sitestore',
            'controller' => 'photo',
            'action' => 'browse-album'
        ),
        'reqs' => array(
            'action' => '(browse-album|view-album|delete-album|view-photo|edit-photo|deletephoto|addphoto)',
        ),
    ),
);


// Sitestoreproduct
$routes['sitestoreproduct'] = array(
    'sitestoreproduct_search' => array(
        'route' => 'sitestore/product/:action/*',
        'defaults' => array(
            'module' => 'sitestoreproduct',
            'controller' => 'index',
            'action' => 'product-search-form'
        ),
        'reqs' => array(
            'action' => '(product-search-form|category|variation-option|make-order|getcombination)',
        ),
    ),
    //sitestore/product/edit/:product_id/
    'sitestoreproduct_product' => array(
        'route' => 'sitestore/product/:action/:product_id/*',
        'defaults' => array(
            'module' => 'sitestoreproduct',
            'controller' => 'index',
            'action' => 'view'
        ),
        'reqs' => array(
            'action' => '(add-to-wishlist|view|edit|delete)',
            'product_id' => '\d+',
        ),
    ),
        'sitestoreproduct_photo_upload' => array(
        'route' => 'sitestore/product/photo/:action/:product_id/*',
        'defaults' => array(
            'module' => 'sitestoreproduct',
            'controller' => 'photo',
            'action' => 'upload-photo'
        ),
        'reqs' => array(
            'action' => '(upload-photo)',
            'product_id' => '\d+',
        ),
    ),
    'sitestoreproduct_general' => array(
        'route' => 'sitestore/product/:action/:store_id/*',
        'defaults' => array(
            'module' => 'sitestoreproduct',
            'controller' => 'index',
            'action' => 'index'
        ),
        'reqs' => array(
            'action' => '(add-section|edit-section|delete-section|upload-section-photo|manage|index|featured|highlight|create|move-product-into-section|get-list-contact-seller)',
        ),
    ),
    'sitestoreproduct_browse' => array(
        'route' => 'sitestore/product/:action/*',
        'defaults' => array(
            'module' => 'sitestoreproduct',
            'controller' => 'index',
            'action' => 'browse'
        ),
        'reqs' => array(
            'action' => '(browse|validate-product|browse-wishlist|create-wishlist|change-order-status|manage-order)',
        ),
    ),
    'sitestoreproduct_specific' => array(
        'route' => 'sitestore/product/:action/:store_id/:product_id/*',
        'defaults' => array(
            'module' => 'sitestoreproduct',
            'controller' => 'index',
            'action' => 'add-to-cart'
        ),
        'reqs' => array(
            'action' => '(add-to-cart|messageowner|tellafriend|askopinion|photos|contact-seller)',
            'store_id' => '\d+',
            'product_id' => '\d+'
        ),
    ),
    
    
    'sitestoreproduct_specific_section' => array(
        'route' => 'sitestore/product/:action/:store_id/:section_id/*',
        'defaults' => array(
            'module' => 'sitestoreproduct',
            'controller' => 'index',
            'action' => 'section-product'
        ),
        'reqs' => array(
            'action' => '(section-product)',
            'store_id' => '\d+',
            'section_id' => '\d+'
        ),
    ),
    'sitestoreproduct_review_general' => array(
        'route' => 'sitestore/product/review/:action/:product_id/*',
        'defaults' => array(
            'module' => 'sitestoreproduct',
            'controller' => 'review',
            'action' => 'browse',
        ),
        'reqs' => array(
            'action' => '(browse|create)',
            'product_id' => '\d+',
        ),
    ),
    'sitestoreproduct_review_specific' => array(
        'route' => 'sitestore/product/review/:action/:product_id/:review_id/*',
        'defaults' => array(
            'module' => 'sitestoreproduct',
            'controller' => 'review',
            'action' => 'view',
        ),
        'reqs' => array(
            'action' => '(view|edit|like|comment|delete|list-comments|helpful)',
            'product_id' => '\d+',
            'review_id' => '\d+',
        ),
    ),
    'sitestoreproduct_wishlist_specific' => array(
        'route' => 'sitestore/product/wishlist/:action/:wishlist_id/*',
        'defaults' => array(
            'module' => 'sitestoreproduct',
            'controller' => 'wishlist',
            'action' => 'edit',
        ),
        'reqs' => array(
            'wishlist_id' => '\d+',
            'action' => '(edit|delete)',
        ),
    ),
    'sitestoreproduct_wishlist_view' => array(
        'route' => 'sitestore/product/wishlist/:wishlist_id/*',
        'defaults' => array(
            'module' => 'sitestoreproduct',
            'controller' => 'wishlist',
            'action' => 'profile',
        ),
        'reqs' => array(
            'wishlist_id' => '\d+'
        )
    ),
    'sitestoreproduct_wishlist_general' => array(
        'route' => 'sitestore/product/wishlist/:action/*',
        'defaults' => array(
            'module' => 'sitestoreproduct',
            'controller' => 'wishlist',
            'action' => 'browse'
        ),
        'reqs' => array(
            'action' => '(browse|search-form|create|add|remove|print|tell-a-friend|message-owner|wishlist-options)',
        ),
    ),
);

// Sitestore offers
$routes['sitestoreoffer'] = array(
    'sitestoreoffer_all' => array(
        'route' => 'sitestore/offers/index/*',
        'defaults' => array(
            'module' => 'sitestoreoffer',
            'controller' => 'index',
            'action' => 'index',
        ),
    ),
    'sitestoreoffer_general' => array(
        'route' => 'sitestore/offers/browse/:store_id/*',
        'defaults' => array(
            'module' => 'sitestoreoffer',
            'controller' => 'index',
            'action' => 'browse',
        ),
        'reqs' => array(
            'action' => '(browse)',
            'store_id' => '\d+',
        ),
    ),
    'sitestoreoffer_specific' => array(
        'route' => 'sitestore/offer/:action/:offer_id/*',
        'defaults' => array(
            'module' => 'sitestoreoffer',
            'controller' => 'index',
            'action' => 'view',
        ),
        'reqs' => array(
            'action' => '(view|enable|delete)',
            'offer_id' => '\d+',
        ),
    ),
);

$routes['sitestorereview'] = array(
    'sitestore_review_browse' => array(
        'route' => 'sitestore/reviews/browse/:store_id/*',
        'defaults' => array(
            'module' => 'sitestorereview',
            'controller' => 'review',
            'action' => 'browse',
        ),
        'reqs' => array(
            'store_id' => '\d+',
            'action' => '(browse)'
        ),
    ),
    'sitestore_user_review_general' => array(
        'route' => 'sitestore/reviews/:action/:store_id/*',
        'defaults' => array(
            'module' => 'sitestorereview',
            'controller' => 'review',
        ),
        'reqs' => array(
            'store_id' => '\d+',
            'action' => '(create|browse)'
        ),
    ),
    'sitestore_view_review' => array(
        'route' => 'sitestore/review/:action/:store_id/:review_id/*',
        'defaults' => array(
            'module' => 'sitestorereview',
            'controller' => 'review',
            'action' => 'view',
        ),
        'reqs' => array(
            'review_id' => '\d+',
            'store_id' => '\d+',
            'action' => '(view|delete|comment|edit|list-comments|like)',
        ),
    ),
);

$routes['sitepage'] = array(
    'sitepage_specific' => array(
        'route' => 'sitepage/:action/:page_id/*',
        'defaults' => array(
            'module' => 'sitepage',
            'controller' => 'profile',
            'action' => 'view',
        ),
        'reqs' => array(
            'action' => '(view|edit|index|delete|close|publish|claim|messageowner|tellafriend|information|follow|overview)',
            'page_id' => '\d+',
        ),
    ),
    'sitepage_general' => array(
        'route' => 'sitepages/:action/*',
        'defaults' => array(
            'module' => 'sitepage',
            'controller' => 'index',
            'action' => 'browse',
        ),
        'reqs' => array(
            'action' => '(browse|manage|create|search-form|pageurlvalidation|category|packages|upgrade-package)',
        )
    ),
    // category sitepage
    'sitepage_category_general' => array(
        'route' => 'sitepage/category/:action/*',
        'defaults' => array(
            'module' => 'sitepage',
            'controller' => 'category',
            'action' => 'index',
        ),
        'reqs' => array(
            'action' => '(index)',
        ),
    ),
);

$routes['sitebusiness'] = array(
    'sitebusiness_specific' => array(
        'route' => 'sitebusiness/:action/:business_id/*',
        'defaults' => array(
            'module' => 'sitebusiness',
            'controller' => 'profile',
            'action' => 'view',
        ),
        'reqs' => array(
            'action' => '(view|edit|index|delete|close|publish|claim|messageowner|tellafriend|information)',
            'business_id' => '\d+',
        ),
    ),
    'sitebusiness_general' => array(
        'route' => 'sitebusiness/:action/*',
        'defaults' => array(
            'module' => 'sitebusiness',
            'controller' => 'index',
            'action' => 'browse',
        ),
        'reqs' => array(
            'action' => '(browse|manage|create|search-form)',
        )
    ),
    'sitebusiness_category_general' => array(
        'route' => 'sitebusiness/category/:action/*',
        'defaults' => array(
            'module' => 'sitebusiness',
            'controller' => 'category',
            'action' => 'index',
        ),
        'reqs' => array(
            'action' => '(index)',
        ),
    ),
);

$routes['sitepagealbum'] = array(
    'sitepage_photo_general' => array(
        'route' => 'sitepage/photos/:action/:page_id/*',
        'defaults' => array(
            'module' => 'sitepagealbum',
            'controller' => 'photo',
            'action' => 'index',
        ),
        'reqs' => array(
            'page_id' => '\d+',
            'action' => '(index)',
        ),
    ),
    'sitepage_album_specific' => array(
        'route' => 'sitepage/photos/:action/:page_id/:album_id/*',
        'defaults' => array(
            'module' => 'sitepagealbum',
            'controller' => 'photo',
            'action' => 'viewalbum',
        ),
        'reqs' => array(
            'page_id' => '\d+',
            'album_id' => '\d+',
            'action' => '(viewalbum|deletealbum|editalbum|addalbumofday|albumfeatured|addphoto|viewalbum-data)',
        ),
    ),
    'sitepage_photo_specific' => array(
        'route' => 'sitepage/photos/:action/:page_id/:album_id/:photo_id/*',
        'defaults' => array(
            'module' => 'sitepagealbum',
            'controller' => 'photo',
            'action' => 'viewphoto',
        ),
        'reqs' => array(
            'page_id' => '\d+',
            'album_id' => '\d+',
            'photo_id' => '\d+',
            'action' => '(viewphoto|editphoto|deletephoto)',
        ),
    ),
);

$routes['sitepagereview'] = array(
    'sitepage_review_browse' => array(
        'route' => 'sitepage/reviews/browse/:page_id/*',
        'defaults' => array(
            'module' => 'sitepagereview',
            'controller' => 'review',
            'action' => 'browse',
        ),
        'reqs' => array(
            'page_id' => '\d+',
            'action' => '(browse)'
        ),
    ),
    'sitepage_user_review_general' => array(
        'route' => 'sitepage/reviews/:action/:page_id/*',
        'defaults' => array(
            'module' => 'sitepagereview',
            'controller' => 'review',
        ),
        'reqs' => array(
            'page_id' => '\d+',
            'action' => '(create|browse|search)'
        ),
    ),
    'sitepage_view_review' => array(
        'route' => 'sitepage/review/:action/:page_id/:review_id/*',
        'defaults' => array(
            'module' => 'sitepagereview',
            'controller' => 'review',
            'action' => 'view',
        ),
        'reqs' => array(
            'review_id' => '\d+',
            'page_id' => '\d+',
            'action' => '(view|delete|comment|edit|listcomments|like|unlike)',
        ),
    ),
        // 'sitepage_review_comment' => array(
        //     'route' => 'sitepage/review/:action/:page_id/:review_id/:comment_id/*',
        //     'defeual'
        // ),
);

$routes['sitepagevideo'] = array(
    'sitepagevideo_general' => array(
        'route' => 'sitepage/videos/:action/:page_id/*',
        'defaults' => array(
            'module' => 'sitepagevideo',
            'controller' => 'index',
            'action' => 'index',
        ),
        'reqs' => array(
            'page_id' => '\d+',
            'action' => '(index|search-form)',
        ),
    ),
    'sitepagevideo_create' => array(
        'route' => 'sitepage/video/create/:page_id/*',
        'defaults' => array(
            'module' => 'sitepagevideo',
            'controller' => 'video',
            'action' => 'create',
        ),
        'reqs' => array(
            'page_id' => '\d+',
            'action' => '(create)',
        ),
    ),
    'sitepagevideo_specific' => array(
        'route' => 'sitepage/video/:action/:page_id/:video_id/*',
        'defaults' => array(
            'module' => 'sitepagevideo',
            'controller' => 'video',
            'action' => 'view',
        ),
        'reqs' => array(
            'page_id' => '\d+',
            'video_id' => '\d+',
            'action' => '(view|edit|listcomments|delete|comment|removecomment|like|unlike|featured|highlight|rating)',
        ),
    ),
);
$slug_singular = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.slugsingular', 'event-item');
$routes['siteevent'] = array(
    'siteevent_general' => array(
        'route' => 'advancedevents/:action/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'index',
            'action' => 'index',
        ),
        'reqs' => array(
            'action' => '(manage|create|search-form|map|categories|calender|packages|get-hosts|upgrade-package)',
        ),
    ),
    'siteevent_entry' => array(
        'route' => 'advancedevents/:action/:event_id/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'index',
            'action' => 'view'
        ),
        'reqs' => array(
            'action' => '(view|messageowner|tellafriend|delete|publish|close|edit|overview|notifications|edit-location|description|information|capacity-and-waitlist)',
            'event_id' => '\d+',
        )
    ),
    'siteevent_specific' => array(
        'route' => 'advancedevents/:action/:event_id/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'index',
            'action' => 'view'
        ),
        'reqs' => array(
            'action' => '(view|messageowner|tellafriend|delete|publish|close|edit|overview|notifications|edit-location|description|information|capacity-and-waitlist)',
            'event_id' => '\d+',
        )
    ),
    'siteevent_waitlist' => array(
        'route' => 'advancedevents/waitlist/:action/:event_id/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'waitlist',
            'action' => 'index'
        ),
        'reqs' => array(
            'action' => '(index|join)',
            'event_id' => '\d+',
        )
    ),
    'siteevent_list_photos_specific' => array(
        'route' => 'advancedevents/photo/:action/:event_id/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'photo',
            'action' => 'list',
        ),
        'reqs' => array(
            'event_id' => '\d+',
            'action' => '(list)',
        ),
    ),
    'siteevent_photos_specific' => array(
        'route' => 'advancedevents/photo/:action/:photo_id/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'photo',
            'action' => 'view',
        ),
        'reqs' => array(
            'photo_id' => '\d+',
            'action' => '(view|edit|delete)',
        ),
    ),
    'siteevent_members_suggest' => array(
        'route' => 'advancedevents/member-suggest/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'index',
            'action' => 'member-suggest',
        ),
    ),
    'siteevent_members_general' => array(
        'route' => 'advancedevents/member/:action/:event_id/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'member',
            'action' => 'list',
        ),
        'reqs' => array(
            'action' => '(search-form|list|join|invite|promote|demote|manage-leaders|accept|reject|leave|request|remove|approve|cancel|confirm|compose)',
        )
    ),
    'siteevent_diary_specific' => array(
        'route' => 'advancedevents/diary/:action/:diary_id/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'diary',
            'action' => 'edit',
        ),
        'reqs' => array(
            'diary_id' => '\d+',
            'action' => '(edit|delete)',
        ),
    ),
    'siteevent_diary_view' => array(
        'route' => 'advancedevents/diary/:diary_id/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'diary',
            'action' => 'profile',
        ),
        'reqs' => array(
            'diary_id' => '\d+'
        )
    ),
    'siteevent_diary_general' => array(
        'route' => 'advancedevents/diaries/:action/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'diary',
            'action' => 'browse'
        ),
        'reqs' => array(
            'action' => '(browse|search-form|create|add|remove|print|tell-a-friend|message-owner)',
        ),
    ),
    'siteevent_topic_general' => array(
        'route' => 'advancedevents/topic/:action/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'topic',
            'action' => 'index'
        ),
        'reqs' => array(
            'action' => '(index|create|view|post|sticky|close|rename|watch|delete)',
        ),
    ),
    'siteevent_video_view' => array(
        'route' => 'advancedevents/video/:event_id/:video_id/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'video',
            'action' => 'view',
        ),
        'reqs' => array(
            'video_id' => '\d+',
            'event_id' => '\d+'
        )
    ),
    'siteevent_video_create' => array(
        'route' => 'advancedevents/video/:action/:event_id/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'video',
            'action' => 'create',
        ),
        'reqs' => array(
            'action' => '(create|rate)',
            'event_id' => '\d+'
        )
    ),
    'siteevent_video_edit' => array(
        'route' => 'advancedevents/video/edit/:event_id/:video_id/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'video',
            'action' => 'edit',
        )
    ),
    'siteevent_video_delete' => array(
        'route' => 'advancedevents/video/delete/:event_id/:video_id/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'video',
            'action' => 'delete',
        ),
        'reqs' => array(
            'video_id' => '\d+',
            'event_id' => '\d+'
        )
    ),
    'siteevent_video_general' => array(
        'route' => 'advancedevents/videos/:event_id/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'video',
            'action' => 'browse',
        ),
        'reqs' => array(
            'event_id' => '\d+'
        )
    ),
    'siteevent_review_browse' => array(
        'route' => 'advancedevents/reviews/browse/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'review',
            'action' => 'browse',
        ),
    ),
    'siteevent_user_review_general' => array(
        'route' => 'advancedevents/review/:action/:event_id/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'review',
        ),
        'reqs' => array(
            'event_id' => '\d+',
            'action' => '(create|edit|rate|update|helpful|delete)'
        ),
    ),
    'siteevent_review_editor' => array(
        'route' => 'advancedevents/editors/:action/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'editor',
            'action' => 'home',
        ),
    ),
    'siteevent_view_review' => array(
        'route' => 'advancedevents/review/:event_id/:review_id/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'review',
            'action' => 'view',
        ),
        'reqs' => array(
            'review_id' => '\d+',
            'event_id' => '\d+'
        ),
    ),
    'siteevent_editor_create' => array(
        'route' => 'advancedevents/editor/create/:event_id/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'editor',
            'action' => 'create',
        ),
        'reqs' => array(
            'event_id' => '\d+'
        )
    ),
    'siteevent_editor_edit' => array(
        'route' => 'advancedevents/editor/edit/:event_id/:review_id/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'editor',
            'action' => 'edit',
        )
    ),
    'siteevent_review_editor_profile' => array(
        'route' => 'advancedevents/editor/:action/:username/:user_id',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'editor',
            'action' => 'profile',
        ),
        'reqs' => array(
            'user_id' => '\d+',
            'action' => '(editor-mail|profile)',
        )
    ),
    'siteevent_organizer_profile' => array(
        'route' => 'advancedevents/organizer/:organizer_id',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'organizer',
            'action' => 'view',
        ),
        'reqs' => array(
            'organizer_id' => '\d+',
        )
    ),
    'siteevent_announcement_general' => array(
        'route' => 'advancedevents/announcement/:event_id',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'announcement',
            'action' => 'index',
        ),
        'reqs' => array(
            'event_id' => '\d+',
            'action' => '(index|create)'
        )
    ),
    'siteevent_announcement_specific' => array(
        'route' => 'advancedevents/announcement/:action/:event_id/:announcement_id',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'announcement',
            'action' => 'view',
        ),
        'reqs' => array(
            'event_id' => '\d+',
            'announcement_id' => '\d+',
            'action' => '(view|delete)'
        )
    ),
    'siteevent_review_editor' => array(
        'route' => 'advancedevents/editors/:action/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'editor',
            'action' => 'home',
        ),
    ),
    'siteevent_editor_general' => array(
        'route' => 'advancedevents/editor/:action/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'editor',
            'action' => 'home',
        ),
        'reqs' => array(
            'action' => '(home|similar-items|add-items|categories)',
        ),
    ),
    'siteevent_review_editor_profile' => array(
        'route' => 'advancedevents/editor/profile/:username/:user_id',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'editor',
            'action' => 'profile',
        ),
        'reqs' => array(
            'user_id' => '\d+'
        )
    ),
    'siteevent_priceinfo' => array(
        'route' => 'advancedevent/priceinfo/:action/:id/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'price-info',
            'action' => 'index',
        ),
        'reqs' => array(
            'action' => '(index|add|edit|delete|redirect)',
        ),
    ),
    'siteevent_entry_view_occurrence' => array(
        'route' => 'advancedevent/:slug/:event_id/:occurrence_id/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'index',
            'action' => 'view',
            'slug' => '',
            'occurrence_id' => ''
        ),
        'reqs' => array(
            'event_id' => '\d+',
            'occurrence_id' => '\d+'
        )
    ),
    'siteevent_entry_view' => array(
        'route' => $slug_singular . '/:slug/:event_id/*',
        'defaults' => array(
            'module' => 'siteevent',
            'controller' => 'index',
            'action' => 'view',
            'slug' => ''
        ),
        'reqs' => array(
            'event_id' => '\d+'
        )
    ),
);

$slug_plural = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.slugplural', 'event-items');
$routes['siteevent']['siteevent_package'] = array(
    'route' => $slug_plural . '/package/:action/*',
    'defaults' => array(
        'module' => 'siteeventpaid',
        'controller' => 'package',
        'action' => 'index',
        'package' => 1,
    ),
    'reqs' => array(
        'action' => '(index|detail|update-package|update-confirmation|cancel)',
    ),
);

//@TODO will remove when we will add sitebusiness api routes.
$moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness');
if ($moduleEnabled) {
    $routeStartS = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebusiness.manifestUrlS', "businessitem");
    $routes['siteevent']['sitebusiness_entry_view'] = array(
        // Public
        'route' => $routeStartS . '/:business_url/*',
        'defaults' => array(
            'module' => 'sitebusiness',
            'controller' => 'index',
            'action' => 'view',
        ),
    );
}

// Siteeventticket browse page
$routes['siteeventticket'] = array(
    'siteeventticket_coupons' => array(
        'route' => 'advancedeventtickets/coupons/:action/*',
        'defaults' => array(
            'module' => 'siteeventticket',
            'controller' => 'coupon',
            'action' => 'index',
        ),
        'reqs' => array(
            'action' => '(index|trends)'
        ),
    ),
    'siteeventticket_ticket' => array(
        'route' => 'advancedeventtickets/tickets/:action/*',
        'defaults' => array(
            'module' => 'siteeventticket',
            'controller' => 'ticket',
            'action' => 'index',
        ),
        'reqs' => array(
            'action' => '(index|trends|tickets-buy)'
        ),
    ),
    'siteeventticket_order' => array(
        'route' => 'advancedeventtickets/order/:action/*',
        'defaults' => array(
            'module' => 'siteeventticket',
            'controller' => 'order',
            'action' => 'view',
        ),
        'reqs' => array(
            'action' => '(buyer-details|trends|checkout|place-order|view)'
        ),
    ),
);
$routes['sitegroup'] = array(
    'sitegroup_specific' => array(
        'route' => 'advancedgroup/:action/:group_id/*',
        'defaults' => array(
            'module' => 'sitegroup',
            'controller' => 'profile',
            'action' => 'view',
        ),
        'reqs' => array(
            'action' => '(view|edit|index|delete|close|publish|claim|messageowner|tellafriend|information|follow|overview)',
            'group_id' => '\d+',
        ),
    ),
    'sitegroup_general' => array(
        'route' => 'advancedgroups/:action/*',
        'defaults' => array(
            'module' => 'sitegroup',
            'controller' => 'index',
            'action' => 'browse',
        ),
        'reqs' => array(
            'action' => '(browse|manage|create|search-form|groupurlvalidation|category|packages|upgrade-package)',
        )
    ),
);

$routes['sitegroupalbum'] = array(
    'sitegroup_photo_general' => array(
        'route' => 'advancedgroups/photos/:action/:group_id/*',
        'defaults' => array(
            'module' => 'sitegroupalbum',
            'controller' => 'photo',
            'action' => 'index',
        ),
        'reqs' => array(
            'group_id' => '\d+',
            'action' => '(index)',
        ),
    ),
    'sitegroup_album_specific' => array(
        'route' => 'advancedgroups/photos/:action/:group_id/:album_id/*',
        'defaults' => array(
            'module' => 'sitegroupalbum',
            'controller' => 'photo',
            'action' => 'viewalbum',
        ),
        'reqs' => array(
            'group_id' => '\d+',
            'album_id' => '\d+',
            'action' => '(viewalbum|deletealbum|editalbum|addalbumofday|albumfeatured|addphoto)',
        ),
    ),
    'sitegroup_photo_specific' => array(
        'route' => 'advancedgroups/photos/:action/:group_id/:album_id/:photo_id/*',
        'defaults' => array(
            'module' => 'sitegroupalbum',
            'controller' => 'photo',
            'action' => 'viewphoto',
        ),
        'reqs' => array(
            'group_id' => '\d+',
            'album_id' => '\d+',
            'photo_id' => '\d+',
            'action' => '(viewphoto|editphoto|deletephoto)',
        ),
    ),
);

$routes['sitegroupreview'] = array(
    'sitegroup_review_browse' => array(
        'route' => 'advancedgroups/reviews/browse/:group_id/*',
        'defaults' => array(
            'module' => 'sitegroupreview',
            'controller' => 'review',
            'action' => 'browse',
        ),
        'reqs' => array(
            'group_id' => '\d+',
            'action' => '(browse)'
        ),
    ),
    'sitegroup_user_review_general' => array(
        'route' => 'advancedgroups/reviews/:action/:group_id/*',
        'defaults' => array(
            'module' => 'sitegroupreview',
            'controller' => 'review',
        ),
        'reqs' => array(
            'group_id' => '\d+',
            'action' => '(create|browse|search)'
        ),
    ),
    'sitegroup_view_review' => array(
        'route' => 'advancedgroups/review/:action/:group_id/:review_id/*',
        'defaults' => array(
            'module' => 'sitegroupreview',
            'controller' => 'review',
            'action' => 'view',
        ),
        'reqs' => array(
            'review_id' => '\d+',
            'group_id' => '\d+',
            'action' => '(view|delete|comment|edit|listcomments|like|unlike)',
        ),
    ),
);

$routes['sitegroupvideo'] = array(
    'sitegroupvideo_general' => array(
        'route' => 'advancedgroups/videos/:action/:group_id/*',
        'defaults' => array(
            'module' => 'sitegroupvideo',
            'controller' => 'index',
            'action' => 'index',
        ),
        'reqs' => array(
            'group_id' => '\d+',
            'action' => '(index|search-form)',
        ),
    ),
    'sitegroupvideo_create' => array(
        'route' => 'advancedgroups/video/create/:group_id/*',
        'defaults' => array(
            'module' => 'sitegroupvideo',
            'controller' => 'video',
            'action' => 'create',
        ),
        'reqs' => array(
            'group_id' => '\d+',
            'action' => '(create)',
        ),
    ),
    'sitegroupvideo_specific' => array(
        'route' => 'advancedgroups/video/:action/:group_id/:video_id/*',
        'defaults' => array(
            'module' => 'sitegroupvideo',
            'controller' => 'video',
            'action' => 'view',
        ),
        'reqs' => array(
            'group_id' => '\d+',
            'video_id' => '\d+',
            'action' => '(view|edit|listcomments|delete|comment|removecomment|like|unlike|featured|highlight|rating)',
        ),
    ),
);

$routes['sitegroupmember'] = array(
    'sitegroupmember_specific' => array(
        'route' => 'advancedgroups/member/:action/:group_id/*',
        'defaults' => array(
            'module' => 'sitegroupmember',
            'controller' => 'member',
            'action' => 'join',
        ),
        'reqs' => array(
            'action' => '(join|leave|cancel|request|reject|approve|invite-members|makeadmin|removeadmin)',
            'group_id' => '\d+',
            'member_id' => '\d+',
        ),
    ),
    'sitegroupmember_general' => array(
        'route' => 'advancedgroups/members/:action/:group_id/*',
        'defaults' => array(
            'module' => 'sitegroupmember',
            'controller' => 'index',
            'action' => 'browse',
        ),
        'reqs' => array(
            'group_id' => '\d+',
            'action' => '(browse|search-form|remove|approve|reject|getmembers|compose|getusers)',
        )
    ),
);

$routes['sitegroupoffer'] = array(
    'sitegroupoffer_general' => array(
        'route' => 'advancedgroups/offers/:group_id/*',
        'defaults' => array(
            'module' => 'sitegroupoffer',
            'controller' => 'index',
            'action' => 'index',
        ),
        'reqs' => array(
            'group_id' => '\d+',
            'action' => '(index)',
        )
    ),
    'sitegroupoffer_specific' => array(
        'route' => 'advancedgroups/offer/:action/:group_id/:offer_id/*',
        'defaults' => array(
            'module' => 'sitegroupoffer',
            'controller' => 'index',
            'action' => 'view',
        ),
        'reqs' => array(
            'group_id' => '\d+',
            'offer_id' => '\d+',
            'action' => '(view|getoffer)',
        ),
    ),
);
$routes['sitegroupintegration'] = array(
    'sitegroupintegration_general' => array(
        'route' => 'advancedgrouplistings/:group_id/*',
        'defaults' => array(
            'module' => 'sitegroupintegration',
            'controller' => 'index',
            'action' => 'index',
        ),
        'reqs' => array(
            'group_id' => '\d+',
            'action' => '(index)',
        )
    ),
);


// Sitehastag browse page
$routes['sitehashtag'] = array(
    'sitehashtag' => array(
        'route' => 'sitehashtag/:action/*',
        'defaults' => array(
            'module' => 'sitehashtag',
            'controller' => 'index',
            'action' => 'browse',
        ),
        'reqs' => array(
            'action' => '(browse|trends)'
        ),
    ),
);

// Sitereaction browse page
$routes['sitereaction'] = array(
    'sitereaction' => array(
        'route' => 'reactions/:action/*',
        'defaults' => array(
            'module' => 'sitereaction',
            'controller' => 'index',
            'action' => 'reactions',
        ),
        'reqs' => array(
            'action' => '(reactions|stickers|content-reaction)'
        ),
    ),
    'sitereaction_sticker' => array(
        'route' => 'reactions/store/:action/*',
        'defaults' => array(
            'module' => 'sitereaction',
            'controller' => 'store',
            'action' => 'list',
        ),
        'reqs' => array(
            'action' => '(list|add|remove)'
        ),
    ),
);

$routes['sitecontentcoverphoto'] = array(
    'sitecontentcoverphoto_general' => array(
        'route' => 'coverphoto/:action/*',
        'defaults' => array(
            'module' => 'sitecontentcoverphoto',
            'controller' => 'index',
            'action' => 'upload-cover-photo',
        ),
        'reqs' => array(
            'action' => '(upload-cover-photo|remove-cover-photo|get-cover-photo-menu)'
        ),
    ),
);
$routes['sitevideo'] = array(
    'sitevideo_general' => array(
        'route' => 'advancedvideos/:action/*',
        'defaults' => array(
            'module' => 'sitevideo',
            'controller' => 'index',
            'action' => 'view',
        ),
        'reqs' => array(
            'action' => '(index|browse|manage|create|search-form|categories|validation)',
        ),
    ),
    'sitevideo_api_specific' => array(
        'route' => 'advancedvideo/:action/:video_id/*',
        'defaults' => array(
            'module' => 'sitevideo',
            'controller' => 'index',
            'action' => 'view',
        ),
        'reqs' => array(
            'video_id' => '\d+',
            'action' => '(view|edit|delete|rate|add-to-playlist|watch-later|favourite-video|information|password-protection|subscription)',
        ),
    ),
    'sitevideo_channel_general' => array(
        'route' => 'advancedvideos/channel/:action/*',
        'defaults' => array(
            'module' => 'sitevideo',
            'controller' => 'channel',
            'action' => 'index',
        ),
        'reqs' => array(
            'action' => '(index|browse|manage|create|search-form|categories|channelurl-validation)',
        ),
    ),
    'sitevideo_extended' => array(
        'route' => 'videos/:user_id/:video_id/:slug/*',
        'defaults' => array(
            'module' => 'sitevideo',
            'controller' => 'video',
            'action' => 'view',
            'slug' => '',
        ),
        'reqs' => array(
            'user_id' => '\d+'
        )
    ),
    'sitevideo_channel_api_specific' => array(
        'route' => 'advancedvideo/channel/:action/:channel_id/*',
        'defaults' => array(
            'module' => 'sitevideo',
            'controller' => 'channel',
            'action' => 'view',
        ),
        'reqs' => array(
            'channel_id' => '\d+',
            'action' => '(view|edit|delete|rate|subscribe-channel|favourite-channel|videos|subscribers|description|photo|channel-subscribe|information)',
        ),
    ),
    'sitevideo_playlist_general' => array(
        'route' => 'advancedvideos/playlist/:action/*',
        'defaults' => array(
            'module' => 'sitevideo',
            'controller' => 'playlist',
            'action' => 'index',
        ),
        'reqs' => array(
            'action' => '(browse|manage|create|search-form)',
        ),
    ),
    'sitevideo_playlist_api_specific' => array(
        'route' => 'advancedvideo/playlist/:action/:playlist_id/*',
        'defaults' => array(
            'module' => 'sitevideo',
            'controller' => 'playlist',
            'action' => 'index',
        ),
        'reqs' => array(
            'action' => '(view|edit|delete|remove-from-playlist)',
        ),
    ),
    'sitevideo_entry_view' => array(
        'route' => 'channel/:channel_url/*',
        'defaults' => array(
            'module' => 'sitevideo',
            'controller' => 'channel',
            'action' => 'view',
        ),
    ),
    'sitevideo_playlist_view' => array(
        'route' => 'videos/playlist/:playlist_id/:slug/*',
        'defaults' => array(
            'module' => 'sitevideo',
            'controller' => 'playlist',
            'action' => 'view',
            'slug' => ''
        ),
        'reqs' => array(
            'action' => '(view)',
        ),
    ),
);
// Sitereaction browse page
$routes['nestedcomment'] = array(
    'friends_tag_suggest' => array(
        'route' => 'advancedcomments/friends/*',
        'defaults' => array(
            'module' => 'nestedcomment',
            'controller' => 'friends',
            'action' => 'suggest-tag',
        ),
    ),
    'nestedcomment' => array(
        'route' => 'advancedcomments/:action/*',
        'defaults' => array(
            'module' => 'nestedcomment',
            'controller' => 'index',
            'action' => 'like',
        ),
        'reqs' => array(
            'action' => '(add-comment-notifications|comment|send-like-notitfication|like|likes-comments)'
        ),
    ),
);

$routes['sitemember'] = array(
    'sitemember_general' => array(
        'route' => 'advancedmember/:action/*',
        'defaults' => array(
            'module' => 'sitemember',
            'controller' => 'index',
            'action' => 'followers',
        ),
        'reqs' => array(
            'action' => '(followers|following|follow)'
        ),
    ),
    'sitemember_location' => array(
        'route' => 'memberlocation/:action/*',
        'defaults' => array(
            'module' => 'sitemember',
            'controller' => 'location',
            'action' => 'edit-location',
        ),
        'reqs' => array(
            'action' => '(edit-location|edit-address)'
        ),
    )
);


return $routes;
?>
