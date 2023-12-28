<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Travel
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    https://socialengine.com/eula
 * @version    $Id: Controller.php 9791 2016-12-08 20:41:41Z pamela $
 * @author     Sami
 */

/**
 * @category   Application_Extensions
 * @package    Travel
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    https://socialengine.com/eula
 */
class Travel_Widget_ListCategoriesController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $this->view->categories = Engine_Api::_()->getDbTable('categories', 'travel')->getCategory();
    if (engine_count($this->view->categories) <= 0)
      return $this->setNoRender();
  }
}
