<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Core.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Poll_Api_Siteapi_Core extends Core_Api_Abstract {

    /**
     * Return the "Browse Search" form. 
     * 
     * @return array
     */
    public function getBrowseSearchForm() {
        $searchForm = array();
        $viewer = Engine_Api::_()->user()->getViewer();
        $searchForm[] = array(
            'type' => 'Text',
            'name' => 'search',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search Polls')
        );

        $searchForm[] = array(
            'type' => 'Select',
            'name' => 'show',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Show'),
            'multiOptions' => array(
                '1' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone\'s Polls'),
                '2' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Only My Friends\' Polls'),
            ),
        );

        $searchForm[] = array(
            'type' => 'Select',
            'name' => 'closed',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Status'),
            'multiOptions' => array(
                '' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Polls'),
                '0' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Only Open Polls'),
                '1' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Only Closed Polls'),
            ),
        );

        $searchForm[] = array(
            'type' => 'Select',
            'name' => 'order',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Browse By'),
            'multiOptions' => array(
                'recent' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Recent'),
                'popular' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Popular'),
            ),
        );

        $searchForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search')
        );

        return $searchForm;
    }

    /**
     * Return the Create Form. 
     * 
     * @return array
     */
    public function getForm($subject = null) {
        $accountForm = array();
        $viewer = Engine_Api::_()->user()->getViewer();

        if (empty($subject)) {
            $accountForm[] = array(
                'type' => 'Text',
                'name' => 'title',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Poll Title'),
                'maxlength' => 63,
                'hasValidator' => true
            );

            $accountForm[] = array(
                'type' => 'Textarea',
                'name' => 'description',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Description'),
                'hasValidator' => true
            );

            $max_options = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.maxoptions', 15);
            for ($option = 1; $option <= $max_options; $option++) {
                $accountForm[] = array(
                    'type' => 'Text',
                    'name' => 'options_' . $option,
                    'label' => ($option == 1) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate('Possible Answers') : ''
                );
            }
        }

        $availableLabels = array(
            'everyone' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone'),
            'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
            'owner_network' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends and Networks'),
            'owner_member_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends of Friends'),
            'owner_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends Only'),
            'owner' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Just Me')
        );

        // Element: auth_view
        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('poll', $viewer, 'auth_view');
        $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
        if (!empty($viewOptions) && count($viewOptions) >= 1) {
            if (count($viewOptions) != 1) {
                $accountForm[] = array(
                    'type' => 'Select',
                    'name' => 'auth_view',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Privacy'),
                    'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who may see this poll?'),
                    'multiOptions' => $viewOptions,
                    'value' => key($viewOptions)
                );
            }
        }

        // Element: auth_comment
        $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('poll', $viewer, 'auth_comment');
        $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));
        if (!empty($commentOptions) && count($commentOptions) >= 1) {
            if (count($commentOptions) != 1) {
                $accountForm[] = array(
                    'type' => 'Select',
                    'name' => 'auth_comment',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Comment Privacy'),
                    'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who may post comments on this poll?'),
                    'multiOptions' => $commentOptions,
                    'value' => key($commentOptions)
                );
            }
        }

        $accountForm[] = array(
            'type' => 'Checkbox',
            'name' => 'search',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Show this poll in search results')
        );

        if (empty($subject))
            $label = Engine_Api::_()->getApi('Core', 'siteapi')->translate('Create Poll');
        else
            $label = Engine_Api::_()->getApi('Core', 'siteapi')->translate('Edit Poll');

        $accountForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => $label
        );

        return $accountForm;
    }

}
