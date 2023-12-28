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
class Album_Api_Siteapi_Core extends Core_Api_Abstract {
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
            'name' => 'search',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search Albums')
        );

        $searchForm[] = array(
            'type' => 'Select',
            'name' => 'sort',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Browse By'),
            'multiOptions' => array(
                'recent' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Recent'),
                'popular' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Viewed'),
            )
        );

        $categories = Engine_Api::_()->getDbtable('categories', 'album')->getCategoriesAssoc();
        if (count($categories) > 0) {
            $searchForm[] = array(
                'type' => 'Select',
                'name' => 'category_id',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Category'),
                'multiOptions' => $categories
            );
        }

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
        $viewer = Engine_Api::_()->user()->getViewer();
          if (empty($subject) && empty($_REQUEST['create_new_album'])) {
            $albumTable = Engine_Api::_()->getItemTable('album');
            $myAlbums = $albumTable->select()
                    ->from($albumTable, array('album_id', 'title'))
                    ->where('owner_type = ?', 'user')
                    ->where('owner_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity())
                    ->query()
                    ->fetchAll();

            $albumOptions = array('0' => 'Create A New Album');
            foreach ($myAlbums as $myAlbum) {
                $albumOptions[$myAlbum['album_id']] = $myAlbum['title'];
            }
            $flag = 1;
            if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.photo.specialalbum', 1))
                $flag = 1;
            if($flag == 1)
            $accountForm[] = array(
                'type' => 'Select',
                'name' => 'album',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Choose Album'),
                'multiOptions' => $albumOptions,
                'value' => 0,
                'hasValidator' => true
            );
        }

        $accountForm[] = array(
            'type' => 'Text',
            'name' => 'title',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Album Title'),
            'hasValidator' => true
        );

        // prepare categories
        $categories = Engine_Api::_()->getDbtable('categories', 'album')->getCategoriesAssoc();
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
            'type' => 'Textarea',
            'name' => 'description',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Album Description'),
            'hasValidator' => true
        );

        $accountForm[] = array(
            'type' => 'Checkbox',
            'name' => 'search',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Show this album in search results'),
            "value"=>1
        );


        $availableLabels = array(
            'everyone' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone'),
            'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
            'owner_network' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends and Networks'),
            'owner_member_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends of Friends'),
            'owner_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends Only'),
            'owner' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Just Me')
        );

// Element: auth_view
        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('album', $viewer, 'auth_view');
        $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
        if (!empty($viewOptions) && count($viewOptions) > 1) {
            $accountForm[] = array(
                'type' => 'Select',
                'name' => 'auth_view',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Privacy'),
                'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who may see this album?'),
                'multiOptions' => $viewOptions,
                'value' => key($viewOptions),
                'hasValidator' => true
            );
        }


// Element: auth_comment
        $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('album', $viewer, 'auth_comment');
        $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

        if (!empty($commentOptions) && count($commentOptions) > 1) {
            $accountForm[] = array(
                'type' => 'Select',
                'name' => 'auth_comment',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Comment Privacy'),
                'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who may post comments on this album?'),
                'multiOptions' => $commentOptions,
                'value' => key($commentOptions),
                'hasValidator' => true
            );
        }


// Element: auth_tag
        $tagOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('album', $viewer, 'auth_tag');
        $tagOptions = array_intersect_key($availableLabels, array_flip($tagOptions));

        if (!empty($tagOptions) && count($tagOptions) > 1) {
            $accountForm[] = array(
                'type' => 'Select',
                'name' => 'auth_tag',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Tagging'),
                'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who may tag photos in this album?'),
                'multiOptions' => $tagOptions,
                'value' => key($tagOptions),
                'hasValidator' => true
            );
        }

        if (empty($subject)) {
            $accountForm[] = array(
                'type' => 'File',
                'name' => 'photo',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Main Photo')
            );
        }

        if (empty($subject)) {
            $accountForm[] = array(
                'type' => 'Submit',
                'name' => 'submit',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Save Photos')
            );
        } else {
            $accountForm[] = array(
                'type' => 'Submit',
                'name' => 'submit',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Save Album')
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
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Caption'),
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
     * Set the photos
     * 
     * @return array
     */
    public function setPhoto($photo, $subject) {
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
        $siteapiAlbumSetPhoto = Zend_Registry::isRegistered('siteapiAlbumSetPhoto') ? Zend_Registry::get('siteapiAlbumSetPhoto') : null;
        $params = array(
            'parent_type' => $subject->getType(),
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

        // Resize image (normal)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(320, 240)
                ->write($path . '/in_' . $imageName)
                ->destroy();

        // Store
        $iMain = $storage->create($path . '/m_' . $imageName, $params);
        $iIconNormal = $storage->create($path . '/in_' . $imageName, $params);
        $iMain->bridge($iIconNormal, 'thumb.normal');

        // Remove temp files
        @unlink($path . '/m_' . $imageName);
        @unlink($path . '/in_' . $imageName);

        // Update row
        if (!empty($siteapiAlbumSetPhoto)) {
            $subject->modified_date = date('Y-m-d H:i:s');
            $subject->file_id = $iMain->getIdentity();
            $subject->save();
        }

        return $subject;
    }

    public function getPhotoTag($photo) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $response = array();
        foreach ($photo->tags()->getTagMaps() as $tagmap) {
            if (($viewer->getIdentity() == $tagmap->tag_id) || (isset($photo->owner_id) && $photo->owner_id == $viewer->getIdentity())) {
                $isRemove = 1;
            } else
                $isRemove = 0;

            $tags = array_merge($tagmap->toArray(), array(
                'id' => $tagmap->getIdentity(),
                'text' => $tagmap->getTitle(),
                'href' => $tagmap->getHref(),
                'guid' => $tagmap->tag_type . '_' . $tagmap->tag_id,
                "isRemove" => $isRemove
            ));
            try {
                $subject = Engine_Api::_()->getItem('user', $tagmap->tag_id);
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($subject);
                $tags = array_merge($tags, $getContentImages);
                if (!empty($isRemove)) {
                    $menu['menus'] = array(
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Remove Tag'),
                        'name' => 'delete_tag',
                        'url' => 'tags/remove',
                        'urlParams' => array(
                            "subject_type" => $photo->getType(),
                            "subject_id" => $photo->getIdentity(),
                            "tagmap_id" => $tagmap->tagmap_id
                        )
                    );
                    $tags = array_merge($tags, $menu);
                }
            } catch (Exception $ex) {
                
            }
            $response[] = $tags;
        }
        return $response;
    }

    public function getPhotoSelect(array $params) {
        $select = Engine_Api::_()->getItemTable('album_photo')->select();

        if (!empty($params['album']) && $params['album'] instanceof Album_Model_Album) {
            $select->where('album_id = ?', $params['album']->getIdentity());
        } else if (!empty($params['album_id']) && is_numeric($params['album_id'])) {
            $select->where('album_id = ?', $params['album_id']);
        }

        if (isset($params['user_id']) && !empty($params['user_id']) && is_numeric($params['user_id'])) {
            $select->where('owner_id = ?', $params['user_id']);
        }

        if (!isset($params['order'])) {
            $select->order('order ASC');
        } else if (is_string($params['order'])) {
            $select->order($params['order']);
        }

        return $select;
    }

    public function getPhotoPaginator(array $params) {
        return Zend_Paginator::factory($this->getPhotoSelect($params));
    }

}
