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
class Blog_Api_Siteapi_Core extends Core_Api_Abstract {

    /**
     * Get the "Advanced Search" form.
     * 
     * @return array
     */
    public function getBrowseSearchForm() {
        $searchForm = array();
        $viewer = Engine_Api::_()->user()->getViewer();
        $searchForm[] = array(
            'type' => 'Text',
            'name' => 'search',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search Blogs')
        );

        $searchForm[] = array(
            'type' => 'Select',
            'name' => 'orderby',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Browse By'),
            'multiOptions' => array(
                'creation_date' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Recent'),
                'view_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Viewed'),
            )
        );

        if (Engine_Api::_()->user()->getViewer()->getIdentity()) {
            $searchForm[] = array(
                'type' => 'Select',
                'name' => 'show',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Show'),
                'multiOptions' => array(
                    '1' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone\'s Blogs'),
                    '2' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Only My Friends\' Blogs'),
                )
            );
        }

        $getCategoryArray[0] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Categories');
        $categories = Engine_Api::_()->getDbtable('categories', 'blog')->getCategoriesAssoc();
        foreach ($categories as $key => $value)
            $getCategoryArray[$key] = $value;

        if (count($categories) > 0) {
            $searchForm[] = array(
                'type' => 'Select',
                'name' => 'category',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Category'),
                'multiOptions' => $getCategoryArray
            );
        }

        $searchForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search')
        );

        return $searchForm;
    }

    /**
     * Get the "Blog Create" form.
     * 
     * @param object $subject get subject only in case of edit.
     * @return array
     */
    public function getForm($subject = null) {
        $accountForm = array();
        $viewer = Engine_Api::_()->user()->getViewer();

        $accountForm[] = array(
            'type' => 'Text',
            'name' => 'title',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Title'),
            'hasValidator' => true
        );

        $accountForm[] = array(
            'type' => 'Text',
            'name' => 'tags',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Tags (Keywords)'),
        );

        $categories = Engine_Api::_()->getDbtable('categories', 'blog')->getCategoriesAssoc();
        ksort($categories);
        if (count($categories) > 0) {
            $accountForm[] = array(
                'type' => 'Select',
                'name' => 'category_id',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Category'),
                'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($categories),
                'hasValidator' => true
            );
        }

        if (empty($subject) || (!empty($subject) && !empty($subject->draft) )) {
            $accountForm[] = array(
                'type' => 'Select',
                'name' => 'draft',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Status'),
                'multiOptions' => array(
                    0 => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Published'),
                    1 => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Saved As Draft')
                ),
                'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('If this entry is published, it cannot be switched back to draft mode.'),
                'value' => 0,
                'hasValidator' => true
            );
        }

        $accountForm[] = array(
            'type' => 'Textarea',
            'name' => 'body',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Body'),
            'hasValidator' => true
        );

        $accountForm[] = array(
            'type' => 'Checkbox',
            'name' => 'search',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Show this blog entry in search results')
        );

        $availableLabels = array(
            'everyone' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone'),
            'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
            'owner_network' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends and Networks'),
            'owner_member_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends of Friends'),
            'owner_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends Only'),
            'owner' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Just Me')
        );
        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('blog', $viewer, 'auth_view');
        $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
        if (!empty($viewOptions) && count($viewOptions) >= 1) {
            if (count($viewOptions) != 1) {
                $accountForm[] = array(
                    'type' => 'Select',
                    'name' => 'auth_view',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Privacy'),
                    'multiOptions' => $viewOptions,
                    'value' => key($viewOptions),
                    'hasValidator' => true
                );
            }
        }

        // Element: auth_comment
        $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('blog', $viewer, 'auth_comment');
        $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

        if (!empty($commentOptions) && count($commentOptions) >= 1) {
            if (count($commentOptions) != 1) {
                $accountForm[] = array(
                    'type' => 'Select',
                    'name' => 'auth_comment',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Comment Privacy'),
                    'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who may post comments on this blog entry?'),
                    'multiOptions' => $commentOptions,
                    'value' => key($commentOptions),
                    'hasValidator' => true
                );
            }
        }

        if (empty($subject)) {
            $accountForm[] = array(
                'type' => 'Submit',
                'name' => 'submit',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Post Entry')
            );
        } else {
            $accountForm[] = array(
                'type' => 'Submit',
                'name' => 'submit',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Edit Entry')
            );
        }

        return $accountForm;
    }

    /**
     * Send notifications for blog
     * 
     * @param object $blog blog object
     * @return object
     */
    public function sendNotifications(Blog_Model_Blog $blog) {
        if (!empty($blog->draft) || $blog->owner_type != 'user') {
            return $this;
        }

        // Get blog owner
        $owner = $blog->getOwner('user');

        // Get notification table
        $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
        $getSubTable = Engine_Api::_()->getDbtable('subscriptions', 'blog');

        // Get all subscribers
        $identities = $getSubTable->select()
                ->from($getSubTable, 'subscriber_user_id')
                ->where('user_id = ?', $blog->owner_id)
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);

        if (empty($identities) || count($identities) <= 0) {
            return $this;
        }

        $users = Engine_Api::_()->getItemMulti('user', $identities);

        if (empty($users) || count($users) <= 0) {
            return $this;
        }

        // Send notifications
        foreach ($users as $user) {
            Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification($user, $owner, $blog, 'blog_subscribed_new');
        }

        return $this;
    }

}
