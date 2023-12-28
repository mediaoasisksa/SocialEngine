<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: admin.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>
<div style="padding: 10px;">

  <?php if( !$this->values ): ?>
    
    <script type="text/javascript">
      scriptJquery(document).ready(function() {
        var params = parent.pullWidgetParams();
        var info = parent.pullWidgetTypeInfo();
        // Populate params
        Object.entries(params).forEach(function([key, value]) {
          if( $type(value) == 'object' ) {
            Object.entries(value).forEach(function([skey, svalue]){
              if(scriptJquery('#'+key + '-' + svalue) ) {
                scriptJquery('#'+key + '-' + svalue).prop('checked', true);
              }
            });
          } else if(scriptJquery('#'+key) ) {
            scriptJquery('#'+key).value = value;
          } else if(scriptJquery('#'+key + '-' + value) ) {
            scriptJquery('#'+key + '-' + value).prop('checked', true);
          }
        });
        scriptJquery('.form-description').html(info.description);

        // Has a playlist selected already
        if( 'playlist_id' in params && params.playlist_id ) {
          scriptJquery('#music-home-playlist-search').css('display', 'none');
          selectPlaylist(params.playlist_id);
        } else {
          
        }
        
      });

      var searchPlaylist = function(query) {
        var request = scriptJquery.ajax({
          url : '<?php echo $this->url(array('module' => 'music', 'controller' => 'manage', 'action' => 'suggest'), 'admin_default', true) ?>',
          method : 'post',
          dataType : 'json',
          data : {
            format : 'json',
            query : query
          },
          success : function(responseJSON) {
            scriptJquery('#music-home-playlist-search-results').css('display', '');
            Object.entries(responseJSON.data).forEach(function([id,title]) {
              (scriptJquery.crtEle('a', {})).html(title).appendTo(
                (scriptJquery.crtEle('li', {
                })).appendTo(
                  scriptJquery('#music-home-playlist-search-results').find('ul')
                )
              );
            });
            parent.Smoothbox.instance.doAutoResize();
          }
        });
      }
      var deselectPlaylist = function() {
        scriptJquery('#playlist_id').val('');
        scriptJquery('#music-home-playlist-search').css('display', '');
        scriptJquery('#music-home-playlist-search-results').css('display', 'none');
        scriptJquery('#music-home-playlist-form').css('display', 'none');
        scriptJquery('#music-home-playlist-selected').css('display', 'none');
      }
      
      var selectPlaylist = function(playlist_id, info) {
        if( !info ) {
          getPlaylistInfo(playlist_id, selectPlaylist);
          return;
        }
        scriptJquery('#playlist_id').val(playlist_id);
        scriptJquery('#title').val(info.title);
        scriptJquery('#music-home-playlist-search').css('display', 'none');
        scriptJquery('#music-home-playlist-search-results').css('display', 'none');
        scriptJquery('#music-home-playlist-form').css('display', '');
        scriptJquery('#music-home-playlist-selected').css('display', '');

        scriptJquery('#music-home-playlist-selected').attr('');

        // Create photo
        if('photo' in info ) {
          (scriptJquery.crtEle('img', {
            src : info.photo
          })).appendTo(
            (scriptJquery.crtEle('div', {
              'class' : 'photo'
            })).appendTo(
              scriptJquery('#music-home-playlist-selected')
            )
          );
        }
        // Create info wrapper
        var infoWrapper = scriptJquery.crtEle('div', {
          'class' : 'info'
        });
        infoWrapper.appendTo(scriptJquery('#music-home-playlist-selected'));

        // Create title
        (scriptJquery.crtEle('a', {
          href : info.href,
          target : '_blank'
        })).html(info.title).appendTo(
          (scriptJquery.crtEle('div', {
            'class' : 'title'
          })).appendTo(
            infoWrapper
          )
        );

        // Create description
        (scriptJquery.crtEle('div', {
          'class' : 'description'
        })).html(info.description).appendTo(
          infoWrapper
        );

        parent.Smoothbox.instance.doAutoResize();
      }

      var getPlaylistInfo = function(playlist_id, callback) {
        scriptJquery.ajax({
          url : '<?php echo $this->url(array('module' => 'music', 'controller' => 'manage', 'action' => 'info'), 'admin_default', true) ?>',
          method : 'post',
          dataType : 'json',
          data : {
            format : 'json',
            playlist_id : playlist_id
          },
          success : function(responseJSON) {
            if( 'status' in responseJSON && responseJSON.status ) {
              callback(playlist_id, responseJSON);
            }
          }
        });
      }
    </script>

    <div id="music-home-playlist-form" style="display:none;">
      <?php echo $this->form->render($this) ?>
    </div>

    <div id="music-home-playlist-search">
      <h3>
        <?php echo $this->translate('Home Playlist') ?>
      </h3>
      <p>
        <?php echo $this->translate('Search for a playlist to display using the text box below.') ?>
      </p>
      <div>
        <input type="text" name="query" id="query" />
      </div>
      <div>
        <button onclick="searchPlaylist($('query').value)">Search</button>
      </div>
    </div>
    <div id="music-home-playlist-search-results" style="display:none;">
      <ul>
      </ul>
    </div>
    <div id="music-home-playlist-selected" style="display:none;">
    </div>
  <?php else: ?>
    <script type="text/javascript">
      parent.setWidgetParams(<?php echo Zend_Json::encode($this->values) ?>);
      parent.Smoothbox.close();
    </script>
  <?php endif; ?>
</div>
