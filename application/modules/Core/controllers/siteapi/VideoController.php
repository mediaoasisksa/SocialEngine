<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    indexController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Core_VideoController extends Siteapi_Controller_Action_Standard {

    protected $_isModule = false;


    public function init() {

        $subject_type = $this->_getParam("subject_type");
        $subject_id = $this->_getParam("subject_id");

        if(!$subject_id || !$subject_type)
            $this->respondWithValidationError("parameter_missing" , "subject_type or subject_id missing");

        $subjectTypeArray = explode("_", $subject_type);
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = 'show tables like "%'.$subjectTypeArray[0].'_videos%"';

        $result = $db->query($sql);
        $result = $result->fetchAll();

        if(empty($result))
        {
            $sql = 'show tables like "%'.$subjectTypeArray[0].'video_videos%"';
            $result = $db->query($sql);
            $result = $result->fetchAll();
            if(empty($result))
                $this->respondWithError("unauthorized", "Videos Tables Not Present in database  , Please check the Plugin");
            else
                $this->_isModule = true;
        }

        if(strstr($subjectTypeArray[0] , 'video')!=null)
            $this->_isModule= true;

        $subject = Engine_Api::_()->getItem($subject_type , $subject_id);

        if(!$subject)
            $this->respondWithError("no_record");

    }

    public function browseAction()
    {
        // Gets logged in user info
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $values = $this->_getAllParams();
        $subjectTypeArray = explode("_", $values['subject_type']);
        $subject = Engine_Api::_()->getItem($values['subject_type'] , $values['subject_id']);

        $tableName = "engine4_".$subjectTypeArray[0]."_videos";

        if($this->_isModule)
            $tableName = "engine4_".$subjectTypeArray[0]."video_videos";

        if(!isset($values['page']) || empty($values['page']))
            $values['page'] = 1;

        if(!isset($values['limit']) || empty($values['limit']))
            $values['limit'] = 10;

        $offset = ($values['page']-1)*$values['limit'];

        $select = "select * from ".$tableName." where `".$subjectTypeArray[1]."_id`='".$values['subject_id']."' and status=1 ";

        if(isset($values['orderBy']) && !empty($values['orderBy']))
            $select .= " ".$values['orderBy'];
        else
            $select .= " order by video_id desc";

        $select .= " limit ".$values['limit']." offset ".$offset;

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $result = $db->query($select);
        $result = $result->fetchAll();

        $totalItemCountSelect = "select count(*) as totalItemCount from ".$tableName." where `".$subjectTypeArray[1]."_id`='".$values['subject_id']."' and status=1";

        $totalItemCount = $db->query($totalItemCountSelect)->fetchColumn();

        $response['totalItemCount'] = $totalItemCount;

        $subject_type = $subjectTypeArray[0]."_video";

        if($this->_isModule)
            $subject_type = $subjectTypeArray[0]."video_video";

        foreach($result as $row => $video)
        {
            $video = Engine_Api::_()->getItem($subject_type , $video['video_id']);
            $browseVideo = $video->toArray();

            // Add images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($video);
            $browseVideo = array_merge($browseVideo, $getContentImages);

            // Add owner images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($video, true);
            $browseVideo = array_merge($browseVideo, $getContentImages);

            $browseVideo["owner_title"] = $video->getOwner()->getTitle();
            $isAllowedView = $video->authorization()->isAllowed($viewer, 'view');
            $browseVideo["allow_to_view"] = empty($isAllowedView) ? 0 : 1;
            $browseVideo["like_count"] = $video->likes()->getLikeCount();
            if(Engine_Api::_()->getDbtable("modules", "core")->isModuleEnabled("siteevent"))
                $browseVideo['video_url'] = Engine_Api::_()->getApi('Siteapi_Core', 'siteevent')->getVideoURL($video);
            else
                $browseVideo['video_url'] = Engine_Api::_()->getApi('Core', 'siteapi')->getVideoURL($video);

            $menus = array();
            if ($this->getRequestParam('menu', true)) {
                if (!empty($viewer_id)) {
                    if ($video->authorization()->isAllowed($viewer, 'edit')) {
                        $menus[] = array(
                            'label' => $this->translate('Edit Video'),
                            'name' => 'edit',
                            'url' => '/videogeneral/edit/',
                            'urlParams' => array(
                                'subject_id' => $video->getIdentity(),
                                'subject_type' => $video->getType(),
                            ),
                        );
                    }

                    if ($video->authorization()->isAllowed($viewer, 'delete')) {
                        $menus[] = array(
                            'label' => $this->translate('Delete Video'),
                            'name' => 'delete',
                            'url' => '/videogeneral/delete/',
                            'urlParams' => array(
                                'subject_id' => $video->getIdentity(),
                                'subject_type' => $video->getType(),
                            ),
                        );
                    }
                    if (isset($menus) && !empty($menus))
                        $browseVideo['menu'] = $menus;
                }
            $response['response'][] = $browseVideo;

        }

    }
    $this->respondWithSuccess($response);
}

    public function viewAction() {

        try{

        $this->validateRequestMethod();

        // Gets logged in user info
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $subject_type = $this->_getParam("subject_type");
        $subject_id = $this->_getParam("subject_id");

        if(!$subject_id || !$subject_type)
            $this->respondWithValidationError("parameter_missing" , "Either subject_type or subject_id missing");

        $subjectTypeArray = explode("_", $subject_type);

        if($subjectTypeArray[1]!="video")
            $this->respondWithError("unauthorized" , "Subject type doesn't Seems to be Video");

        $video = Engine_Api::_()->getItem($subject_type , $subject_id);

        if(!$video)
            $this->respondWithError("no_record");

        $value['response'] = $video->toArray();
        
        // User Rating work
        if($this->_isModule)
            $ratingTable = Engine_Api::_()->getDbtable('ratings',$subjectTypeArray[0]);
        else
            $ratingTable = Engine_Api::_()->getDbtable('videoratings',$subjectTypeArray[0]);

        $checkRated = $ratingTable->checkRated($video->video_id , $viewer_id);
        $averagerating = $ratingTable->rateVideo($video->video_id);
        $ratingCount = $ratingTable->ratingCount($video->getIdentity());
        
        $value['response']['owner_title'] = $video->getOwner()->getTitle();
        $value['response']['rating'] = $averagerating;
        $value['response']['ratingCount'] = $ratingCount;
        $ownerImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($video, true);
        $videoImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($video, false);

        // Getting viewer like or not to content.
        $value['response']["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($video);
        $value['response'] = array_merge($value['response'], $ownerImages);
        $value['response'] = array_merge($value['response'], $videoImages);

        $tempMenu = array();
        if ($video->authorization()->isAllowed($viewer, 'edit')) {

            $tempMenu[] = array(
                'label' => $this->translate('Edit Video'),
                'name' => 'edit',
                'url' => 'videogeneral/edit/',
                'urlParams' => array(
                    'subject_id' => $video->getIdentity(),
                    'subject_type' => $video->getType(),
                )
            );

            $tempMenu[] = array(
                'label' => $this->translate('Delete Video'),
                'name' => 'delete',
                'url' => 'videogeneral/delete/',
                'urlParams' => array(
                    'subject_id' => $video->getIdentity(),
                    'subject_type' => $video->getType(),
                )
            );
        }
        
        if(!$checkRated)
        {
            $tempMenu[] = array(
                'label' => $this->translate('Rating'),
                'description' => $this->translate("give rating in stars"),
                'name' => 'rating',
                'url' => 'videogeneral/rating/',
                'urlParams' => array(
                    'subject_id' => $video->getIdentity(),
                    'subject_type' => $video->getType(),
                )
            );
        }


        $tempMenu[] = array(
            'label' => $this->translate('Highlight Video'),
            'name' => 'highlight',
            'url' => 'videogeneral/highlight/',
            'urlParams' => array(
                'subject_id' => $video->getIdentity(),
                'subject_type' => $video->getType(),
            )
        );

        $tempMenu[] = array(
            'label' => $this->translate('Make Featured'),
            'name' => 'featured',
            'url' => 'videogeneral/featured/',
            'urlParams' => array(
                'subject_id' => $video->getIdentity(),
                'subject_type' => $video->getType(),
            )
        );

        // $tempMenu[] = array(
        //     'label' => $this->translate('Comment on Video'),
        //     'name' => 'comment',
        //     'url' => 'sitepage/video/comment/' . $sitepage->getIdentity() . '/' . $video->getIdentity(),
        //     'urlParams' => array(
        //     )
        // );

        // $likeTable = Engine_Api::_()->getDbtable('likes', 'core');
        // if (!$likeTable->isLike($subject, $viewer)) {
        //     $tempMenu[] = array(
        //         'label' => $this->translate('Like on Video'),
        //         'name' => 'like',
        //         'url' => 'sitepage/video/like/' . $sitepage->getIdentity() . '/' . $video->getIdentity(),
        //         'urlParams' => array(
        //         )
        //     );
        // } else {
        //     $tempMenu[] = array(
        //         'label' => $this->translate('unlike on Video'),
        //         'name' => 'unlike',
        //         'url' => 'sitepage/video/unlike/' . $sitepage->getIdentity() . '/' . $video->getIdentity(),
        //         'urlParams' => array(
        //         )
        // }

        $tempMenu[] = array(
            'name' => 'share',
            'label' => $this->translate('Share This Video'),
            'url' => 'activity/share',
            'urlParams' => array(
                "type" => $video->getType(),
                "id" => $video->getIdentity()
            )
        );

        $value['guttermenu'] = $tempMenu;

        $this->respondWithSuccess($value, true);
    }
    catch(Exception $e)
    {
        echo $e;
        die;
    }
    }

    /*
     * Edit Directory Page video
     */

    public function editAction() {
        // Gets logged in user info
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $subject_type = $this->_getParam("subject_type");
        $subject_id = $this->_getParam("subject_id");

        if(!$subject_id || !$subject_type)
            $this->respondWithValidationError("parameter_missing" , "Either subject_type or subject_id missing");

        $subjectTypeArray = explode("_", $subject_type);

        if($subjectTypeArray[1]!="video")
            $this->respondWithError("unauthorized" , "Subject type doesn't Seems to be Video");

        $video = $subject = Engine_Api::_()->getItem($subject_type , $subject_id);

        if(!$video)
            $this->respondWithError("no_record");

        if ($this->getRequest()->isGet()) {
            $form_fields = Engine_Api::_()->getApi('Siteapi_Core', 'core')->getEditForm($subject);
            $response['form'] = $form_fields;
            $response['formFields'] = $subject->toArray();
            // Get tag string from array
            //PREPARE TAGS
            $sitepageTags = $subject->tags()->getTagMaps();
            $tagString = '';
            foreach ($sitepageTags as $tagmap) {
                if ($tagString !== '') {
                    $tagString .= ', ';
                }
                $tagString .= $tagmap->getTag()->getTitle();
            }
            $response['formFields']['tags'] = $tagString;
            $this->respondWithSuccess($response);
        } elseif ($this->getRequest()->isPost()) {
            Engine_Api::_()->sitepagevideo()->setVideoPackages();

            $values = $this->_getAllParams();

            // Start form validation
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'core')->createformvalidators();
            $values['validators'] = $validators;
            $validationMessage = $this->isValid($values);

            // Response validation error
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }


            // Process
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            
            try {

                $subject->setFromArray($values);

                // Add tags
                $tags = preg_split('/[,]+/', $values['tags']);
                $subject->tags()->setTagMaps($viewer, $tags);
                $subject->save();

                $db->commit();
                $this->successResponseNoContent('no_content');
            } catch (Exception $ex) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $ex->getMessage());
            }
        }
    }
    
    /*
     * Give rating to a video
     */
    public function ratingAction()
    {
        $this->validateRequestMethod("POST");
        
        // Gets logged in user info
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $subject_type = $this->_getParam("subject_type");
        $subject_id = $this->_getParam("subject_id");

        if(!$subject_id || !$subject_type)
            $this->respondWithValidationError("parameter_missing" , "Either subject_type or subject_id missing");

        $subjectTypeArray = explode("_", $subject_type);

        if($subjectTypeArray[1]!="video")
            $this->respondWithError("unauthorized" , "Subject type doesn't Seems to be Video");

        $video = $subject = Engine_Api::_()->getItem($subject_type , $subject_id);

        if(!$video)
            $this->respondWithError("no_record");
        
        $rating = intval($this->_getParam('rating'));
        if(empty($rating) || $rating<=0 || $rating>5)
            $this->respondWithValidationError('parameter_missing', 'rating is missing or is invalid');
        
        // User Rating work
        if($this->_isModule)
            $ratingTable = Engine_Api::_()->getDbtable('ratings',$subjectTypeArray[0]);
        else
            $ratingTable = Engine_Api::_()->getDbtable('videoratings',$subjectTypeArray[0]);

        $checkrated = $ratingTable->checkRated($video->video_id , $viewer_id);
        
        if($checkrated)
            $this->respondWithValidationError('already_rated', "You have already rated this video");
        
        // Process
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                
                $ratingTable->setRating($video->video_id , $viewer_id , $rating);
                
                $db->commit();
                $this->successResponseNoContent('no_content');
                
            } catch (Exception $ex) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $ex->getMessage());
            }
        
    }

    /*
     * Delete's the video
     *
     */

    public function deleteAction() {

        $this->validateRequestMethod("DELETE");
        
        // Gets logged in user info
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $subject_type = $this->_getParam("subject_type");
        $subject_id = $this->_getParam("subject_id");

        if(!$subject_id || !$subject_type)
            $this->respondWithValidationError("parameter_missing" , "Either subject_type or subject_id missing");

        $subjectTypeArray = explode("_", $subject_type);

        if($subjectTypeArray[1]!="video")
            $this->respondWithError("unauthorized" , "Subject type doesn't Seems to be Video");

        $video = $subject = Engine_Api::_()->getItem($subject_type , $subject_id);

        if(!$video)
            $this->respondWithError("no_record");

        $db = $video->getTable()->getAdapter();
        $db->beginTransaction();

        try {

            Engine_Api::_()->getDbtable('ratings', $subjectTypeArray[0])->delete(array('video_id =?' => $video->getIdentity()));
            $video->delete();

            $db->commit();
            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithError('internal_server_error', $e->getMessage());
        }
    }

    /*
     * Make a video as featured
     */

    public function featuredAction() {
        
        // Gets logged in user info
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $subject_type = $this->_getParam("subject_type");
        $subject_id = $this->_getParam("subject_id");

        if(!$subject_id || !$subject_type)
            $this->respondWithValidationError("parameter_missing" , "Either subject_type or subject_id missing");

        $subjectTypeArray = explode("_", $subject_type);

        if($subjectTypeArray[1]!="video")
            $this->respondWithError("unauthorized" , "Subject type doesn't Seems to be Video");

        $video = $subject = Engine_Api::_()->getItem($subject_type , $subject_id);

        if(!$video)
            $this->respondWithError("no_record");

        $subject->featured = !$subject->featured;

        $subject->save();
        $this->successResponseNoContent('no_content', true);
    }

    /*
     *   Highlight a video
     */

    public function highlightAction() {
        
        // Gets logged in user info
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $subject_type = $this->_getParam("subject_type");
        $subject_id = $this->_getParam("subject_id");

        if(!$subject_id || !$subject_type)
            $this->respondWithValidationError("parameter_missing" , "Either subject_type or subject_id missing");

        $subjectTypeArray = explode("_", $subject_type);

        if($subjectTypeArray[1]!="video")
            $this->respondWithError("unauthorized" , "Subject type doesn't Seems to be Video");

        $video = $subject = Engine_Api::_()->getItem($subject_type , $subject_id);

        if(!$video)
            $this->respondWithError("no_record");

        $subject->highlighted = !$subject->highlighted;
        $subject->save();
        $this->successResponseNoContent('no_content', true);
    }

    //ACTION FOR HANDLES THUMBNAIL
    private function handleThumbnail($type, $code = null) {
        switch ($type) {
            //youtube
            case "1":
                //https://i.ytimg.com/vi/Y75eFjjgAEc/default.jpg
                return "https://i.ytimg.com/vi/$code/default.jpg";
            //vimeo
            case "2":
                //thumbnail_medium
                $data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
                $thumbnail = $data->video->thumbnail_medium;
                return $thumbnail;
        }
    }

    //ACTION FOR HANDLE INFORMATION
    private function handleInformation($type, $code) {
        switch ($type) {
            //youtube
            case "1":
                $key = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
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
                return $information;
            //vimeo
            case "2":
                //thumbnail_medium
                $data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
                $thumbnail = $data->video->thumbnail_medium;
                $information = array();
                $information['title'] = $data->video->title;
                $information['description'] = $data->video->description;
                $information['duration'] = $data->video->duration;
                //http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
                return $information;
        }
    }
}