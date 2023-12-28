<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Core_Api_Core extends Core_Api_Abstract
{
    /**
     * @var Core_Model_Item_Abstract|mixed The object that represents the subject of the page
     */
    protected $_subject;

    /**
     * Set the object that represents the subject of the page
     *
     * @param Core_Model_Item_Abstract|mixed $subject
     * @return Core_Api_Core
     */
    public function setSubject($subject)
    {
        if( null !== $this->_subject ) {
            throw new Core_Model_Exception("The subject may not be set twice");
        }

        if( !($subject instanceof Core_Model_Item_Abstract) ) {
            throw new Core_Model_Exception("The subject must be an instance of Core_Model_Item_Abstract");
        }

        $this->_subject = $subject;
        return $this;
    }

    /**
     * Get the previously set subject of the page
     *
     * @return Core_Model_Item_Abstract|null
     */
    public function getSubject($type = null)
    {
        if( null === $this->_subject ) {
            throw new Core_Model_Exception("getSubject was called without first setting a subject.  Use hasSubject to check");
        } else if( is_string($type) && $type !== $this->_subject->getType() ) {
            throw new Core_Model_Exception("getSubject was given a type other than the set subject");
        } else if( is_array($type) && !engine_in_array($this->_subject->getType(), $type) ) {
            throw new Core_Model_Exception("getSubject was given a type other than the set subject");
        }

        return $this->_subject;
    }

    /**
     * Checks if a subject has been set
     *
     * @return bool
     */
    public function hasSubject($type = null)
    {
        if( null === $this->_subject ) {
            return false;
        } else if( null === $type ) {
            return true;
        } else {
            return ( $type === $this->_subject->getType() );
        }
    }

    public function clearSubject()
    {
        $this->_subject = null;
        return $this;
    }

    public function getCaptchaOptions(array $params = array())
    {
        $spamSettings = Engine_Api::_()->getApi('settings', 'core')->core_spam;
        $recaptchaVersionSettings = Engine_Api::_()->getApi('settings', 'core')->core_spam_recaptcha_version;
        
        if(empty($spamSettings['recaptchapublic']) && empty($spamSettings['recaptchaprivate']) && empty($spamSettings['recaptchapublicv3']) && empty($spamSettings['recaptchaprivatev3'])) {
            // Image captcha
            return array_merge(array(
                'label' => 'Human Verification',
                'description' => 'Please type the characters you see in the image.',
                'captcha' => 'image',
                'required' => true,
                'captchaOptions' => array(
                    'wordLen' => 6,
                    'fontSize' => '30',
                    'timeout' => 300,
                    'imgDir' => APPLICATION_PATH . '/public/temporary/',
                    'imgUrl' => Zend_Registry::get('Zend_View')->baseUrl() . '/public/temporary',
                    'font' => APPLICATION_PATH . '/application/modules/Core/externals/fonts/arial.ttf',
                ),
            ), $params);
        } else if($recaptchaVersionSettings == 1 && $spamSettings['recaptchaprivate'] && $spamSettings['recaptchapublic']) {
            // Recaptcha v2
            return array_merge(array(
                'label' => 'Human Verification',
                'captcha' => 'ReCaptcha2',
                'required' => true,
                'captchaOptions' => array(
                    'privkey' => $spamSettings['recaptchaprivate'],
                    'pubkey' => $spamSettings['recaptchapublic'],
                    'theme' => 'light',
                    'size' => (isset($params['size']) ? $params['size'] : 'normal' ),
                    'lang' => Zend_Registry::get('Locale')->getLanguage(),
                    'tabindex' => (isset($params['tabindex']) ? $params['tabindex'] : null ),
                    'ssl' => constant('_ENGINE_SSL'),   // Fixed Captcha does not work well when ssl is enabled on website
                    //'onload' => 'en4CoreReCaptcha',
                    //'render' => 'explicit',
                    //'loadJs' => 'en4.core.reCaptcha.loadJs'
                ),
            ), $params);
        } else if($recaptchaVersionSettings == 0  && $spamSettings['recaptchaprivatev3'] && $spamSettings['recaptchapublicv3']) {
            $script = "scriptJquery(document).ready(function() {
            scriptJquery('#captcha-wrapper').hide();
              scriptJquery('<input>').attr({ 
                  name: 'recaptcha_response', 
                  id: 'recaptchaResponse', 
                  type: 'hidden', 
              }).appendTo('.global_form'); 
            });";
            $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
            $view->headScript()->appendScript($script);
            
            // Recaptcha v3
            return array_merge(array(
                //'label' => 'Human Verification',
                'captcha' => 'ReCaptcha3',
                'required' => true,
                'captchaOptions' => array(
                    'privkey' => $spamSettings['recaptchaprivatev3'],
                    'pubkey' => $spamSettings['recaptchapublicv3'],
                    //'theme' => 'light',
                    //'size' => (isset($params['size']) ? $params['size'] : 'normal' ),
                    //'lang' => Zend_Registry::get('Locale')->getLanguage(),
                    //'tabindex' => (isset($params['tabindex']) ? $params['tabindex'] : null ),
                    'ssl' => constant('_ENGINE_SSL'),   // Fixed Captcha does not work well when ssl is enabled on website
                    //'onload' => 'en4CoreReCaptchaV3',
                    //'render' => 'explicit',
                    //'loadJs' => 'en4.core.reCaptcha.loadJs'
                ),
            ), $params);
        }
    }

    public function smileyToEmoticons($string = null)
    {
        $emoticonsTag = Engine_Api::_()->activity()->getEmoticons(true);
        if (empty($emoticonsTag)) {
            return $string;
        }

        $string = str_replace("&lt;:o)", "<:o)", $string);
        $string = str_replace("(&amp;)", "(&)", $string);

        return strtr($string, $emoticonsTag);
    }
    public  function floodCheckMessage($data = array()){
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        if(engine_count($data)){
            $duration = $data[0];
            $type = $data[1];
            //$time = $duration.' '.($duration == 1 ? $type : $type."s");
            $time =  "1 ".$type;
            return $view->translate('You have reached maximum limit of posting in %s. Try again after this duration expires.',$time);
        }
        return "";
    }
    public function clearLogs() {

      $logfileSize = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.logfile.size', 50);

      if(file_exists(APPLICATION_PATH . '/temporary/log/main.log')) {
        $logSize = filesize(APPLICATION_PATH . '/temporary/log/main.log');
        $logSize = number_format($logSize / 1048576, 2);
        if($logfileSize < $logSize) {
            file_put_contents(APPLICATION_PATH . '/temporary/log/main.log', '');
        }
      }

      if(file_exists(APPLICATION_PATH . '/temporary/log/warnings.log')) {
        $logSize = filesize(APPLICATION_PATH . '/temporary/log/warnings.log');
        $logSize = number_format($logSize / 1048576, 2);
        if($logfileSize < $logSize) {
            file_put_contents(APPLICATION_PATH . '/temporary/log/warnings.log', '');
        }
      }

      if(file_exists(APPLICATION_PATH . '/temporary/log/install.log')) {
        $logSize = filesize(APPLICATION_PATH . '/temporary/log/install.log');
        $logSize = number_format($logSize / 1048576, 2);
        if($logfileSize < $logSize) {
            file_put_contents(APPLICATION_PATH . '/temporary/log/install.log', '');
        }
      }

      if(file_exists(APPLICATION_PATH . '/temporary/log/task.log')) {
        $logSize = filesize(APPLICATION_PATH . '/temporary/log/task.log');
        $logSize = number_format($logSize / 1048576, 2);
        if($logfileSize < $logSize) {
            file_put_contents(APPLICATION_PATH . '/temporary/log/task.log', '');
        }
      }

      if(file_exists(APPLICATION_PATH . '/temporary/log/translate.log')) {
        $logSize = filesize(APPLICATION_PATH . '/temporary/log/translate.log');
        $logSize = number_format($logSize / 1048576, 2);
        if($logfileSize < $logSize) {
            file_put_contents(APPLICATION_PATH . '/temporary/log/translate.log', '');
        }
      }
    }
    public function getFileUrl($image) {
        $table = Engine_Api::_()->getDbTable('files', 'core');
        $result = $table->select()
                    ->from($table->info('name'), 'storage_file_id')
                    ->where('storage_path =?', $image)
                    ->query()
                    ->fetchColumn();
        if(!empty($result)) {
          $storage = Engine_Api::_()->getItem('storage_file', $result);
          return $storage->map();
        } else {
          return $image;
        }
    }
    public function isMobile()
    {
        // No UA defined?
        if( !isset($_SERVER['HTTP_USER_AGENT']) ) {
          return false;
        }

        // Windows is (generally) not a mobile OS
        if( false !== stripos($_SERVER['HTTP_USER_AGENT'], 'windows') &&
            false === stripos($_SERVER['HTTP_USER_AGENT'], 'windows phone os')) {
          return false;
        }

        // Sends a WAP profile header
        if( isset($_SERVER['HTTP_PROFILE']) ||
            isset($_SERVER['HTTP_X_WAP_PROFILE']) ) {
          return true;
        }

        // Accepts WAP as a valid type
        if( isset($_SERVER['HTTP_ACCEPT']) &&
            false !== stripos($_SERVER['HTTP_ACCEPT'], 'application/vnd.wap.xhtml+xml') ) {
          return true;
        }

        // Is Opera Mini
        if( isset($_SERVER['ALL_HTTP']) &&
            false !== stripos($_SERVER['ALL_HTTP'], 'OperaMini') ) {
          return true;
        }

        if( preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android)/i', $_SERVER['HTTP_USER_AGENT']) ) {
          return true;
        }

        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
        $mobile_agents = array(
          'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird',
          'blac', 'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric',
          'hipt', 'inno', 'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c',
          'lg-d', 'lg-g', 'lge-', 'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi',
          'mot-', 'moto', 'mwbp', 'nec-', 'newt', 'noki', 'oper', 'palm', 'pana',
          'pant', 'phil', 'play', 'port', 'prox', 'qwap', 'sage', 'sams', 'sany',
          'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar', 'sie-', 'siem', 'smal',
          'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-', 'tosh', 'tsm-',
          'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp', 'wapr',
          'webc', 'winw', 'winw', 'xda ', 'xda-'
        );

        if( engine_in_array($mobile_ua, $mobile_agents) ) {
          return true;
        }
        return false;
    }
    
    /**
    * Decode emoji in text
    * @param string $text text to decode
    */
    public function DecodeEmoji($text) {
      return $this->convertEmoji($text,"DECODE");
    }
    
    public function encode($text) {
      return $this->convertEmoji($text, 'ENCODE');
    }
    
    /**
    * Decode emoji in text
    * @param string $text text to decode
    */
    public function decode($text) {
      return $this->convertEmoji($text, 'DECODE');
    }
    
    public function convertEmoji($text,$op) {
    
      if($op=="ENCODE") {
        return preg_replace_callback('/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{1F000}-\x{1FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F9FF}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F9FF}][\x{1F000}-\x{1FEFF}]?/u',array('self',"encodeEmojis"),$text);
      } else {
        return preg_replace_callback('/(\\\u[0-9a-f]{4})+/',array('self',"decodeEmojis"),$text);
      }
    }
    
    private static function encodeEmojis($match) {
      return str_replace(array('[',']','"'),'',json_encode($match));
    }
    
    private static function decodeEmojis($text) {
      if(!$text) return '';
      $text = $text[0];
      $decode = json_decode($text,true);
      if($decode) return $decode;
      $text = '["' . $text . '"]';
      $decode = json_decode($text);
      if(engine_count($decode) == 1){
        return $decode[0];
      }
      return $text;
    }
    
    public function dateFormatCalendar() {
      $localeObject = Zend_Registry::get('Locale');
      $dateLocaleString = $localeObject->getTranslation('long', 'Date', $localeObject);
      $dateLocaleString = preg_replace('~\'[^\']+\'~', '', $dateLocaleString);
      $dateLocaleString = strtolower($dateLocaleString);
      $dateLocaleString = preg_replace('/[^ymd]/i', '', $dateLocaleString);
      $dateLocaleString = preg_replace(array('/y+/i', '/m+/i', '/d+/i'), array('yy/', 'mm/', 'dd/'), $dateLocaleString);
      return trim($dateLocaleString, '/');
    }
    
    /**
      * This function transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
      * 
      * @param string $sSize
      * @return integer The value in bytes
      */
      public function convertPHPSizeToBytes($sSize) {
        $sSuffix = strtoupper(substr($sSize, -1));
        if (!engine_in_array($sSuffix,array('P','T','G','M','K'))){
            return (int)$sSize;  
        } 
        $iValue = substr($sSize, 0, -1);
        switch ($sSuffix) {
          case 'P':
            $iValue *= 1024;
          case 'T':
            $iValue *= 1024;
          case 'G':
            $iValue *= 1024;
          case 'M':
            $iValue *= 1024;
          case 'K':
            $iValue *= 1024;
            break;
        }
        return (int)$iValue;
      }

}
