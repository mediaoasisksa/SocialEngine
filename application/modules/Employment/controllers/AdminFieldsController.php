<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: AdminFieldsController.php 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Employment_AdminFieldsController extends Fields_Controller_AdminAbstract
{
  protected $_fieldType = 'employment';

  protected $_requireProfileType = false;

  public function indexAction()
  {
    // Make navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('employment_admin_main', array(), 'employment_admin_main_fields');

    parent::indexAction();
  }

  public function fieldCreateAction() {

    parent::fieldCreateAction();

    // remove stuff only relavent to profile questions
    $form = $this->view->form;

    if($form){
      $form->setTitle('Add Employment Question');
      $form->removeElement('show');
      $display = $form->getElement('display');
      $display->setLabel('Show on employment page?');
      $display->setOptions(array('multiOptions' => array(
          1 => 'Show on employment page',
          0 => 'Hide on employment page'
      )));

      $search = $form->getElement('search');
      $search->setLabel('Show on the search options?');
      $search->setOptions(array('multiOptions' => array(
          0 => 'Hide on the search options',
          1 => 'Show on the search options'
      )));
      $form->addElement('hidden', 'show', array('value' => 0));
    }
  }

  public function fieldEditAction() {

    parent::fieldEditAction();
    
    // remove stuff only relavent to profile questions
    $form = $this->view->form;

    if($form){
      $form->setTitle('Edit Employment Question');
      $form->removeElement('show');
      $display = $form->getElement('display');
      $display->setLabel('Show on employment page?');
      $display->setOptions(array('multiOptions' => array(
          1 => 'Show on employment page',
          0 => 'Hide on employment page'
      )));

      $search = $form->getElement('search');
      $search->setLabel('Show on the search options?');
      $search->setOptions(array('multiOptions' => array(
          0 => 'Hide on the search options',
          1 => 'Show on the search options'
      )));
      $form->addElement('hidden', 'show', array('value' => 0));
    }
  }
}
