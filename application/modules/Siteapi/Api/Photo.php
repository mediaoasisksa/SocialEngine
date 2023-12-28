<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Feed.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_Api_Photo extends Core_Api_Abstract {

    public function setPhoto($photo, $subject) {

        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
            $fileName = $file;
        } else if ($photo instanceof Storage_Model_File) {
            $file = $photo->temporary();
            $fileName = $photo->name;
        } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
            $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
            $file = $tmpRow->temporary();
            $fileName = $tmpRow->name;
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
            $fileName = $photo['name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
            $fileName = $photo;
        } else {
            throw new User_Model_Exception('invalid argument passed to setPhoto');
        }

        if (!$fileName) {
            $fileName = $file;
        }

        $imageName = $photo['name'];
        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

        $params = array(
            'parent_type' => $subject->getType(),
            'parent_id' => $subject->getIdentity(),
            'user_id' => $subject->owner_id,
            'name' => $fileName,
        );


        // Add autorotation for uploded images. It will work only for SocialEngine-4.8.9 Or more then.
        $hasVersion = Engine_Api::_()->seaocore()->usingLessVersion('core', '4.8.9');
        if (!empty($hasVersion)) {
            $storage = Engine_Api::_()->storage();

            $image = Engine_Image::factory();
            $image->open($file)
                    ->resize(720, 720)
                    ->write($path . '/_m' . $imageName)
                    ->destroy();

            $image = Engine_Image::factory();
            $image->open($file)
                    ->resize(140, 160)
                    ->write($path . '/_in' . $imageName)
                    ->destroy();

            $image = Engine_Image::factory();
            $image->open($file)
                    ->resize(250, 250)
                    ->write($path . '/_inl' . $imageName)
                    ->destroy();
        } else {
            $storage = Engine_Api::_()->storage();

            $image = Engine_Image::factory();
            $image->open($file)
                    ->resize(720, 720)
                    ->write($path . '/_m' . $imageName)
                    ->destroy();

            $image = Engine_Image::factory();
            $image->open($file)
                    ->resize(140, 160)
                    ->write($path . '/_in' . $imageName)
                    ->destroy();
            $image = Engine_Image::factory();
            $image->open($file)
                    ->resize(250, 250)
                    ->write($path . '/_inl' . $imageName)
                    ->destroy();
        }



        //RESIZE IMAGE (ICON)

        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
                ->write($path . '/_is' . $imageName)
                ->destroy();
        try {

            $iMain = $storage->create($path . '/_m' . $imageName, $params);
            $iProfile = $storage->create($path . '/_in' . $imageName, $params);
            $iIconNormal = $storage->create($path . '/_inl' . $imageName, $params);
            $iSquare = $storage->create($path . '/_is' . $imageName, $params);

            $iMain->bridge($iProfile, 'thumb.large');
            $iMain->bridge($iIconNormal, 'thumb.normal');
            $iMain->bridge($iSquare, 'thumb.icon');
        } catch (Exception $e) {
            // Remove temp files
            @unlink($path . '/_m' . $imageName);
            @unlink($path . '/_in' . $imageName);
            @unlink($path . '/_inl' . $imageName);
            @unlink($path . '/_is' . $imageName);

            if ($e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE) {
                throw new Album_Model_Exception($e->getMessage(), $e->getCode());
            } else {
                throw $e;
            }
        }
        // Remove temp files
        @unlink($path . '/_m' . $imageName);
        @unlink($path . '/_in' . $imageName);
        @unlink($path . '/_inl' . $imageName);
        @unlink($path . '/_is' . $imageName);

        $subject->modified_date = date('Y-m-d H:i:s');
        $subject->photo_id = $iMain->file_id;
        $subject->save();
        if (!empty($tmpRow)) {
            $tmpRow->delete();
        }
        return $subject;
    }

}
?>
