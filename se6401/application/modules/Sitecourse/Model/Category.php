<?php  

class Sitecourse_Model_Category extends Core_Model_Category
{
  
  public function getTitle()
  {
    return $this->category_name;
  }
  
  public function getHref($params = array())
  {
    return Zend_Controller_Front::getInstance()->getRouter()
            ->assemble($params, $this->_route, true) . '?category=' . $this->category_id;
  }

    public function getCategorySlug() {
    return Engine_Api::_()->seaocore()->getSlug($this->category_name, 225);
  }
}

?>
