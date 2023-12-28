<?php

class Sitebooking_Widget_ProviderOfTheDayController extends Engine_Content_Widget_Abstract {

  public function indexAction() {


    $this->view->dayitem = $dayitem = Engine_Api::_()->getDbtable('pros', 'sitebooking')->getItemOfDay();

    //DONT RENDER IF COUNT ZERO
    if (!($dayitem)) {
      return $this->setNoRender();
    } 

    if($dayitem->approved == 0)
      return $this->setNoRender();  
  }
}
?>