<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Core.php 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Music_Api_Core extends Core_Api_Abstract
{
    // handle song upload
    public function createSong($file, $params = array())
    {
        if( is_array($file) ) {
            if( !is_uploaded_file($file['tmp_name']) ) {
                throw new Music_Model_Exception('Invalid upload or file too large');
            }
            $filename = $file['name'];
        } else if( is_string($file) ) {
            $filename = $file;
        } else {
            throw new Music_Model_Exception('Invalid upload or file too large');
        }

        // Check file extension
        if( !preg_match('/\.(mp3|m4a|aac|mp4|wav)$/iu', $filename) ) {
            throw new Music_Model_Exception('Invalid file type');
        }

        // upload to storage system
        $params = array_merge(array(
            'type' => 'song',
            'name' => $filename,
            'parent_type' => 'music_song',
            'parent_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
            'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
            'extension' => substr($filename, strrpos($filename, '.') + 1),
        ), $params);

        $song = Engine_Api::_()->storage()->create($file, $params);

        return $song;
    }

    public function getPlaylistSelect($params = array())
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $ps_table = Engine_Api::_()->getDbTable('playlistSongs', 'music');
        $ps_name = $ps_table->info('name');
        $p_table = Engine_Api::_()->getDbTable('playlists', 'music');
        $p_name = $p_table->info('name');

        $select = $p_table->select()
            ->from($p_table)
            ->group("$p_name.playlist_id");

        // WALL SEARCH
        if( !empty($params['wall']) ) {
            $select->where('`special` = ?', 'wall');
        }
        
        if( !empty($params['category']) )
        {
            $select->where('category_id = ?', $params['category']);
        }
        
        if( !empty($params['category_id']) )
        {
            $select->where('category_id = ?', $params['category_id']);
        }

        if( !empty($params['subcat_id']) )
        {
            $select->where('subcat_id = ?', $params['subcat_id']);
        }
        if( !empty($params['subsubcat_id']) )
        {
            $select->where('subsubcat_id = ?', $params['subsubcat_id']);
        }
        
        // USER SEARCH
        if( !empty($params['user']) ) {
            if( is_object($params['user']) ) {
                $owner = $params['user'];
                $select = $this->getProfileItemsSelect($select, $owner);
            } elseif( is_numeric($params['user']) ) {
                $owner = Engine_Api::_()->getItem('user', $params['user']);
                $select = $this->getProfileItemsSelect($select, $owner);
            }
            if( !empty($params['searchBit']) ) {
                $select->where('search = 1');
            }
        } else if( isset($params['users']) && is_array($params['users']) ) {
            if( empty($params['users']) ) {
                return $select ->where('1 != 1');
            }
            $select->where('owner_id IN (?)', $params['users']);
            if( !engine_in_array($viewer->level_id, $this->_excludedLevels) ) {
                $select->where("view_privacy != ? ", 'owner');
            }

            if( !empty($params['searchBit']) ) {
                $select->where('search = 1');
            }
        } else if(!empty ($params['adminView'])){
            $select->joinLeft($ps_name, "$p_name.playlist_id = $ps_name.playlist_id", '');
        } else {
            $param = array('search' => 1);
            $select = $this->getItemsSelect($select,$param);

            // prevent empty playlists from showing
            $select
                ->joinLeft($ps_name, "$p_name.playlist_id = $ps_name.playlist_id", '')
                ->where("$ps_name.song_id IS NOT NULL")
            ;
        }

        // SORT
        if( !empty($params['sort']) ) {
          $sort = $params['sort'];
          if( 'recent' == $sort ) {
              $select->order('creation_date DESC');
          } else if( 'popular' == $sort ) {
              $select->order("$p_name.play_count DESC");
          } else if( 'rating' == $sort ) {
              $select->order("$p_name.rating DESC");
          }
        }
        else $select->order('creation_date DESC');

        // STRING SEARCH
        if( !empty($params['search']) ) {
            $select
                ->where("$p_name.title LIKE ?", "%{$params['search']}%")
                ->orWhere("$p_name.description LIKE ?", "%{$params['search']}%")
                ->joinLeft($ps_name, "$p_name.playlist_id = $ps_name.playlist_id", '')
                ->orWhere("$ps_name.title LIKE ?", "%{$params['search']}%")
            ;
        }

        $select = Engine_Api::_()->network()->getNetworkSelect($p_name, $select);

        if( !empty($owner) ) {
            return $select;
        }

        return $this->getAuthorisedSelect($select);
    }

    public function getPlaylistPaginator($params = array())
    {
        $paginator = Zend_Paginator::factory($this->getPlaylistSelect($params));
        if( !empty($params['page']) ) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) ) {
            $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
    }
}
