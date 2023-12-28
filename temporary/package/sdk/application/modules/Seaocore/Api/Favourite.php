<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Favourite.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Api_Favourite extends Core_Api_Abstract {

  /**
   * check the item is favourite or not.
   *
   * @param Stirng $resource_type
   * @param Int $resource_id
   * @return results
   */
  public function hasFavourite( $resource_type , $resource_id ) {

    //GET THE VIEWER.
    $viewer = Engine_Api::_()->user()->getViewer() ;
    $favouriteTable = Engine_Api::_()->getItemTable( 'seaocore_favourite' ) ;
    $favouriteTableName = $favouriteTable->info( 'name' ) ;
    $sub_status_select = $favouriteTable->select()
            ->from( $favouriteTableName , array ( 'favourite_id' ) )
            ->where( 'resource_type = ?' , $resource_type )
            ->where( 'resource_id = ?' , $resource_id )
            ->where( 'poster_type =?' , $viewer->getType() )
            ->where( 'poster_id =?' , $viewer->getIdentity() )
            ->limit( 1 ) ;
    return $sub_status_select->query()->fetchAll() ;
  }


  /**
   * Function for showing 'Number of Favourites'.
   *
   * @param Stirng $resource_type
   * @param Int $resource_id
   * @return number of favourites
   */
  public function favouriteCount( $resource_type , $resource_id ) {

    //GET THE VIEWER (POSTER) AND RESOURCE.
    $poster = Engine_Api::_()->user()->getViewer() ;
    $resource = Engine_Api::_()->getItem( $resource_type , $resource_id ) ;
    return Engine_Api::_()->getDbtable( 'favourites' , 'seaocore' )->getFavouriteCount( $resource , $poster ) ;
  }
  
  /**
   * THIS FUNCTION SHOW PEOPLE FAVOURITES.
   *
   * @param String $resource_type
   * @param Int $resource_id
   * @param int $limit
   * @return array of result
   */
  public function peopleFavourite($resource_type, $resource_id, $limit = null) {

    $favouriteTable = Engine_Api::_()->getItemTable( 'seaocore_favourite' ) ;
    $favouriteTableName = $favouriteTable->info( 'name' ) ;
    $select = $favouriteTable->select()
            ->from( $favouriteTableName , array ( 'poster_id' ) )
            ->where( 'resource_type = ?' , $resource_type )
            ->where( 'resource_id = ?' , $resource_id )
            ->order( 'favourite_id DESC' );

    if($limit)
       $select->limit( $limit ) ;

		if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      return Zend_Paginator::factory($select);
    }

    return $select->query()->fetchAll() ;
  }

  /**
   * THIS FUNCTION SHOW PEOPLE FAVOURITES OR FRIEND FAVOURITES.
   *
   * @param String $call_status
   * @param String $resource_type
   * @param int $resource_id
   * @param Int $user_id
   * @param Int $search
   * @return results
   */
  public function friendPublicFavourite($call_status, $resource_type, $resource_id, $user_id, $search) {

    $favouriteTableName = Engine_Api::_()->getItemTable('seaocore_favourite')->info( 'name' ) ;
    $membershipTableName = Engine_Api::_()->getDbtable( 'membership' , 'user' )->info( 'name' ) ;
    
    $userTable = Engine_Api::_()->getItemTable( 'user' ) ;
    $userTableName = $userTable->info( 'name' ) ;

    $select = $userTable->select()
            ->setIntegrityCheck( false )
            ->from( $favouriteTableName , array ( 'poster_id' ) )
            ->where( $favouriteTableName . '.resource_type = ?' , $resource_type )
            ->where( $favouriteTableName . '.resource_id = ?' , $resource_id )
            ->where( $favouriteTableName . '.poster_id != ?' , 0 )
            ->where( $userTableName . '.displayname FAVOURITE ?' , '%' . $search . '%' )
            ->order( 'favourite_id DESC' ) ;

    if ( $call_status == 'friend' || $call_status == 'myfriendfavourites' ) {
      $select->joinInner( $membershipTableName , "$membershipTableName . resource_id = $favouriteTableName . poster_id" , NULL )
          ->joinInner( $userTableName , "$userTableName . user_id = $membershipTableName . resource_id" )
          ->where( $membershipTableName . '.user_id = ?' , $user_id )
          ->where( $membershipTableName . '.active = ?' , 1 )
          ->where( $favouriteTableName . '.poster_id != ?' , $user_id ) ;
    }
    else if ( $call_status == 'public' ) {
      $select->joinInner( $userTableName , "$userTableName . user_id = $favouriteTableName . poster_id" ) ;
    }
    return $select;
  }

  /**
   * THIS FUNCTION USE FOR USER OR FRIEND NUMBER OF FAVOURITES.
   *
   * @param String $resource_type
   * @param Int $resource_id
   * @param String $params
   * @param Int $limit
   * @return count results
   */
  public function userFriendNumberOffavourite($resource_type, $resource_id, $params, $limit = null) {

    //GET THE USER ID.
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity() ;
    
    $favouriteTable = Engine_Api::_()->getItemTable( 'seaocore_favourite' ) ;
    $favouriteTableName = $favouriteTable->info( 'name' ) ;

    $memberTableName = Engine_Api::_()->getDbtable( 'membership' , 'user' )->info( 'name' ) ;

    $select = $favouriteTable->select();
    
    if ( $params == 'friendNumberOfFavourite' ) {
      $select->from($favouriteTableName , array ( 'COUNT(' . $favouriteTableName . '.favourite_id) AS favourite_count'));
    }
    elseif ( $params == 'userFriendFavourites' ) {
      $select->from( $favouriteTableName , array ( 'poster_id' ) ) ;
    }
    
    $select->joinInner($memberTableName, "$memberTableName . resource_id = $favouriteTableName . poster_id" , NULL)
					->where( $memberTableName . '.user_id = ?' , $viewer_id )
					->where( $memberTableName . '.active = ?' , 1 )
					->where( $favouriteTableName . '.resource_type = ?' , $resource_type )
					->where( $favouriteTableName . '.resource_id = ?' , $resource_id )
					->where( $favouriteTableName . '.poster_id != ?' , $viewer_id )
					->where( $favouriteTableName . '.poster_id != ?' , 0 ) ;

    if ( $params == 'friendNumberOfFavourite' ) {
      $select->group( $favouriteTableName . '.resource_id' ) ;
    }
    elseif ( $params == 'userFriendFavourites' ) {
      $select->order( $favouriteTableName . '.favourite_id DESC' )->limit( $limit ) ;
    }
    //$fetch_count = $select->query()->fetchAll() ;
    $fetch_count = $select ->query()->fetchColumn();
    
    if (!empty($fetch_count)) {
      return $fetch_count;
    } 
    else {
      return 0;
    }
  }

}