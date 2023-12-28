<?php

class Sitecourse_Plugin_Job_Encode extends Core_Plugin_Job_Abstract {

    protected function _execute() {
        // Get job and params
        $job = $this->getJob();
        set_time_limit(0);
        // No video id?
        if (!($video_id = $this->getParam('video_id'))) {
            $this->_setState('failed', 'No video identity provided.');
            $this->_setWasIdle();
            return;
        }

        // Get video object
        $video = Engine_Api::_()->getItem('sitecourse_video', $video_id);
        if (!$video || !($video instanceof Sitecourse_Model_Video)) {
            $this->_setState('failed', 'Video is missing.');
            $this->_setWasIdle();
            return;
        }

        // Check video status
        if (0 != $video->status) {
            $this->_setState('failed', 'Video has already been encoded, or has already failed encoding.');
            $this->_setWasIdle();
            return;
        }

        $type = $this->getParam('type');
        $type = empty($type) ? 'flv' : $this->getParam('type');

        // Process
        try {
            $this->_process($video, $type);
            $this->_setIsComplete(true);
        } catch (Exception $e) {
            $this->_setState('failed', 'Exception: ' . $e->getMessage());

            // Attempt to set video state to failed
            try {
                if (1 != $video->status) {
                    $video->status = 3;
                    $video->save();
                }
            } catch (Exception $e) {
                $this->_addMessage($e->getMessage());
            }
        }
    }

    private function getFFMPEGPath() {
        set_time_limit(0);
        // Check we can execute
        if (!function_exists('shell_exec')) {
            throw new Sitecourse_Model_Exception('Unable to execute shell commands using shell_exec(); the function is disabled.');
        }

        if (!function_exists('exec')) {
            throw new Sitecourse_Model_Exception('Unable to execute shell commands using exec(); the function is disabled.');
        }
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');

        // Make sure FFMPEG path is set
        $ffmpeg_path = $coreSettings->getSetting('sitecourse.ffmpeg.path', $coreSettings->getSetting('sitecourse.ffmpeg.path', ''));
        if (!$ffmpeg_path) {
            throw new Sitecourse_Model_Exception('Ffmpeg not configured');
        }

        // Make sure FFMPEG can be run
        if (!@file_exists($ffmpeg_path) || !@is_executable($ffmpeg_path)) {
            $output = null;
            $return = null;
            exec($ffmpeg_path . ' -version', $output, $return);

            if ($return > 0) {
                throw new Sitecourse_Model_Exception('Ffmpeg found, but is not executable');
            }
        }

        return $ffmpeg_path;
    }

    private function getTmpDir() {
        // Check the video temporary directory
        $tmpDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' .
                DIRECTORY_SEPARATOR . 'sitecourse';

        if (!is_dir($tmpDir) && !mkdir($tmpDir, 0777, true)) {
            throw new Sitecourse_Model_Exception('Video temporary directory did not exist and could not be created.');
        }

        if (!is_writable($tmpDir)) {
            throw new Sitecourse_Model_Exception('Video temporary directory is not writable.');
        }

        return $tmpDir;
    }

    private function getVideo($video) {
        // Get the video object
        if (is_numeric($video)) {
            $video = Engine_Api::_()->getItem('sitecourse_video', $video);
        }

        if (!($video instanceof Sitecourse_Model_Video)) {
            throw new Sitecourse_Model_Exception('Argument was not a valid video');
        }

        return $video;
    }

    private function getStorageObject($video) {
        // Pull video from storage system for encoding
        $storageObject = Engine_Api::_()->getItem('storage_file', $video->file_id);

        if (!$storageObject) {
            throw new Sitecourse_Model_Exception('Video storage file was missing');
        }

        return $storageObject;
    }

    private function getOriginalPath($storageObject) {
        $originalPath = $storageObject->temporary();

        if (!file_exists($originalPath)) {
            throw new Sitecourse_Model_Exception('Could not pull to temporary file');
        }

        return $originalPath;
    }

    private function getVideoFilters($video, $width, $height) {
        $filters = "scale=$width:$height";

        if ($video->rotation > 0) {
            $filters = "pad='max(iw,ih*($width/$height))':ow/($width/$height):(ow-iw)/2:(oh-ih)/2,$filters";

            if ($video->rotation == 180)
                $filters = "hflip,vflip,$filters";
            else {
                $transpose = array(90 => 1, 270 => 2);

                if (empty($transpose[$video->rotation]))
                    throw new Sitecourse_Model_Exception('Invalid rotation value');

                $filters = "transpose=${transpose[$video->rotation]},$filters";
            }
        }

        return $filters;
    }

    private function conversionSucceeded($video, $videoOutput, $outputPath) {
        $success = true;

        // Unsupported format
        if (preg_match('/Unknown format/i', $videoOutput) ||
                preg_match('/Unsupported codec/i', $videoOutput) ||
                preg_match('/patch welcome/i', $videoOutput) ||
                preg_match('/Audio encoding failed/i', $videoOutput) ||
                !is_file($outputPath) ||
                filesize($outputPath) <= 0) {
            $success = false;
            $video->status = 3;
        }

        // This is for audio files
        else if (preg_match('/video:0kB/i', $videoOutput)) {
            $success = false;
            $video->status = 5;
        }

        return $success;
    }

    private function notifyOwner($video, $owner) {
        $translate = Zend_Registry::get('Zend_Translate');
        $language = !empty($owner->language) && $owner->language != 'auto' ? $owner->language : null;

        $notificationMessage = '';
        $exceptionMessage = 'Unknown encoding error.';

        if ($video->status == 3) {
            $exceptionMessage = 'Video format is not supported by FFMPEG.';
            $notificationMessage = 'Video conversion failed. Video format is not supported by FFMPEG. Please try %1$sagain%2$s.';
        } else if ($video->status == 5) {
            $exceptionMessage = 'Audio-only files are not supported.';
            $notificationMessage = 'Video conversion failed. Audio files are not supported. Please try %1$sagain%2$s.';
        } else if ($video->status == 7) {
            $notificationMessage = 'Video conversion failed. You may be over the site upload limit.  Try %1$suploading%2$s a smaller file, or delete some files to free up space.';
        }

        $notificationMessage = $translate->translate(sprintf($notificationMessage, '', ''), $language);

        Engine_Api::_()->getDbtable('notifications', 'activity')
                ->addNotification($owner, $owner, $video, 'sitecourse_processed_failed', array(
                    'message' => $notificationMessage,
                    'message_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'sitecourse_general', true),
        ));

        return $exceptionMessage;
    }

    private function getDuration($videoOutput) {
        $duration = 0;

        if (preg_match('/Duration:\s+(.*?)[.]/i', $videoOutput, $matches)) {
            list($hours, $minutes, $seconds) = preg_split('[:]', $matches[1]);
            $duration = ceil($seconds + ($minutes * 60) + ($hours * 3600));
        }

        return $duration;
    }

    private function generateMainThumbnail($outputPath, $output, $thumb_splice, $thumbPath, $log) {set_time_limit(0);
        $ffmpeg_path = $this->getFFMPEGPath();

        // Thumbnail process command
        $thumbCommand = $ffmpeg_path . ' '
                . '-i ' . escapeshellarg($outputPath) . ' '
                . '-f image2' . ' '
                . '-ss ' . $thumb_splice . ' '
                . '-vframes 1' . ' '
                . '-v 2' . ' '
                . '-y ' . escapeshellarg($thumbPath) . ' '
                . '2>&1';

        // Process thumbnail
        $thumbOutput = $output .
                $thumbCommand . PHP_EOL .
                shell_exec($thumbCommand);

        // Log thumb output
        if ($log) {
            $log->log($thumbOutput, Zend_Log::INFO);
        }

        // Check output message for success
        $thumbSuccess = true;
        if (preg_match('/video:0kB/i', $thumbOutput)) {
            $thumbSuccess = false;
        }
        $mainHeight = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.height', 1600);
        $mainWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.height', 1600);
        // Resize thumbnail
        if ($thumbSuccess) {
            try {
                $image = Engine_Image::factory();
                $image->open($thumbPath)
                        ->resize($mainHeight, $mainWidth)
                        ->write($thumbPath)
                        ->destroy();
            } catch (Exception $e) {
                $this->_addMessage((string) $e->__toString());
                $thumbSuccess = false;
            }
        }

        return $thumbSuccess;
    }

    private function buildVideoCmd($originalPath, $outputPath) {
        $ffmpeg_path = $this->getFFMPEGPath();

        $videoCommand = $ffmpeg_path . ' '. '-y -i '.escapeshellarg($originalPath). ' '
                        .'-vcodec copy -acodec copy -absf aac_adtstoasc '
                        .escapeshellarg($outputPath);

        return $videoCommand;
    }

    protected function _process($video, $type, $compatibilityMode = false) {
        $tmpDir = $this->getTmpDir();
        $video = $this->getVideo($video);

        // Update to encoding status
        $video->status = 2;
        $video->type = 'upload';
        $video->save();

        // Prepare information
        $owner = $video->getOwner();

        // Pull video from storage system for encoding
        $storageObject = $this->getStorageObject($video);
        $originalPath = $this->getOriginalPath($storageObject);

        $outputPath = $tmpDir . DIRECTORY_SEPARATOR . $video->getIdentity() . '_vconverted.' . $type;
        $thumbPath = $tmpDir . DIRECTORY_SEPARATOR . $video->getIdentity() . '_vnormalthumb.jpg';

        $thumbNormalLargePath = $tmpDir . DIRECTORY_SEPARATOR . $video->getIdentity() . '_vnormallargethumb.jpg';
        $thumbMainPath = $tmpDir . DIRECTORY_SEPARATOR . $video->getIdentity() . '_vmainthumb.jpg';
        $width = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.width', 1600); //480;
        $height = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.height', 1600); //386;

        $videoCommand = $this->buildVideoCmd($originalPath, $outputPath);

        // Prepare output header
        $output = PHP_EOL;
        $output .= $originalPath . PHP_EOL;
        $output .= $outputPath . PHP_EOL;
        $output .= $thumbPath . PHP_EOL;

        // Prepare logger
        $log = new Zend_Log();
        $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/sitecourse.log'));

        // Execute video encode command
        $videoOutput = $output .
                $videoCommand . PHP_EOL .
                shell_exec($videoCommand);

        // Log
        if ($log) {
            $log->log($videoOutput, Zend_Log::INFO);
        }

        // Check for failure
        $success = $this->conversionSucceeded($video, $videoOutput, $outputPath);

        // Failure
        if (!$success) {
            if (!$compatibilityMode) {
                $this->_process($video, true);
                return;
            }

            $exceptionMessage = '';

            $db = $video->getTable()->getAdapter();
            $db->beginTransaction();

            try {
                $video->save();
                $exceptionMessage = $this->notifyOwner($video, $owner);
                $db->commit();
            } catch (Exception $e) {
                $videoOutput .= PHP_EOL . $e->__toString() . PHP_EOL;

                if ($log) {
                    $log->write($e->__toString(), Zend_Log::ERR);
                }

                $db->rollBack();
            }

            // Write to additional log in dev
            if (APPLICATION_ENV == 'development') {
                file_put_contents($tmpDir . '/' . $video->video_id . '.txt', $videoOutput);
            }

            throw new Sitecourse_Model_Exception($exceptionMessage);
        }

        // Success
        else {
            // Get duration of the video to caculate where to get the thumbnail
            $duration = $this->getDuration($videoOutput);

            // Log duration
            if ($log) {
                $log->log('Duration: ' . $duration, Zend_Log::INFO);
            }

            // Fetch where to take the thumbnail
            $thumb_splice = $duration / 2;

            $thumbMainSuccess = $this->generateMainThumbnail($outputPath, $output, $thumb_splice, $thumbMainPath, $log);

            // Save video and thumbnail to storage system
            $params = array(
                'parent_id' => $video->getIdentity(),
                'parent_type' => $video->getType(),
                'user_id' => $video->owner_id
            );

            $db = $video->getTable()->getAdapter();
            $db->beginTransaction();

            try {
                $storageObject->setFromArray($params);
                $storageObject->store($outputPath);

                if ($thumbMainSuccess) {
                    $thumbMainSuccessRow = Engine_Api::_()->storage()->create($thumbMainPath, array_merge($params, array('type' => 'thumb.main')));
                }

                $thumbPath = $tmpDir . DIRECTORY_SEPARATOR . $video->getIdentity() . '_vnormalthumb.jpg';

                $thumbNormalLargePath = $tmpDir . DIRECTORY_SEPARATOR . $video->getIdentity() . '_vnormallargethumb.jpg';

                $image = Engine_Image::factory();
                $image->open($thumbMainPath)
                        ->resize(720, 720)
                        ->write($thumbNormalLargePath)
                        ->destroy();

                $image = Engine_Image::factory();
                $image->open($thumbMainPath)
                        ->resize(375, 375)
                        ->write($thumbPath)
                        ->destroy();
                Engine_Api::_()->storage()->create($thumbNormalLargePath, array_merge($params, array('type' => 'thumb.large')));
                $thumbNormalSuccessRow = Engine_Api::_()->storage()->create($thumbPath, array_merge($params, array('type' => 'thumb.normal')));

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();

                // delete the files from temp dir
                unlink($originalPath);
                unlink($outputPath);

                if ($thumbSuccess) {
                    unlink($thumbPath);
                }

                $video->status = 7;
                $video->save();

                $this->notifyOwner($video, $owner);

                throw $e; // throw
            }

            // Video processing was a success!
            // Save the information
            if ($thumbMainSuccess && empty($video->photo_id)) {
                $video->photo_id = $thumbMainSuccessRow->file_id;
            }

            $video->duration = $duration;
            $video->status = 1;
            $video->save();

            // delete the files from temp dir
            unlink($originalPath);
            unlink($outputPath);
            unlink($thumbPath);
            unlink($thumbMainPath);
            unlink($thumbNormalLargePath);
            // insert action in a separate transaction if video status is a success

        }
    }

}
