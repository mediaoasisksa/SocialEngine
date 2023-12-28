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
class Forum_Api_Siteapi_Core extends Core_Api_Abstract {

    /**
     * Return the forum create form.
     * 
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
            'type' => 'Textarea',
            'name' => 'body',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Body'),
            'hasValidator' => true
        );

        $accountForm[] = array(
            'type' => 'Checkbox',
            'name' => 'watch',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Send me notifications when other members reply to this topic.'),
            'value' => 1
        );

        $accountForm[] = array(
            'type' => 'File',
            'name' => 'photo',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Add Photo')
        );

        $accountForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Post Topic')
        );

        return $accountForm;
    }

    /**
     * Set the forum uploadded image.
     *
     * @return object
     */
    public function setPhoto($photo, $subject) {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
        } else {
            throw new Event_Model_Exception('invalid argument passed to setPhoto');
        }

        $name = basename($file);
        $imageName = $photo['name'];
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_id' => $subject->getIdentity(),
            'parent_type' => 'forum_post'
        );

        // Save
        $storage = Engine_Api::_()->storage();

        // Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(2000, 2000)
                ->write($path . '/m_' . $imageName)
                ->destroy();

        // Store
        $iMain = $storage->create($path . '/m_' . $imageName, $params);

        // Remove temp files
        @unlink($path . '/m_' . $imageName);

        // Update row
        $subject->modified_date = date('Y-m-d H:i:s');
        $subject->file_id = $iMain->getIdentity();
        $subject->save();

        return $subject;
    }

}
