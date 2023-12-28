<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Travel
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    https://socialengine.com/eula
 * @version    $Id: Travels.php 9747 2012-07-26 02:08:08Z john $
 * @author     Donna
 */

/**
 * @category   Application_Extensions
 * @package    Travel
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    https://socialengine.com/eula
 */
class Travel_Model_DbTable_Travels extends Core_Model_Item_DbTable_Abstract
{
    protected $_rowClass = "Travel_Model_Travel";

    /**
     * Gets a paginator for travel listings
     *
     * @param Core_Model_Item_Abstract $user The user to get the messages for
     * @return Zend_Paginator
     */
    public function getTravelsPaginator($params = array(),
                                            $customParams = null)
    {
        $paginator = Zend_Paginator::factory($this->getTravelsSelect($params, $customParams));
        if( !empty($params['page']) ) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) ) {
            $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
    }

    /**
     * Gets a select object for the user's travel entries
     *
     * @param Core_Model_Item_Abstract $user The user to get the messages for
     * @return Zend_Db_Table_Select
     */
    public function getTravelsSelect($params = array(), $customParams = null)
    {
        $tableName = $this->info('name');

        $tagMapsTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
        $tagMapsTableName = $tagMapsTable->info('name');

        $searchTable = Engine_Api::_()->fields()->getTable('travel', 'search');
        $searchTableName = $searchTable->info('name');

        $select = $this->select()
            ->from($this)
            ->order(!empty($params['orderby']) ? $tableName . '.' . $params['orderby'] . ' DESC'
                : $tableName . '.creation_date DESC' );

        if( !empty($params['user_id']) && is_numeric($params['user_id']) ) {
            $owner = Engine_Api::_()->getItem('user', $params['user_id']);
            $select = $this->getProfileItemsSelect($owner, $select);
        } elseif( !empty($params['user']) && $params['user'] instanceof User_Model_User ) {
            $owner = $params['user'];
            $select = $this->getProfileItemsSelect($owner, $select);
        } elseif( !empty($params['users']) ) {
            $str = (string) ( is_array($params['users']) ? "'" . join("', '",
                    $params['users']) . "'" : $params['users'] );
            $select->where($tableName . '.owner_id in (?)', new Zend_Db_Expr($str))
                ->where("view_privacy != ? ", 'owner');
        } else if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('travel.allow.unauthorized',0)){
            $param = array();
            $select = $this->getItemsSelect($param, $select);
        }

        if( isset($customParams) ) {
            $select = $select
                ->joinLeft($searchTableName, "$searchTableName.item_id = $tableName.travel_id", null);

            $searchParts = Engine_Api::_()->fields()->getSearchQuery('travel', $customParams);
            foreach( $searchParts as $k => $v ) {
                $select->where("`{$searchTableName}`.{$k}", $v);
            }
        }

        if( !empty($params['tag']) ) {
            $select
                ->joinLeft($tagMapsTableName, "$tagMapsTableName.resource_id = $tableName.travel_id", null)
                ->where($tagMapsTableName . '.resource_type = ?', 'travel')
                ->where($tagMapsTableName . '.tag_id = ?', $params['tag']);
        }

        if( !empty($params['category']) ) {
            $select->where($tableName . '.category_id = ?', $params['category']);
        }
        
        if( !empty($params['category_id']) )
        {
            $select->where($tableName.'.category_id = ?', $params['category_id']);
        }

        if( !empty($params['subcat_id']) )
        {
            $select->where($tableName.'.subcat_id = ?', $params['subcat_id']);
        }
        if( !empty($params['subsubcat_id']) )
        {
            $select->where($tableName.'.subsubcat_id = ?', $params['subsubcat_id']);
        }

        if( isset($params['closed']) && $params['closed'] != "" ) {
          $select->where($tableName . '.closed = ?', $params['closed']);
        } elseif(@$params['closed'] == '0'){
          $select->where('closed =?', 0);
        }

        // Could we use the search indexer for this?
        if( !empty($params['search']) ) {
            $select->where($tableName . ".title LIKE ? OR " . $tableName . ".body LIKE ?",
                '%' . $params['search'] . '%');
        }

        if( !empty($params['start_date']) ) {
            $select->where($tableName . ".creation_date > ?",
                date('Y-m-d', $params['start_date']));
        }

        if( !empty($params['end_date']) ) {
            $select->where($tableName . ".creation_date < ?",
                date('Y-m-d', $params['end_date']));
        }

        if( !empty($params['has_photo']) ) {
            $select->where($tableName . ".photo_id > ?", 0);
        }

        $select = Engine_Api::_()->network()->getNetworkSelect($tableName, $select);

        if( !empty($owner) ) {
            return $select;
        }
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('travel.allow.unauthorized', 0)) {
            return $this->getAuthorisedSelect($select);
        }else{
            return $select;
        }
    }
    
    public function isTravelExists($category_id, $categoryType = 'category_id') {
      return $this->select()
              ->from($this->info('name'), 'travel_id')
              ->where($categoryType . ' = ?', $category_id)
              ->query()
              ->fetchColumn();
    }
}
