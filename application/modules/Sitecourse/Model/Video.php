<?php  
class Sitecourse_Model_Video extends Core_Model_Item_Abstract
{
	public function saveVideoThumbnail($photo) {
		
		$valid_thumb = true;
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
		} else {
			$file = $photo;
			$fileName = $photo;
			if (@GetImageSize($fileName)) {
				$valid_thumb = true;
			} else {
				$valid_thumb = false;
			}
		}
		if(empty($fileName))
			return;
		if (!$fileName) {
			$fileName = basename($file);
		}
		$params = array(
			'parent_type' => $this->getType(),
			'parent_id' => $this->getIdentity(),
			'user_id' => $this->owner_id,
			'name' => $fileName,
		);

		$thumbnail_parsed = @parse_url($fileName);
		$ext = ltrim(strrchr($fileName, '.'), '.');
		if (isset($thumbnail_parsed['path'])) {
			$ext = ltrim(strrchr($thumbnail_parsed['path'], '.'), '.');
		}
		if ($valid_thumb && $fileName && $ext && $thumbnail_parsed && in_array(strtolower($ext), array('jpg', 'jpeg', 'gif', 'png'))) {

			$file = APPLICATION_PATH . '/temporary/link_' . md5($fileName) . '.' . $ext;
			$mainPath = APPLICATION_PATH . '/temporary/link_thumb_' . md5($fileName) . '_m.' . $ext;
			$normalPath = APPLICATION_PATH . '/temporary/link_thumb_' . md5($fileName) . '_in.' . $ext;
			$largePath = APPLICATION_PATH . '/temporary/link_thumb_' . md5($fileName) . '_l.' . $ext;
            //Fetching the width and height of thumbmail
			$normalHeight = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.video.height', 375);
			$normalWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.video.width', 375);
			$largeHeight = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.video.height', 720);
			$largeWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.video.width', 720);
			$mainHeight = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.height', 1600);
			$mainWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.width', 1600);

			$src_fh = fopen($fileName, 'r');
			$tmp_fh = fopen($file, 'w');
			stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 30);
            // Resize image (main)
			if (file_exists($file)) {
				$image = Engine_Image::factory();
				$image->open($file)
				->resize($mainWidth, $mainHeight)
				->write($mainPath)
				->destroy();

                // Resize image (large)
				$image = Engine_Image::factory();
				$image->open($file)
				->resize($largeWidth, $largeHeight)
				->write($largePath)
				->destroy();

                // Resize image (normal)
				$image = Engine_Image::factory();
				$image->open($file)
				->resize($normalWidth, $normalHeight)
				->write($normalPath)
				->destroy();
			}
			$filesTable = Engine_Api::_()->getDbtable('files', 'storage');
            // Store
			$iMain = $filesTable->createFile($mainPath, $params);
			$iLarge = $filesTable->createFile($largePath, $params);
			$iNormal = $filesTable->createFile($normalPath, $params);

			$iMain->bridge($iLarge, 'thumb.large');
			$iMain->bridge($iNormal, 'thumb.normal');
			$iMain->bridge($iMain, 'thumb.main');
            // Remove temp files
			@unlink($mainPath);
			@unlink($largePath);
			@unlink($normalPath);
			$this->photo_id = $iMain->getIdentity();
			$this->save();
			return $this;
		} elseif (!$ext && $valid_thumb && $fileName && $thumbnail_parsed ) {
			
			$file = APPLICATION_PATH . '/temporary/link_' . md5($fileName) ;
			$mainPath = APPLICATION_PATH . '/temporary/link_thumb_' . md5($fileName) . '_m.'.'jpg' ;
			$normalPath = APPLICATION_PATH . '/temporary/link_thumb_' . md5($fileName) . '_in.'.'jpg';
			$largePath = APPLICATION_PATH . '/temporary/link_thumb_' . md5($fileName) . '_l.'.'jpg';
            //Fetching the width and height of thumbmail
			$normalHeight = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.video.height', 375);
			$normalWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.video.width', 375);
			$largeHeight = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.video.height', 720);
			$largeWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.video.width', 720);
			$mainHeight = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.height', 1600);
			$mainWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.width', 1600);

			$src_fh = fopen($fileName, 'r');
			$tmp_fh = fopen($file, 'w');
			stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 30);
            // Resize image (main)
			if (file_exists($file)) {
				$image = Engine_Image::factory();
				$image->open($file)
				->resize($mainWidth, $mainHeight)
				->write($mainPath)
				->destroy();

                // Resize image (large)
				$image = Engine_Image::factory();
				$image->open($file)
				->resize($largeWidth, $largeHeight)
				->write($largePath)
				->destroy();

                // Resize image (normal)
				$image = Engine_Image::factory();
				$image->open($file)
				->resize($normalWidth, $normalHeight)
				->write($normalPath)
				->destroy();
			}
			$filesTable = Engine_Api::_()->getDbtable('files', 'storage');
            // Store
			$iMain = $filesTable->createFile($mainPath, $params);
			$iLarge = $filesTable->createFile($largePath, $params);
			$iNormal = $filesTable->createFile($normalPath, $params);

			$iMain->bridge($iLarge, 'thumb.large');
			$iMain->bridge($iNormal, 'thumb.normal');
			$iMain->bridge($iMain, 'thumb.main');
            // Remove temp files
			@unlink($mainPath);
			@unlink($largePath);
			@unlink($normalPath);
			$this->photo_id = $iMain->getIdentity();
			$this->save();
			return $this;
		}
		return NULL;
	}
}
?>