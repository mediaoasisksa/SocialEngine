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
class Video_Api_Siteapi_Core extends Core_Api_Abstract {

    /**
     * Get the "Advanced Search" form.
     * 
     * @return array
     */
    public function getBrowseSearchForm() {
        $getCategoryArray = $searchForm = array();
        $viewer = Engine_Api::_()->user()->getViewer();
        $searchForm[] = array(
            'type' => 'Text',
            'name' => 'search',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search')
        );

        $searchForm[] = array(
            'type' => 'Select',
            'name' => 'orderby',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Browse By'),
            'multiOptions' => array(
                'creation_date' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Recent'),
                'view_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Viewed'),
                'rating' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Highest Rated'),
            )
        );

        $getCategoryArray[0] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Categories');
        $categories = Engine_Api::_()->video()->getCategories();
        foreach ($categories as $category)
            $getCategoryArray[$category->category_id] = $category->category_name;


        if (COUNT($getCategoryArray) > 0) {
            $searchForm[] = array(
                'type' => 'Select',
                'name' => 'category',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Category'),
                'multiOptions' => $getCategoryArray
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
     * Get the video create form.
     * 
     * @return array
     */
    public function getForm($post_attach = 0, $subject = null, $type = 0,$message=0) {
        $accountForm = array();
        $viewer = Engine_Api::_()->user()->getViewer();

        if ($post_attach == 1 || $message ==1) {
            // Element: Add Type
            $video_options = Array();
            $key = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
            if (isset($key) && !empty($key))
                $video_options[1] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('YouTube');
            $video_options[2] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('Vimeo');

            //My Computer
            if (_CLIENT_TYPE && (_CLIENT_TYPE == 'android') && _ANDROID_VERSION && _ANDROID_VERSION >= '1.8.2') {
                $allowed_upload = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $viewer, 'upload');
                $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->video_ffmpeg_path;
                if (!empty($ffmpeg_path) && $allowed_upload && empty($message)) {
                    $video_options[3] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('My Device');
                }
            }

            $accountForm[] = array(
                'type' => 'Select',
                'name' => 'type',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Video Source'),
                'multiOptions' => $video_options,
                'value' => $type,
                'hasValidator' => true
            );
            $accountForm[] = array(
                'type' => 'Text',
                'name' => 'url',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Video Link (URL)'),
                'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Paste the web address of the video here.'),
                'maxlength' => 50
            );
            $accountForm[] = array(
                'type' => 'Submit',
                'name' => 'submit',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Attach')
            );

            return $accountForm;
        }

        $accountForm[] = array(
            'type' => 'Text',
            'name' => 'title',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Video Title'),
            'maxlength' => 100,
            'hasValidator' => true
        );

        $accountForm[] = array(
            'type' => 'Text',
            'name' => 'tags',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Tags (Keywords)'),
            'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Separate tags with commas.')
        );

        $accountForm[] = array(
            'type' => 'Textarea',
            'name' => 'description',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Video Description'),
            'hasValidator' => true
        );

        $categories = Engine_Api::_()->video()->getCategories();
        if (count($categories) != 0) {
            $categories_prepared[0] = "";
            foreach ($categories as $category) {
                $categories_prepared[$category->category_id] = $category->category_name;
            }

            if (!empty($categories_prepared)) {
                $accountForm[] = array(
                    'type' => 'Select',
                    'name' => 'category_id',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Category'),
                    'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($categories_prepared),
                    'hasValidator' => true
                );
            }
        }

        $accountForm[] = array(
            'type' => 'Checkbox',
            'name' => 'search',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Show this video entry in search results')
        );

        $accountForm[] = array(
            'type' => 'Text',
            'name' => 'location',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Location'),
            'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Eg: Fairview Park, Berkeley, CA')
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
        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $viewer, 'auth_view');
        $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
        if (!empty($viewOptions) && count($viewOptions) >= 1) {
            if (count($viewOptions) != 1) {
                $accountForm[] = array(
                    'type' => 'Select',
                    'name' => 'auth_view',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Privacy'),
                    'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who may see this video?'),
                    'multiOptions' => $viewOptions,
                    'value' => key($viewOptions),
                    'hasValidator' => true
                );
            }
        }

        // Element: auth_comment
        $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $viewer, 'auth_comment');
        $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));
        if (!empty($commentOptions) && count($commentOptions) >= 1) {
            if (count($commentOptions) != 1) {
                $accountForm[] = array(
                    'type' => 'Select',
                    'name' => 'auth_comment',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Comment Privacy'),
                    'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who may post comments on this video?'),
                    'multiOptions' => $commentOptions,
                    'value' => key($commentOptions),
                    'hasValidator' => true
                );
            }
        }

        if (empty($subject)) {
            $accountForm[] = array(
                'type' => 'Select',
                'name' => 'rotation',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Video Rotation'),
                'multiOptions' => array(
                    0 => '',
                    90 => '90°',
                    180 => '180°',
                    270 => '270°'
                ),
                'value' => 0
            );

            // Element: Add Type
            $video_options = Array();
            $video_options[0] = "";
            $key = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
            if (isset($key) && !empty($key))
                $video_options[1] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('YouTube');
            $video_options[2] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('Vimeo');

            //My Computer
            $allowed_upload = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $viewer, 'upload');
            $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->video_ffmpeg_path;
            if (!empty($ffmpeg_path) && $allowed_upload) {
                $video_options[3] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('My Device');
            }

            $accountForm[] = array(
                'type' => 'Select',
                'name' => 'type',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Video Source'),
                'multiOptions' => $video_options,
                'value' => $type,
                'hasValidator' => true
            );

            $accountForm[] = array(
                'type' => 'Text',
                'name' => 'url',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Video Link (URL)'),
                'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Paste the web address of the video here.'),
                'maxlength' => 50
            );

            $accountForm[] = array(
                'type' => 'File',
                'name' => 'filedata',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Add Video')
            );

            $accountForm[] = array(
                'type' => 'Submit',
                'name' => 'submit',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Post Video')
            );
        } else {
            $accountForm[] = array(
                'type' => 'Submit',
                'name' => 'submit',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Save Video')
            );
        }

        return $accountForm;
    }

    /**
     * Get video url according to upload type.
     * 
     * @return array
     */
    public function getVideoURL($video, $autoplay = true) {
    // YouTube
        if ($video->type == 1 || $video->type == 'youtube') {
            return 'www.youtube.com/embed/' . $video->code . '?wmode=opaque' . ($autoplay ? "&autoplay=1" : "");
        } elseif ($video->type == 2 || $video->type == 'vimeo') { // Vimeo
            return 'player.vimeo.com/video/' . $video->code . '?title=0&amp;byline=0&amp;portrait=0&amp;wmode=opaque' . ($autoplay ? "&amp;autoplay=1" : "");
        } elseif ($video->type == 4 || $video->type == 'dailymotion') {
            return 'www.dailymotion.com/embed/video/' . $video->code . '?wmode=opaque' . ($autoplay ? "&amp;autoplay=1" : "");
        } elseif ($video->type == 3 || $video->type == 'upload' || $video->type == 'mydevice') { // Uploded Videos
            $staticBaseUrl = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.static.baseurl', null);

            $getHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
            $getDefaultStorageId = Engine_Api::_()->getDbtable('services', 'storage')->getDefaultServiceIdentity();
            $getDefaultStorageType = Engine_Api::_()->getDbtable('services', 'storage')->getService($getDefaultStorageId)->getType();

            $host = '';
            if ($getDefaultStorageType == 'local')
                $host = !empty($staticBaseUrl) ? $staticBaseUrl : $getHost;

            $video_location = Engine_Api::_()->storage()->get($video->file_id, $video->getType());
            if (!empty($video_location)) {
                $video_location = $video_location->getHref();
            } else
                return '';


            $video_location = strstr($video_location, 'http') ? $video_location : $host . $video_location;

            return $video_location;
        }
        elseif ($video->type == 5 || $video->type == 6 || $video->type == 'embedcode' || $video->type == 'iframely') {

            if (isset($video->code) && !empty($video->code))
                return $video->code;
            else
                return '';
        }
        elseif ( $video->type == 'stream') {

           $storage_file = Engine_Api::_()->storage()->get($video->file_id, $video->getType());
          if( $storage_file ) {
            return $storage_file->getHref();
          }
        }
    }

    public function videoType($type) {
        switch ($type) {
            case 1:
            case 'youtube':
                return 1;
            case 2:
            case 'vimeo':
                return 2;
            case 3:
            case 'mydevice':
            case 'upload':
                return 3;
            case 4:
            case 'dailymotion':
                return 4;
            case 5:
            case 'embedcode':
            case 'iframely' :
                return 5;
            default : return $type;
        }
    }

    public function getVideoType($type) {
        switch ($type) {
            case 1:
            case 'youtube':
                return 'youtube';
            case 2:
            case 'vimeo':
                return 'vimeo';
            case 3:
            case 'mydevice':
            case 'upload' :
                return 'upload';
            case 4:
            case 'dailymotion':
                return 'dailymotion';
            case 5:
            case 'embedcode':
            case 'iframely' :
                return 'iframely';
            default : return $type;
        }
    }
    
    
        public function setPhoto($photo, $values, $setRow = true) {

        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
        } else {
            throw new Banner_Model_Exception('invalid argument passed to setPhoto');
        }
        $imageName = $photo['name'];
        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

        $params = array(
            'parent_type' => $values->getType(),
            'parent_id' => $values->getIdentity(),
        );


// Save
        $storage = Engine_Api::_()->storage();

// Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(720, 750)
                ->write($path . '/m_' . $imageName)
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
        $iSquare = $storage->create($path . '/is_' . $imageName, $params);

        $iMain->bridge($iSquare, 'thumb.icon');

// Remove temp files

        @unlink($path . '/m_' . $imageName);
        @unlink($path . '/is_' . $imageName);


// Update row
        if (!empty($setRow)) {
            $values->file_id = $iMain->getIdentity();
            $values->save();
        } else {
            $values->photo_id = $iMain->getIdentity();
            $values->save();
        }

//return $photoItem;
    }

}
