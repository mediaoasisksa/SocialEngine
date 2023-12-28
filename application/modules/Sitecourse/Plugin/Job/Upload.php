<?php

class Sitecourse_Plugin_Job_Upload extends Core_Plugin_Job_Abstract {

    protected function _execute() {

        set_time_limit(0);
        //No Youtube API KEY
        $key = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.youtube.apikey');
        if (empty($key)) {
            $this->_setState('failed', 'Youtube API key is not configured.');
            $this->_setWasIdle();
            return;
        }

        $youtubeVideos = $tempVideos = explode(',', $channel->pending_video);
        try {
            foreach ($youtubeVideos as $key => $youtubeVideo) {
                try {
                    $this->createVideo($youtubeVideo, $channel);
                    unset($tempVideos[$key]);
                    $channel->pending_video = implode(',', $tempVideos);
                    $channel->save();
                } catch (Exception $e) {
                    throw $e;
                }
            }
            $this->_setIsComplete(true);
        } catch (Exception $e) {
            $this->_setState('failed', 'Exception: ' . $e->getMessage());
            $this->_addMessage($e->getMessage());
        }
    }

    public function createVideo($youtubeVideoId, $channel) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $table = Engine_Api::_()->getDbtable('videos', 'sitecourse');
            $video = $table->createRow();
            $information = $this->handleInformation($youtubeVideoId);
            $thumbnail = $this->handleThumbnail($youtubeVideoId);
            $video->type = 'youtube';
            $video->code = $youtubeVideoId;
            if (isset($information['duration'])) {
                $video->duration = $information['duration'];
            }
            if (isset($information['description'])) {
                $video->description = $information['description'];
            }
            if (isset($information['title'])) {
                $video->title = $information['title'];
            } else {
                $video->title = $channel->title;
            }
            $video->owner_id = $channel->owner_id;
            $video->owner_type = $channel->owner_type;
            $video->save();
            $video->saveVideoThumbnail($thumbnail);
            $video->synchronized = 1;
            $video->status = 1;
            $video->main_channel_id = $channel->channel_id;

            $video->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function handleThumbnail($code = null) {
        $thumbnail = "";
        $thumbnailSize = array('maxresdefault', 'sddefault', 'hqdefault', 'mqdefault', 'default');
        foreach ($thumbnailSize as $size) {
            $thumbnailUrl = "https://i.ytimg.com/vi/$code/$size.jpg";
            $file_headers = @get_headers($thumbnailUrl);
            if (isset($file_headers[0]) && strpos($file_headers[0], '404 Not Found') == false) {
                $thumbnail = $thumbnailUrl;
                break;
            }
        }
        return $thumbnail;
    }

    // retrieves infromation and returns title + desc
    public function handleInformation($code) {
        $key = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.youtube.apikey');
        $data = file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id=' . $code . '&key=' . $key);
        if (empty($data)) {
            return;
        }
        $data = Zend_Json::decode($data);
        $information = array();
        $youtube_video = $data['items'][0];
        $information['title'] = $youtube_video['snippet']['title'];
        $information['description'] = $youtube_video['snippet']['description'];
        $information['duration'] = Engine_Date::convertISO8601IntoSeconds($youtube_video['contentDetails']['duration']);
        $information['tags'] = $youtube_video['snippet']['tags'];
        return $information;
    }

}
