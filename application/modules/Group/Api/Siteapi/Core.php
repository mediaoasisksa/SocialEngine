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
class Group_Api_Siteapi_Core extends Core_Api_Abstract {
    /**
     * Return the "Browse Search" form. 
     * 
     * @return array
     */
    public function getBrowseSearchForm() {
        $searchForm = array();
        $viewer = Engine_Api::_()->user()->getViewer();
        $searchForm[] = array(
            'type' => 'Text',
            'name' => 'search_text',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search Groups')
        );

        $categories = Engine_Api::_()->getDbtable('categories', 'group')->getCategoriesAssoc();
        asort($categories, SORT_LOCALE_STRING);
        $categoryOptions = array('' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Categories'));
        foreach ($categories as $k => $v) {
            $categoryOptions[$k] = $v;
        }

        $searchForm[] = array(
            'type' => 'Select',
            'name' => 'category_id',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Category'),
            'multiOptions' => $categoryOptions,
        );

        if (Engine_Api::_()->user()->getViewer()->getIdentity()) {
            $searchForm[] = array(
                'type' => 'Select',
                'name' => 'view',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('View'),
                'multiOptions' => array(
                    '' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone\'s Groups'),
                    '1' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Only My Friends\' Groups'),
                )
            );
        }

        $searchForm[] = array(
            'type' => 'Select',
            'name' => 'order',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('List By'),
            'multiOptions' => array(
                'creation_date DESC' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Recently Created'),
                'member_count DESC' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Popular'),
            )
        );

        $searchForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search')
        );

        return $searchForm;
    }

    /**
     * Return the Create Form. 
     * 
     * @return array
     */
    public function getForm($subject = null) {
        $accountForm = array();
        $viewer = Engine_Api::_()->user()->getViewer();

        $accountForm[] = array(
            'type' => 'Text',
            'name' => 'title',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Group Name'),
            'hasValidator' => true
        );

        $accountForm[] = array(
            'type' => 'Textarea',
            'name' => 'description',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Description'),
            'hasValidator' => true
        );

//        if (empty($subject)) {
        $accountForm[] = array(
            'type' => 'File',
            'name' => 'photo',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Profile Photo')
        );
//        }

        $categories = Engine_Api::_()->getDbtable('categories', 'group')->getCategoriesAssoc();
        if (count($categories) > 0) {
            $accountForm[] = array(
                'type' => 'Select',
                'name' => 'category_id',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Category'),
                'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($categories),
                'hasValidator' => true
            );
        }

        $accountForm[] = array(
            'type' => 'Radio',
            'name' => 'search',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Include in search results?'),
            'multiOptions' => array(
                '1' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Yes, include in search results.'),
                '0' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('No, hide from search results.'),
            ),
            'value' => 1
        );

        $accountForm[] = array(
            'type' => 'Radio',
            'name' => 'auth_invite',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Let members invite others?'),
            'multiOptions' => array(
                'member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Yes, members can invite other people.'),
                'officer' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('No, only officers can invite other people.'),
            ),
            'value' => 'member',
            'hasValidator' => true
        );

        $accountForm[] = array(
            'type' => 'Radio',
            'name' => 'approval',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Approve members?'),
            'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('When people try to join this group, should they be allowed to join immediately, or should they be forced to wait for approval?'),
            'multiOptions' => array(
                '0' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('New members can join immediately.'),
                '1' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('New members must be approved.'),
            ),
            'value' => '0',
        );

        // Privacy
        $availableLabels = array(
            'everyone' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone'),
            'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Registered Members'),
            'member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Group Members'),
            'officer' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Officers and Owner Only'),
                //'owner' => 'Owner Only',
        );

        // View
        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $viewer, 'auth_view');
        $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));

        if (!empty($viewOptions) && count($viewOptions) > 1) {
            // Make a hidden field
            $accountForm[] = array(
                'type' => 'Select',
                'name' => 'auth_view',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('View Privacy'),
                'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who may see this group?'),
                'multiOptions' => $viewOptions,
                'value' => key($viewOptions),
                'hasValidator' => true
            );
        }

        // Comment
        $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $viewer, 'auth_comment');
        $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

        if (!empty($commentOptions) && count($commentOptions) > 1) {
            $accountForm[] = array(
                'type' => 'Select',
                'name' => 'auth_comment',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Comment Privacy'),
                'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who may post on this group\'s wall?'),
                'multiOptions' => $commentOptions,
                'value' => key($commentOptions),
                'hasValidator' => true
            );
        }

        // Photo
        $photoOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $viewer, 'auth_photo');
        $photoOptions = array_intersect_key($availableLabels, array_flip($photoOptions));

        if (!empty($photoOptions) && count($photoOptions) > 1) {
            $accountForm[] = array(
                'type' => 'Select',
                'name' => 'auth_photo',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Photo Uploads'),
                'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who may upload photos to this group?'),
                'multiOptions' => $photoOptions,
                'value' => key($photoOptions),
                'hasValidator' => true
            );
        }

        // Event
        $eventOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $viewer, 'auth_event');
        $eventOptions = array_intersect_key($availableLabels, array_flip($eventOptions));

        if (!empty($eventOptions) && count($eventOptions) > 1) {
            $accountForm[] = array(
                'type' => 'Select',
                'name' => 'auth_event',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Event Creation'),
                'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who may create events for this group?'),
                'multiOptions' => $eventOptions,
                'value' => key($eventOptions),
                'hasValidator' => true
            );
        }

        if (!empty($subject)) {
            $accountForm[] = array(
                'type' => 'Submit',
                'name' => 'submit',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Edit Group')
            );
        } else {
            $accountForm[] = array(
                'type' => 'Submit',
                'name' => 'submit',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Create Group')
            );
        }

        return $accountForm;
    }

    /**
     * Return the "Photo Edit" form. 
     * 
     * @return array
     */
    public function getPhotoEditForm($form = array()) {
        $form[] = array(
            'type' => 'Text',
            'name' => 'title',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Title'),
            'hasValidator' => true
        );

        $form[] = array(
            'type' => 'Textarea',
            'name' => 'description',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Description'),
            'hasValidator' => true
        );

        $form[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Edit Photo')
        );

        return $form;
    }

    /**
     * Set the group uploadded image.
     *
     * @return object
     */
    public function setPhoto($photo, $subject, $needToUplode = false) {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
        } else {
            throw new Group_Model_Exception('invalid argument passed to setPhoto');
        }
        $imageName = $photo['name'];
        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => 'group',
            'parent_id' => $subject->getIdentity()
        );

        // Save
        $storage = Engine_Api::_()->storage();

        // Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(720, 720)
                ->write($path . '/m_' . $imageName)
                ->destroy();

        // Resize image (profile)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(200, 400)
                ->write($path . '/p_' . $imageName)
                ->destroy();

        // Resize image (normal)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(140, 160)
                ->write($path . '/in_' . $imageName)
                ->destroy();

        // Resize image (icon)
        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
                ->write($path . '/is_' . $imageName)
                ->destroy();

        // Store
        $iMain = $storage->create($path . '/m_' . $imageName, $params);
        $iProfile = $storage->create($path . '/p_' . $imageName, $params);
        $iIconNormal = $storage->create($path . '/in_' . $imageName, $params);
        $iSquare = $storage->create($path . '/is_' . $imageName, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        $iMain->bridge($iSquare, 'thumb.icon');

        // Remove temp files
        @unlink($path . '/p_' . $imageName);
        @unlink($path . '/m_' . $imageName);
        @unlink($path . '/in_' . $imageName);
        @unlink($path . '/is_' . $imageName);

        // Update row
        if (empty($needToUplode)) {
            $subject->modified_date = date('Y-m-d H:i:s');
            $subject->photo_id = $iMain->file_id;
            $subject->save();
        }

        // Add to album
        $viewer = Engine_Api::_()->user()->getViewer();
        $photoTable = Engine_Api::_()->getItemTable('group_photo');
        $groupAlbum = $subject->getSingletonAlbum();
        $photoItem = $photoTable->createRow();
        $photoItem->setFromArray(array(
            'group_id' => $subject->getIdentity(),
            'album_id' => $groupAlbum->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'file_id' => $iMain->getIdentity(),
            'collection_id' => $groupAlbum->getIdentity(),
        ));
        $photoItem->save();

        if (!empty($needToUplode)) {
            return $photoItem;
        }

        return $subject;
    }

}
