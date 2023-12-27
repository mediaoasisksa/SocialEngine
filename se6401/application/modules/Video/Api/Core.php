<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Core.php 10212 2014-05-13 17:34:39Z andres $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Video_Api_Core extends Core_Api_Abstract
{
    public function getVideosPaginator($params = array())
    {
        $paginator = Zend_Paginator::factory($this->getVideosSelect($params));
        if( !empty($params['page']) )
        {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) )
        {
            $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
    }

    public function getVideosSelect($params = array())
    {
        $table = Engine_Api::_()->getDbtable('videos', 'video');
        $rName = $table->info('name');

        $tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
        $tmName = $tmTable->info('name');

        $select = $table->select()
            ->from($table->info('name'))
            ->order( !empty($params['orderby']) ? $rName.'.'.$params['orderby'].' DESC' : "$rName.creation_date DESC" );

        if( !empty($params['user_id']) && is_numeric($params['user_id']) )
        {
            $owner = Engine_Api::_()->getItem('user', $params['user_id']);
            $select = $this->getProfileItemsSelect($select, $owner);
        } elseif( !empty($params['user']) && $params['user'] instanceof User_Model_User ) {
            $owner = $params['user'];
            $select = $this->getProfileItemsSelect($select, $owner);
        } else if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('video.allow.unauthorized', 0)){
            $select = $this->getItemsSelect($select);
        }

        if( !empty($params['text']) ) {
            $searchTable = Engine_Api::_()->getDbtable('search', 'core');
            $db = $searchTable->getAdapter();
            $sName = $searchTable->info('name');
            $select
                ->joinRight($sName, $sName . '.id=' . $rName . '.video_id', null)
                ->where($sName . '.type = ?', 'video')
                ->where(new Zend_Db_Expr($db->quoteInto('MATCH(' . $sName . '.`title`, ' . $sName . '.`description`, ' . $sName . '.`keywords`, ' . $sName . '.`hidden`) AGAINST (? IN BOOLEAN MODE)', $params['text'])))
                //->order(new Zend_Db_Expr($db->quoteInto('MATCH(' . $sName . '.`title`, ' . $sName . '.`description`, ' . $sName . '.`keywords`, ' . $sName . '.`hidden`) AGAINST (?) DESC', $params['text'])))
            ;
        }

        if( !empty($params['status']) && is_numeric($params['status']) )
        {
            $select->where($rName.'.status = ?', $params['status']);
        }
        if( !empty($params['search']) && is_numeric($params['search']) )
        {
            $select->where($rName.'.search = ?', $params['search']);
        }

        if( !empty($params['category']) )
        {
            $select->where($rName.'.category_id = ?', $params['category']);
        }
        
        if( !empty($params['category_id']) )
        {
            $select->where($rName.'.category_id = ?', $params['category_id']);
        }

        if( !empty($params['subcat_id']) )
        {
            $select->where($rName.'.subcat_id = ?', $params['subcat_id']);
        }
        if( !empty($params['subsubcat_id']) )
        {
            $select->where($rName.'.subsubcat_id = ?', $params['subsubcat_id']);
        }

        if( !empty($params['tag']) )
        {
            $select
                // ->setIntegrityCheck(false)
                // ->from($rName)
                ->joinLeft($tmName, "$tmName.resource_id = $rName.video_id", NULL)
                ->where($tmName.'.resource_type = ?', 'video')
                ->where($tmName.'.tag_id = ?', $params['tag']);
        }


        $select = Engine_Api::_()->network()->getNetworkSelect($rName, $select);

        if( !empty($owner) ) {
            return $select;
        }
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('video.allow.unauthorized', 0))
            return $this->getAuthorisedSelect($select);
        else
            return $select;
    }

    public function getCategories()
    {
        $table = Engine_Api::_()->getDbTable('categories', 'video');
        $select = $table->select()
                        ->from($table->info('name'))
                        ->where('subcat_id = ?', 0)
                        ->where('subsubcat_id = ?', 0)
                        ->order('order DESC');
        return $table->fetchAll($select);
    }

    public function getCategory($category_id)
    {
        return Engine_Api::_()->getDbtable('categories', 'video')->find($category_id)->current();
    }

    // handle video upload
    public function createVideo($params, $file, $values)
    {
        if( $file instanceof Storage_Model_File ) {
            $params['file_id'] = $file->getIdentity();
        } else {
            // create video item
            $video = Engine_Api::_()->getDbtable('videos', 'video')->createRow();
            $file_ext = pathinfo($file['name']);
            $file_ext = $file_ext['extension'];
            $video->code = $file_ext;
            $video->save();

            // Store video in temporary storage object for ffmpeg to handle
            $storage = Engine_Api::_()->getItemTable('storage_file');
            $storageObject = $storage->createFile($file, array(
                'parent_id' => $video->getIdentity(),
                'parent_type' => $video->getType(),
                'user_id' => $video->owner_id,
            ));

            // Remove temporary file
            @unlink($file['tmp_name']);

            $video->file_id = $storageObject->file_id;
            $video->save();

            // Add to jobs
            Engine_Api::_()->getDbtable('jobs', 'core')->addJob('video_encode', array(
                'video_id' => $video->getIdentity(),
                'type' => 'mp4',
            ));
        }

        return $video;
    }

    public function deleteVideo($video)
    {

        // delete video ratings
        Engine_Api::_()->getDbtable('ratings', 'video')->delete(array(
            'video_id = ?' => $video->video_id,
        ));

        // check to make sure the video did not fail, if it did we wont have files to remove
        if ($video->status == 1){
            // delete storage files (video file and thumb)
            if ($video->type == 3) Engine_Api::_()->getItem('storage_file', $video->file_id)->remove();
            if ($video->photo_id) Engine_Api::_()->getItem('storage_file', $video->photo_id)->remove();
        }

        // Check activity actions
        $attachDB = Engine_Api::_()->getDbtable('attachments', 'activity');
        $actions =  $attachDB->fetchAll($attachDB->select()->where('type = ?', $video->getType())->where('id = ?',$video->getIdentity()));
        $actionsDB = Engine_Api::_()->getDbtable('actions', 'activity');

        foreach($actions as $action){
            $action_id = $action->action_id;
            $attachDB->delete(array('type = ?' => $video->getType(), 'id = ?' => $video->getIdentity()));

            $action =  $actionsDB->fetchRow($actionsDB->select()->where('action_id = ?', $action_id));
            $action->delete();
//             $count = $action->params['count'];
//             if( !is_null($count) && ($count > 1) ) {
//                 $action->params = array('count' => (integer)$count-1);
//                 $action->save();
//             }
//             else {
//                 $action->delete();
//             }
        }

        // delete activity feed and its comments/likes
        $item = Engine_Api::_()->getItem('video', $video->video_id);
        if ($item) {
            $item->delete();
        }


    }
}

