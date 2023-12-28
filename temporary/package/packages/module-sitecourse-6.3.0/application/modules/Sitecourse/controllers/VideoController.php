<?php  
class Sitecourse_VideoController extends Seaocore_Controller_Action_Standard
{
    //ACTION FOR ADD VIDEOS
	public function createAction() {
    	// In smoothbox
		$this->_helper->layout->setLayout('default-simple');
		if (!$this->_helper->requireUser()->isValid()) {
			return;
		}
		if (isset($_GET['ul'])) {
			return $this->_forward('upload-video', null, null, array('format' => 'json'));
		}
		$viewer     = Engine_Api::_()->user()->getViewer();
		$viewerId   = Engine_Api::_()->user()->getViewer()->user_id;
		$this->view->vtype = $par =$this->_getParam('type');

		if ($par != 'upload') {
			$this->view->url = $this->_getParam('url');
		}

		if (isset($_FILES['Filedata'])) {
			$_POST['id'] = $this->uploadVideoAction();
		}
		$this->view->parent_type = $parent_type = $this->_getParam('parent_type');
		// get parent id basd on parent type
		if($parent_type == 'course')
			$this->view->parent_id = $parent_id   = $this->_getParam('parent_id');
		if($parent_type == 'lesson')
			$this->view->parent_id = $parent_id   = null;

		$videoTable = Engine_Api::_()->getDbtable('videos', 'sitecourse');

        // Render
		$db = Engine_Db_Table::getDefaultAdapter();

        // Get form based on parent type
		if($parent_type == 'course')
			$this->view->form = $form = new Sitecourse_Form_Video();
		if($parent_type == 'lesson')
			$this->view->form = $form = new Sitecourse_Form_VideoLesson();

		$level_id = $viewer->level_id;
		// check post request
		if (!$this->getRequest()->isPost()) {
			$this->renderScript('video/create.tpl');
			return;
		}
		$title = "";
		$data  = $this->getRequest()->getPost();
		// check fields
		if (!$form->isValid($data)) {
			$this->renderScript('video/create.tpl');
			return;
		}
		$values = $form->getValues();
		$db->beginTransaction();

		try {
			$insert_action = false;
			$values        = $form->getValues();
			$values['owner_id'] = $viewerId;
			$values['owner_type'] = $viewer->getType();
			$table         = Engine_Api::_()->getDbtable('videos', 'sitecourse');
			if ($values['type'] == 'upload') {
				$video = Engine_Api::_()->getItem('sitecourse_video', $this->_getParam('id'));
			} else {
				$video = $table->createRow();
			}

			// check course intro video exits
			// remove if exits
			if($parent_type == 'course') {
				$intro_video = $table->getVideoItem($parent_id, $parent_type);
				if(!empty($intro_video)) {
					$introVideoItem = Engine_Api::_()->getItem('sitecourse_video', $intro_video['video_id']);
					$introVideoItem->delete();
				}
			}

			// when parent type is course 
			if($parent_type == 'course')
				$video->setFromArray($values);
			/**
			 * parent type is lesson
			 * get the parent id by making a db entry
			 * then enter the details in video db table
			 */
			if($parent_type == 'lesson'){
				$lessonTable = Engine_Api::_()->getDbtable('lessons', 'sitecourse');
				$topic_id = $this->_getParam('topic_id');
				// lesson db values
				$lessonValues = array(
					'topic_id' => $topic_id,
					'course_id' => ($this->_getParam('course_id'))?$this->_getParam('course_id'):0,
					'title' => $values['title'],
					'order' => 9999,
					'type' => 'video'
				);
				 // create row
				$lesson = $lessonTable->createRow();
				 // set row values
				$lesson->setFromArray($lessonValues);
				$lesson->save();
				 // get parent id

				$parent_id = $lessonTable->getLastEntry($topic_id);
				$values['parent_id'] = $parent_id;
				// set values for parent type lesson

				if(!$video->duration) {
					$information = $this->handleInformation($values['type'],$values['code']);
					$values['duration'] = $information['duration'] ?: 0;
				}
				$video->setFromArray($values);
			}
			if ($parent_type && $parent_id) {
				$video->parent_type = $parent_type;
				$video->parent_id   = $parent_id;				
				$video->save();
			}
			$video->save();

            // Now try to create thumbnail
			if ($video->type == 'iframely') {

				$information = $this->handleIframelyInformation($values['url']);
				if (empty($information)) {
					$form->addError('We could not find a video there - please check the URL and try again.');
				} else {
					if (!$video->photo_id) {
						$video->saveVideoThumbnail($information['thumbnail']);
					}
					$video->duration    = $information['duration'];
					$video->code        = $information['code'];
					$video->save();
					$insert_action = true;
				}
			}

			if ($values['type'] != 'upload' && $values['type'] != 'stream') {
				$video->status = 1;
				$video->save();
				$insert_action = true;
			}

			if($video->type == 'embedcode' && isset($values['vtype'])) {
				$video->type = $values['vtype'];
			}

			$video->synchronized = 1;
			$video->save();

			$db->commit();
		} catch (Exception $e) {
			$db->rollBack();
			throw $e;
		}

		return $this->_forward('success', 'utility', 'core', array(
			'smoothboxClose' => 20,
			'parentRefresh'=> 10,
			'messages' => array('Video Added Successfully'),
		));
	}


    //ACTION FOR UPLOADING VIDEOS BY FANCY UPLOADER
	public function uploadVideoAction() {
		if (!$this->_helper->requireUser()->checkRequire()) {
			$this->view->status = false;
			$this->view->error  = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
			$this->view->error = "sing in error";
			return;
		}
		if (!$this->getRequest()->isPost()) {
			$this->view->status = false;
			$this->view->error  = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
			// $this->view->error = "sing in error";
			return;
		}
		$video_duration = $this->_getParam('X-File-Size',0);
		$values = $this->getRequest()->getPost();
		// $response = Seaocore_Service_FancyUpload::upload();
		// if(!empty($response['error'])) {
		// 	$this->view->status = false;
		// 	$this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload') . $response['error'];
		// 	return;
		// }

        if(empty($_FILES['file']['tmp_name']) || empty(file_exists($_FILES['file']['tmp_name']))) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
            return;
        }
		$illegal_extensions = array('php', 'pl', 'cgi', 'html', 'htm', 'txt');
		if( in_array(pathinfo($response['path'], PATHINFO_EXTENSION), $illegal_extensions) ) {
			$this->view->status = false;
			$this->view->error  = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
			return;
		}

		$db = Engine_Api::_()->getDbtable('videos', 'sitecourse')->getAdapter();
		$db->beginTransaction();
		try {
			$viewer             = Engine_Api::_()->user()->getViewer();
			$values['owner_id'] = $viewer->getIdentity();

			$params = array(
				'owner_type' => 'user',
				'owner_id'   => $viewer->getIdentity(),
				'duration'   => $video_duration,
			);
			
			$video = Engine_Api::_()->sitecourse()->createVideo($params, $_FILES['file'], $values);
			$this->view->status   = true;
			$this->view->name     = $response['name'];
			$this->view->code     = $video->code;
			$this->view->video_id = $video->video_id;

			$video->owner_id    = $viewer->getIdentity();
			$video->type        = 'upload';
			$video->status      = 0;
			$video->duration = $video_duration;
			$video->save();
			$db->commit();
			return $video->video_id;
		} catch (Exception $e) {
			$db->rollBack();
			$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_($e->getMessage());
            // throw $e;
			return;
		}

		@unlink($response['path']);
	}

	public function validationAction() {
		$video_type = $this->_getParam('type');
		$code       = $this->_getParam('code');
		$ajax       = $this->_getParam('ajax', false);
		$valid      = false;
        // check which API should be used
		if ($video_type == "iframely") {
			$url                     = trim(strip_tags($code));
			$information             = $this->handleIframelyInformation($url);
			$this->view->code        = $code;
			$this->view->ajax        = $ajax;
			$this->view->valid       = !empty($information['code']);
		} else {

			if ($video_type == "youtube") {
				$valid = $this->checkYouTube($code);
				$type  = "youtube";
			} elseif ($video_type == "vimeo") {
				$valid = $this->checkVimeo($code);
				$type  = "vimeo";
			} elseif ($video_type == "dailymotion") {

				$valid = $this->checkDailymotion($code);
				$type  = "dailymotion";
			}
			$this->view->code        = $code;
			$this->view->ajax        = $ajax;
			$this->view->valid       = $valid;
			$code                    = $this->extractCode($code, $type);
			$this->view->information = $this->handleInformation($type, $code);
		}
		$this->_helper->layout->setLayout('default-simple');
	}

	// YouTube Functions
	public function checkYouTube($code) {
		$coreSettings = Engine_Api::_()->getApi('settings', 'core');
		$key          = $coreSettings->getSetting('sitecourse.youtube.apikey', $coreSettings->getSetting('video.youtube.apikey'));
		if (!$data = @file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=id&id=' . $code . '&key=' . $key)) {
			return false;
		}

		$data = Zend_Json::decode($data);
		if (empty($data['items'])) {
			return false;
		}

		return true;
	}

	// Vimeo Function
	public function checkVimeo($code) {
        //http://www.vimeo.com/api/docs/simple-api
        //http://vimeo.com/api/v2/video
		$data = json_decode(file_get_contents("http://vimeo.com/api/v2/video/" . $code . ".json"), true);

		$id   = count($data[0]['id']);
		if ($id == 0) {
			return false;
		}
		return true;
	}

	// Dailymotion Function
	public function checkDailymotion($code) {
		$path = "http://www.dailymotion.com/services/oembed?url=http://www.dailymotion.com/video/" . $code;
		$data = @file_get_contents($path);
		return ((is_string($data) &&
			(is_object(json_decode($data)) ||
				is_array(json_decode($data))))) ? true : false;
	}

	// HELPER FUNCTIONS
	public function extractCode($url, $type) {
		switch ($type) {
            //youtube
			case "youtube":
            // change new youtube URL to old one
			$new_code = @pathinfo($url);
			$url      = preg_replace("/#!/", "?", $url);

            // get v variable from the url
			$arr = array();
			$arr = @parse_url($url);
			if ($arr['host'] === 'youtu.be') {
				$data = explode("?", $new_code['basename']);
				$code = $data[0];
			} else {
				$parameters = $arr["query"];
				parse_str($parameters, $data);
				$code = $data['v'];
				if ($code == "") {
					$code = $new_code['basename'];
				}
			}
			return $code;
            //vimeo
			case "vimeo":
            // get the first variable after slash
			$code = @pathinfo($url);
			return isset($code['basename']) ? $code['basename'] : "";
			case "dailymotion":
            // get the first variable after slash
			$code = @pathinfo($url);
			return isset($code['basename']) ? $code['basename'] : "";
			return $url;
		}
	}

	// iframely 
	public function handleIframelyInformation($uri) {
		$iframelyDisallowHost = Engine_Api::_()->getApi('settings', 'core')->getSetting('video_iframely_disallow');
		if (parse_url($uri, PHP_URL_SCHEME) === null) {
			$uri = "http://" . $uri;
		}
		$uriHost = Zend_Uri::factory($uri)->getHost();
		if ($iframelyDisallowHost && in_array($uriHost, $iframelyDisallowHost)) {
			return;
		}
		$config   = Engine_Api::_()->getApi('settings', 'core')->core_iframely;
		$iframely = Engine_Iframely::factory($config)->get($uri);
		if (!in_array('player', array_keys($iframely['links']))) {
			return;
		}
		$information = array('thumbnail' => '', 'title' => '', 'description' => '', 'duration' => '');
		if (!empty($iframely['links']['thumbnail'])) {
			$information['thumbnail'] = $iframely['links']['thumbnail'][0]['href'];
			if (parse_url($information['thumbnail'], PHP_URL_SCHEME) === null) {
				$information['thumbnail'] = str_replace(array('://', '//'), '', $information['thumbnail']);
				$information['thumbnail'] = "http://" . $information['thumbnail'];
			}
		}
		if (!empty($iframely['meta']['title'])) {
			$information['title'] = $iframely['meta']['title'];
		}
		if (!empty($iframely['meta']['description'])) {
			$information['description'] = $iframely['meta']['description'];
		}
		if (!empty($iframely['meta']['duration'])) {
			$information['duration'] = $iframely['meta']['duration'];
		}
		$information['code'] = $iframely['html'];
		return $information;
	}

	// retrieves infromation and returns title + desc
	public function handleInformation($type, $code) {
		switch ($type) {
            //youtube
			case "youtube":
			$key  = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.youtube.apikey');
			$data = file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id=' . $code . '&key=' . $key);
			if (empty($data)) {
				return;
			}
			$data                       = Zend_Json::decode($data);
			$information                = array();

			$youtube_video              = $data['items'][0];
			$information['title']       = $youtube_video['snippet']['title'];
			$information['description'] = $youtube_video['snippet']['description'];
			$information['duration']    = Engine_Date::convertISO8601IntoSeconds($youtube_video['contentDetails']['duration']);
			
			return $information;
            //vimeo
			case "vimeo":
                //thumbnail_medium
			$data                       = json_decode(file_get_contents("http://vimeo.com/api/v2/video/" . $code . ".json"), true);

			$information                = array();
			$information['title']       = $data[0]['title'];
			$information['description'] = $data[0]['description'];
			$information['duration']    = $data[0]['duration'];
                //http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
			return $information;
            //dailymotion
			case "dailymotion":
			$path = "http://www.dailymotion.com/services/oembed?url=http://www.dailymotion.com/video/" . $code;

			$data        = @file_get_contents($path);
			$information = array();
			if (((is_string($data) && (is_object(json_decode($data)) || is_array(json_decode($data)))))) {
				$dailymotionData = Zend_Json::decode($data);

				$information['title']       = $dailymotionData['title'];
				$information['description'] = $dailymotionData['description'];
				$durationUrl                = 'https://api.dailymotion.com/video/' . $code . '?fields=duration';

				$json_duration = file_get_contents($durationUrl);
				if ($json_duration) {
					$durationDecode          = json_decode($json_duration);
					$information['duration'] = $durationDecode->duration;
				}
			}
			return $information;
			case "instagram":
			$path        = "https://api.instagram.com/oembed/?url=" . $code;
			$data        = @file_get_contents($path);
			$information = array();
			if (((is_string($data) && (is_object(json_decode($data)) || is_array(json_decode($data)))))) {
				$instagramData              = Zend_Json::decode($data);
				$information['title']       = $instagramData['title'];
				$information['description'] = "";
			}
			return $information;
			case "twitter":
			$path        = "https://api.twitter.com/1/statuses/oembed.json?id=" . $code;
			$data        = @file_get_contents($path);
			$information = array();
			if (((is_string($data) && (is_object(json_decode($data)) || is_array(json_decode($data)))))) {
				$twitterData        = Zend_Json::decode($data);
				$information['url'] = $twitterData['url'];
			}
			return $information;
		}
	}

}




?>
