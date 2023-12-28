<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Elpis
 * @copyright  Copyright 2006-2022 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: IndexController.php 2022-06-21
 */

class Elpis_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    $this->view->someVar = 'someVal';
  }
  function fontAction(){
      if(!engine_count($_POST)){
          echo false;die;
      }
      $font = $this->_getParam('size','');
      $_SESSION['font_theme'] = $font;
      echo true;die;
  }
    function modeAction(){
        if(!engine_count($_POST)){
            echo false;die;
        }
        $font = $this->_getParam('mode','');
        $_SESSION['mode_theme'] = $font;
        echo true;die;
    }
}
