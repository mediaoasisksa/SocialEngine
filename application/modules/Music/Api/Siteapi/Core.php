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
class Music_Api_Siteapi_Core extends Core_Api_Abstract {

    /**
     * Return the "Browse Search" form. 
     * 
     * @return array
     */
    public function getBrowseSearchForm() {
        $getCategoryArray = $searchForm = array();
        $viewer = Engine_Api::_()->user()->getViewer();
        $searchForm[] = array(
            'type' => 'Text',
            'name' => 'search',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search Music')
        );

        $searchForm[] = array(
            'type' => 'Select',
            'name' => 'show',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Show'),
            'multiOptions' => array(
                '1' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone\'s Playlists'),
                '2' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Only My Friends\' Playlists'),
            )
        );

        $searchForm[] = array(
            'type' => 'Select',
            'name' => 'sort',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Browse By'),
            'multiOptions' => array(
                'recent' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Recent'),
                'popular' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Popular'),
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
    public function getForm() {
        $accountForm = array();
        $viewer = Engine_Api::_()->user()->getViewer();

        $accountForm[] = array(
            'type' => 'Text',
            'name' => 'title',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Playlist Name'),
            'maxlength' => 63,
            'hasValidator' => true
        );

        $accountForm[] = array(
            'type' => 'Textarea',
            'name' => 'description',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Playlist Description'),
            'maxlength' => 300,
            'hasValidator' => true
        );

        $accountForm[] = array(
            'type' => 'Checkbox',
            'name' => 'search',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Show this playlist in search results')
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
        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('music_playlist', $viewer, 'auth_view');
        $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
        if (!empty($viewOptions) && count($viewOptions) >= 1) {
            if (count($viewOptions) != 1) {
                $accountForm[] = array(
                    'type' => 'Select',
                    'name' => 'auth_view',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Privacy'),
                    'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who may see this playlist?'),
                    'multiOptions' => $viewOptions,
                    'value' => key($viewOptions),
                    'hasValidator' => true
                );
            }
        }

        // Element: auth_comment
        $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('music_playlist', $viewer, 'auth_comment');
        $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));
        if (!empty($commentOptions) && count($commentOptions) >= 1) {
            if (count($commentOptions) != 1) {
                $accountForm[] = array(
                    'type' => 'Select',
                    'name' => 'auth_comment',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Comment Privacy'),
                    'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who may post comments on this playlist?'),
                    'multiOptions' => $commentOptions,
                    'value' => key($commentOptions),
                    'hasValidator' => true
                );
            }
        }

        $accountForm[] = array(
            'type' => 'File',
            'name' => 'photo',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Playlist Artwork')
        );

        $accountForm[] = array(
            'type' => 'File',
            'name' => 'songs',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Add Music'),
            'hasValidator' => true
        );

        $accountForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Save Music to Playlist')
        );

        return $accountForm;
    }

    /**
     * Set the classified uploadded image.
     *
     * @return object
     */
    public function setPhoto($photo, $playlist) {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
        }

        $imageName = $photo['name'];
        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => 'music_playlist',
            'parent_id' => $playlist->getIdentity(),
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
        $playlist->modified_date = date('Y-m-d H:i:s');
        $playlist->photo_id = $iMain->getIdentity();
        $playlist->save();

        return $playlist;
    }

    public function uploadSong($file, $playlist, $params = array()) {
        // Process
        $db = Engine_Api::_()->getDbtable('playlists', 'music')->getAdapter();
        $db->beginTransaction();

        try {
            if (is_array($file)) {
                $filename = $file['name'];
            } else if (is_string($file)) {
                $filename = $file;
            }

            // Check file extension
            if (!preg_match('/\.(mp3|m4a|aac|mp4)$/iu', $filename)) {
                throw new Music_Model_Exception('Invalid file type');
            }

            // upload to storage system
            $params = array_merge(array(
                'type' => 'song',
                'name' => $filename,
                'parent_type' => 'music_song',
                'parent_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
                'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
                'extension' => substr($filename, strrpos($filename, '.') + 1),
                    ), $params);

            $file = Engine_Api::_()->storage()->create($file, $params);

            if (!empty($file)) {
                $playlist_song = Engine_Api::_()->getDbtable('playlistSongs', 'music')->createRow();
                $playlist_song->playlist_id = $playlist->getIdentity();
                $playlist_song->file_id = $file->getIdentity();
                $playlist_song->title = preg_replace('/\.(mp3|m4a|aac|mp4)$/i', '', $file->name);
                $playlist_song->order = count($this->getSongs($file->getIdentity(), $playlist->getIdentity()));
                $playlist_song->save();
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    public function getSongs($file_id, $playlist_id) {
        $table = Engine_Api::_()->getDbtable('playlistSongs', 'music');
        $select = $table->select()
                ->where('playlist_id = ?', $playlist_id)
                ->order('order ASC');
        if (!empty($file_id))
            $select->where('file_id = ?', $file_id);

        return $table->fetchAll($select);
    }

    function array_depth(array $array) {
        $max_depth = 1;

        foreach ($array as $value) {
            if (is_array($value)) {
                $depth = $this->array_depth($value) + 1;

                if ($depth > $max_depth) {
                    $max_depth = $depth;
                }
            }
        }

        return $max_depth;
    }

}
