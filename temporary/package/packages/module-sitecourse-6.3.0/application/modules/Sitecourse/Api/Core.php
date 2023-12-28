<?php
class Sitecourse_Api_Core extends Core_Api_Abstract
{
    /**
     * Convert Decoded String into Encoded
     * @param string $string : decodeed string
     * @return string
     */
    public function getDecodeToEncode($string = null) {
    	$encodeString = '';
    	$string = (string) $string;
    	if (!empty($string)) {
    		$startIndex = 11;
    		$CodeArray = array("x4b1e4ty6u", "bl42iz50sq", "pr9v41c19a", "ddr5b8fi7s", "lc44rdya6c", "o5or323c54", "xazefrda4p", "54er65ee9t", "8ig5f2a6da", "kkgh5j9x8c", "ttd3s2a16b", "5r3ec7w46z", "0d1a4f7af3", "sx4b8jxxde", "hf5blof8ic", "4a6ez5t81f", "3yf5fc3o12", "sd56hgde4f", "d5ghi82el9","sd56hkde4f", "d5gdi82el9");

    		$time = time();
    		$timeLn = Engine_String::strlen($time);
    		$last2DigtTime = substr($time, $timeLn - 2, 2);
    		$sI1 = (int) ($last2DigtTime / 10);
    		$sI2 = $last2DigtTime % 10;
    		$Index = $sI1 + $sI2;

    		$codeString = $CodeArray[$Index];
    		$startIndex+=$Index % 10;
    		$lenght = Engine_String::strlen($string);
    		for ($i = 0; $i < $lenght; $i++) {
    			$code = md5(uniqid(mt_rand(), true));
    			$encodeString.= substr($code, 0, $startIndex);
    			$encodeString.=$string{$i};
    			$startIndex++;
    		}
    		$code = md5(uniqid(mt_rand(), true));
    		$appendEnd = substr($code, 5, $startIndex);
    		$prepandStart = substr($code, 20, 10);
    		$encodeString = $prepandStart . $codeString . $encodeString . $appendEnd;
    	}

    	return $encodeString;
    }
    /**
     * Convert Encoded String into Decoded
     * @param string $string : encoded string
     * @return string
     */
    public function getEncodeToDecode($string) {
    	$decodeString = '';

    	if (!empty($string)) {
    		$startIndex = 11;
    		$CodeArray = array("x4b1e4ty6u", "bl42iz50sq", "pr9v41c19a", "ddr5b8fi7s", "lc44rdya6c", "o5or323c54", "xazefrda4p", "54er65ee9t", "8ig5f2a6da", "kkgh5j9x8c", "ttd3s2a16b", "5r3ec7w46z", "0d1a4f7af3", "sx4b8jxxde", "hf5blof8ic", "4a6ez5t81f", "3yf5fc3o12", "sd56hgde4f", "d5ghi82el9","sd56hkde4f", "d5gdi82el9");
    		$string = substr($string, 10, (Engine_String::strlen($string) - 10));
    		$codeString = substr($string, 0, 10);

    		$Index = array_search($codeString, $CodeArray);
    		$string = substr($string, 10, Engine_String::strlen($string) - 10);
    		$startIndex+=$Index % 10;

    		$string = substr($string, 0, (Engine_String::strlen($string) - $startIndex));

    		$lenght = Engine_String::strlen($string);
    		$j = 1;
    		for ($i = $startIndex; $i < $lenght;) {
    			$j++;
    			$decodeString.= $string{$i};
    			$i = $i + $startIndex + $j;
    		}
    	}
    	return $decodeString;
    }

	/**
     * Getting the host name
     * @return string
     */
	public function getHost() {
		return _ENGINE_SSL ? 'https://' . $_SERVER['HTTP_HOST'] : 'http://' . $_SERVER['HTTP_HOST'];
	}
    /**
     * Getting the content URL
     * @param type $subject: Object of content
     * @return array
     */
    public function getContentURL($subject) {
    	$url = array();
    	try {
    		if (!empty($subject)) {
    			$getHref = $subject->getHref();
    			if (!empty($getHref)) {
    				$host = $this->getHost();
    				$url['content_url'] = !empty($getHref) ? $host . $getHref : '';
    			}
    		}
    	} catch (Exception $ex) {
            // Blank Exception
    	}
    	return $url;
    }
    public function saveVideoThumbnail($photo, $video) {
    	$valid_thumb = true;
    	if ($photo instanceof Zend_Form_Element_File) {
    		$file = $photo->getFileName();
    		$fileName = $file;
    	} else if ($photo instanceof Storage_Model_File) {
    		$file = $photo->temporary();
    		$fileName = $photo->name;
    	} else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
    		$tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
    		$file = $tmpRow->temporary();
    		$fileName = $tmpRow->name;
    	} else if (is_array($photo) && !empty($photo['tmp_name'])) {
    		$file = $photo['tmp_name'];
    		$fileName = $photo['name'];
    	} else {
    		$file = $photo;
    		$fileName = $photo;
    		if (@GetImageSize($fileName)) {
    			$valid_thumb = true;
    		} else {
    			$valid_thumb = false;
    		}
    	}

    	if(empty($fileName))
    		return;
    	if (!$fileName) {
    		$fileName = basename($file);
    	}
    	$params = array(
    		'parent_type' => $video->getType(),
    		'parent_id' => $video->getIdentity(),
    		'user_id' => $video->owner_id,
    		'name' => $fileName,
    	);
    	$thumbnail_parsed = @parse_url($fileName);

    	$ext = ltrim(strrchr($fileName, '.'), '.');
    	if (isset($thumbnail_parsed['path'])) {
    		$ext = ltrim(strrchr($thumbnail_parsed['path'], '.'), '.');
    	}

    	if (empty($ext)) {
    		$ext = 'jpeg';
    	}

    	if ($valid_thumb && $fileName && $ext && $thumbnail_parsed && in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {

    		$file = APPLICATION_PATH . '/temporary/link_' . md5($fileName) . '.' . $ext;
    		$mainPath = APPLICATION_PATH . '/temporary/link_thumb_' . md5($fileName) . '_m.' . $ext;
    		$normalPath = APPLICATION_PATH . '/temporary/link_thumb_' . md5($fileName) . '_in.' . $ext;
    		$largePath = APPLICATION_PATH . '/temporary/link_thumb_' . md5($fileName) . '_l.' . $ext;
            //Fetching the width and height of thumbmail
    		$normalHeight = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.video.height', 375);
    		$normalWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.video.width', 375);
    		$largeHeight = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.video.height', 720);
    		$largeWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.video.width', 720);
    		$mainHeight = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.height', 1600);
    		$mainWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.height', 1600);

    		$src_fh = fopen($fileName, 'r');
    		$tmp_fh = fopen($file, 'w');
    		stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 30);
            // Resize image (main)
    		if (file_exists($file)) {
    			$image = Engine_Image::factory();
    			$image->open($file)
    			->resize($mainWidth, $mainHeight)
    			->write($mainPath)
    			->destroy();

                // Resize image (large)
    			$image = Engine_Image::factory();
    			$image->open($file)
    			->resize($largeWidth, $largeHeight)
    			->write($largePath)
    			->destroy();

                // Resize image (normal)
    			$image = Engine_Image::factory();
    			$image->open($file)
    			->resize($normalWidth, $normalHeight)
    			->write($normalPath)
    			->destroy();
    		}
    		$filesTable = Engine_Api::_()->getDbtable('files', 'storage');
            // Store
    		$iMain = $filesTable->createFile($mainPath, $params);
    		$iLarge = $filesTable->createFile($largePath, $params);
    		$iNormal = $filesTable->createFile($normalPath, $params);

    		$iMain->bridge($iLarge, 'thumb.large');
    		$iMain->bridge($iNormal, 'thumb.normal');
    		$iMain->bridge($iMain, 'thumb.main');
            // Remove temp files
    		@unlink($mainPath);
    		@unlink($largePath);
    		@unlink($normalPath);
    		$video->photo_id = $iMain->getIdentity();
    		$video->status = 1;
    		$video->save();
    		return $video;
    	}
    	return NULL;
    }
    // handle video upload
    public function createVideo($params, $file, $values) {
    	if ($file instanceof Storage_Model_File) {
    		$params['file_id'] = $file->getIdentity();
    	} else {
            // create video item
    		$video = Engine_Api::_()->getDbtable('videos', 'sitecourse')->createRow();
            if ( is_array($file) && !empty($file['tmp_name']) ) {
                $file_ext = pathinfo($file['name']);
            } else {
                $file_ext = pathinfo($file);
            }
    		$file_ext = $file_ext['extension'];
    		$video->code = $file_ext;
    		$video->synchronized = 1;
    		$video->save();

            // Channel video in temporary storage object for ffmpeg to handle
    		$storage = Engine_Api::_()->getItemTable('storage_file');
    		$storageObject = $storage->createFile($file, array(
    			'parent_id' => $video->getIdentity(),
    			'parent_type' => $video->getType(),
    			'user_id' => $video->owner_id,
    		));

            // Remove temporary file
    		@unlink($file);

    		$video->file_id = $storageObject->file_id;
    		$video->save();
            // Add to jobs
    		if (strtolower($file_ext)=='mp4') {
    			Engine_Api::_()->getDbtable('jobs', 'core')->addJob('sitecourse_encode', array(
    				'video_id' => $video->getIdentity(),
    				'type' => 'mp4',
    			));
    		} else {
    			Engine_Api::_()->getDbtable('jobs', 'core')->addJob('sitecourse_encode', array(
    				'video_id' => $video->getIdentity(),
    				'type' => 'flv',
    			));
    		}
    	}

    	return $video;
    }

	/**
     * Getting the all type(main, icon, normal and profile) of image urls.
     * @param type $subject: Object of content
     * @param type $getOwnerImage: Need Object Owner images
     * @param type $key: Need to modify response key value
     * @return array
     */
	public function getContentImage($subject, $getOwnerImage = false, $key = false) {
		if (!isset($subject) || empty($subject))
			return;
		$getParentHost = $this->getHost();
		$baseParentUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
		$baseParentUrl = @trim($baseParentUrl, "/");
		$staticBaseUrl = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.static.baseurl', null);

        // Check IF default service "Local Storage" or not.
		$getDefaultStorageId = Engine_Api::_()->getDbtable('services', 'storage')->getDefaultServiceIdentity();
		$getDefaultStorageType = Engine_Api::_()->getDbtable('services', 'storage')->getService($getDefaultStorageId)->getType();
		$host = '';
		if ($getDefaultStorageType == 'local')
			$host = !empty($staticBaseUrl) ? $staticBaseUrl : $this->getHost();

		$type = (empty($getOwnerImage)) ? $subject->getType() : $subject->getOwner()->getType();
		$images = array();
        if (empty($getOwnerImage)) { // Getting content images
            // If image url already contains http://
        	if (strstr($subject->getPhotoUrl('thumb.main'), 'http://') || strstr($subject->getPhotoUrl('thumb.main'), 'https://'))
        		$host = '';

        	$tempKey = empty($key) ? 'image' : $key . '_image';
        	$images[$tempKey] = (($thumbMain = $subject->getPhotoUrl('thumb.main')) && !empty($thumbMain)) ? (!strstr($thumbMain, "application/modules")) ? $host . $subject->getPhotoUrl('thumb.main') : $this->getDefaultImage($type, 'main') : $this->getDefaultImage($type, 'main');
        	if (!strstr($images[$tempKey], 'http'))
        		$images[$tempKey] = $getParentHost . DIRECTORY_SEPARATOR . $baseParentUrl . $images[$tempKey];

        	$images[$tempKey . '_normal'] = (($thubNormal = $subject->getPhotoUrl('thumb.normal')) && !empty($thubNormal)) ? (!strstr($thubNormal, "application/modules")) ? $host . $subject->getPhotoUrl('thumb.normal') : $this->getDefaultImage($type, 'normal') : $this->getDefaultImage($type, 'normal');
        	if (!strstr($images[$tempKey . '_normal'], 'http'))
        		$images[$tempKey . '_normal'] = $getParentHost . DIRECTORY_SEPARATOR . $baseParentUrl . $images[$tempKey . '_normal'];

        	$images[$tempKey . '_profile'] = (($thumbProfile = $subject->getPhotoUrl('thumb.profile')) && !empty($thumbProfile)) ? (!strstr($thubNormal, "application/modules")) ? $host . $subject->getPhotoUrl('thumb.profile') : $this->getDefaultImage($type, 'profile') : $this->getDefaultImage($type, 'profile');
        	if (!strstr($images[$tempKey . '_profile'], 'http'))
        		$images[$tempKey . '_profile'] = $getParentHost . DIRECTORY_SEPARATOR . $baseParentUrl . $images[$tempKey . '_profile'];

        	$images[$tempKey . '_icon'] = (($thumbIcon = $subject->getPhotoUrl('thumb.icon')) && !empty($thumbIcon)) ? (!strstr($thubNormal, "application/modules")) ? $host . $subject->getPhotoUrl('thumb.icon') : $this->getDefaultImage($type, 'icon') : $this->getDefaultImage($type, 'icon');
        	if (!strstr($images[$tempKey . '_icon'], 'http'))
        		$images[$tempKey . '_icon'] = $getParentHost . DIRECTORY_SEPARATOR . $baseParentUrl . $images[$tempKey . '_icon'];

            // Add content url
        	$contentURL = $this->getContentURL($subject);
        	$contentCoverImage = $this->getContentCoverPhoto($subject);
        	$images = array_merge($images, $contentURL);
        	if (isset($contentCoverImage) && !empty($contentCoverImage))
        		$images = array_merge($images, $contentCoverImage);
        } else { // Getting owner images
        	if (strstr($subject->getOwner()->getPhotoUrl('thumb.main'), 'http://') || strstr($subject->getOwner()->getPhotoUrl('thumb.main'), 'https://'))
        		$host = '';

        	$tempKey = empty($key) ? 'owner_image' : $key . '_owner_image';
        	$images[$tempKey] = ($subject->getOwner()->getPhotoUrl('thumb.main')) ? $host . $subject->getOwner()->getPhotoUrl('thumb.main') : $this->getDefaultImage($type, 'main');
        	if (!strstr($images[$tempKey], 'http'))
        		$images[$tempKey] = $getParentHost . DIRECTORY_SEPARATOR . $baseParentUrl . $images[$tempKey];

        	$images[$tempKey . '_normal'] = ($subject->getOwner()->getPhotoUrl('thumb.normal')) ? $host . $subject->getOwner()->getPhotoUrl('thumb.normal') : $this->getDefaultImage($type, 'normal');
        	if (!strstr($images[$tempKey . '_normal'], 'http'))
        		$images[$tempKey . '_normal'] = $getParentHost . DIRECTORY_SEPARATOR . $baseParentUrl . $images[$tempKey . '_normal'];

        	$images[$tempKey . '_profile'] = ($subject->getOwner()->getPhotoUrl('thumb.profile')) ? $host . $subject->getOwner()->getPhotoUrl('thumb.profile') : $this->getDefaultImage($type, 'profile');
        	if (!strstr($images[$tempKey . '_profile'], 'http'))
        		$images[$tempKey . '_profile'] = $getParentHost . DIRECTORY_SEPARATOR . $baseParentUrl . $images[$tempKey . '_profile'];

        	$images[$tempKey . '_icon'] = ($subject->getOwner()->getPhotoUrl('thumb.icon')) ? $host . $subject->getOwner()->getPhotoUrl('thumb.icon') : $this->getDefaultImage($type, 'icon');
        	if (!strstr($images[$tempKey . '_icon'], 'http'))
        		$images[$tempKey . '_icon'] = $getParentHost . DIRECTORY_SEPARATOR . $baseParentUrl . $images[$tempKey . '_icon'];
        }

        return $images;
    }
    public function getContentCoverPhoto($subject) {
    	if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecontentcoverphoto'))
    		return;
    	if (!$subject)
    		return;

    	$resource_type = $subject->getType();

    	if (!$resource_type)
    		return;

    	if (isset($subject->listingtype_id)) {
    		$params = array('resource_type' => $resource_type . '_' . $subject->listingtype_id);
    	} else {
    		$params = array('resource_type' => $resource_type);
    	}
    	if (!Engine_Api::_()->getDbtable('modules', 'sitecontentcoverphoto')->checkEnableModule($params))
    		return;

    	$contentName = strtolower($subject->getShortType()) . '_cover';

    	$host = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
    	$moduleName = strtolower($subject->getModuleName());
    	$user = Engine_Api::_()->user()->getViewer();


    	try {
    		if ($subject->getType() != 'sitereview_listing') {
    			if (isset($subject->$contentName)) {
    				$photo = Engine_Api::_()->getItem($moduleName . "_photo", $subject->$contentName);
    				if (isset($photo) && !empty($photo))
    					$getPhotoURL = $photo->getPhotoUrl();
    			}
    		} else {
    			$tableName = 'engine4_sitereview_otherinfo';
    			$db = Engine_Db_Table::getDefaultAdapter();
    			$field = $db->query("SHOW COLUMNS FROM $tableName LIKE '$contentName'")->fetch();
    			if (!empty($field)) {
    				$fieldNameValue = Engine_Api::_()->getDbTable('otherinfo', 'sitereview')->getColumnValue($subject->listing_id, $contentName);
    				if ($fieldNameValue) {
    					$photo = Engine_Api::_()->getItem($moduleName . "_photo", $fieldNameValue);
    					if (isset($photo) && !empty($photo))
    						$getPhotoURL = $photo->getPhotoUrl();
    				}
    			}
    		}
    		$finalPhotoURL['default_cover'] = 0;

    		$permissionType = 'sitecontentcoverphoto_' . $resource_type;
    		$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');

    		if (isset($user->level_id) && !empty($user->level_id) && !isset($photo)) {
    			$id = $user->level_id;
    			$resource = Engine_Api::_()->getDbtable('modules', 'sitecontentcoverphoto')->getModuleName(array('resource_type' => $resource_type));
    			if (isset($subject->listingtype_id) && $resource) {
    				$listingType = Engine_Api::_()->getItem('sitereview_listingtype', $subject->listingtype_id);
    				$titleSinLc = strtolower($listingType->title_singular);
    				$setting = Engine_Api::_()->getApi("settings", "core")->getSetting("sitecontentcoverphoto.$resource.$titleSinLc.cover.photo.preview.level.$id.id");
    			} elseif ($resource) {
    				$photo = $setting = Engine_Api::_()->getApi("settings", "core")->getSetting("sitecontentcoverphoto.$resource.cover.photo.preview.level.$id.id");
    			}

    			if ($resource && isset($setting) && !empty($setting)) {
    				$getPhotoURL = $photo = Engine_Api::_()->storage()->get($setting, 'thumb.cover')->map();
    				$finalPhotoURL['default_cover'] = 1;
    			}
    		}

    		if (!empty($photo) && !empty($photo->album_id)) {
    			if ($moduleName != 'album') {
    				$album = Engine_Api::_()->getItem("$moduleName" . "_album", $photo->album_id);
    			} else {
    				$album = Engine_Api::_()->getItem("album", $photo->album_id);
    			}

    			if ($album && $album->cover_params && is_array($album->cover_params) && isset($album->cover_params['top']) && isset($album->cover_params['left'])) {
    				$finalPhotoURL['top'] = $album->cover_params['top'];
    				$finalPhotoURL['left'] = $album->cover_params['left'];
    			} else if ($album && !is_array($album->cover_params) && $album->cover_params) {
    				$decodedArray = Zend_Json_Decoder::decode($album->cover_params);
    				$finalPhotoURL['top'] = $decodedArray['top'];
    				$finalPhotoURL['left'] = $decodedArray['left'];
    			}
    			$finalPhotoURL['cover_image'] = (strstr($getPhotoURL, 'http')) ? $getPhotoURL : $host . $getPhotoURL;
    		}
    		return $finalPhotoURL;
    	} catch (Exception $ex) {
            // Blank Exception
    	}
    	return;
    }

    /**
     * @param video object
     * @param autoplay boolean
     * @return uploaded video path/url
     */
    public function getVideoURL($video, $autoplay = true) {
        // YouTube
    	if ($video['type'] == 'youtube') {
    		return '///www.youtube.com/embed/' . $video['code'] . '?wmode=opaque' . ($autoplay ? "&autoplay=1" : "");
        } elseif ($video['type'] == 'vimeo') { // Vimeo
        	return '///player.vimeo.com/video/' . $video['code'] . '?title=0&amp;byline=0&amp;portrait=0&amp;wmode=opaque' . ($autoplay ? "&amp;autoplay=1" : "");
        } elseif ($video['type'] == 'dailymotion') {
        	return '///www.dailymotion.com/embed/video/' . $video['code'] . '?wmode=opaque' . ($autoplay ? "&amp;autoplay=1" : "");
        } elseif ($video['type'] == 'upload' || $video['type'] == 'mydevice') {
            // Uploded Videos
        	$staticBaseUrl = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.static.baseurl', null);

            // $getHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
        	$getHost = $this->getHost();
        	$getDefaultStorageId = Engine_Api::_()->getDbtable('services', 'storage')->getDefaultServiceIdentity();
        	$getDefaultStorageType = Engine_Api::_()->getDbtable('services', 'storage')->getService($getDefaultStorageId)->getType();

        	$host = '';
        	if ($getDefaultStorageType == 'local')
        		$host = !empty($staticBaseUrl) ? $staticBaseUrl : $getHost;

        	$video_location = Engine_Api::_()->storage()->get($video['file_id'], $video['type']);
        	if (!empty($video_location)) {
        		$video_location = $video_location->getHref();
        	} else
        	return '';
        	$video_location = strstr($video_location, 'http') ? $video_location : $host . $video_location;

        	return $video_location;
        }
        elseif ($video['type'] == 5 || $video['type'] == 6 || $video['type'] == 'embedcode' || $video['type'] == 'iframely') {

        	if (isset($video['code']) && !empty($video['code']))
        		return $video['code'];
        	else
        		return '';
        }
        elseif ( $video['type'] == 'stream') {
        	$storage_file = Engine_Api::_()->storage()->get($video['file_id'], $video['type']);
        	if( $storage_file ) {
        		return $storage_file->getHref();
        	}
        }
    }


    public function deleteCourse($course_id) {
    	$course = Engine_Api::_()->getItem('sitecourse_course',$course_id);
    	// course not available
    	if(!$course || !$course->getIdentity()) return;
    	$viewer = Engine_Api::_()->user()->getViewer();
    	// check enrollments cnt if not zero: return
    	$enrollementCount = Engine_Api::_()->getDbTable('buyerdetails','sitecourse')->courseEnrollementCount($course_id);
    	if($enrollementCount) return;
    	// process
    	$db = Engine_Db_Table::getDefaultAdapter();
    	$db->beginTransaction();

    	try{
    		// fetch all lessons
    		$lessons = Engine_Api::_()->getItemTable('sitecourse_lesson')->getLessonsBelongsToCourse($course_id);
    		// fetch intro video
    		$introVideo = Engine_Api::_()->getItemTable('sitecourse_video')->getVideoItem($course_id,'course');
        	// get the ids where type is video
    		$videoLessonsIds = $this->getVideoLessonsId($lessons);
        	// get the ids where type is document
    		$docLessonsIds = $this->getDocLessonsId($lessons);
        	// get the storage file ids where type of video is upload
    		$storageFileVideoIds = Engine_Api::_()->getItemTable('sitecourse_video')->getStorageFileIds($videoLessonsIds);
        	// delete videos entry from storage table for video type upload
    		foreach($storageFileVideoIds as $ids){
    			$storageFile = Engine_Api::_()->getItem('storage_file',$ids);
    			if($storageFile && !empty($storageFile))
    				$storageFile->delete();
    		}
        	// remove the intro video if type of is upload
    		if($introVideo['type'] == 'upload'){
    			$storageFile = Engine_Api::_()->getItem('storage_file',$introVideo['file_id']);
    			if($storageFile && !empty($storageFile))
    				$storageFile->delete();
    		}
        	// delete documents entry from storage table for lessons
    		$storageTable = Engine_Api::_()->getDbtable('files','storage');
    		foreach($docLessonsIds as $id){
    			$storageTable->delete(array(
    				'parent_type = ?' => 'lesson',
    				'parent_id = ?' => $id,
    			));
    		}
        	//remove intro video if any
    		$storageTable->delete(array(
    			'parent_type = ?' => 'course',
    			'parent_id = ?' => $course_id,
    		));

        	//remove videos from video table
    		foreach($lessons as $idx => $lesson){
    			$video = Engine_Api::_()->getItemTable('sitecourse_video')->getVideoItem($lesson['lesson_id'],'lesson');
    			// video present
    			if($video){
    				$videoItem = Engine_Api::_()->getItem('sitecourse_video',$video['video_id']);
    				// valid video item
    				if($videoItem)
    					$videoItem->delete();
    			}
    		}
        	//remove lessons
    		$lessonTable = Engine_Api::_()->getDbtable('lessons','sitecourse');
    		$lessonTable->delete(array(
    			'course_id = ?' => $course_id
    		));
        	//remove topics
    		$topicsTable = Engine_Api::_()->getDbtable('topics','sitecourse');
    		$topicsTable->delete(array(
    			'course_id = ?' => $course_id
    		));
        	// remove course
    		$course->delete();
    		$db->commit();
    	} catch( Exception $e ) {
    		$db->rollBack();
    		throw $e;
    	}
    }

    /**
     * @param {int} course id,viewer id
     * @return {boolean} viewer is buyer of course
     */
    public function isCoursePurchased($course_id = null,$viewer_id = null){
    	$flag = false;
    	if(!$course || !$viewer_id) return $flag;
    	$purchased = Engine_Api::_()->getDbtable('buyerdetails','sitecourse')->validatePurchase($course_id,$viewer_id);
    	return $purchased;
    }


    /**
     * @param {array} lessons
     * @return {array} video lessons ids 
     */
    private function getVideoLessonsId($lessons)
    {
    	$ids = array();
    	foreach($lessons as $key=>$value){
    		if($value['type'] == 'video'){
    			$ids[] = $value['lesson_id'];
    		}
    	}
    	return $ids;
    }

    /**
     * @param {array} lessons
     * @return {array} doc lessons ids
     */
    private function getDocLessonsId($lessons)
    {
    	$ids = array();
    	foreach($lessons as $key=>$value){
    		if($value['type'] == 'doc'){
    			$ids[] = $value['lesson_id'];
    		}
    	}
    	return $ids;
    }

    /**
     * 
	 * Send emails for perticuler course
   	 * @params $type : which mail send
     * $params $courseId : Id of course
     * 
   	 */
    public function sendMail($type, $courseId, $status = null) {
    	if( empty($type) || empty($courseId) ) {
    		return;
    	}
    	// fetch course
    	$course = Engine_Api::_()->getItem('sitecourse_course', $courseId);
    	$mail_template = null;
    	if( !empty($course) ) {
    		// put the object link
    		$params = array(
    			'title' => $course['title'],
    			'object_link' => '',
                'host' => $_SERVER['HTTP_HOST']
            );
    		$recipient = Engine_Api::_()->user()->getUser($course->owner_id);
    		switch( $type ) {
    			case "STATUS":
    			$mail_template = 'notify_sitecourse_course_status';
    			$params['object_link'] = 'http://' . $_SERVER['HTTP_HOST'] .
    			Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'edit','course_id'=>$course->course_id), 'sitecourse_dashboard', true);
                $params['status'] = $status;
    			break;
    			case "NEWEST":
    			$mail_template = 'notify_sitecourse_course_newest';
    			$params['object_link'] = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'edit','course_id'=>$course->course_id), 'sitecourse_dashboard', true);
    			break;
    			case "BESTSELLER":
    			$mail_template = 'notify_sitecourse_course_bestseller';
    			$params['object_link'] = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'edit','course_id'=>$course->course_id), 'sitecourse_dashboard', true);
    			break;
    			case "TOPRATED":
    			$mail_template = 'notify_sitecourse_course_toprated';
    			$params['object_link'] = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'edit','course_id'=>$course->course_id), 'sitecourse_dashboard', true);
    			break;
    			case "REVIEW":
    			$mail_template = 'notify_sitecourse_course_review';
                $viewer = Engine_Api::_()->user()->getViewer();
                $params['object_link'] = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'profile','url'=>$course->url), 'sitecourse_entry_profile', true);
                $params['user_name'] = $viewer->getTitle();
                break;
                case "PURCHASE":
                $mail_template = 'notify_sitecourse_course_purchase';
                $viewer = Engine_Api::_()->user()->getViewer();
                $params['object_link'] = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'index','course_id'=>$course->course_id), 'sitecourse_learning', true);
                $params['user_name'] = $viewer->getTitle();
                break;
                case "CERTIFICATE":
                $mail_template = 'notify_sitecourse_course_purchase';
                $viewer = Engine_Api::_()->user()->getViewer();
                $params['object_link'] = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'index','course_id'=>$course->course_id), 'sitecourse_learning', true);
                break;  
                case "REPORTED":
                $mail_template = 'notify_sitecourse_course_report';
                $params['object_link'] = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'index','course_id'=>$course->course_id), 'sitecourse_learning', true);
                $superAdmins = Engine_Api::_()->user()->getSuperAdmins();
                $recepient = $superAdmins[0];
                break;
            }
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($recipient, $mail_template,$params);
        }
    }

    public function getHref($course_id, $owner_id, $slug = null) {

    	$course_url = Engine_Api::_()->sitecourse()->getCourseUrl($course_id);
    	$params = array_merge(array('url' => $course_url));

    	$urlO = Zend_Controller_Front::getInstance()->getRouter()
    	->assemble($params, 'sitecourse_entry_profile', true);
        //SITECOURSEURL WORK START

    	$routeStartS = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.UrlS', "course");

        //$banneUrlArray = Engine_Api::_()->sitecourse()->getBannedPageUrls();
    	return $urlO;
    }
   /**
   * Get course url
   *
   * @param int $course_id
   * @return string $courseUrl
   */
   public function getCourseUrl($course_id)
   {

   	$courseUrl = 0;
   	if( !empty($course_id) ) {
   		$sitecourse_table = Engine_Api::_()->getItemTable('sitecourse_course');
   		$courseUrl = $sitecourse_table->select()
   		->from($sitecourse_table->info('name'), 'url')
   		->where('course_id = ?', $course_id)
   		->limit(1)
   		->query()
   		->fetchColumn();
   	}
   	return $courseUrl;
   }

   public function getCourseId($course_url)
   {

   	$courseId = 0;
   	if( !empty($course_url) ) {
   		$sitecourse_table = Engine_Api::_()->getItemTable('sitecourse_course');
   		$courseId = $sitecourse_table->select()
   		->from($sitecourse_table->info('name'), 'course_id')
   		->where('url = ?', $course_url)
   		->limit(1)
   		->query()
   		->fetchColumn();
   	}
   	return $courseId;
   }

   public function getOldVersionImages($array){
        // Get available files
   	$imgOptions = array();
   	$imgOptions = $array;
   	$imageExtensions = array('gif', 'jpg', 'jpeg', 'png');

   	$it = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
   	foreach ($it as $file) {
   		if ($file->isDot() || !$file->isFile())
   			continue;
   		$basename = basename($file->getFilename());
   		if (!($pos = strrpos($basename, '.')))
   			continue;
   		$ext = strtolower(ltrim(substr($basename, $pos), '.'));
   		if (!in_array($ext, $imageExtensions))
   			continue;
   		$imgOptions['public/admin/' . $basename] = $basename;
   	}
   	return $imgOptions;
   }


   public function getNewVersionImages($array){
   	$params = array();
   	$params['extension'] = array('gif', 'jpg', 'jpeg', 'png');
   	$filesTable = Engine_Api::_()->getDbTable('files','core');
   	$select = $filesTable->getFiles($params);
   	$fileData = $filesTable->fetchAll($select);
    
   	$imgOptions = array();
   	if(!empty($array)) 
   		$imgOptions = $array;
   	else
   		$imgOptions = array('0' => $default);
   	foreach ($fileData as $key => $value) {
   		$storage_file = Engine_Api::_()->getItem('storage_file', $value->storage_file_id);
   		$imgOptions[$storage_file->map()] =  $value->name;
   	}
   	return $imgOptions;

   }

   public function getImages($array = array('0' => 'No Image')){
   	$coreVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core')->version;
   	$checkVersion = Engine_Api::_()->seaocore()->checkVersion($coreVersion, '5.0.0p1');
        // changes in calling of function on the basis of core version
   	if($checkVersion) {
   		return $this->getNewVersionImages($array);
   	} else {
   		return $this->getOldVersionImages($array);
   	}
   }


   public function generateFeed($course_id){

    $viewer = Engine_Api::_()->user()->getViewer();
    $course = Engine_Api::_()->getItem('sitecourse_course', $course_id);

    $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($course->getOwner(), $course, 'course_new','', null);

    // make sure action exists before attaching the course to the activity
    if( 
        $action 
    ) {
      
        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $course);
    }
}


    public function getTrucated($text, $limit = 14) {
        if(strlen($text) > $limit) {
            $text = trim(substr($text, 0, $limit)) . "...";
        } 
        return $text;
    }

    public function getDefaultImage($module, $type = 'icon') {
        $getHost = $this->getHost();
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $baseUrl = @trim($baseUrl, "/");
        $path = '/application/modules/Sitecourse/externals/images/'; 
        $imageName = 'default_course_profile.png'; 
        $imageUrl = $getHost . '/' . $baseUrl . $path . $imageName;
        if (strstr($imageUrl, 'index.php/'))
            $imageUrl = str_replace('index.php/', '', $imageUrl);

        if (!empty($imageUrl))
            return $imageUrl;
    }

    public function getPriceWithCurrencyAdmin($price) {
        if (empty($price)) {
            return $price;
        }

        $defaultParams = array();
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if (empty($viewer_id)) {
            $defaultParams['locale'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'auto');
        }

        $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $defaultParams['precision'] = 2;
        $price = (float) $price;
        $priceStr = Zend_Registry::get('Zend_View')->locale()->toCurrency($price, $currency, $defaultParams);
        return $priceStr;
    }
   
}
?>
