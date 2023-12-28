<?php

class Sitebooking_Widget_ServiceOfTheDayController extends Engine_Content_Widget_Abstract {
  public function indexAction() {

    $this->view->dayitem = $dayitem = Engine_Api::_()->getDbtable('sers', 'sitebooking')->getItemOfDay();

    //DONT RENDER IF DAYITEM COUNT ZERO
    if (!($dayitem)) {
    return $this->setNoRender();
    } 

    if($dayitem->approved == 0)
    return $this->setNoRender();  
	
  }
}
?>