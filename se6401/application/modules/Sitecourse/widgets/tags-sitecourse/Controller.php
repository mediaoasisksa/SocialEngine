<?php 
class Sitecourse_Widget_TagsSitecourseController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $itemCount = $this->_getParam('itemCount',0);
    $alphabetical = $this->_getParam('alphabetical',0);

    $courseTable = Engine_Api::_()->getDbtable('courses','sitecourse');
    $tag_cloud_array = $courseTable->getTagCloud($itemCount,$alphabetical);

    // no tags found
    if(!count($tag_cloud_array)) {
      $this->setNoRender();
    }
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $action = $front->getRequest()->getActionName();
    $controller = $front->getRequest()->getControllerName();

    if (($module == 'sitecourse' && $controller == 'index' && $action == 'tagscloud') || $this->_getParam('notShowExploreTags', false)) {
      $this->view->notShowExploreTags = true;
    }

    $tag_array = array();
    $tag_id_array = array();
    foreach ($tag_cloud_array as $values) {
      $tag_array[$values['text']] = $values['Frequency'];
      $tag_id_array[$values['text']] = $values['tag_id'];
    }

    if (!empty($tag_array)) {
      $max_font_size = 18;
      $min_font_size = 12;
      $max_frequency = max(array_values($tag_array));
      $min_frequency = min(array_values($tag_array));
      $spread = $max_frequency - $min_frequency;
      if ($spread == 0) {
        $spread = 1;
      }
      $step = ($max_font_size - $min_font_size) / ($spread);

      $tag_data = array('min_font_size' => $min_font_size, 'max_font_size' => $max_font_size, 'max_frequency' => $max_frequency, 'min_frequency' => $min_frequency, 'step' => $step);

      $this->view->tag_data = $tag_data;
      $this->view->tag_id_array = $tag_id_array;
    }
    $this->view->tag_array = $tag_array;
    
  }
}

?>
