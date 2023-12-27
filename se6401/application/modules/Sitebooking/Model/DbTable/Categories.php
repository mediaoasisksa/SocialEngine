<?php

class Sitebooking_Model_DbTable_Categories extends Core_Model_Item_DbTable_Abstract 
{
  protected $_rowClass = "Sitebooking_Model_Category";

  /**
   * Get profile_type corresponding to category_id
   *
   * @param int category_id
   */
    public function getProfileType($categoryId = 0, $profileTypeName = 'profile_type') {

      $profile_type = 0;
      if (!empty($categoryId)) {
        //FETCH DATA
        $profile_type = $this->select()
                ->from($this->info('name'), array("$profileTypeName"))
                ->where("category_id = ?", $categoryId)
                ->query()
                ->fetchColumn();

        return $profile_type;
      }

      return $profile_type;
    }

    /**
    * Get Mapping array
    *
    */
    public function getMapping($profileTypeName = 'profile_type') {

      //MAKE QUERY
      $select = $this->select()->from($this->info('name'), array('category_id', "$profileTypeName"));

      //FETCH DATA
      $mapping = $this->fetchAll($select);

      //RETURN DATA
      if (!empty($mapping)) {
        return $mapping->toArray();
      }

      return null;
    }

  public function getSubCategories($category_id, $fetchColumns = array()) {

      //RETURN IF CATEGORY ID IS EMPTY
      if (empty($category_id)) {
        return;
      }

      //MAKE QUERY
      $select = $this->select();

      if (!empty($fetchColumns)) {
        $select->from($this->info('name'), $fetchColumns);
      }

      $select->where('first_level_category_id = ?', $category_id)
          ->where('second_level_category_id = ?', 0)
              ->order('cat_order');

      //RETURN RESULTS
      return $this->fetchAll($select);
    }

    public function getSubSubCategories($category_id, $fetchColumns = array()) {

      //RETURN IF CATEGORY ID IS EMPTY
      if (empty($category_id)) {
        return;
      }

      //MAKE QUERY
      $select = $this->select();

      if (!empty($fetchColumns)) {
        $select->from($this->info('name'), $fetchColumns);
      }

      $select->where('second_level_category_id = ?', $category_id)
              ->order('cat_order');

      //RETURN RESULTS
      return $this->fetchAll($select);
    }

  public function getCategories($fetchColumns = array(), $category_ids = null, $count_only = 0, $sponsored = 0, $first_level_category_id = 0, $limit = 0, $orderBy = 'cat_order', $visibility = 0, $havingServices = 0) {

      //MAKE QUERY
      $select = $this->select();

      //GET CATEGORY TABLE NAME
      $categoryTableName = $this->info('name');

      if ($orderBy == 'category_name') {
        $select->order('category_name');
      } else {
        $select->order('cat_order');
      }

      if (!empty($first_level_category_id)) {
        $select->where('first_level_category_id = ?', 0);
      }

      if (!empty($sponsored)) {
        $select->where('sponsored = ?', 1);
      }

      if (!empty($category_ids)) {
        foreach ($category_ids as $ids) {
          $categoryIdsArray[] = "category_id = $ids";
        }
        $select->where("(" . join(") or (", $categoryIdsArray) . ")");
      }

      if (!empty($count_only)) {
        return $select->from($this->info('name'), 'category_id')->query()->fetchColumn();
      } else {
        if (!empty($fetchColumns)) {
          $select->setIntegrityCheck(false)->from($categoryTableName, $fetchColumns);
        } else {
          $select->setIntegrityCheck(false)->from($categoryTableName);
        }
      }

      if (!empty($limit)) {
        $select->limit($limit);
      }

      //RETURN DATA
      return $this->fetchAll($select);
    
    }

    public function getMainCategories($params = array())
    {

      //GET CATEGORY TABLE NAME
      $categoryTableName = $this->info('name');
      $select = $this->select()
          ->order($categoryTableName . '.cat_order ASC');

      $sql = $categoryTableName.".first_level_category_id = ".'0'." AND ".$categoryTableName.".second_level_category_id = ".'0';

      $select->where($sql);

      if(isset($params['limit'])) {
          $select->limit($params['limit']);
      }
      return $select;

    }
    
    public function getCategoryName($cat_id) {

      
      $category_name = $this->select()
        ->from($this->info('name'), array("category_name"))
        ->where("category_id = ?", $cat_id)
        ->query()
        ->fetchColumn();
                
      return $category_name;
    }

}

?>