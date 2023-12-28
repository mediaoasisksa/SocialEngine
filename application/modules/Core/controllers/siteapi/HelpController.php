<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    HelpController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Core_HelpController extends Siteapi_Controller_Action_Standard {

    /**
     * Contact-Us form
     *
     * @return array
     */
    public function contactAction() {
        if (!Zend_Registry::isRegistered('Zend_Translate'))
            Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        if ($this->getRequest()->isGet()) {
            $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'core')->getContactForm());
        } else if ($this->getRequest()->isPost()) {
            $values = array();
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'core')->getContactForm();
            foreach ($getForm as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            // START FORM VALIDATION
            $data = $values;
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'core')->getContactFormValidators();
            $data['validators'] = $validators;
            $validationMessage = $this->isValid($data);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            // Success! Process
            // Mail gets logged into database, so perform try/catch in this Controller
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                // the contact form is emailed to the first SuperAdmin by default
                $users_table = Engine_Api::_()->getDbtable('users', 'user');
                $users_select = $users_table->select()
                        ->where('level_id = ?', 1)
                        ->where('enabled >= ?', 1);
                $super_admin = $users_table->fetchRow($users_select);
                $adminEmail = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.contact');
                if (!$adminEmail) {
                    $adminEmail = $super_admin->email;
                }

                $viewer = Engine_Api::_()->user()->getViewer();

                // Make params
                $mail_settings = array(
                    'host' => $_SERVER['HTTP_HOST'],
                    'email' => $adminEmail,
                    'date' => time(),
                    'recipient_title' => $super_admin->getTitle(),
                    'recipient_link' => $super_admin->getHref(),
                    'recipient_photo' => $super_admin->getPhotoUrl('thumb.icon'),
                    'sender_title' => $values['name'],
                    'sender_email' => $values['email'],
                    'message' => $values['body'],
                    'error_report' => '',
                );

                if ($viewer && $viewer->getIdentity()) {
                    $mail_settings['sender_title'] .= ' (' . $viewer->getTitle() . ')';
                    $mail_settings['sender_email'] .= ' (' . $viewer->email . ')';
                    $mail_settings['sender_link'] = $viewer->getHref();
                }

                // send email
                Engine_Api::_()->getApi('mail', 'core')->sendSystem(
                        $adminEmail, 'core_contact', $mail_settings
                );

                // if the above did not throw an exception, it succeeded
                $db->commit();
                $this->successResponseNoContent('no_content');
            } catch (Zend_Mail_Transport_Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * Terms and Services
     *
     * @return array
     */
    public function termsAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $str = $this->translate('_CORE_TERMS_OF_SERVICE');
        if ($str == strip_tags($str)) {
            // there is no HTML tags in the text
            $str = nl2br($str);
        }

        $this->respondWithSuccess($str);
    }

    /**
     * Privacy
     *
     * @return array
     */
    public function privacyAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $str = $this->translate('_CORE_PRIVACY_STATEMENT');
        if ($str == strip_tags($str)) {
            // there is no HTML tags in the text
            $str = nl2br($str);
        }

        $this->respondWithSuccess($str);
    }

}
