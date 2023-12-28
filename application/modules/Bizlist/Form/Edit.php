<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Bizlist
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Edit.php 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Bizlist
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Bizlist_Form_Edit extends Bizlist_Form_Create
{
    public $_error = array();
    protected $_item;

    public function getItem()
    {
        return $this->_item;
    }

    public function setItem(Core_Model_Item_Abstract $item)
    {
        $this->_item = $item;
        return $this;
    }

    public function init()
    {
        parent::init();


        $this->setTitle('Edit Business Listing')
            ->setDescription('Edit your listing below, then click \"Save Changes\" to save your listing.');
        $this->addElement('Radio', 'cover', array(
            'label' => 'Album Cover',
        ));
        $this->execute->setLabel('Save Changes');
    }
}
