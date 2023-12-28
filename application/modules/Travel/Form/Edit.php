<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Travel
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    https://socialengine.com/eula
 * @version    $Id: Edit.php 9747 2012-07-26 02:08:08Z john $
 * @author     Donna
 */

/**
 * @category   Application_Extensions
 * @package    Travel
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    https://socialengine.com/eula
 */
class Travel_Form_Edit extends Travel_Form_Create
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


        $this->setTitle('Edit Travel Listing')
            ->setDescription('Edit your listing below, then click \"Save Changes\" to save your listing.');
        $this->addElement('Radio', 'cover', array(
            'label' => 'Album Cover',
        ));
        $this->execute->setLabel('Save Changes');
    }
}
