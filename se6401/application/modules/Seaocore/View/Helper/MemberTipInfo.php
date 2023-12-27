<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MemberTipInfo.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_View_Helper_MemberTipInfo extends Zend_View_Helper_Abstract {

    public function memberTipInfo($user, $memberTipInfo, $params = array()) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $view->addHelperPath(APPLICATION_PATH . '/application/modules/Seaocore/View/Helper', 'Seaocore_View_Helper');
        ?>  
        <?php
        if (!empty($memberTipInfo) && in_array('age', $memberTipInfo)) {
            $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($user);
            if (!empty($fieldsByAlias['birthdate'])) {
                $optionId = $fieldsByAlias['birthdate']->getValue($user);
                if ($optionId) {
                    $age = floor((time() - strtotime($optionId->value)) / 31556926);
                    echo '<div class="seao_listings_stats"><i title="' . $view->translate("Age") . '" class="seao_icon_strip seao_icon seao_icon_age"></i><div class="o_hidden f_small seaocore_txt_light">' . $view->translate(array('%s year old', '%s years old', $age), $view->locale()->toNumber($age)) . '</div></div>';
                }
            }
        }

        if (!empty($memberTipInfo) && in_array('membertype', $memberTipInfo)) {
            $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($user);
            if (!empty($fieldsByAlias['profile_type'])) {
                $optionId = $fieldsByAlias['profile_type']->getValue($user);
                if ($optionId) {
                    $optionObj = Engine_Api::_()->fields()
                            ->getFieldsOptions($user)
                            ->getRowMatching('option_id', $optionId->value);
                    if ($optionObj) {
                        echo '<div class="seao_listings_stats"><i title="' . $view->translate("Member Type") . '" class="seao_icon_strip seao_icon seao_icon_member"></i><div class="o_hidden f_small seaocore_txt_light">' . $optionObj->label . '</div></div>';
                    }
                }
            }
        }
        $statistics = '';
        if (!empty($memberTipInfo) && in_array('viewCount', $memberTipInfo)) {
            $statistics .= $view->translate(array('%s view', '%s views', $user->view_count), $view->locale()->toNumber($user->view_count)) . ', ';
        }
        if (!empty($memberTipInfo) && in_array('memberCount', $memberTipInfo)) {
            $statistics .= $view->translate(array('%s friend', '%s friends', $user->member_count), $view->locale()->toNumber($user->member_count)) . ', ';
        }
        $statistics = trim($statistics);
        $statistics = rtrim($statistics, ',');
        if (!empty($statistics)) {
            echo '<div class="seao_listings_stats"><i title="' . $view->translate("Statistics") . '" class="seao_icon_strip seao_icon seao_icon_stats"></i><div class="o_hidden f_small seaocore_txt_light">' . $statistics . '</div></div>';
        }

        if (!empty($memberTipInfo) && in_array('joined', $memberTipInfo)) {
            echo '<div class="seao_listings_stats"><i title="' . $view->translate("Creation Date") . '" class="seao_icon_strip seao_icon seao_icon_time"></i><div class="o_hidden f_small seaocore_txt_light">' . $view->translate("Joined: %s", $view->timestamp($user->creation_date)) . '</div></div>';
        }

        if (!empty($memberTipInfo) && in_array('lastupdate', $memberTipInfo)) {
            if ($user->modified_date != "0000-00-00 00:00:00") {
                echo '<div class="seao_listings_stats"><i title="' . $view->translate("Last Update") . '" class="seao_icon_strip seao_icon seao_icon_date"></i><div class="o_hidden f_small seaocore_txt_light">' . $view->translate("Last Update: %s", $view->timestamp($user->modified_date)) . '</div></div>';
            } else {
                echo '<div class="seao_listings_stats"><i title="' . $view->translate("Last Update") . '" class="seao_icon_strip seao_icon seao_icon_date"></i><div class="o_hidden f_small seaocore_txt_light">' . $view->translate("Last Update: %s", $view->timestamp($user->creation_date)) . '</div></div>';
            }
        }
        if (!empty($memberTipInfo) && in_array('networks', $memberTipInfo)) {
            $select = Engine_Api::_()->getDbtable('membership', 'network')->getMembershipsOfSelect($user)->where('hide = ?', 0);
            $networks = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll($select);
            if (count($networks) > 0) {
                echo '<div class="seao_listings_stats"><i title="' . $view->translate("Network") . '" class="seao_icon_strip seao_icon seao_icon_location"></i><div class="o_hidden f_small seaocore_txt_light">' . $view->fluentList($networks) . '</div></div>';
            }
        }
        if (!empty($memberTipInfo) && in_array('profile_field', $memberTipInfo)) {
            $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
             $fieldsValueByAlias = Engine_Api::_()->fields()->getFieldsValuesByAlias($user);
            if (!empty($fieldsValueByAlias['website'])) {
                echo '<div class="seao_listings_stats"><i title="' . $view->translate("Website") . '" class="seao_icon_strip seao_icon fa fa-globe"></i><div class="o_hidden f_small seaocore_txt_light">' . $fieldsValueByAlias['website'] . '</div></div>';
            }
            if (!empty($fieldsValueByAlias['twitter'])) {
                echo '<div class="seao_listings_stats"><i title="' . $view->translate("Twitter") . '" class="seao_icon_strip seao_icon seao_icon_twitter"></i><div class="o_hidden f_small seaocore_txt_light">' . $fieldsValueByAlias['twitter'] . '</div></div>';
            }
            if (!empty($fieldsValueByAlias['facebook'])) {
                echo '<div class="seao_listings_stats"><i title="' . $view->translate("Facebook") . '" class="seao_icon_strip seao_icon seao_icon_facebook"></i><div class="o_hidden f_small seaocore_txt_light">' . $fieldsValueByAlias['facebook'] . '</div></div>';
            }
            $isEnabledSitemember = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemember');
            if($isEnabledSitemember) {
            $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($user);
            $userProfileFields = $view->userFieldValueLoop($user, $fieldStructure, array('customParams' => $memberTipInfo, 'custom_field_title' => $params['custom_field_title'], 'custom_field_heading' => $params['custom_field_heading']));
            if (!empty($userProfileFields) && strlen($userProfileFields) > 80 ) {
                echo '<div class="seao_listings_stats"><i title="' . $view->translate("Profile Fields") . '" class="seao_icon_strip seao_icon seao_icon_host"></i><div class="o_hidden f_small seaocore_txt_light">' . $userProfileFields . '</div></div>';
            }
         }
        }
    }

}
?>
