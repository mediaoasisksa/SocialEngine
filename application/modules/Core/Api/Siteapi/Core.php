<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Core.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Core_Api_Siteapi_Core extends Core_Api_Abstract {

    protected $_noPhotos;

    public function getEditForm($subject) {

        $fieldsArray = array();

        $fieldsArray[] = array(
            'type' => 'text',
            'name' => 'title',
            'label' => $this->translate('Title of the video'),
        );

        $fieldsArray = array(
            'type' => 'text',
            'name' => 'tags',
            'label' => $this->translate('Tags (Keywords)'),
            'description' => $this->translate('Separate tags with commas.'),
        );

        $fieldsArray[] = array(
            'type' => 'textarea',
            'name' => 'description',
            'label' => $this->translate('Description of the video'),
        );

        $fieldsArray[] = array(
            'type' => 'checkbox',
            'name' => 'search',
            'label' => $this->translate('Show this video in search results.'),
            'value' => 1,
        );

        $fieldsArray[] = array(
            'type' => 'button',
            'name' => 'cancel',
            'label' => $this->translate("Canel"),
            'description' => $this->translate("Cancels the video edition"),
        );

        $fieldsArray[] = array(
            'type' => 'submit',
            'name' => 'submit',
            'label' => $this->translate("Submit"),
            'description' => $this->translate("Submits the form"),
        );

        return $fieldsArray;
    }

    /*
     * Contact Form
     */

    public function getContactForm() {
        $contactForm = array();

        $contactForm[] = array(
            'type' => 'Text',
            'name' => 'name',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Name'),
            'hasValidator' => true
        );

        $contactForm[] = array(
            'type' => 'Text',
            'name' => 'email',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Email Address'),
            'hasValidator' => true
        );

        $contactForm[] = array(
            'type' => 'Textarea',
            'name' => 'body',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Message'),
            'hasValidator' => true
        );

        $contactForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Send Message')
        );

        return $contactForm;
    }

    /*
     * Report Form
     */

    public function getReportForm() {
        $searchForm = array();
        $viewer = Engine_Api::_()->user()->getViewer();

        $reportCategories = array(
            'spam' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Spam'),
            'abuse' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Abuse'),
            'inappropriate' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Inappropriate Content'),
            'licensed' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Licensed Material'),
            'other' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Other'),
        );

        $searchForm[] = array(
            'type' => 'Select',
            'name' => 'category',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Type'),
            'multiOptions' => $reportCategories,
            'value' => 'spam',
            'hasValidator' => true
        );

        $searchForm[] = array(
            'type' => 'Textarea',
            'name' => 'description',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Description'),
            'hasValidator' => true
        );

        $searchForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Submit Report')
        );

        return $searchForm;
    }

    /*
     * Get no photo urls
     */

    public function getNoPhoto($item, $type) {
        $type = ( $type ? str_replace('.', '_', $type) : 'main' );

        if (($item instanceof Core_Model_Item_Abstract)) {
            $item = $item->getType();
        } else if (!is_string($item)) {
            return '';
        }

        if (!Engine_Api::_()->hasItemType($item)) {
            return '';
        }

        // Load from registry
        if (null === $this->_noPhotos) {
            // Process active themes
//      $themesInfo = Zend_Registry::get('Themes');
//      foreach( $themesInfo as $themeName => $themeInfo ) {
//        if( !empty($themeInfo['nophoto']) ) {
//          foreach( (array)@$themeInfo['nophoto'] as $itemType => $moreInfo ) {
//            if( !is_array($moreInfo) ) continue;
//            $this->_noPhotos[$itemType] = array_merge((array)@$this->_noPhotos[$itemType], $moreInfo);
//          }
//        }
//      }
//    }
//    echo '42543';die;    
        }
        // Use default    
        $getHosts = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
        if (!isset($this->_noPhotos[$item][$type])) {
            $shortType = $item;
            if (strpos($shortType, '_') !== false) {
                list($null, $shortType) = explode('_', $shortType, 2);
            }

            $module = Engine_Api::_()->inflect(Engine_Api::_()->getItemModule($item));
            $this->_noPhotos[$item][$type] = //$this->view->baseUrl() . '/' .
                    'application/modules/' .
                    $module .
                    '/externals/images/nophoto_' .
                    $shortType . '_'
                    . $type . '.png';

//        $this->view->layout()->staticBaseUrl . 'application/modules/' .
//        $module .
//        '/externals/images/nophoto_' .
//        $shortType . '_'
//        . $type . '.png';
        }

        return $this->_noPhotos[$item][$type];
    }

    public function checkConditionsForAlbum($moduleName) {

        //CHECK CONDITIONS FOR IN WHICH ALBUMS ARE GENERETED
        $checkConditionsForAlbum = 0;
        $moduleArray = array('sitepage', 'sitebusiness', 'sitegroup', 'sitestore');
        if (in_array($moduleName, $moduleArray)) {
            $checkConditionsForAlbum = 1;
        }

        return $checkConditionsForAlbum;
    }

    public function getProfilePhotoMenu($subject, $profilePhoto, $type) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $can_edit = 0;
        $moduleName = strtolower($subject->getModuleName());
        //START MANAGE-ADMIN CHECK
        if ($this->checkConditionsForAlbum($moduleName)) {
            $can_edit = $isManageAdmin = Engine_Api::_()->$moduleName()->isManageAdmin($subject, 'edit');
            if (empty($isManageAdmin))
                $can_edit = 0;
        } else {
            if ($moduleName == 'sitereview') {
                $can_edit = $subject->authorization()->isAllowed($viewer, "edit_listtype_$subject->listingtype_id");
            } elseif ($moduleName == 'sitecrowdfunding') {
                $can_edit = Engine_Api::_()->sitecrowdfunding()->isEditPrivacy($subject->parent_type, $subject->parent_id, $subject);
            } else {
                $can_edit = $subject->authorization()->isAllowed($viewer, 'edit');
            }
        }


        if (($subject->getType() === 'sitepage_page') && (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum') || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.isActivate', 1))) {
            $can_edit = 0;
        } else if (($subject->getType() === 'sitebusiness_business') && (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessalbum') || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebusinessalbum.isActivate', 1))) {
            $can_edit = 0;
        }

        if ($can_edit) {
            if ($type == 'profile' || $type == 'both') {
                $coverMenu['profilePhotoMenu'][] = array(
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Upload Profile Photo'),
                    'name' => 'upload_photo',
                    'url' => 'upload-profile-photo',
                    'urlParams' => array(
                        'subject_type' => $subject->getType(),
                        'subject_id' => $subject->getIdentity(),
                        'special' => 'profile',
                        "actionType" => "manage",
                        "successMessage" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Photo uploaded successfuly.")
                    )
                );

                $coverMenu['profilePhotoMenu'][] = array(
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Choose from Albums'),
                    'name' => 'choose_from_album',
                    "actionType" => "manage",
                        "successMessage" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Photo uploaded successfuly.")
                );

                if (isset($profilePhoto) && !empty($profilePhoto)) {
                    $coverMenu['profilePhotoMenu'][] = array(
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('View Profile Photo'),
                        'name' => 'view_profile_photo',
                        "actionType" => "manage",
                    );
                 
                    $subjectPhotoId = ($subject->getType() == 'sitevideo_channel') ? $subject->file_id : $subject->photo_id ;
                    if($subjectPhotoId)
                    $coverMenu['profilePhotoMenu'][] = array(
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Remove Profile Photo'),
                        'name' => 'remove_photo',
                        'url' => 'remove-profile-photo',
                         "actionType" => "processDialog",
                        "successMessage" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Photo removed successfuly."),
                        'urlParams' => array(
                            'subject_type' => $subject->getType(),
                            'subject_id' => $subject->getIdentity(),
                            'special' => 'profile',
                        )
                    );
                }
            }
        return $coverMenu;
        }
        return ;
    }

    /*
     * Get mainphoto menus
     * 
     * @param object user
     * @return array
     */

    public function getMainPhotoMenu($user) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $canEdit = $user->authorization()->isAllowed($viewer, 'edit');
        if (empty($canEdit))
            return;

        if ($viewer->getIdentity() != $user->getIdentity())
            return;

        $mainPhotoMenu[] = array(
            'label' => $this->_translate('Upload Photo'),
            'name' => 'upload_photo',
            'url' => 'user/profilepage/upload-cover-photo/user_id/' . $user->getIdentity() . '/special/profile',
            'urlParams' => array(
            )
        );

        $mainPhotoMenu[] = array(
            'label' => $this->_translate('Choose from Albums'),
            'name' => 'choose_from_album',
            'urlParams' => array(
            )
        );

        if (isset($user->photo_id) && !empty($user->photo_id)) {
            $host = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
            $getPhotoURL = $user->getPhotoUrl();
            $finalPhotoURL = (strstr($getPhotoURL, 'http')) ? $getPhotoURL : $host . $getPhotoURL;
            $tempInfo = array(
                'label' => $this->_translate('View Profile Photo'),
                'name' => 'view_profile_photo',
                'url' => $finalPhotoURL,
                'urlParams' => array(
                )
            );

            $mainPhotoMenu[] = $tempInfo;

            $mainPhotoMenu[] = array(
                'label' => $this->_translate('Remove'),
                'name' => 'remove_photo',
                'url' => 'user/profilepage/remove-cover-photo/user_id/' . $user->getIdentity() . '/special/profile',
                'urlParams' => array(
                )
            );
        }

        return $mainPhotoMenu;
    }

}
