<?php


class Sitecourse_Api_Language extends Core_Api_Abstract {

  protected $_languagePath;
  protected $_defaultLanguagePath;
  protected $_hasUnlinkFlag = true;

  public function __construct() {
    $this->_languagePath = APPLICATION_PATH . '/application/languages';
    $this->_defaultLanguagePath = APPLICATION_PATH . '/application/languages/en/';
  }

  public function getDataWithoutKeyPhase($flag = null, $course = null, $courses = null) {
  
		$coreSettings = Engine_Api::_()->getApi('settings', 'core');
		
	  $plural	= $courses; //$coreSettings->getSetting( "language.phrases.courses" ,'courses');
		$singular = $course; //$coreSettings->getSetting( "language.phrases.course" ,'course');
		if (!empty($flag)) {
		return array('text_courses' => 'courses', 'text_course' => 'course');
		} else {
    return array('text_courses' => $plural, 'text_course' => $singular);
    }
  }
  
  public function hasDirectoryPermissions() {
    
    $flage = false;
    $test = new Engine_Sanity(array(
						'basePath' => APPLICATION_PATH,
							'tests' => array(
								array(
									'type' => 'FilePermission',
									'name' => 'Language Directory Permissions',
									'path' => 'application/languages',
									'value' => 7,
									'recursive' => true,
									'messages' => array(
										'insufficientPermissions' => 'Please log in over FTP and set CHMOD 0777 (recursive) on the application/languages/ directory',
									),
								),
							),
						));
    $test->run();
    foreach ($test->getTests() as $stest) {
      $errorLevel = $stest->getMaxErrorLevel();
      if (empty($errorLevel))
        $flage = true;
    }
    
    return $flage;
  }
  
  public function setUnlinkFlag($flage=true) {
    $this->_hasUnlinkFlag = $flage;
  }

  public function checkLocal($locale='en') {
    // Check Locale
    $locale = Zend_Locale::findLocale();
    // Make Sure Language Folder Exist
    $languageFolder = is_dir(APPLICATION_PATH . '/application/languages/' . $locale);
    if ($languageFolder === false) {
      $locale = substr($locale, 0, 2);
      $languageFolder = is_dir(APPLICATION_PATH . '/application/languages/' . $locale);
    }
    return $languageFolder;
  }

  public function addLanguageFile($fileName, $locale ='en', $replaceDatas=array(), $replaceDataWithoutKey=array(), $oldFileName=null) {
    if (empty($fileName) || !$this->checkLocal($locale)) {
      return;
    }
    $output = array();
    $dataLocale = array();

    $output = $dataEn = $this->loadTranslationData('en', $fileName);

    if (empty($output))
      return;
    $output = $this->convertData($output, $replaceDatas, $replaceDataWithoutKey);
    $language_file = $this->_languagePath . '/' . $locale . '/' . $fileName;

    if ($this->_hasUnlinkFlag && file_exists($language_file)) {
      @unlink($language_file);
    }

    touch($language_file);
    chmod($language_file, 0777);

    $export = new Engine_Translate_Writer_Csv($language_file);
    $export->setTranslations($output);
    $export->write();
  }

  public function addLanguageFiles($fileName, $replaceDatas=array(), $replaceDataWithoutKey=array(), $oldFileName=null) {
    $translate = Zend_Registry::get('Zend_Translate');

    // Prepare language list
    $languageList = $translate->getList();
    foreach ($languageList as $key) { 
      $this->addLanguageFile($fileName, $key, $replaceDatas, $replaceDataWithoutKey, $oldFileName);
    }
  }

  protected function loadTranslationData($locale='en',$filename = null,  array $options = array()) {
    $file_data = array();
    $options['delimiter'] = ";";
    $options['length'] = 0;
    $options['enclosure'] = '"';
    $filename = APPLICATION_PATH . '/application/languages/en/'. $filename;
    $tmp = Engine_Translate_Parser_Csv::parse($filename, 'null', $options);
    if (!empty($tmp['null']) && is_array($tmp['null'])) {
      $file_data = $tmp['null'];
    } else {
      $file_data = array();
    }
    return $file_data;
  }

  public function getReplaceDataWithoutKey($listType, $flag = null, $course = null, $courses = null) {
    $replaceWithOutKeyDatas = array();
    $replaceWithOutKeyDatasDefault = $listType;
    if(empty ($replaceWithOutKeyDatasDefault))
      return;
    $defaultPhase = $this->getDataWithoutKeyPhase($flag, $course, $courses);
    foreach ($replaceWithOutKeyDatasDefault as $arraykey => $data) {
      if (!isset($defaultPhase[$arraykey]))
        continue;
        
			if (!empty($flag)) { 
				$key = $defaultPhase[$arraykey];
			} else {
				$key = $defaultPhase[$arraykey];
			}
     
      $replaceWithOutKeyDatas[strtolower($key)] = strtolower($data);
      $replaceWithOutKeyDatas[ucfirst($key)] = ucfirst($data);
      $replaceWithOutKeyDatas[strtoupper($key)] = strtoupper($data);
      $replaceWithOutKeyDatas[ucwords($key)] = ucwords($data);
    }
    return $replaceWithOutKeyDatas;
  }

  public function setTranslateForListType($listType, $flag= null, $course = null, $courses = null) {

		$coreModulesTable = Engine_Api::_()->getDbtable('modules', 'core');
		$coreModulesTableName = $coreModulesTable->info('name');
		$select = $coreModulesTable->select()
													->from($coreModulesTableName)
													->where($coreModulesTableName . '.name LIKE ?', '%' . 'sitecourse' . '%');
		$datas = $coreModulesTable->fetchAll($select);
		foreach ($datas as $data) {
			$fileName =   $data['name'] . '.csv';
		  $oldFileName = null;
			$replaceDatas = array();
			
			$replaceDataWithoutKey = $this->getReplaceDataWithoutKey($listType, $flag, $course, $courses);
			$this->addLanguageFiles($fileName, $replaceDatas, $replaceDataWithoutKey, $oldFileName);
    }
  }

  public function convertData($datas, $replaceDatas, $replaceDataWithoutKey) {
    $data = array();
    foreach ($datas as $data_key => $data) {
      foreach ($replaceDataWithoutKey as $search => $replace) { 
      
        $data = str_replace($search, $replace, $data); 
				if (strstr($data, $replace . "_title")) {
					$data = str_replace($replace . "_title", 'course_title', $data);
				}
				if (strstr($data, $replace . "_description")) {
					$data = str_replace($replace . "_description", 'course_description', $data);
				}
				if (strstr($data, $replace . "_title_with_link")) {
					$data = str_replace($replace . "_title_with_link", 'course_title_with_link', $data);
				}
				if (strstr($data, $replace . "_url")) {
					$data = str_replace($replace . "_url", 'course_url', $data);
				}
      }
      $datas[$data_key] = $data;
    }
    return $datas;
  }
  
  public function languageChanges() {
                  return ;
  		//START LANGUAGE WORK
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$select = new Zend_Db_Select($db);
		$select
			->from('engine4_core_settings')
			->where('name = ?', 'language.phrases.course');
		$language_course = $select->query()->fetch();

		$select = new Zend_Db_Select($db);
		$select
			->from('engine4_core_settings')
			->where('name = ?', 'language.phrases.courses');
		$language_courses = $select->query()->fetch();
		if (isset($language_courses['value']) && $language_courses['value'] != 'courses' && isset($language_course['value'])  && $language_course['value'] != 'course') {

            $language_pharse = array('text_courses' => '$plural$' , 'text_course' => '$singular$'); 

            $this->setTranslateForListType($language_pharse, '', 'course', 'courses');

            $language_pharse = array('text_courses' => $language_courses['value'], 'text_course' => $language_course['value']); 

            $this->setTranslateForListType($language_pharse, '', '$singular$', '$plural$'); 
		}
		//END LANGUAGE WORK
  }
  
}