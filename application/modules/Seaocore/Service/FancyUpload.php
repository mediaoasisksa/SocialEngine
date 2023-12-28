<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.Seaocores.com/license/
 * @version    $Id: Mooupload.php 2010-11-18 9:40:21Z Seaocores $
 * @author     SocialEngineAddOns
 */
class Seaocore_Service_FancyUpload extends Zend_Service_Abstract {

    /**
     * Detect if upload method is HTML5
     * @return	boolean
     */
    public static function is_HTML5_upload() {
        return empty($_FILES);
    }

    /**
     * Upload a file using HTML5
     * @param		string	Directory destination path	 	 	 	 	 
     * @param		boolean	Return response to the script	 
     * @return	array		Response
     */
    public static function HTML5_upload($destpath) {
        // Normalize path
        $destpath = self::_normalize_path($destpath);
        // Check if path exist
        if (!file_exists($destpath))
            throw new Exception('Destination Path do not exist!');

        $fileType = $_GET['accept'] ?: 'default';
        $limit = self::getMaximumUploadSize($fileType);

        // Read headers
        $response = array();
        $headers = self::_read_headers();
        $response['id'] = $_GET['X-File-Id'];
        $response['name'] = basename($_GET['X-File-Name']);
        $response['size'] = $headers['Content-Length'];
        $response['error'] = UPLOAD_ERR_OK;
        $response['finish'] = FALSE;

        // Detect upload errors
        if ($_GET['X-File-Size'] > $limit)
            $response['error'] = UPLOAD_ERR_INI_SIZE;

        // Firefox 4 sometimes sends a empty packet as last packet
        // else if ($headers['Content-Length'] == 0)
        // $response['error'] = UPLOAD_ERR_NO_FILE;
        // Is resume?
        // $flag = (bool) $_GET['X-File-Resume'] ? FILE_APPEND : 0;
        $flag = $_GET['X-File-Resume'] ? FILE_APPEND : FILE_USE_INCLUDE_PATH;
        $filePath = $destpath . $response['id'] . '-' . $response['name'];

        // Write file
        $content = file_get_contents('php://input');
        if (!empty($_GET['is_url'])) {
            $content = file_get_contents($content);
        }

        if (file_put_contents($filePath, $content, $flag) === FALSE) {
            $response['error'] = UPLOAD_ERR_CANT_WRITE;
        } else if (filesize($filePath) >= $_GET['X-File-Size']) {
            $response['finish'] = TRUE;
        }

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $view->assign($response);
        if (empty($response['finish'])) {
            self::sendJson($response);
        }

        if (empty(self::isValid($filePath))) {
            $response['error'] = 'Invalid File Type';
            @unlink($filePath);
            self::sendJson($response);
        }

        @chmod($filePath, 0777);
        $response['path'] = $filePath;
        $response['type'] = 'html5';
        return $response;
    }

    public static function HTML4_upload() {
        return array(
            'finish' => true,
            'type' => 'html4',
            'path' => reset($_FILES),
        );
    }

    /**
     * Detect the upload method and process the files uploaded
     * @param		string	Directory destination path	 	 	 	 	 
     * @param		boolean	Return response to the script	 
     * @return	array		Response
     */
    public static function upload($destpath = null) {
        if (empty($destpath)) {
            $destpath = APPLICATION_PATH . DS . 'temporary' . DS;
        }
        return self::is_HTML5_upload() ? self::HTML5_upload($destpath) : self::HTML4_upload();
    }

    /**
     * Convert to bytes a information scale
     * @param		string	Information scale
     * @return	integer	Size in bytes	 
     */
    public static function _convert_size($val) {
        $last = strtolower($val[strlen($val) - 1]);
        $val = intval(trim($val));
        switch ($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }
        return $val;
    }

    /**
     * Normalize a directory path
     * @param		string	Directory path
     * @return	string	Path normalized	 
     */
    public static function _normalize_path($path) {
        if ($path[sizeof($path) - 1] != DIRECTORY_SEPARATOR)
            $path .= DIRECTORY_SEPARATOR;
        return $path;
    }

    /**
     * Read and normalize headers
     * @return	array	 
     */
    public static function _read_headers() {
        // GetAllHeaders doesn't work with PHP-CGI
        if (function_exists('getallheaders')) {
            return getallheaders();
        }
        $headers = array();
        $headers['Content-Length'] = $_SERVER['CONTENT_LENGTH'];
        $headers['X-File-Id'] = $_SERVER['HTTP_X_FILE_ID'];
        $headers['X-File-Name'] = $_SERVER['HTTP_X_FILE_NAME'];
        $headers['X-File-Resume'] = $_SERVER['HTTP_X_FILE_RESUME'];
        $headers['X-File-Size'] = $_SERVER['HTTP_X_FILE_SIZE'];
        return $headers;
    }

    protected function sendJson($response) {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    protected function isValid($filePath) {
        if (empty($_GET['accept']) || empty(class_exists('finfo', false)) || empty(function_exists('mime_content_type')))
            return true;
        $accept = str_replace('/*', '', $_GET['accept']);
        $validator = new Zend_Validate_File_MimeType($accept);
        return $validator->isValid($filePath);
    }

    /**
     * Return maximum upload limit of a file
     * @param    upload element type
     * @return   return upload limit in bytes    
     */
    public static function getMaximumUploadSize($fileType) {
        if ($fileType == 'default') {
            $fileType = 'image';
        }
        $maxSize = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.' . $fileType . '.upload');
        $maxSize = !empty($maxSize) ? $maxSize . 'M' : ini_get('upload_max_filesize');
        $upload_limit = self::_convert_size($maxSize);
        $remainSpace = self::getRemainingQuota();
        if ($remainSpace > 0) {
            $upload_limit = min($remainSpace, $upload_limit);
        } elseif ($remainSpace < 0) {
            // SET UPLOAD LIMIT TO 1 BYTE IF REMAINING STORAGE QUOTA IS NEGATIVE - CAN NOT UPLOAD
            $upload_limit = 1;
        }
        return $upload_limit;
    }

    /**
     * Return remaining memory space of user  
     */
    public static function getRemainingQuota() {
        $user = Engine_Api::_()->user()->getViewer();
        $user_id = $user->getIdentity();
        if (empty($user_id))
            return 0;
        $level_id = $user->level_id;
        if (!empty($level_id)) {
            $space_limit = (int) Engine_Api::_()->authorization()->getPermission($level_id, 'user', 'quota');
            if (empty($space_limit))
                return 0;
            $tableStorage = Engine_Api::_()->getItemTable('storage_file');
            $tableName = $tableStorage->info('name');
            $space_used = (int) $tableStorage->select()
            ->from($tableName, new Zend_Db_Expr('SUM(size) AS space_used'))
            ->where("user_id = ?", (int) $user_id)
            ->query()->fetchColumn(0);
            return $space_limit - $space_used ;
        }
        return 0;
    }

}
