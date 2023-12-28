<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Bannerslides.php 2016-11-22 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sescompany_Model_DbTable_Bannerslides extends Engine_Db_Table {

	protected $_rowClass = "Sescompany_Model_Bannerslide";

  public function getBannerslides($id, $show_type = '',$status = false,$params = array()) {
    $tableName = $this->info('name');
    $select = $this->select()
            ->where('banner_id =?', $id);
    if(empty($show_type))
            $select->where('enabled =?', 1);
	   $select->from($tableName);
		if(isset($params['order']) && $params['order'] == 'random'){
			$select ->order('RAND()')	;
		}else
			$select ->order('order ASC');
	  if($status)
			$select = $select->where('status = 1');
    return Zend_Paginator::factory($select);
  }

}
