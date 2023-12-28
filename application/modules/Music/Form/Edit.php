<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Edit.php 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Music_Form_Edit extends Music_Form_Create
{
    public function init()
    {
        // Init form
        parent::init();
        $this
            ->setDescription('')
            ->setAttrib('id',      'form-upload-music')
            ->setAttrib('name',    'playlist_edit')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
        ;

        // Pre-fill form values
        $this->addElement('Hidden', 'playlist_id');

        $this->submit->setLabel('Save Changes');
    }

    public function populateWithObject($playlist)
    {
        $this->setTitle('Edit Playlist');

        $element_array = array(
            'playlist_id' => $playlist->getIdentity(),
            'title'       => $playlist->getTitle(),
            'description' => $playlist->description,
            'search'      => $playlist->search,
        );

        if (Engine_Api::_()->authorization()->isAllowed('music_playlist', Engine_Api::_()->user()->getViewer(), 'allow_network'))
            $element_array['networks'] = explode(',', $playlist->networks);

        foreach ($element_array as $key => $value) {
            $this->getElement($key)->setValue($value);
        }

        // If this is THE profile playlist, hide the title/desc fields
        if( $playlist->special ) {
            $this->removeElement('title');
            $this->removeElement('description');
            $this->removeElement('search');
        }

        // AUTHORIZATIONS
        $auth = Engine_Api::_()->authorization()->context;


        $auth_view = $this->getElement('auth_view');
        if ( $auth_view ) {
            $roles = array_keys($this->_roles);
            $lowestViewer = array_pop($roles);
            foreach (array_reverse(array_keys($this->_roles)) as $role) {
                if ($auth->isAllowed($playlist, $role, 'view')) {
                    $lowestViewer = $role;
                }
            }
            $auth_view->setValue($lowestViewer);
        }

        $auth_comment = $this->getElement('auth_comment');
        if( $auth_comment ){
            $roles = array_keys($this->_roles);
            $lowestCommenter = array_pop($roles);
            foreach (array_reverse(array_keys($this->_roles)) as $role) {
                if ($auth->isAllowed($playlist, $role, 'comment')) {
                    $lowestCommenter = $role;
                }
            }
            $auth_comment->setValue($lowestCommenter);
        }
    }

    public function saveValues()
    {
        $playlist = parent::saveValues();
        $values   = $this->getValues();
        if ($playlist && $playlist->isEditable()) {
            if (isset($values['networks'])) {
                $network_privacy = 'network_'. implode(',network_', $values['networks']);
                $values['networks'] = implode(',', $values['networks']);
            }
            if( empty($values['auth_view']) ) {
                $values['auth_view'] = 'everyone';
            }
            if( empty($values['auth_comment']) ) {
                $values['auth_comment'] = 'everyone';
            }

            $playlist->title       = $values['title'];
            $playlist->description = $values['description'];
            $playlist->search      = $values['search'];
            $playlist->networks      = $values['networks'];
            $playlist->view_privacy = $values['auth_view'];
            $playlist->setFromArray($values);
            $playlist->save();

            // Rebuild privacy
            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
            foreach( $actionTable->getActionsByObject($playlist) as $action ) {
                $action->privacy = isset($values['networks'])? $network_privacy : null;
                $action->save();
                $actionTable->resetActivityBindings($action);
            }

            return $playlist;
        } else {
            return false;
        }
    }
}
