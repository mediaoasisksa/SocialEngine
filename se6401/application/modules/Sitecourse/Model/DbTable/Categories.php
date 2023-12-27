<?php   



class Sitecourse_Model_DbTable_Categories extends Engine_Db_Table
{
  protected $_rowClass = 'Sitecourse_Model_Category';
  
  public function getCategoriesAssoc()
  {
    $stmt = $this->select()
    ->from($this, array('category_id', 'category_name'))->where('cat_dependency = 0')
    ->order('cat_order ASC')
    ->query();    
    return $stmt->fetchAll();
  }


  public function getCategoriesContainsCourse(){
    $stmt = $this->select()
    ->from($this, array('category_id', 'category_name'))->where('cat_dependency = 0')
    ->where('course_count > 0')
    ->order('cat_order ASC')
    ->query();    
    return $stmt->fetchAll();   
  }

  public function getSubCategoresAssoc($parent_id){
    $stmt = $this->select()
    ->from($this, array('category_id', 'category_name'))->where('cat_dependency = ?',$parent_id)
    ->order('category_name ASC')
    ->query();  
    return $stmt->fetchAll();
  }


  /**
   * @param category title
   * @return whether title present already or not
   * 
   */

  public function categoryPresent($title){

    $stmt = $this->select()->from($this)
    ->where("category_name = ?",$title)->query();
    $result = $stmt->fetch();
    return $result;
  }

    /**
   * @param category title
   * @return whether title present already or not
   * 
   */

    public function subCategoryPresent($title,$parent_id){

      $stmt = $this->select()->from($this)
      ->where("category_name = '".$title."' AND cat_dependency = ?",$parent_id)
      ->query();
      $result = $stmt->fetch();
      return $result;
    }

  /**
   * @param category id
   * @return all categories where category id is not equal to param
   */

  public function getRemainingCategory($category_id){
    $stmt = $this->select()->from($this)
    ->where("category_id != ? AND cat_dependency = 0",$category_id)->query();
    return $stmt->fetchAll();
  }


  /**
   * @param category id
   * @return whether there is subcategory under the category id
   * 
   */
  public function containsSubCategory($category_id){
    $stmt = $this->select()->from($this)->where('cat_dependency = ?',$category_id)->query();
    return $stmt->fetchAll();
  }


  public function getRemainingSubCategory($category_id,$parent_id){
    $stmt = $this->select()->from($this)
    ->where("category_id != ".$category_id." AND cat_dependency = ?",$parent_id)->query();
    return $stmt->fetchAll();
  }
  
  public function getCategory($category_id) {

    if (empty($category_id)) {
      return;
    }

    //if (!array_key_exists($category_id, $this->_categories)) {
      $this->_categories[$category_id] = $this->find($category_id)->current();
    //}
    return $this->_categories[$category_id];
  }
  
  public function getCategorySlug($categoryname) {
    $slug = $categoryname;
    return Engine_Api::_()->seaocore()->getSlug($slug, 225);
  }

  public function getAllRows() {
    $stmt = $this->select()
    ->order('cat_order ASC')
    ->query();    
    return $stmt->fetchAll();  	
  }

   public function getMaxCatOrder() {
    $stmt = $this->select()
    ->order('cat_order DESC')
    ->query();    
    return $stmt->fetchAll();   
  }

}

?>
