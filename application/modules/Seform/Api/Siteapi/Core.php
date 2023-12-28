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
class Classified_Api_Siteapi_Core extends Core_Api_Abstract {

    /**
     * Return the "Browse Search" form. 
     * 
     * @return array
     */
    public function getBrowseSearchForm($subject = null) {
        $searchForm = array();
        $viewer = Engine_Api::_()->user()->getViewer();
        $searchForm[] = array(
            'type' => 'Text',
            'name' => 'search',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search Classifieds')
        );

        $searchForm[] = array(
            'type' => 'Select',
            'name' => 'orderby',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Browse By'),
            'multiOptions' => array(
                'creation_date' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Recent'),
                'view_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Viewed'),
            )
        );

//    $searchForm[] = array(
//        'type' => 'Select',
//        'name' => 'show',
//        'label' => 'Show',
//        'multiOptions' => array(
//            '1' => 'Everyone\'s Posts',
//            '2' => 'Only My Friends\' Posts',
//        )
//    );

        $searchForm[] = array(
            'type' => 'Select',
            'name' => 'closed',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Status'),
            'multiOptions' => array(
                '' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Listings'),
                '0' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Only Open Listings'),
                '1' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Only Closed Listings'),
            )
        );

        $getCategoryArray = array(0 => 'All Categories');
        $categories = Engine_Api::_()->getDbtable('categories', 'classified')->getCategoriesAssoc();

        if (count($categories) > 0) {
            foreach ($categories as $key => $value)
                $getCategoryArray[$key] = $value;
        }

        $searchForm[] = array(
            'type' => 'Select',
            'name' => 'category',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Category'),
            'multiOptions' => $getCategoryArray,
        );

        if (!empty($subject)) {
            $getContentProfileFields = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getContentProfileFields('classified');
            $searchForm = array_merge($searchForm, $getContentProfileFields);
        }

        $searchForm[] = array(
            'type' => 'Checkbox',
            'name' => 'has_photo',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Only Classifieds With Photos')
        );

        $searchForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search')
        );

        return $searchForm;
    }

    /**
     * Get the "Classified Create" form.
     * 
     * @param object $subject get subject only in case of edit.
     * @return array
     */
    public function getForm($subject=null) {
        $accountForm = array();
        $viewer = Engine_Api::_()->user()->getViewer();

        $accountForm[] = array(
            'type' => 'Text',
            'name' => 'title',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Listing Title'),
            'hasValidator' => true
        );

        $accountForm[] = array(
            'type' => 'Text',
            'name' => 'tags',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Tags (Keywords)')
        );

        $categories = Engine_Api::_()->getDbtable('categories', 'classified')->getCategoriesAssoc();
        foreach ($categories as $key => $value)
            $getCategoryArray[$key] = $value;

        if (count($getCategoryArray) > 0) {
            $accountForm[] = array(
                'type' => 'Select',
                'name' => 'category_id',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Category'),
                'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($getCategoryArray),
                'hasValidator' => true
            );
        }

        $accountForm[] = array(
            'type' => 'Textarea',
            'name' => 'body',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Description'),
            'hasValidator' => true
        );

        if (empty($subject)) {
            $accountForm[] = array(
                'type' => 'File',
                'name' => 'photo',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Main Photo')
            );
        }

        // Get profile fields form
        $accountForm = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getContentProfileFields("classified", $accountForm);

        $availableLabels = array(
            'everyone' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone'),
            'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
            'owner_network' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends and Networks'),
            'owner_member_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends of Friends'),
            'owner_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends Only'),
            'owner' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Just Me')
        );
        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('classified', $viewer, 'auth_view');
        $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
        if (!empty($viewOptions) && count($viewOptions) >= 1) {
            // Make a hidden field
            if (count($viewOptions) != 1) {
                $accountForm[] = array(
                    'type' => 'Select',
                    'name' => 'auth_view',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Privacy'),
                    'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who may see this classified listing?'),
                    'multiOptions' => $viewOptions,
                    'value' => key($viewOptions),
                    'hasValidator' => true
                );
            }
        }

        // Element: auth_comment
        $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('classified', $viewer, 'auth_comment');
        $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

        if (!empty($commentOptions) && count($commentOptions) >= 1) {
            // Make a hidden field
            if (count($commentOptions) != 1) {
                $accountForm[] = array(
                    'type' => 'Select',
                    'name' => 'auth_comment',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Comment Privacy'),
                    'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who may post comments on this classified listing?'),
                    'multiOptions' => $commentOptions,
                    'value' => key($commentOptions),
                    'hasValidator' => true
                );
            }
        }

        $accountForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Post Listing')
        );

        return $accountForm;
    }

    /**
     * Set the classified uploadded image.
     *
     * @return object
     */
    public function setPhoto($photo, $subject, $setRow = true) {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
        } else {
            throw new Classified_Model_Exception('invalid argument passed to setPhoto');
        }
        $imageName = $photo['name'];
        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => 'classified',
            'parent_id' => $subject->getIdentity(),
            'user_id' => $subject->owner_id,
            'name' => $name
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


        // Add to album
        $viewer = Engine_Api::_()->user()->getViewer();
        $photoTable = Engine_Api::_()->getItemTable('classified_photo');
        $classifiedAlbum = $subject->getSingletonAlbum();
        $photoItem = $photoTable->createRow();
        $photoItem->setFromArray(array(
            'classified_id' => $subject->getIdentity(),
            'album_id' => $classifiedAlbum->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'file_id' => $iMain->getIdentity(),
            'collection_id' => $classifiedAlbum->getIdentity(),
        ));
        $photoItem->save();

        // Update row
        if (!empty($setRow)) {
            $subject->modified_date = date('Y-m-d H:i:s');
            $subject->photo_id = $photoItem->file_id;
            $subject->save();
        }

        return $photoItem;
    }

}
