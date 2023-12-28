<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: IndexController.php 9878 2013-02-13 03:18:43Z shaun $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Music_IndexController extends Core_Controller_Action_Standard
{
  public function init()
  {
    // Check auth
    if( !$this->_helper->requireAuth()->setAuthParams('music_playlist', null, 'view')->isValid()) {
      return;
    }

    // Get viewer info
    $this->view->viewer     = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id  = Engine_Api::_()->user()->getViewer()->getIdentity();
  }
  
  public function rateAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $user_id = $viewer->getIdentity();

    $rating = $this->_getParam('rating');
    $music_id =  $this->_getParam('resource_id');


    $table = Engine_Api::_()->getDbtable('ratings', 'music');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
        Engine_Api::_()->getDbtable('ratings', 'music')->setRating($music_id, $user_id, $rating);

        $music = Engine_Api::_()->getItem('music_playlist', $music_id);
        $music->rating = Engine_Api::_()->getDbtable('ratings', 'music')->getRating($music->getIdentity());
        $music->save();
        
        $owner = Engine_Api::_()->getItem('user', $music->owner_id);
        if($owner->user_id != $user_id)
        Engine_Api::_()->getDbTable('notifications', 'activity')->addNotification($owner, $viewer, $music, 'music_rating');
        
        $db->commit();
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }

    $total = Engine_Api::_()->getDbtable('ratings', 'music')->ratingCount($music->getIdentity());

    $data = array();
    $data[] = array(
        'total' => $total,
        'rating' => $rating,
    );
    return $this->_helper->json($data);
  }
    
  public function browseAction()
  {
    // Can create?
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('music_playlist', null, 'create');

    // Get browse params
    $this->view->formFilter = $formFilter = new Music_Form_Search();
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    } else {
      $values = array();
    }
    $this->view->formValues = array_filter($values);

    // Show
    $viewer = Engine_Api::_()->user()->getViewer();
    if( @$values['show'] == 2 && $viewer->getIdentity() ) {
      // Get an array of friend ids
      $values['users'] = $viewer->membership()->getMembershipsOfIds();
      $values['searchBit'] = 1;
    }
    unset($values['show']);

    // Get paginator
    $this->view->paginator = $paginator = Engine_Api::_()->music()->getPlaylistPaginator($values);
    $paginator->setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('music.playlistsperpage', 10));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Render
    $this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;
  }
  
  public function manageAction()
  {

    // only members can manage music
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }

    // Render
    $this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;

    // Can create?
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('music_playlist', null, 'create');
    
    // Get browse params
    $this->view->formFilter = $formFilter = new Music_Form_Search();
    $formFilter->removeElement('show');
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    } else {
      $values = array();
    }
    $this->view->formValues = array_filter($values);

    // Get paginator
    $values['user'] = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->paginator = $paginator = Engine_Api::_()->music()->getPlaylistPaginator($values);
    $paginator->setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('music.playlistsperpage', 10));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
  }

  public function createAction()
  {
    // only members can upload music
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireAuth()->setAuthParams('music_playlist', null, 'create')->isValid() ) {
      return;
    }

    $this->_helper->content
        // ->setNoRender()
        ->setEnabled()
        ;

    // catch uploads from FLASH fancy-uploader and redirect to uploadSongAction()
    if( $this->getRequest()->getQuery('ul', false) ) {
      return $this->_forward('upload', 'song', null, array('format' => 'json'));
    }
    
    $this->view->category_id = (isset($_POST['category_id']) && $_POST['category_id'] != 0) ? $_POST['category_id'] : 0;
    $this->view->subcat_id = (isset($_POST['subcat_id']) && $_POST['subcat_id'] != 0) ? $_POST['subcat_id'] : 0;
    $this->view->subsubcat_id = (isset($_POST['subsubcat_id']) && $_POST['subsubcat_id'] != 0) ? $_POST['subsubcat_id'] : 0;
    
    // Get form
    $this->view->form = $form = new Music_Form_Create();
    $this->view->playlist_id = $this->_getParam('playlist_id', '0');

    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

      $itemFlood = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('music_playlist', $this->view->viewer()->level_id, 'flood');
      if(!empty($itemFlood[0])){
          //get last activity
          $tableFlood = Engine_Api::_()->getDbTable("playlists",'music');
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

    // Process
    $db = Engine_Api::_()->getDbTable('playlists', 'music')->getAdapter();
    $db->beginTransaction();
    try {
      $playlist = $this->view->form->saveValues();
      $db->commit();
    } catch( Exception $e ) {
      return $this->exceptionWrapper($e, $form, $db);
    }
    
    return $this->_helper->redirector->gotoUrl($playlist->getHref(), array('prependBase' => false));
  }
  
  public function subcategoryAction() {

    $category_id = $this->_getParam('category_id', null);
    $CategoryType = $this->_getParam('type', null);
    $selected = $this->_getParam('selected', null);
    if ($category_id) {
      $categoryTable = Engine_Api::_()->getDbtable('categories', 'music');
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
      $categoryTable = Engine_Api::_()->getDbtable('categories', 'music');
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
