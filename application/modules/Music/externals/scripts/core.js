
/* $Id: core.js 10161 2014-04-11 19:49:41Z andres $ */



(function() { // START NAMESPACE
var $ = 'id' in document ? document.id : window.$;


if( !('en4' in window) ) {
  en4 = {};
}
if( !('music' in en4) ) {
  en4.music = {};
}

en4.core.runonce.add(function() {
  // preload pause button element as defined in CSS class '.music_player_button_pause'
  scriptJquery.crtEle('div', {
    'id': 'pause_preloader',
    'class': 'music_player_button_pause',
  }).css({position:'absolute',top:-9999,left:-9999}).appendTo(scriptJquery("body"));
  
  // ADD TO PLAYLIST
  scriptJquery('a.music_add_to_playlist').on('click', function(){
    scriptJquery('#song_id').val(this.id.substring(5));
    Smoothbox.open(scriptJquery('#music_add_to_playlist'), {mode: 'Inline'} );
    var pl = scriptJquery('#TB_ajaxContent > div');
    pl.show();
  });

  // PLAY ON MY PROFILE
  scriptJquery(document).on('click','a.music_set_profile_playlist', function(e) {
    e.preventDefault();
    var url_part    = this.href.split('/');
    var playlist_id = 0;
    url_part.forEach(function(val, i) {
      if (val == 'playlist_id')
        playlist_id = url_part[i+1];
    });
    scriptJquery.ajax({
      method: 'post',
      url: this.href,
      noCache: true,
      data: {
        'playlist_id': playlist_id,
        'format': 'json'
      },
      success: function(json){
        var link = scriptJquery('#music_playlist_item_' + json.playlist_id + ' a.music_set_profile_playlist');
        if (json && json.success) {
          scriptJquery('a.music_set_profile_playlist')
            .text(en4.core.language.translate('Play on my Profile'))
            .addClass('icon_music_playonprofile')
            .removeClass('icon_music_disableonprofile')
            ;
          if( json.enabled && link ) {
            link
              .text(en4.core.language.translate('Disable Profile Playlist'))
              .addClass('icon_music_disableonprofile')
              .removeClass('icon_music_playonprofile')
              ;
          }
        }
      }
    });
    return false;
  });
  en4.music.player.enablePlayers();

});

en4.music.player = {

  playlists : [],

  mute : ( Cookie.read('en4_music_mute') == 1 ? true : false ),

  volume : ( Cookie.read('en4_music_volume') ? Cookie.read('en4_music_volume') : 85 ),
  
  getSoundManager : function() {

    if( !('soundManager' in en4.music) && 'soundManager' in window ) {
      en4.music.soundManager = soundManager;
    }

    return en4.music.soundManager;
  },

  getPlaylists : function() {
    return this.playlists;
  },

  getVolume : function() {
    if( this.mute ) {
      return 0;
    } else {
      return this.volume;
    }
  },

  setVolume : function(volume) {
    if( 0 == volume ) {
      this.mute = true;
    } else {
      this.mute = false;
      this.volume = volume;
    }
    this._writeCookies();
    this._updatePlaylists();
  },

  toggleMute : function(flag) {
    if( $type(flag) ) {
      this.mute = ( true == flag );
    } else {
      this.mute = !this.mute;
    }
    this._writeCookies();
    this._updatePlaylists();
  },

  enablePlayers : function() {
    // enable players automatically?
    var players = scriptJquery('.music_player_wrapper');
    //if( players.length > 0 ) {
      // Initialize sound manager?
      en4.music.player.getSoundManager();
    //}
    players.each(function(el) {
      el = scriptJquery(this);
      var matches = el.attr('id').match(/music_player_([\w\d]+)/i);
      if( matches && matches.length >= 2 && !el.hasClass('music_player_active') ) {
        el.addClass('music_player_active');
        en4.music.player.createPlayer(matches[1]);
      }
    });
  },
  createPlayer : function(id) {

    var par = scriptJquery('#music_player_' + id);
    var el  = par.find('div.music_player');
    en4.music.player.getSoundManager().onready(function() {
      // show the entire player
      if( !par.find('div.playlist_short_player:first') ) {
        if( !el.hasClass('playlist_player_loaded') ) {
          var playlist = new en4.music.playlistAbstract(el);
          en4.music.player.playlists.push(playlist);
          el.addClass('playlist_player_loaded');
        }

      // show the short player first
      } else {
        par.find('div.music_player:not(div.playlist_short_player:first)').hide();
        par.find('div.playlist_short_player').on('click', function(){
          var par = scriptJquery('#music_player_' + id);
          var el = par.find('div.music_player');
          el.show();
          par.find('div.playlist_short_player:first').hide();

          if( !el.hasClass('playlist_player_loaded') ) {
            var playlist = new en4.music.playlistAbstract(el);
            en4.music.player.playlists.push(playlist);
            playlist.play();
            el.addClass('playlist_player_loaded');
          }
        });
      }
    });

    return this;
  },

  _writeCookies : function() {
    var tmpUri = new URL(scriptJquery('head base[href]').eq(0).attr("href"));
    Cookie.write('en4_music_volume', this.volume, {
      duration: 7, // days
      path: tmpUri.pathname,
      domain: tmpUri.hostname
    });
    Cookie.write('en4_music_mute', ( this.mute ? 1 : 0 ), {
      duration: 7, // days
      path: tmpUri.pathname,
      domain: tmpUri.hostname
    });
  },
  _updatePlaylists : function() {
    // this.playlists.each(function(playlist) {
    //   playlist._updateScrub();
    //   playlist._updateVolume();
    // });
  }
};
})(); // END NAMESPACE
