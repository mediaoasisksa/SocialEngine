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
class Event_Api_Siteapi_Core extends Core_Api_Abstract {

    /**
     * Get event advanced search form.
     * 
     * @return array
     */
    public function getBrowseSearchForm() {
        $searchForm = array();
        $viewer = Engine_Api::_()->user()->getViewer();
        $searchForm[] = array(
            'type' => 'Text',
            'name' => 'search_text',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search Events')
        );

        $getCategoryArray[0] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Categories');
        $categories = Engine_Api::_()->getDbtable('categories', 'event')->getCategoriesAssoc();
        foreach ($categories as $key => $value)
            $getCategoryArray[$key] = $value;

        if (count($categories) > 0) {
            $searchForm[] = array(
                'type' => 'Select',
                'name' => 'category_id',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Category'),
                'multiOptions' => $getCategoryArray
            );
        }

//    $searchForm[] = array(
//        'type' => 'Select',
//        'name' => 'view',
//        'label' => 'View',
//        'multiOptions' => array(
//            '' => 'Everyone\'s Events',
//            '1' => 'Only My Friends\' Events',
//        )
//    );

        $searchForm[] = array(
            'type' => 'Select',
            'name' => 'order',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('List By'),
            'multiOptions' => array(
                'starttime ASC' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Start Time'),
                'creation_date DESC' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Recently Created'),
                'member_count DESC' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Popular'),
            ),
        );

        $searchForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search')
        );

        return $searchForm;
    }

    /**
     * Get event create form
     * 
     * @return array
     */
    public function getForm($subject = null, $parent_type = null, $parent_id = null) {
        $accountForm = array();
        $viewer = Engine_Api::_()->user()->getViewer();

        $accountForm[] = array(
            'type' => 'Text',
            'name' => 'title',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Event Name'),
            'hasValidator' => true
        );

        $accountForm[] = array(
            'type' => 'Textarea',
            'name' => 'description',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Event Description'),
            'hasValidator' => true
        );

        $accountForm[] = array(
            'type' => 'Date',
            'name' => 'starttime',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Start Time'),
            'hasValidator' => true
        );

        $accountForm[] = array(
            'type' => 'Date',
            'name' => 'endtime',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('End Time'),
            'hasValidator' => true
        );

        if ($parent_type == 'user') {
            $accountForm[] = array(
                'type' => 'Text',
                'name' => 'host',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Host')
            );
        }

        $accountForm[] = array(
            'type' => 'Text',
            'name' => 'location',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Location')
        );

        $accountForm[] = array(
            'type' => 'File',
            'name' => 'photo',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Main Photo')
        );

        $categories = Engine_Api::_()->getDbtable('categories', 'event')->getCategoriesAssoc();
        asort($categories, SORT_LOCALE_STRING);
        $categoryOptions = array('0' => '');
        foreach ($categories as $k => $v) {
            $categoryOptions[$k] = $v;
        }

        if (count($categories) > 0) {
            $accountForm[] = array(
                'type' => 'Select',
                'name' => 'category_id',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Event Category'),
                'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($categoryOptions),
                'hasValidator' => true
            );
        }

        $accountForm[] = array(
            'type' => 'Checkbox',
            'name' => 'search',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('People can search for this event'),
            'value' => true
        );

        $accountForm[] = array(
            'type' => 'Checkbox',
            'name' => 'approval',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('People must be invited to RSVP for this event')
        );

        $accountForm[] = array(
            'type' => 'Checkbox',
            'name' => 'auth_invite',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Invited guests can invite other people as well'),
            'value' => true
        );

        // Privacy
        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('event', $viewer, 'auth_view');
        $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('event', $viewer, 'auth_comment');
        $photoOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('event', $viewer, 'auth_photo');

        if ($parent_type == 'user') {
            $availableLabels = array(
                'everyone' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone'),
                'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
                'owner_network' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends and Networks'),
                'owner_member_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends of Friends'),
                'owner_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends Only'),
                'member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Event Guests Only'),
                'owner' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Just Me')
            );
            $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
            $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));
            $photoOptions = array_intersect_key($availableLabels, array_flip($photoOptions));
        } else if ($parent_type == 'group') {
            $availableLabels = array(
                'everyone' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone'),
                'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
                'parent_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Group Members'),
                'member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Event Guests Only'),
                'owner' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Just Me'),
            );
            $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
            $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));
            $photoOptions = array_intersect_key($availableLabels, array_flip($photoOptions));
        }

        // View
        if (!empty($viewOptions) && count($viewOptions) > 1) {
            $accountForm[] = array(
                'type' => 'Select',
                'name' => 'auth_view',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Privacy'),
                'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who may see this event?'),
                'multiOptions' => $viewOptions,
                'value' => key($viewOptions),
                'hasValidator' => true
            );
        }

        // Comment
        if (!empty($commentOptions) && count($commentOptions) > 1) {
            $accountForm[] = array(
                'type' => 'Select',
                'name' => 'auth_comment',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Comment Privacy'),
                'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who may post comments on this event?'),
                'multiOptions' => $commentOptions,
                'value' => key($commentOptions),
                'hasValidator' => true
            );
        }

        // Photo
        if (!empty($photoOptions) && count($photoOptions) > 1) {
            $accountForm[] = array(
                'type' => 'Select',
                'name' => 'auth_photo',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Photo Uploads'),
                'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who may upload photos to this event?'),
                'multiOptions' => $photoOptions,
                'value' => key($photoOptions),
                'hasValidator' => true
            );
        }

        $accountForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Post Entry')
        );

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
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Submit')
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
            'parent_type' => 'event',
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
        $photoTable = Engine_Api::_()->getItemTable('event_photo');
        $eventAlbum = $subject->getSingletonAlbum();
        $photoItem = $photoTable->createRow();
        $photoItem->setFromArray(array(
            'event_id' => $subject->getIdentity(),
            'album_id' => $eventAlbum->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'file_id' => $iMain->getIdentity(),
            'collection_id' => $eventAlbum->getIdentity()
        ));
        $photoItem->save();

        return $subject;
    }

}
