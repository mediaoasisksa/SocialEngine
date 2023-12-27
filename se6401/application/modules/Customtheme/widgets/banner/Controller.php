<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9806 2012-10-30 23:54:12Z matthew $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Hpbblock_Widget_BannerController extends Engine_Content_Widget_Abstract
{
 
  public function indexAction()
  {
    $table = Engine_Api::_()->getDbTable('banners', 'hpbblock');
    $params = array(
      'limit' => $this->_getParam('limit', 10),
      'page' => $this->_getParam('page', 1),
    );
    $this->view->paginator = $table->getBannersPaginator($params);
  }

}
