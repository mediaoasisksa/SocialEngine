<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Logo.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_Form_Admin_Widget_Logo extends Core_Form_Admin_Widget_Standard {

    public function init() {
        parent::init();

        // Set form attributes
        $this
                ->setTitle('Tip Logo')
                ->setDescription('Shows image on the tip message')
        ;

        // Get available files
        $logoOptions = array('' => 'Text-only (No logo)');
        $imageExtensions = array('gif', 'jpg', 'jpeg', 'png');

        $it = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
        foreach ($it as $file) {
            if ($file->isDot() || !$file->isFile())
                continue;
            $basename = basename($file->getFilename());
            if (!($pos = strrpos($basename, '.')))
                continue;
            $ext = strtolower(ltrim(substr($basename, $pos), '.'));
            if (!in_array($ext, $imageExtensions))
                continue;
            $logoOptions['public/admin/' . $basename] = $basename;
        }

        $this->addElement('Select', 'logo', array(
            'label' => 'Tip Logo',
            'multiOptions' => $logoOptions,
        ));

        $this->addElement('Dummy', 'dummyText', array(
            'label' => 'Please <a href="admin/siteapi/settings/tip-messages" target="_blank">click here</a>, If you want to configure tip messages.'
        ));
        $this->dummyText->getDecorator('Label')->setOptions(array('escape' => false));
    }

}
