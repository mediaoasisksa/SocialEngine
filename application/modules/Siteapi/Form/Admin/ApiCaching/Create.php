<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Create.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_Form_Admin_ApiCaching_Create extends Engine_Form {

    public function init() {

        $this->setTitle('API Caching')
                ->setDescription("Here, you will be able to enable caching only for GET requests coming to the API.");

        $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
        $this->addElement('Radio', 'siteapi_caching_status', array(
            'label' => 'Enable Caching',
            'description' => 'Do you want to enable caching for APIs? (Enabling caching will decrease the CPU usage of your database server, resulting in increased speed of API responses.). While some non-critical data may appear slightly out of date with caching enabled, this will usually not be noticeable to users.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onchange' => 'cacheStatus()',
        ));

        $this->addElement('Radio', 'siteapi_lifetime_status', array(
            'label' => 'Default Cache Lifetime',
            'multiOptions' => array(
                1 => 'Custom Lifetime',
                0 => 'Default Lifetime'
            ),
            'onchange' => 'cacheLifetime()',
        ));

        $this->addElement('Text', 'siteapi_caching_lifetime', array(
            'label' => 'Cache Lifetime',
            'description' => 'This determines how long the system will keep cached data before reloading it from the database server. A shorter cache lifetime causes greater database server CPU usage, however the data will be more current. We recommend starting off with 60-120 seconds.',
            'required' => true,
            'allowEmpty' => false,
        ));

    $this->addElement('Checkbox', 'flush', array(
            'label' => 'Flush cache?',
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
        ));
    }
    
    public function populate(array $currentCache)
    {
        $enabled = true;
        if (isset($currentCache['frontend']['caching'])) {
            $enabled = $currentCache['frontend']['caching'];
        }
        $this->getElement('siteapi_caching_status')->setValue($enabled);

        if (isset($currentCache['frontend']['status'])) {
            $status = $currentCache['frontend']['status'];
        } else {
            $status = 0;
        }
        
        $this->getElement('siteapi_lifetime_status')->setValue($status);
        
        if (isset($currentCache['frontend']['lifetime'])) {
            $lifetime = $currentCache['frontend']['lifetime'];
        } else {
            $lifetime = 300; // 5 minutes
        }
        
        $this->getElement('siteapi_caching_lifetime')->setValue($lifetime);
    }

}
