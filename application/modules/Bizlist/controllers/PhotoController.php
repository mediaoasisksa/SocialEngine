<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Bizlist
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: PhotoController.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Bizlist
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Bizlist_PhotoController extends Core_Controller_Action_Standard
{
  public function init()
  {
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
          null !== ($photo = Engine_Api::_()->getItem('bizlist_photo', $photo_id)) )
      {
        Engine_Api::_()->core()->setSubject($photo);
      }

      else if( 0 !== ($bizlist_id = (int) $this->_getParam('bizlist_id')) &&
          null !== ($bizlist = Engine_Api::_()->getItem('bizlist', $bizlist_id)) )
      {
        Engine_Api::_()->core()->setSubject($bizlist);
      }
    }

    $this->_helper->requireUser->addActionRequires(array(
      'upload',
      'upload-photo', // Not sure if this is the right
      'edit',
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'list' => 'bizlist',
      'upload' => 'bizlist',
      'view' => 'bizlist_photo',
      'edit' => 'bizlist_photo',
    ));
  }

  public function listAction()
  {
    $this->view->bizlist = $bizlist = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $group->getSingletonAlbum();

    $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $this->view->canUpload = $group->authorization()->isAllowed(null, 'photo.upload');
  }

  public function viewAction()
  {
    $this->view->photo = $photo = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $photo->getCollection();
    $this->view->group = $group = $photo->getGroup();
    $this->view->canEdit = $photo->authorization()->isAllowed(null, 'photo_edit');
  }

  public function uploadAction()
  {
    $bizlist = Engine_Api::_()->core()->getSubject();
    if( isset($_GET['ul']) ) {
      return $this->_forward('upload-photo', null, null, array('format' => 'json', 'bizlist_id'=>(int) $bizlist->getIdentity()));
    }

    if( isset($_FILES['Filedata']) ) {
      $_POST['file'] = $this->uploadPhotoAction();
    }
    //if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'photo.upload')->isValid() ) return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $bizlist = Engine_Api::_()->getItem('bizlist', (int) $bizlist->getIdentity());

    $album = $bizlist->getSingletonAlbum();

    $this->view->bizlist_id = $bizlist->bizlist_id;
    $this->view->form = $form = new Bizlist_Form_Photo_Upload();
    $form->file->setAttrib('data', array('bizlist_id' => $bizlist->getIdentity()));

    if( !$this->getRequest()->isPost() )
    {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process
    $table = Engine_Api::_()->getItemTable('bizlist_photo');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $values = $form->getValues();
      $params = array(
        'bizlist_id' => $bizlist->getIdentity(),
        'user_id' => $viewer->getIdentity(),
      );

      // Add action and attachments
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $bizlist, 'bizlist_photo_upload', null, array('count' => engine_count($values['file'])));

      // Do other stuff
      $count = 0;
      foreach( $values['file'] as $photo_id )
      {
        $photo = Engine_Api::_()->getItem("bizlist_photo", $photo_id);
        if( !($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity() ) continue;

        /*
        if( $set_cover )
        {
          $album->photo_id = $photo_id;
          $album->save();
          $set_cover = false;
        }
        */

        $photo->collection_id = $album->album_id;
        $photo->album_id = $album->album_id;
        $photo->save();

        if ($bizlist->photo_id == 0) {
          $bizlist->photo_id = $photo->file_id;
          $bizlist->save();
        }

        if( $action instanceof Activity_Model_Action && $count < 100 ) {
          $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
        }
        $count++;
      }

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }


    $this->_redirectCustom($bizlist);
  }

  public function uploadPhotoAction()
  {
    $bizlistId = (int) $this->_getParam('bizlist_id');
    if( empty($bizlistId) ) {
      $bizlist = Engine_Api::_()->core()->getSubject();
    } else {
      $bizlist = Engine_Api::_()->getItem('bizlist', $bizlistId);
    }

    if( !$this->_helper->requireUser()->checkRequire() ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    if (empty($_FILES['file'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
      return;
    }

    $photoTable = Engine_Api::_()->getDbtable('photos', 'bizlist');
    $db = $photoTable->getAdapter();
    $db->beginTransaction();

    try {
      $viewer = Engine_Api::_()->user()->getViewer();
      $album = $bizlist->getSingletonAlbum();

      $params = array(
        // We can set them now since only one album is allowed
        'collection_id' => $album->getIdentity(),
        'album_id' => $album->getIdentity(),

        'bizlist_id' => $bizlist->getIdentity(),
        'user_id' => $viewer->getIdentity(),
      );
      
      $photoId = Engine_Api::_()->bizlist()->createPhoto($params, $_FILES['file'])->photo_id;

      // $photo = $photoTable->createRow();
      // $photo->setFromArray($params);
      // $photo->save();
      // 
      // $photo->setPhoto($_FILES['Filedata']);
      // 
      // $photo_id = $photo->photo_id;

      if( !$bizlist->photo_id ) {
        $bizlist->photo_id = $photoId;
        $bizlist->save();
      }

      $this->view->status = true;
      $this->view->name = $_FILES['file']['name'];
      $this->view->photo_id = $photoId;

      $db->commit();

      $this->sendJson([
        'id' => $photoId,
        'fileName' => $_FILES['file']['name']
      ]);
    } catch( Exception $e ) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      // throw $e;
      return;
    }
  }

  public function editAction()
  {
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'photo_edit')->isValid() ) return;

    $photo = Engine_Api::_()->core()->getSubject();

    $this->view->form = $form = new Bizlist_Form_Photo_Edit();

    if( !$this->getRequest()->isPost() )
    {
      $form->populate($photo->toArray());
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('photos', 'group')->getAdapter();
    $db->beginTransaction();

    try
    {
      $photo->setFromArray($form->getValues())->save();

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
      'messages' => array('Changes saved'),
      'layout' => 'default-simple',
      'parentRefresh' => true,
      'closeSmoothbox' => true,
    ));
  }

  public function removeAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    $photo_id= (int) $this->_getParam('photo_id');
    $photo = Engine_Api::_()->getItem('bizlist_photo', $photo_id);

    $db = $photo->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      $photo->delete();

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
  }


}
