<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Edit.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_Form_Admin_Consumer_Edit extends Siteapi_Form_Admin_Consumer_Create {

    public function init() {

        parent::init();

        $this->setTitle('Edit API Client');
        $this->setDescription('');

        $this->key->disable = true;
        $this->secret->disable = true;
        $this->removeElement('status');
    }

}
