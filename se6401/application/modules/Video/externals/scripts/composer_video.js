
/* $Id: composer_video.js 10258 2014-06-04 16:07:47Z lucas $ */



(function() { // START NAMESPACE
var $ = 'id' in document ? document.id : window.$;



Composer.Plugin.Video = function(options){

  this.__proto__ = new Composer.Plugin.Interface(options);

  this.name = 'video';

  this.options = {
    title : 'Add Video',
    lang : {},
    // Options for the link preview request
    requestOptions : {},
    // Various image filtering options
    imageMaxAspect : ( 10 / 3 ),
    imageMinAspect : ( 3 / 10 ),
    imageMinSize : 48,
    imageMaxSize : 5000,
    imageMinPixels : 2304,
    imageMaxPixels : 1000000,
    imageTimeout : 5000,
    // Delay to detect links in input
    monitorDelay : 250
  };

  this.initialize = function(options) {
    this.elements = new Hash(this.elements);
    this.params = new Hash(this.params);
    this.__proto__.initialize.call(this,options);
  }

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

    // Generate body contents
    // Generate form

    this.elements.formInput = scriptJquery.crtEle('select', {
      'id' : 'compose-video-form-type',
      'class' : 'compose-form-input',
      'option' : 'test',
    })
    .change(this.updateVideoFields.bind(this))
    .appendTo(this.elements.body);
    var options = 0;
    scriptJquery('#compose-video-form-type').append(scriptJquery.crtEle('option',{
      'value': 0,
    }).html(this._lang('Choose Source')));
    scriptJquery('#compose-video-form-type').append(scriptJquery.crtEle('option',{
      'value' : 'iframely',
    }).html(this._lang('External Site')));
    
    this.elements.formInputContainer = scriptJquery.crtEle('div', {
      'id' : 'compose-video-form-input',
      'class' : 'compose-form-input-container',
      'type' : 'text',
    }).css("display","none");

    this.elements.formInput = scriptJquery.crtEle('input', {
      'id' : 'compose-form-input',
      'class' : 'compose-form-input',
      'type' : 'text',
    }).appendTo(this.elements.formInputContainer);

    this.elements.formInputDes = scriptJquery.crtEle('p', {
      'id' : 'compose-video-form-description',
      'class' : 'compose-form-description',
      'type' : 'text',    
    })
    .html(this._lang('Paste the web address of the video here. (For Instagram videos, only IGTV videos are supported.)'))
    .appendTo(this.elements.formInputContainer);
    this.elements.formInputContainer.appendTo(this.elements.body);
   
    if(DetectMobileQuick() || DetectIpad()){
      if (this.options.allowed == 1 && this.options.type != 'message'){
        scriptJquery('#compose-video-form-type').append(scriptJquery.crtEle('option',{
          'value' : 'upload',
        }).html(this._lang('My Device')));
      }
      this.elements.previewDescription = scriptJquery.crtEle('div', {
        'id' : 'compose-video-upload',
        'class' : 'compose-video-upload',
      })
      .html(this._lang('To upload a video from your device, please use our full uploader.'))
      .css('display','none')
      .appendTo(this.elements.body);
    }
    else{
      if (this.options.allowed == 1 && this.options.type != 'message'){
        scriptJquery('#compose-video-form-type').append(scriptJquery.crtEle('option',{
          'value' : 'upload',
        }).html(this._lang('My Device')));
      }
      this.elements.previewDescription = scriptJquery.crtEle('div', {
        'id' : 'compose-video-upload',
        'class' : 'compose-video-upload',
        'style': 'display:none;'
      })
      .html(this._lang('To upload a video from your computer, please use our full uploader.'))
      .css('display','none')
      .appendTo(this.elements.body);
    }

    this.elements.formSubmit = scriptJquery.crtEle('button', {
      'id' : 'compose-video-form-submit',
      'class' : 'compose-form-submit',
    })
    .html(this._lang('Attach'))
    .css('display','none')
    .click(function(e) {
          e.preventDefault();
          this.doAttach();
    }.bind(this))
    .appendTo(this.elements.body);
    console.log(this.elements.body,"this.elements.body");
    this.elements.formInput.focus();
  }

  this.deactivate = function() {
    // clean video out if not attached
    if (this.params.video_id)
      scriptJquery.ajax({
        url: en4.core.basePath + 'video/index/delete',
        dataType : 'json',
        method : 'post',
        data: {
          format: 'json',
          video_id: this.params.video_id
        }
      });
    if( !this.active ) return;
    this.__proto__.deactivate.call(this);
  }

  // Getting into the core stuff now

  this.doAttach = function(e) {
    var val = this.elements.formInput.val();
    if( !val )
    {
      return;
    }
    if( !val.match(/^[a-zA-Z]{1,5}:\/\//) )
    {
      val = 'http://' + val;
    }
    this.params.set('uri', val)
    // Input is empty, ignore attachment
    if( val == '' ) {
      e.stop();
      return;
    }
    
    var video_element = document.getElementById("compose-video-form-type");
    var type = video_element.value;
    // Send request to get attachment
    var options = scriptJquery.extend({
      'dataType':'json',
      'method':'post',
      'data' : {
        'format' : 'json',
        'uri' : val,
        'type': type
      },
      'success' : this.doProcessResponse.bind(this)
    }, this.options.requestOptions);

    // Inject loading
    this.makeLoading('empty');

    // Send request
    scriptJquery.ajax(options);
  }

  this.doProcessResponse = function(responseJSON, responseText) {
    // Handle error
    if($type(responseJSON) != 'object' || $type(responseJSON.src) != 'string' || $type(parseInt(responseJSON.video_id)) != 'number' ) {
      //this.elements.body.empty();
      if( this.elements.loading ) this.elements.loading.remove();
      //this.makeaError(responseJSON.message, 'empty');
      this.makeError(responseJSON.message);

      //compose-video-error
      //ignore test
      this.elements.ignoreValidation = scriptJquery.crtEle('a', {
        'href' : this.params.uri,
      })
      .html(this.params.title)
      .click(function(e) {
          e.preventDefault();
          self.doAttach(this);
      }).appendTo(this.elements.previewTitle);
      
      return;
      //throw "unable to upload image";
    }

    var title = responseJSON.title || this.params.get('uri').replace('http://', '');
    

    this.params.set('title', responseJSON.title);
    this.params.set('description', responseJSON.description);
    this.params.set('photo_id', responseJSON.photo_id);
    this.params.set('video_id', responseJSON.video_id);
    if (responseJSON.src) {
      this.elements.preview = scriptJquery.crtEle('img', {
        'id' : 'compose-video-preview-image',
        'class' : 'compose-preview-image',
        'src' : responseJSON.src,
      }).load(this.doImageLoaded.bind(this));
    } else {
      this.doImageLoaded();
    }
  }

  this.doImageLoaded = function() {
    var self = this;
    if( this.elements.loading.length) this.elements.loading.remove();
    if( this.elements.preview ) {
      this.elements.preview.attr('width','');
      this.elements.preview.attr('height','');
      this.elements.preview.appendTo(this.elements.body);
    }

    this.elements.previewInfo = scriptJquery.crtEle('div', {
      'id' : 'compose-video-preview-info',
      'class' : 'compose-preview-info'
    }).appendTo(this.elements.body);

    this.elements.previewTitle = scriptJquery.crtEle('div', {
      'id' : 'compose-video-preview-title',
      'class' : 'compose-preview-title'
    }).appendTo(this.elements.previewInfo);

    this.elements.previewTitleLink = scriptJquery.crtEle('a', {
      'href' : this.params.uri,
    })
    .html(this.params.title)
    .click(function(e) {
        e.preventDefault();
        self.handleEditTitle(this);
    })
    .appendTo(this.elements.previewTitle);

    this.elements.previewDescription = scriptJquery.crtEle('div', {
      'id' : 'compose-video-preview-description',
      'class' : 'compose-preview-description',
    })
    .html(this.params.description)
    .click(function(e) {
      e.preventDefault();
      self.handleEditDescription(this);
    })
    .appendTo(this.elements.previewInfo);
    this.makeFormInputs();
  }

  this.makeFormInputs = function() {
    this.ready();
    this.__proto__.makeFormInputs.call(this,{
      'photo_id' : this.params.photo_id,
      'video_id' : this.params.video_id,
      'title' : this.params.title,
      'description' : this.params.description
    });
  }

  this.updateVideoFields = function(element) {
    var video_element = scriptJquery("#compose-video-form-type");
    var url_element = scriptJquery("#compose-video-form-input");
    var post_element = scriptJquery("#compose-video-form-submit");
    var upload_element = scriptJquery("#compose-video-upload");
    // clear url if input field on change
    scriptJquery('#compose-video-form-input').val("");

  // If video source is empty
    if (video_element.val() == 0)
    {
      upload_element.hide();
      post_element.hide();
      url_element.hide();
    }

    // If video source is youtube or vimeo
    if (video_element.val() == 'iframely')
    {
      upload_element.hide();
      post_element.show();
      url_element.show();
      url_element.focus();
    }

    // if video source is upload
    if (video_element.val() == 'upload')
    {
      upload_element.show();
      post_element.hide();
      url_element.hide();
    }
  }
  this.handleEditTitle = function(element) {
    element.css('display', 'none');
    var input = scriptJquery.crtEle('input', {
      'type' : 'text',
      'value' : htmlspecialchars_decode(element.get('html').trim()),
    })
    .blur(function() {
          if(input.value.trim() != '' ) {
            this.params.title = input.value;
            element.html(this.params.title);
            this.setFormInputValue('title', this.params.title);
          }
          element.css('display', '');
          input.remove();
    }.bind(this))
    .insertAfter(element, 'after');
    input.focus();
  }
  this.handleEditDescription = function(element) {
    element.css('display', 'none');
    var input = scriptJquery.crtEle('textarea', {})
    .html(htmlspecialchars_decode(element.html().trim()))
    .blur(function() {
          if( input.val().trim() != '' ) {
            this.params.description = input.val();
            element.html(this.params.description);
            this.setFormInputValue('description', this.params.description);
          }
          else{
            this.params.description = '';
            element.html('');
            this.setFormInputValue('description', '');
          }
          element.css('display', '');
          input.remove();
    }.bind(this))
    .insertAfter(element, 'after');
    input.focus();
  }
  this.initialize(options);
};

})(); // END NAMESPACE
