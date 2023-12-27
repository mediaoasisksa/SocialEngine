<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2011-09-26 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$sitememberEnabled = Engine_Api::_()->hasModuleBootstrap('sitemember');
$lightboxEnabled = Engine_Api::_()->hasModuleBootstrap('sitecore') || Engine_Api::_()->hasModuleBootstrap('advancedactivity') || Engine_Api::_()->hasModuleBootstrap('sitealbum');
$temp_show_in_mini_options = array(
    0 => 'None',
    1 => 'Global Search',
);

$detactLocationElement = array(
    'Select',
    'detactLocation',
    array(
        'label' => 'Do you want to detect user’s current location?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => '0'
    )
);

$content_array = array(
//    array(
//        'title' => $view->translate('SocialEngineAddOns Comments'),
//        'description' => $view->translate('Shows the comments about an item.'),
//        'category' => 'SocialEngineAddOns-Core',
//        'type' => 'widget',
//        'name' => 'seaocore.seaocores-comments',
//        'defaultParams' => array(
//            'title' => $view->translate('Comments')
//        ),
//        'requirements' => array(
//            'subject',
//        ),
//    ),
// 		array(
// 			'title' => $view->translate('Nested Comments'),
// 			'description' => $view->translate('Shows the nested comment about an item.'),
// 			'category' => 'SocialEngineAddOns-Core',
// 			'type' => 'widget',
// 			'name' => 'seaocore.seaocores-nestedcomments',
// 			'defaultParams' => array(
// 				'title' => 'Nested Comments'
// 			),
// 			'requirements' => array(
// 				'subject',
// 			),
// 		),
    array(
        'title' => $view->translate('Scroll To Top'),
        'description' => $view->translate('This widget displays a "Scroll To Top" button when users scroll down to the bottom of the page. This widget should be placed at the height of your page where you want the user to be scrolled-to upon clicking.'),
        'category' => 'SocialEngineAddOns-Core',
        'type' => 'widget',
        'name' => 'seaocore.scroll-top',
        'adminForm' => array(
            'elements' => array(
                array(
                    'hidden',
                    'title',
                    array(
                        'label' => '',
                        'order' => 1005
                    )
                ),
                array(
                    'Text',
                    'mouseOverText',
                    array(
                        'label' => $view->translate('Enter the HTML title that you want to display when users mouse-over on "Scroll to Top" button.'),
                        'value' => $view->translate('Scroll to Top'),
                    )
                ),
            )
        )
    ),
    array(
        'title' => $view->translate('Column Width'),
        'description' => $view->translate('This widget is used to set the width of the columns on a page. You can enter the width from the Edit Settings of this widget.'),
        'category' => 'SocialEngineAddOns-Core',
        'type' => 'widget',
        'name' => 'seaocore.layout-width',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'hidden',
                    'title',
                    array(
                        'label' => '',
                        'order' => 1006
                    )
                ),
                array(
                    'text',
                    'layoutWidth',
                    array(
                        'label' => $view->translate('Enter the width of this column.'),
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(1)),
                        ),
                    )
                ),
                array(
                    'select',
                    'layoutWidthType',
                    array(
                        'label' => $view->translate('Selete the type of width'),
                        'multiOptions' => array(
                            'px' => 'px',
                            '%' => '%',
                        ),
                        'value' => 'px',
                    )
                ),
            )
        )
    ),
    array(
        'title' => $view->translate('SEAO - Login or SignUp Poupup'),
        'description' => $view->translate('This widget is used to set the width of the columns on a page. You can enter the width from the Edit Settings of this widget.'),
        'category' => 'SocialEngineAddOns-Core',
        'type' => 'widget',
        'name' => 'seaocore.login-or-signup-popup',
    ),

    array(
        'title' => 'SEAO - Mini Menu',
        'description' => 'Shows the mini menu. You can edit its contents in your menu editor.',
        'category' => 'SocialEngineAddOns-Core',
        'type' => 'widget',
        'name' => 'seaocore.menu-mini',
        'autoEdit' => true,
        'requirements' => array(
            'header-footer',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'seaocore_on_logged_out',
                    array(
                        'label' => 'Do you want to show this widget to non-logged-in users?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1
                )),
                array(
                    'Radio',
                    'seaocore_show_icon',
                    array(
                        'label' => 'Select the design type you want to show for your mini menu.',
                        'description' => '(Note: Not all menus support icons. Also, you will be able to configure the icons only for the menus you add from the Menu Editor.)',
                        'multiOptions' => array(
                            1 => 'Icon Type',
                            0 => 'Label Type'
                        ),
                        'value' => 1
                )),
                array(
                    'Text',
                    'no_of_updates',
                    array(
                        'label' => 'Count',
                        'description'=> 'No. of Friend Requests, Messages and Notifications to show.',
                        'value' => 10
                    )
                ),
                array(
                    'Radio',
                    'seaocore_show_in_mini_options',
                    array(
                        'label' => 'Select the searching type you want to show.',
                        'multiOptions' => $temp_show_in_mini_options,
                        'value' => 1
                    )
                ),
                array(
                    'Text',
                    'search_position',
                    array(
                        'label' => 'Enter position of the above menu to be placed in this widget.',
                        'description' => 'Enter 1 for the first position and 999 for last position.',
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'changeMyLocation',
                    array(
                        'label' => "Do you want to enable 'Location' field, using which users can set their default location.",
                        'multiOptions' => array(1 => "Yes", 0 => "No"),
                        'value' => 0
                    )
                ),
                array(
                    'Radio',
                    'showLocationBasedContent',
                    array(
                        'label' => 'Show results based on the location, saved in user’s browser cookie.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0
                    )
                ),                                   
                array(
                    'Text',
                    'seaocore_mini_search_width',
                    array(
                        'label' => 'Enter width for searchbox.',
                        'value' => 275,
                    )
                ),
                array(
                    'Text',
                    'seaocore_location_box_width',
                    array(
                        'label' => 'Enter width for locationbox.',
                        'value' => 275,
                    )
                ),
                $element_show_suggestion,
                array(
                    'Radio',
                    'seaocore_enable_login_lightbox',
                    array(
                        'label' => 'Enabled lightbox for Sign In?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'Value' => 1
                    )
                ),
                array(
                    'Radio',
                    'seaocore_enable_signup_lightbox',
                    array(
                        'label' => 'Enabled lightbox for Sign Up?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'Value' => 1
                    )
                ),
            )
          )
        ),
    array(
        'title' => $view->translate('Social Share Buttons'),
        'description' => $view->translate('This widget is used to add social share buttons.'),
        'category' => 'SocialEngineAddOns-Core',
        'type' => 'widget',
        'name' => 'seaocore.social-share-buttons',
        'autoEdit' => true,
        'defaultParams' => array(
            'show_buttons' => array('facebook', 'twitter', 'linkedin', 'plusgoogle', 'share')
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'show_buttons',
                    array(
                        'label' => $view->translate('Choose the action links that you want to be available in this block.'),
                        'multiOptions' => array('facebook' => 'Facebook (Dependency on "Advanced Facebook Integration / Likes, Social Plugins and Open Graph" Plugin)', 'twitter' => 'Twitter', 'linkedin' => 'LinkedIn', 'plusgoogle' => 'Google+', 'share' => 'Share'),
                    )
                ),
                array(
                    'hidden',
                    'title',
                    array(
                        'label' => '',
                        'order' => 1007
                    )
                ),
//                array(
//                    'select',
//                    'position',
//                    array(
//                        'label' => $view->translate('Selete the postion of this widget.'),
//                    'multiOptions' => array(
//                                  'left' => 'Left Side',
//                                  'right' => 'Right Side',
//                              ),
//                     'value' => 'left',
//                    )
//                ),
            )
        )
    ),
     array(
        'title' => $view->translate('Favourite Button'),
        'description' => $view->translate('This widget is used to add favourite button.'),
        'category' => 'SocialEngineAddOns-Core',
        'type' => 'widget',
        'name' => 'seaocore.favourite-button',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'hidden',
                    'title',
                    array(
                        'label' => '',
                        'order' => 1008
                    )
                ),
            )
        )
    ),
 
);

if ($lightboxEnabled) {
  $content_array[] = array(
        'title' => $view->translate('Photos Lightbox Viewer'),
        'description' => $view->translate('This widget displays the lightbox on your site. It is recommended to not to remove this widget from the header.'),
        'category' => 'SocialEngineAddOns-Core',
        'type' => 'widget',
        'name' => 'seaocore.seaocores-lightbox',
        'adminForm' => array(
            'elements' => array(
                array(
                    'hidden',
                    'title',
                    array(
                        'label' => '',
                        'order' => 1001
                    )
                ),
                array(
                    'hidden',
                    'nomobile',
                    array(
                        'label' => '',
                        'order' => 1002
                    )
                ),
                array(
                    'hidden',
                    'execute',
                    array(
                        'label' => '',
                        'order' => 1003
                    )
                ),
                array(
                    'hidden',
                    'cancel',
                    array(
                        'label' => '',
                        'order' => 1004
                    )
                ),
            )
        ),
    );
}
if (Engine_Api::_()->seaocore()->getLocationsTabs() && $sitememberEnabled) {
    $content_array[] = array(
        'title' => "Change User's Location",
        'description' => "Displays user's location with 'change my location' link to change current location. Setting to choose how user's can change their location is available in the edit section of this widget.",
        'category' => 'SocialEngineAddOns-Core',
        'type' => 'widget',
        'name' => 'seaocore.change-my-location',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                $detactLocationElement,
                array(
                    'Radio',
                    'updateUserLocation',
                    array(
                        'label' => "Do you want to update 'User Location' field with the location selected in this widget ? (If set to yes, user's actual location displayed in 'Info' tab on profile page will be updated each time a new location is selected in this widget.)",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'showLocationPrivacy',
                    array(
                        'label' => 'Do you want to show the privacy dropdown field to users when they try to update their location using this widget ? (If set to yes, users will be able to select who will be able to see their location in "Info" tab of their profile.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
//                array(
//                    'Radio',
//                    'showSeperateLink',
//                    array(
//                        'label' => 'How do you want users to change their location? [Note: This setting will not work if you have enabled "Specific Locations" setting.]',
//                        'multiOptions' => array(
//                            1 => 'By using "change my location" link.',
//                            0 => 'By clicking on existing location name.'
//                        ),
//                        'value' => 0,
//                    )
//                )
            ),
        ),
    );
} elseif (Engine_Api::_()->seaocore()->getLocationsTabs()) {
    $content_array[] = array(
        'title' => "Change User's Location",
        'description' => "Displays user's location with 'change my location' link to change current location. Setting to choose how user's can change their location is available in the edit section of this widget.",
        'category' => 'SocialEngineAddOns-Core',
        'type' => 'widget',
        'name' => 'seaocore.change-my-location',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                $detactLocationElement,
                array(
                    'Radio',
                    'showSeperateLink',
                    array(
                        'label' => 'How do you want users to change their location? [Note: This setting will not work if you have enabled "Specific Locations" setting.]',
                        'multiOptions' => array(
                            1 => 'By using "change my location" link.',
                            0 => 'By clicking on existing location name.'
                        ),
                        'value' => 0,
                    )
                )
            ),
        ),
    );
}

return $content_array;
?>