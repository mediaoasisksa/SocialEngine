<?php  
class Sitecourse_Model_Lesson extends Core_Model_Item_Abstract
{	
	 public function setFile($file_pass) {
    if ($file_pass instanceof Zend_Form_Element_File) {
      $file = $file_pass->getFileName();
    } else if (is_array($file_pass) && !empty($file_pass['tmp_name'])) {
      $file = $file_pass['tmp_name'];
    } else if (is_string($file_pass) && file_exists($file_pass)) {
      $file = $file_pass;
    } else {
      throw new Eventdocument_Model_Exception('invalid argument passed to setFile');
    }
    $params = array(
        'parent_type' => 'lesson',
        'parent_id' => $this->getIdentity()
    );
   
    try {
      return Engine_Api::_()->storage()->create($file, $params);
    } catch (Exception $e) {
      throw $e;
      
    } 
  }
}
?>