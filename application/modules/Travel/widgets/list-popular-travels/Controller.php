<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Travel
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    https://socialengine.com/eula
 * @version    $Id: Controller.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Travel
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    https://socialengine.com/eula
 */
class Travel_Widget_ListPopularTravelsController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        // Horizontal or vertical alignment
        $this->view->trAlign = $trAlign = $this->_getParam('trAlign', 0);
        // listing text length
        $this->view->trDesLength = $trDesLength = $this->_getParam('trDesLength', 300);

        // Should we consider views or comments popular?
        $this->view->popularType = $popularType = $this->_getParam('popularType', 'view_count');
        $params = array('search' => true);

        // Get paginator
        $table = Engine_Api::_()->getItemTable('travel');
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('travel.allow.unauthorized', 0)) {
            $select = $table->getItemsSelect($params);
        }else{
            $select = $table->select()->where('search =?',1);
        }
        $select->order($popularType . ' DESC');
        $select = Engine_Api::_()->network()->getNetworkSelect($table->info('name'), $select);
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('travel.allow.unauthorized', 0))
            $select = $table->getAuthorisedSelect($select);
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Hide if nothing to show
        if( $paginator->getTotalItemCount() <= 0 ) {
            return $this->setNoRender();
        }

        // Add fields view helper path
        $view = $this->view;
        $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    }
}
