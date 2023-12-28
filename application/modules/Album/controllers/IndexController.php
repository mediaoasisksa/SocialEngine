<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: IndexController.php 10238 2014-05-23 21:00:39Z andres $
 * @author     Sami
 */

/**
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Album_IndexController extends Core_Controller_Action_Standard
{

    public function rateAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $user_id = $viewer->getIdentity();

        $rating = $this->_getParam('rating');
        $album_id =  $this->_getParam('resource_id');
        $type =  $this->_getParam('resource_type');

        $table = Engine_Api::_()->getDbtable('ratings', 'album');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            Engine_Api::_()->getDbtable('ratings', 'album')->setRating($album_id, $user_id, $rating, $type);
            $album = Engine_Api::_()->getItem($type, $album_id);
            $album->rating = Engine_Api::_()->getDbtable('ratings', 'album')->getRating($album->getIdentity(), $type);
            $album->save();
            
            $owner = Engine_Api::_()->getItem('user', $album->owner_id);
            if($type == 'album')
              $notificationType = 'album_rating';
            else
              $notificationType = 'album_photo_rating';
            if($owner->user_id != $user_id)
            Engine_Api::_()->getDbTable('notifications', 'activity')->addNotification($owner, $viewer, $album, $notificationType);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $total = Engine_Api::_()->getDbtable('ratings', 'album')->ratingCount($album->getIdentity(), $type);

        $data = array();
        $data[] = array(
            'total' => $total,
            'rating' => $rating,
        );
        return $this->_helper->json($data);
    }
    public function browseAction()
    {
        if( !$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid() ) return;
        
        $settings = Engine_Api::_()->getApi('settings', 'core');

        // Get params
        switch($this->_getParam('sort', 'recent')) {
            case 'popular':
                $order = 'view_count';
                break;
            case 'rating':
                $order = 'rating';
                break;
            case 'recent':
            default:
                $order = 'modified_date';
                break;
        }

        $userId = $this->_getParam('user');
        $this->view->excludedLevels = $excludedLevels = array(1, 2, 3);   // level_id of Superadmin,Admin & Moderator
        $registeredPrivacy = array('everyone', 'registered');
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        if( $viewer->getIdentity() && !engine_in_array($viewer->level_id, $excludedLevels) && empty($userId) ) {
            $viewerId = $viewer->getIdentity();
            $netMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
            $this->view->viewerNetwork = $viewerNetwork = $netMembershipTable->getMembershipsOfIds($viewer);
            if( !empty($viewerNetwork) ) {
                array_push($registeredPrivacy,'owner_network');
            }

            $friendsIds = $viewer->membership()->getMembersIds();
            $friendsOfFriendsIds = $friendsIds;
            foreach( $friendsIds as $friendId ) {
                $friend = Engine_Api::_()->getItem('user', $friendId);
                $friendMembersIds = $friend->membership()->getMembersIds();
                $friendsOfFriendsIds = array_merge($friendsOfFriendsIds, $friendMembersIds);
            }
        }

        // Prepare data
        $table = Engine_Api::_()->getItemTable('album');
        if( !engine_in_array($order, $table->info('cols')) ) {
            $order = 'modified_date';
        }

        $select = $table->select();
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('album.allow.unauthorized', 0)) {
            if (!$viewer->getIdentity()) {
                $select->where("view_privacy = ?", 'everyone');
            } elseif ($userId) {
                $owner = Engine_Api::_()->getItem('user', $userId);
                if ($owner) {
                    $select = $table->getAlbumSelect(array('owner' => $owner));
                }
            } elseif (!engine_in_array($viewer->level_id, $excludedLevels)) {
                $select->Where("owner_id = ?", $viewerId)
                    ->orwhere("view_privacy IN (?)", $registeredPrivacy);
                if (!empty($friendsIds)) {
                    $select->orWhere("view_privacy = 'owner_member' AND owner_id IN (?)", $friendsIds);
                }
                if (!empty($friendsOfFriendsIds)) {
                    $select->orWhere("view_privacy = 'owner_member_member' AND owner_id IN (?)", $friendsOfFriendsIds);
                }
                if (empty($viewerNetwork) && !empty($friendsOfFriendsIds)) {
                    $select->orWhere("view_privacy = 'owner_network' AND owner_id IN (?)", $friendsOfFriendsIds);
                }

                $subquery = $select->getPart(Zend_Db_Select::WHERE);
                $select->reset(Zend_Db_Select::WHERE);
                $select->where(implode(' ', $subquery));
            }
        }
        
        $select->where("search = 1")
            ->order($order . ' DESC');
            
        if( $this->_getParam('category_id') )
        {
            $select->where('category_id = ?', $this->_getParam('category_id'));
        }

        if( $this->_getParam('subcat_id') )
        {
            $select->where('subcat_id = ?', $this->_getParam('subcat_id'));
        }
        if( $this->_getParam('subsubcat_id') )
        {
            $select->where('subsubcat_id = ?', $this->_getParam('subsubcat_id'));
        }
        
        if ($this->_getParam('search', false)) {
            $select->where('title LIKE ? OR description LIKE ?', '%'.$this->_getParam('search').'%');
        }

        $select = Engine_Api::_()->network()->getNetworkSelect($table->info('name'), $select);

        $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');
        $paginator = $this->view->paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage($settings->getSetting('album_page', 28));
        $paginator->setCurrentPageNumber( $this->_getParam('page') );

        $searchForm = new Album_Form_Search();
        $searchForm->getElement('sort')->setValue($this->_getParam('sort'));
        $searchForm->getElement('search')->setValue($this->_getParam('search'));
        $category_id = $searchForm->getElement('category_id');
        if ($category_id) {
            $category_id->setValue($this->_getParam('category_id'));
        }
        $this->view->searchParams = $searchForm->getValues();

        // Render
        $this->_helper->content
            //->setNoRender()
            ->setEnabled()
        ;
    }

    public function browsePhotosAction()
    {
        if(!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid()) {
            return;
        }

        $this->view->canCreate = $this->_helper->requireAuth()->setAuthParams('album', null, 'create')->checkRequire();
        $this->view->form = $form = new Album_Form_Photo_Search();
        if( $form->isValid($this->_getAllParams()) ) {
            $values = $form->getValues();
        } else {
            $values = array();
        }
        $this->view->formValues = array_filter($values);

        if (!empty($values['tag'])) {
            $this->view->tag = Engine_Api::_()->getItem('core_tag', $values['tag'])->text;
        }

        if (!empty($params['search'])) {
            $this->view->search = $params['search'];
        }

        switch($this->_getParam('sort', 'recent')) {
            case 'popular':
                $order = 'view_count';
                break;
            case 'rating':
                $order = 'rating';
                break;
            case 'recent':
            default:
                $order = 'modified_date';
                break;
        }

        // Prepare data
        $albumTable = Engine_Api::_()->getItemTable('album');
        $select = $albumTable->select()->from($albumTable->info('name'), 'album_id');

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('album.allow.unauthorized', 0)) {
            $excludedLevels = array(1, 2, 3);   // level_id of Superadmin,Admin & Moderator
            $registeredPrivacy = array('everyone', 'registered');
            $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
            if ($viewer->getIdentity() && !engine_in_array($viewer->level_id, $excludedLevels)) {
                $viewerId = $viewer->getIdentity();
                $netMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
                $this->view->viewerNetwork = $viewerNetwork = $netMembershipTable->getMembershipsOfIds($viewer);
                if (!empty($viewerNetwork)) {
                    array_push($registeredPrivacy, 'owner_network');
                }

                $friendsIds = $viewer->membership()->getMembersIds();
                $friendsOfFriendsIds = $friendsIds;
                foreach ($friendsIds as $friendId) {
                    $friend = Engine_Api::_()->getItem('user', $friendId);
                    $friendMembersIds = $friend->membership()->getMembersIds();
                    $friendsOfFriendsIds = array_merge($friendsOfFriendsIds, $friendMembersIds);
                }
            }


            if (!$viewer->getIdentity()) {
                $select->where("view_privacy = ?", 'everyone');
            } elseif (!engine_in_array($viewer->level_id, $excludedLevels)) {
                $select->Where("owner_id = ?", $viewerId)
                    ->orwhere("view_privacy IN (?)", $registeredPrivacy);
                if (!empty($friendsIds)) {
                    $select->orWhere("view_privacy = 'owner_member' AND owner_id IN (?)", $friendsIds);
                }
                if (!empty($friendsOfFriendsIds)) {
                    $select->orWhere("view_privacy = 'owner_member_member' AND owner_id IN (?)", $friendsOfFriendsIds);
                }
                if (empty($viewerNetwork) && !empty($friendsOfFriendsIds)) {
                    $select->orWhere("view_privacy = 'owner_network' AND owner_id IN (?)", $friendsOfFriendsIds);
                }

                $subquery = $select->getPart(Zend_Db_Select::WHERE);
                $select->reset(Zend_Db_Select::WHERE);
                $select->where(implode(' ', $subquery));
            }
        }



        $select->where("search = 1");
        $select = Engine_Api::_()->network()->getNetworkSelect($albumTable->info('name'), $select);
        $albums = $albumTable->fetchAll($select);
        $albumIds = array();
        foreach ($albums as $album) {
            $albumIds[] = $album->album_id;
        }

        $photoTable = Engine_Api::_()->getItemTable('album_photo');
        $this->view->paginator = $paginator = $photoTable->getPhotoPaginator(array_merge(
            ['album_ids' => $albumIds, 'order' => $order],
            $values
        ));
				$paginator->setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('photo_page', 12));
        $paginator->setCurrentPageNumber($this->_getParam('page'));

        // Render
        $this->_helper->content
            //->setNoRender()
            ->setEnabled()
        ;
    }


    public function manageAction()
    {
        if( !$this->_helper->requireUser()->isValid() ) return;
        if( !$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid() ) return;

        $search_form = $this->view->search_form = new Album_Form_Search();
        if ($this->getRequest()->isPost() && $search_form->isValid($this->getRequest()->getPost())) {
            $this->_helper->redirector->gotoRouteAndExit(array(
                'page'   => 1,
                'sort'   => $this->getRequest()->getPost('sort'),
                'search' => $this->getRequest()->getPost('search'),
                'category_id' => $this->getRequest()->getPost('category_id'),
            ));
        } else {
            $search_form->getElement('search')->setValue($this->_getParam('search'));
            $search_form->getElement('sort')->setValue($this->_getParam('sort'));
            if($search_form->getElement('category_id')) $search_form->getElement('category_id')->setValue($this->_getParam('category_id'));
        }

        // Render
        $this->_helper->content
            //->setNoRender()
            ->setEnabled()
        ;

        // Get params
        $this->view->page = $page = $this->_getParam('page');

        // Get params
        switch($this->_getParam('sort', 'recent')) {
            case 'popular':
                $order = 'view_count';
                break;
            case 'rating':
                $order = 'rating';
                break;
            case 'recent':
            default:
                $order = 'modified_date';
                break;
        }

        // Prepare data
        $user = Engine_Api::_()->user()->getViewer();
        $table = Engine_Api::_()->getItemTable('album');
        $tableAlbumName = $table->info('name');
        $tablePhotoName = Engine_Api::_()->getItemTable('album_photo')->info('name');

        if( !engine_in_array($order, $table->info('cols')) ) {
            $order = 'modified_date';
        }

        $select = $table->select()
            ->from($tableAlbumName)
            ->setIntegrityCheck(false)
            ->join($tablePhotoName, "$tablePhotoName.album_id = $tableAlbumName.album_id",null)
            ->where($tableAlbumName.'.owner_id = ?', $user->getIdentity())
            ->order($order . ' DESC')
            ->group($tablePhotoName.'.album_id');

        if ($this->_getParam('category_id')) $select->where($tableAlbumName.".category_id = ?", $this->_getParam('category_id'));

        if ($this->_getParam('search', false)) {
            $select->where($tableAlbumName.'.title LIKE ? OR '.$tableAlbumName.'.description LIKE ?', '%'.$this->_getParam('search').'%');
        }
        

        $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');

        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('album_page', 12));
        $paginator->setCurrentPageNumber($page);
    }



    public function uploadAction()
    {
        if( isset($_GET['ul']) )
            return $this->_forward('upload-photo', null, null, array('format' => 'json'));

        if( isset($_FILES['Filedata']) )
            $_POST['file'] = $this->uploadPhotoAction();

        if( !$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid() ) return;

        // Render
        $this->_helper->content
            //->setNoRender()
            ->setEnabled()
        ;
        
        $this->view->category_id = (isset($_POST['category_id']) && $_POST['category_id'] != 0) ? $_POST['category_id'] : 0;
        $this->view->subcat_id = (isset($_POST['subcat_id']) && $_POST['subcat_id'] != 0) ? $_POST['subcat_id'] : 0;
        $this->view->subsubcat_id = (isset($_POST['subsubcat_id']) && $_POST['subsubcat_id'] != 0) ? $_POST['subsubcat_id'] : 0;
        
        // Get form
        $this->view->form = $form = new Album_Form_Album();

        if( !$this->getRequest()->isPost() )
        {
            if( null !== ($album_id = $this->_getParam('album_id')) )
            {
                $form->populate(array(
                    'album' => $album_id
                ));
            }
            return;
        }

        if( !$form->isValid($this->getRequest()->getPost()) )
        {
            return;
        }
        $itemFlood = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('album', $this->view->viewer()->level_id, 'flood');
        if(!empty($itemFlood[0])){
            //get last activity
            $tableFlood = Engine_Api::_()->getDbTable("albums",'album');
            $select = $tableFlood->select()->where("owner_id = ?",$this->view->viewer()->getIdentity())->order("creation_date DESC");
            if($itemFlood[1] == "minute"){
                $select->where("creation_date >= DATE_SUB(NOW(),INTERVAL 1 MINUTE)");
            }else if($itemFlood[1] == "day"){
                $select->where("creation_date >= DATE_SUB(NOW(),INTERVAL 1 DAY)");
            }else{
                $select->where("creation_date >= DATE_SUB(NOW(),INTERVAL 1 HOUR)");
            }
            $floodItem = $tableFlood->fetchAll($select);
            if(engine_count($floodItem) && $itemFlood[0] <= engine_count($floodItem)){
                $message = Engine_Api::_()->core()->floodCheckMessage($itemFlood,$this->view);
                $form->addError($message);
                return;
            }
        }
        $db = Engine_Api::_()->getItemTable('album')->getAdapter();
        $db->beginTransaction();

        try
        {
            $album = $form->saveValues();

            $db->commit();
        }
        catch( Exception $e )
        {
            $db->rollBack();
            throw $e;
        }

        $this->_helper->redirector->gotoRoute(array('action' => 'editphotos', 'album_id' => $album->album_id), 'album_specific', true);
    }

    public function uploadPhotoAction()
    {
        if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid()) {
            return;
        }

        if (!$this->_helper->requireUser()->checkRequire()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        if (empty($_FILES['file'])) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
            return;
        }

        $db = Engine_Api::_()->getDbtable('photos', 'album')->getAdapter();
        $db->beginTransaction();

        try {
            $viewer = Engine_Api::_()->user()->getViewer();

            $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
            $tempId = $photoTable->uploadTemPhoto($_FILES['file']);
            if(!$tempId){
                 $this->view->status = false;
            } else {
                 $this->view->status = true;
            }
            $this->view->name = $_FILES['file']['name'];
            $this->view->photo_id = $tempId;
            $db->commit();

            $this->sendJson([
                'id' => $tempId,
                'fileName' => $_FILES['file']['name']
            ]);
        } catch (Album_Model_Exception $e) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->name = $_FILES['file']['name'];
            $this->view->error = $this->view->translate($e->getMessage());
            return;
        } catch (Exception $e) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->name = $_FILES['file']['name'];
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
            return;
        }
    }
    
    public function subcategoryAction() {

      $category_id = $this->_getParam('category_id', null);
      $CategoryType = $this->_getParam('type', null);
      $selected = $this->_getParam('selected', null);
      if ($category_id) {
        $categoryTable = Engine_Api::_()->getDbtable('categories', 'album');
        $category_select = $categoryTable->select()
                                        ->from($categoryTable->info('name'))
                                        ->where('subcat_id = ?', $category_id);
        $subcategory = $categoryTable->fetchAll($category_select);
        $count_subcat = engine_count($subcategory->toarray());

        $data = '';
        if ($subcategory && $count_subcat) {
          if ($CategoryType == 'search') {
            $data .= '<option value="0">' . Zend_Registry::get('Zend_Translate')->_("Choose 2nd Level Category") . '</option>';
            foreach ($subcategory as $category) {
              $data .= '<option ' . ($selected == $category['category_id'] ? 'selected = "selected"' : '') . ' value="' . $category["category_id"] . '" >' . Zend_Registry::get('Zend_Translate')->_($category["category_name"]) . '</option>';
            }
          } else {
            $data .= '<option value=""></option>';
            foreach ($subcategory as $category) {
              $data .= '<option ' . ($selected == $category['category_id'] ? 'selected = "selected"' : '') . ' value="' . $category["category_id"] . '" >' . Zend_Registry::get('Zend_Translate')->_($category["category_name"]) . '</option>';
            }

          }
        }
      } else
        $data = '';
      echo $data;die;
    }

    public function subsubcategoryAction() {

      $category_id = $this->_getParam('subcategory_id', null);
      $CategoryType = $this->_getParam('type', null);
      $selected = $this->_getParam('selected', null);
      if ($category_id) {
        $categoryTable = Engine_Api::_()->getDbtable('categories', 'album');
        $category_select = $categoryTable->select()
          ->from($categoryTable->info('name'))
          ->where('subsubcat_id = ?', $category_id);
        $subcategory = $categoryTable->fetchAll($category_select);
        $count_subcat = engine_count($subcategory->toarray());

        $data = '';
        if ($subcategory && $count_subcat) {
          $data .= '<option value=""></option>';
          foreach ($subcategory as $category) {
            $data .= '<option ' . ($selected == $category['category_id'] ? 'selected = "selected"' : '') . ' value="' . $category["category_id"] . '">' . Zend_Registry::get('Zend_Translate')->_($category["category_name"]) . '</option>';
          }

        }
      } else
        $data = '';
      echo $data;
      die;
    }
}
