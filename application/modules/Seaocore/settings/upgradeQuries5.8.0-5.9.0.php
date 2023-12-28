<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upgradeQuries.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'seaocore')
        ->where('version >= ?', '5.8.0');
$version_enabled = $select->query()->fetchObject();
if($version_enabled){
    $typeTableArray= array('engine4_album_fields_options','engine4_sitevideo_channel_fields_options','engine4_video_fields_options','engine4_sitebooking_ser_fields_options','engine4_sesjob_job_fields_options','engine4_sitecrowdfunding_project_fields_options','engine4_sitestore_store_fields_options','engine4_sitestoreproduct_review_fields_options','engine4_sitestoreproduct_product_fields_options','engine4_sitestoreproduct_fields_options','engine4_sitestoreproduct_cartproduct_fields_options','engine4_sitestoreform_fields_options','engine4_sitestaticpage_page_fields_options','engine4_siteevent_review_fields_options','engine4_siteevent_event_fields_options','engine4_siteeventdocument_document_fields_options',
        'engine4_sitereview_review_fields_options','engine4_sitereview_listing_fields_options','engine4_sitepage_page_fields_options
','engine4_sitepageoffer_offer_fields_options','engine4_sitepageform_fields_options','engine4_sitepagedocument_document_fields_options','engine4_sitegroup_group_fields_options','engine4_sitegroupoffer_offer_fields_options','engine4_sitegroupform_fields_options');
    foreach ($typeTableArray as $tableName) {
        $table_exist = $db->query("SHOW TABLES LIKE '$tableName'")->fetch();
        if (!empty($table_exist)) {
            $column_exist= $db->query("SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='$db' AND TABLE_NAME='$tableName' and column_name='type';");
            if(!empty($column_exist)){
                $db->query("ALTER TABLE `$tableName` ADD COLUMN 'type' varchar(50);");
            }
        }
    }
}
