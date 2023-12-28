<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Music_Widget_HomePlaylistController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if( !$this->_getParam('playlist_id') ) {
      return $this->setNoRender();
    }

    $playlist = Engine_Api::_()->getItem('music_playlist', $this->_getParam('playlist_id'));
    if( !$playlist ) {
      return $this->setNoRender();
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->authorization()->isAllowed($playlist, $viewer, 'view') ) {
      return $this->setNoRender();
    }

    $this->view->playlist = $playlist;
    $this->view->owner = $owner = $playlist->getOwner();
    $this->view->viewer = $viewer;
  }

  public function adminAction()
  {
    // Check auth
    if( !Engine_Api::_()->getApi('core', 'authorization')->isAllowed('admin', null, 'view') ) {
      return $this->setNoRender();
    }
    
    $this->view->form = $form = new Music_Form_Admin_Widget_HomePlaylist();

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $this->view->values = $form->getValues();
  }
}
