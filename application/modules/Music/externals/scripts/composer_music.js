
/* $Id: composer_music.js 9572 2011-12-27 23:41:06Z john $ */



(function() { // START NAMESPACE
Composer.Plugin.Music = function(options){

  this.__proto__ = new Composer.Plugin.Interface(options);

  this.name = 'music';

  this.options = {
    title : 'Add Music',
    lang : {},
    requestOptions : false,
    fancyUploadEnabled : true,
    fancyUploadOptions : {},
    debug : ('en4' in window && en4.core.environment == 'production' ? false : true )
  };

  this.initialize = function(options) {
    this.elements = new Hash(this.elements);
    this.params = new Hash(this.params);

    this.__proto__.initialize.call(this,scriptJquery.extend(options,this.__proto__.options));
  };

  this.attach = function() {
    this.__proto__.attach.call(this);
    this.makeActivator();
    return this;
  }

  this.detach = function() {
    this.__proto__.detach.call(this);
    return this;
  }

  this.activate = function() {
    if( this.active ) return;
    this.__proto__.activate.call(this);

    this.makeMenu();
    this.makeBody();

    // Generate form
    var fullUrl = this.options.requestOptions.url;
    this.elements.form = scriptJquery.crtEle('form', {
      'id' : 'compose-music-form',
      'class' : 'compose-form',
      'method' : 'post',
      'action' : fullUrl,
      'enctype' : 'multipart/form-data'
    }).appendTo(this.elements.body);

    this.elements.formInput = scriptJquery.crtEle('input', {
      'id' : 'compose-music-form-input',
      'class' : 'compose-form-input',
      'type' : 'file',
      'name' : 'file',
      'accept' : 'audio/*',
    })
    .change(this.doRequest.bind(this))
    .appendTo(this.elements.form);

    // Try to init fancyupload
  }

  this.deactivate = function() {
    if (this.params.song_id)
      scriptJquery.ajax({
        url: en4.core.basePath + '/music/remove-song',
        dataType : 'json',
        method : 'post',
        data: {
          format: 'json',
          song_id: this.params.song_id
        }
      });
    if( !this.active ) return;
    this.__proto__.deactivate.call(this);
  };

  this.doRequest = function(that) {

    if (this.elements.formInput[0].files.length > 0) {
      var FileSize = this.elements.formInput[0].files[0].size / 1024 / 1024; // in MB
      if(FileSize > post_max_size) {
        alert("The size of the file exceeds the limits set on the server.");
        scriptJquery(this.elements.formInput).val('');
        return;
      }
    }
    
    var submittedForm = false;
    this.elements.iframe = scriptJquery.crtEle('iframe',{
      'name' : 'composeMusicFrame',
      'src' : 'javascript:false;',
    })
    .css({'display' : 'none'})
    .load(function() {
          if( !submittedForm ) {
            return;
          }
          this.doProcessResponse(window._composeMusicResponse);
          window._composeMusicResponse = false;
    }.bind(this))
    .appendTo(this.elements.body);

    window._composeMusicResponse = false;
    this.elements.form.attr('target', 'composeMusicFrame');

    // Submit and then remove form
    this.elements.form.trigger("submit");
    submittedForm = true;
    this.elements.form.remove();

    // Start loading screen
    this.makeLoading();
  }

  this.makeLoading = function(action) {
    if( !this.elements.loading ) {
      if( action == 'empty' ) {
        this.elements.body.empty();
      } else if( action == 'hide' ) {
        this.elements.body.children().each(function(e){ scriptJquery(this).css('display', 'none')});
      } else if( action == 'invisible' ) {
        this.elements.body.children().each(function(e){ scriptJquery(this).css('height', '0px').css('visibility', 'hidden')});
      }

      this.elements.loading = scriptJquery.crtEle('div', {
        'id' : 'compose-' + this.getName() + '-loading',
        'class' : 'compose-loading'
      }).appendTo(this.elements.body);

      var image = this.elements.loadingImage || (scriptJquery.crtEle('img', {
        'id' : 'compose-' + this.getName() + '-loading-image',
        'class' : 'compose-loading-image'
      }));

      image.appendTo(this.elements.loading);

      scriptJquery.crtEle('span', {}).html(this._lang('Loading song, please wait...')).appendTo(this.elements.loading);
    }
  }

  this.doProcessResponse = function(responseJSON) {
	  if( typeof responseJSON == 'object' && typeof responseJSON.error != 'undefined' ) {
		  if( this.elements.loading ) {
			  this.elements.loading.remove();
		  }
		  return this.makeError(responseJSON.error, 'empty');
	  }

    // An error occurred
    if ( ($type(responseJSON) != 'object' && $type(responseJSON) != 'hash' )) {
      if( this.elements.loading )
          this.elements.loading.remove();
      this.makeError(this._lang('Unable to upload music. Please click cancel and try again'), 'empty');
      return;
    }

    if (  $type(parseInt(responseJSON.id)) != 'number' ) {
      if( this.elements.loading )
          this.elements.loading.remove();
      //if ($type(console))
      //  console.log('responseJSON: %o', responseJSON);
      this.makeError(this._lang('Song got lost in the mail. Please click cancel and try again'), 'empty');
      return;
    }
    // Success
    this.params.set('rawParams',  responseJSON);
    this.params.set('song_id',    responseJSON.id);
    this.params.set('song_title', responseJSON.fileName);
    this.params.set('song_url',   responseJSON.song_url);
    this.elements.preview = scriptJquery.crtEle('a', {
      'href': responseJSON.song_url,
      'class': 'compose-music-link',
    }).text(responseJSON.fileName).click(function(event) {
      event.preventDefault();
      scriptJquery(this).toggleClass('compose-music-link-playing');
      scriptJquery(this).toggleClass('compose-music-link');
      // var song = (responseJSON.song_url.match(/\.mp3$/)
      //   ? soundManager.createSound({id:'s'+responseJSON.id, url:responseJSON.song_url})
      // : soundManager.createVideo({id:'s'+responseJSON.id, url:responseJSON.song_url}));
      // song.togglePause();
      this.blur();
    });
    this.elements.preview.text(responseJSON.fileName);
    this.doSongLoaded();
  }

  this.doSongLoaded = function() {
    if( this.elements.loading )
        this.elements.loading.remove();
    if( this.elements.formFancyContainer )
        this.elements.formFancyContainer.remove();
    if( this.elements.error ) {
      this.elements.error.remove();
    }
    this.elements.preview.appendTo(this.elements.body);
    this.makeFormInputs();
  }
  this.makeFormInputs = function() {
    this.ready();
    this.__proto__.makeFormInputs.call(this,{
      'song_id' : this.params.song_id
    });
  }
   this.initialize(options);
};
})(); // END NAMESPACE
