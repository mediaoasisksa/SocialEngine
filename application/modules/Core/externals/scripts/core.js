
/* $Id: core.js 9968 2013-03-19 00:20:56Z john $ */



(function() { // START NAMESPACE
var $ = 'id' in document ? document.id : window.$;



en4 = {};


/**
 * Core methods
 */
en4.core = {

  baseUrl : '',

  basePath : '',

  loader : false,

  environment : 'production',

  setBaseUrl : function(url)
  {
    this.baseUrl = url;
    var m = this.baseUrl.match(/^(.+?)index[.]php/i);
    this.basePath = ( m ? m[1] : this.baseUrl );
  },

  subject : {
    type : '',
    id : 0,
    guid : ''
  },

  showError : function(text){
    Smoothbox.close();
    Smoothbox.instance = new Smoothbox.Modal.String({
      bodyText : text
    });
  }

};


/**
 * Run Once scripts
 */
en4.core.runonce = {

  executing : false,
  
  fns : [],

  add : function(fn){
    this.fns.push(fn);
  },

  trigger : function(){
    if( this.executing ) return;
    this.executing = true;
    var fn;
    while( (fn = this.fns.shift()) ){
      try { 
        fn(); 
      }catch(err){}
    }
    this.fns = [];
    this.executing = false;
  }
  
};


/**
 * shutdown scripts
 */
en4.core.shutdown = {

  executing : false,
  
  fns : [],

  add : function(fn){
    this.fns.push(fn);
  },

  trigger : function(){
    if( this.executing ) return;
    this.executing = true;
    var fn;
    while( (fn = this.fns.shift()) ){
      try{fn();}catch(err){};
    }
    this.fns = [];
    this.executing = false;
  }
  
};

window.addEventListener('load', function(){
  en4.core.runonce.trigger();
});
// This is experimental
window.addEventListener('DOMContentLoaded', function(){
  en4.core.runonce.trigger();
});

window.addEventListener('unload', function() {
  en4.core.shutdown.trigger();
});


/**
 * Dynamic page loader
 */
en4.core.dloader = {

  loopId : false,

  currentHref : false,

  activeHref : false,

  xhr : false,

  frame : false,

  enabled : false,
  
  previous : false,
  
  hash : false,
  
  registered : false,

  setEnabled : function(flag) {
    this.enabled = ( flag == true );
  },

  start : function(options) {
    if( this.frame || this.xhr ) return this;

    this.activeHref = options.url;

    // Use an iframe for get requests
    if( $type(options.conntype) && options.conntype == 'frame' ) {
      options = scriptJquery.extend({
        data : {
          format : 'async',
          mode : 'frame'
        },
        styles : {
          'position' : 'absolute',
          'top' : '-200px',
          'left' : '-200px',
          'height' : '100px',
          'width' : '100px'
        },
        events : {
          //load : this.handleLoad.bind(this)
        }
      }, options);
      
      if( $type(options.url) ) {
        options.src = options.url;
        delete options.url;
      }
      // Add format as query string
      if( $type(options.data) ) {
        var separator = ( options.src.indexOf('?') > -1 ? '&' : '?' );
        options.src += separator + $H(options.data).toQueryString();
        delete options.data;
      }
      this.frame = scriptJquery.crtEle('iframe',options);
      this.frame.appendTo(scriptJquery(document.body));
    } else {
      options = scriptJquery.extend({
        method : 'get',
        dataType : 'html',
        data : {
          'format' : 'html',
          'mode' : 'xhr'
        },
        complete : this.handleLoad.bind(this)
      }, options);
      this.xhr = scriptJquery.ajax(options);
    }
    
    return this;
  },

  cancel : function() {
    if( this.frame ) {
      this.frame.destroy();
      this.frame = false;
    }
    if( this.xhr ) {
      this.xhr.cancel();
      this.xhr = false;
    }
    this.activeHref = false;
    return this;
  },

  attach : function(els) {
    var bind = this;

    if( !$type(els) ) {
      els = scriptJquery('a');
    }

    // Attach to links
    els.each(function(element) {
      if( !this.shouldAttach(element) ) {
        return;
      } else if( element.hasEvents() ) {
        return;
      }
      
      element.addEventListener('click', function(event) {
        if( !this.shouldAttach(element) ) {
          return;
        }
        
        var events = element.getEvents('click');
        if( events && events.length > 1 ) {
          return;
        }
        

        // Remove host + basePath
        var basePath = window.location.protocol + '//' + window.location.hostname + en4.core.baseUrl;
        var newPath;
        if( element.href.indexOf(basePath) === 0 ) {
          // Cancel link click
          if( event ) {
            event.stopPropagation();
            event.preventDefault();
          }
          
          // Start request
          newPath = element.href.substring(basePath.length);
          
          // Update url
          if( this.hasPushState() ) {
            this.push(element.href);
          } else {
            this.push(newPath);
          }
          
          // Make request
          this.startRequest(newPath);
        }
      }.bind(this));
    }.bind(this));

    // Monitor location
    //window.addEventListener('unload', this.monitorAddress.bind(this));
    this.currentHref = window.location.href;
    
    if( !this.registered ) {
      this.registered = true;
      if( this.hasPushState() ) {
        window.addEventListenerListener("popstate", function(e) {
          this.pop(e)
        }.bind(this));
      } else {
        this.loopId = this.monitor.periodical(200, this);
      }
    }
  },
  
  shouldAttach : function(element) {
    return (
      element.get('tag') == 'a' &&
      !element.onclick &&
      element.href &&
      !element.href.match(/^(javascript|[#])/) &&
      !element.hasClass('no-dloader') &&
      !element.hasClass('smoothbox')
    );  
  },

  handleLoad : function(response1, response2, response3, response4) {
    var response;
    
    if( this.frame ) {
      try { 
        response = (function() {
          return response1;
        }, function(){
          return this.frame.contentWindow.document.documentElement.innerHTML;
        }.bind(this));
      } catch(err){}
    } else if( this.xhr ) {
      response = response3;
    }

    if( response ) {
      // Shutdown previous scripts
      en4.core.shutdown.trigger();
      // Replace HTML
      scriptJquery('#global_content').html(response);
      // Evaluate scripts in content
      en4.core.request.evalScripts(scriptJquery('#global_content'));
      // Attach dloader to a's in content
      this.attach(scriptJquery('#global_content').find('a'));
      // Execute runonce
      en4.core.runonce.trigger();
    }
    
    this.cancel();
    this.activeHref = false;
  },
  
  handleRedirect : function(url) {
    this.push(url);
    this.startRequest(url);
  },

  startRequest : function(url) {
    
    var fullUrl = window.location.protocol + '//' + window.location.hostname + en4.core.baseUrl + url;
    //console.log(url, fullUrl);
    
    // Cancel current request if active
    if( this.activeHref ) {
      // Ignore if equal
      if( this.activeHref == url ) {
        return;
      }
      // Otherwise cancel an continue
      this.cancel();
    }

    //$('global_content').innerHTML = '<h1>Loading...</h1>';
      
    this.start({
      url : fullUrl,
      conntype : 'frame'
    });
    
  },
  
  
  
  // functions for history
  hasPushState : function() {
    //return false;
    return ('pushState' in window.history);
  },
  
  push : function(url, title, state) {
    if( this.previous == url ) return;
    
    if( this.hasPushState() ) {
      window.history.pushState(state || null, title || null, url);
      this.previous = url;
    } else {
      window.location.hash = url;
    }
  },
  
  replace : function(url, title, state) {
    if( this.hasPushState() ) {
      window.history.replaceState(state || null, title || null, url);
    } else {
      this.hash = '#' + url;
      this.push(url);
    }
  },
  
  pop : function(event) {
    if( this.hasPushState() ) {
      if( window.location.pathname.indexOf(en4.core.baseUrl) === 0 ) {
        this.onChange(window.location.pathname.substring(en4.core.baseUrl.length));
      } else {
        this.onChange(window.location.pathname);
      }
    } else {
      var hash = window.location.hash;
      if( this.hash == hash ) {
        return;
      }
      
      this.hash = hash;
      this.onChange(hash.substr(1));
    }
  },
  
  onChange : function(url) {
    this.startRequest(url);
  },
  
  back : function() {
    window.history.back();
  },
  
  forward : function() {
    window.history.forward();
  },
  
  monitor : function() {
    if( this.hash != window.location.hash ) {
      this.pop();
    }
  }
};


/**
 * Request pipeline
 */
en4.core.request = {

  activeRequests : [],

  isRequestActive : function(){
    return ( this.activeRequests.length > 0 );
  },

  send : function(req, options){
    options = options || {};
    if( !$type(options.force) ) options.force = false;
    

    // If there are currently active requests, ignore
    if(this.activeRequests.length > 0 && !options.force ){
      req.abort();
      return req;
    }
    this.activeRequests.push(req);
    // Process options
    if( !$type(options.htmlJsonKey) ) options.htmlJsonKey = 'body';
    if( $type(options.element) ){
      options.updateHtmlElement   = options.element;
      options.evalsScriptsElement = options.element;
    }

    // OnComplete
    var bind = this;
    req.success(function(response, response2, response3, response4){
      bind.activeRequests.forEach((re,i)=>{
        if(req == re){
          bind.activeRequests.splice(i,1);
        }
      });
      if(options.successCallBack){
        options.successCallBack(response, response2, response3, response4);
      }
      var htmlBody;
      var jsBody;

      // Get response
      if( $type(response) == 'object' ){ // JSON response
        htmlBody = response[options.htmlJsonKey];
      } else if( $type(response) == 'string' ){ // HTML response
        htmlBody = response;
        jsBody = response;
      }

      // An error probably occurred
      if( !response && !response3 && $type(options.updateHtmlElement) ){
        en4.core.showError('An error has occurred processing the request. The target may no longer exist.');
        return;
      }

      if( $type(response) == 'object' && $type(response.status) && response.status == false  && $type(response.error) === 'string' )
      {
        en4.core.showError(response.error + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
        return;
      }

      if( $type(response) == 'object' && $type(response.status) && response.status == false /* && $type(response.error) */ )
      {
        en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
        return;
      }
      if( $type(options.updateHtmlElement) && htmlBody ){
        if( $type(options.updateHtmlMode) && options.updateHtmlMode == 'append' ){
          scriptJquery(htmlBody).appendTo(scriptJquery(options.updateHtmlElement));
        } else if( $type(options.updateHtmlMode) && options.updateHtmlMode == 'prepend' ){

          scriptJquery(htmlBody).prependTo(scriptJquery(options.updateHtmlElement));

        } else if ($type(options.updateHtmlMode) && options.updateHtmlMode == 'comments' && scriptJquery(htmlBody).length > 1 && scriptJquery(htmlBody).eq(0).find('.comments').length) {
            scriptJquery(options.updateHtmlElement).find('.comments').remove();
            scriptJquery(options.updateHtmlElement).find('.feed_item_date').remove();
            if (scriptJquery(htmlBody).eq(0).find('.feed_item_date').length)
                scriptJquery(htmlBody).eq(0).find('.feed_item_date').appendTo(scriptJquery(options.updateHtmlElement.find('.feed_item_body')));
            scriptJquery(htmlBody).eq(0).find('.comments').appendTo(scriptJquery(options.updateHtmlElement.find('.feed_item_body')));
        } else if ($type(options.updateHtmlMode) && options.updateHtmlMode == 'comments2') {
          scriptJquery(options.updateHtmlElement).empty();
          scriptJquery(htmlBody).appendTo(scriptJquery(options.updateHtmlElement));
        } else {
          scriptJquery(options.updateHtmlElement).empty();
          scriptJquery(htmlBody).appendTo(scriptJquery(options.updateHtmlElement));
        }
        Smoothbox.bind(scriptJquery(options.updateHtmlElement));
      }

      if( !$type(options.doRunOnce) || !options.doRunOnce ){
        en4.core.runonce.trigger();
      }
    });

    req.error(function(){
      bind.activeRequests.forEach((re,i)=>{
        if(req == re){
          bind.activeRequests.splice(i,1);
        }
      });
    });
    return this;
  },
  
  evalScripts : function(e) {
    element = scriptJquery(this);
    if( !element ) return this;
    element.find('script').each(function(script){
      if( script.type != 'text/javascript' ) return;
      if( script.src ){
        scriptJquery.getScript(script.src);
      }
      else if( script.innerHTML.trim() ) {
        eval(script.innerHTML);
      }
    });

    return this;
  }

};


/**
 * Comments
 */
en4.core.comments = {

  loadComments : function(type, id, page){
    en4.core.request.send(scriptJquery.ajax({
      url : en4.core.baseUrl + 'core/comment/list',
      method:'post',
      dataType : 'html',
      data : {
        format : 'html',
        type : type,
        id : id,
        page : page
      }
    }), {
      'element' : scriptJquery('#comments')
    });
  },

  attachCreateComment : function(formElement){
    var bind = this;
    formElement.addEventListener('submit', function(event){
      event.stop();
      var form_values  = formElement.toQueryString();
          form_values += '&format=json';
          form_values += '&id='+formElement.identity.value;
      en4.core.request.send(scriptJquery.ajax({
        url : en4.core.baseUrl + 'core/comment/create',
        data : form_values
      }), {
        'element' : $('comments')
      });
      //bind.comment(formElement.type.value, formElement.identity.value, formElement.body.value);
    })
  },

 comment : function(formData){
    if( formData.body.trim() == '') return;
    scriptJquery('#comment-compose-container').after('<div class="comment_loading_overlay"></div>');
    en4.core.request.send(scriptJquery.ajax({
      method:'post',
      dataType: 'json',
      url : en4.core.baseUrl + 'core/comment/create',
      data : formData,
    }), {
      'element' : scriptJquery('#comments')
    });
  },

  like : function(type, id, comment_id) {
    en4.core.request.send(scriptJquery.ajax({
      url : en4.core.baseUrl + 'core/comment/like',
      method:'post',
      dataType:'json',
      data : {
        format : 'json',
        type : type,
        id : id,
        comment_id : comment_id
      }
    }), {
      'element' : scriptJquery('#comments')
    });
  },

  unlike : function(type, id, comment_id) {
    en4.core.request.send(scriptJquery.ajax({
      url : en4.core.baseUrl + 'core/comment/unlike',
      method:'post',
      dataType:'json',
      data : {
        format : 'json',
        type : type,
        id : id,
        comment_id : comment_id
      }
    }), {
      'element' : scriptJquery('#comments')
    });
  },

  showLikes : function(type, id){
    en4.core.request.send(scriptJquery.ajax({
      url : en4.core.baseUrl + 'core/comment/list',
      method:'post',
      dataType:'html',
      data : {
        format : 'html',
        type : type,
        id : id,
        viewAllLikes : true
      }
    }), {
      'element' : scriptJquery('#comments')
    });
  },

  deleteComment : function(type, id, comment_id) {
    if( !confirm(en4.core.language.translate('Are you sure you want to delete this?')) ) {
      return;
    }
    (scriptJquery.ajax({
      url : en4.core.baseUrl + 'core/comment/delete',
      method:'post',
      dataType:'json',
      data : {
        format : 'json',
        type : type,
        id : id,
        comment_id : comment_id
      },
      complete: function() {
        if(scriptJquery('#comment-' + comment_id).length) {
          scriptJquery('#comment-' + comment_id).remove();
        }
        try {
          var commentCount = scriptJquery('.comments_options span');
          var m = commentCount.html().match(/\d+/);
          var newCount = ( parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 1 ? parseInt(m[0]) - 1 : 0 );
          commentCount.html(commentCount.html().replace(m[0], newCount));
        } catch( e ) {}
      }
    }));
  }
};


en4.core.layout = {
  setLeftPannelMenu: function (type) {
      var pannelElement = scriptJquery(document).find('body')
      var navigationElement = pannelElement.find('.layout_core_menu_main .main_menu_navigation');
			var navMain = pannelElement.find('.navbar');
			var windowWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
      var setContent = function () {
        if (type == 'horizontal' && windowWidth >= 1025) {
          pannelElement.removeClass('global_left_panel');
          navigationElement.addClass('horizontal_core_main_menu');
          return;
        }
				navMain.removeClass('navbar-expand-lg')
        pannelElement.addClass('global_left_panel panel-collapsed');
        navigationElement.removeClass('horizontal_core_main_menu');
      };
      window.addEventListener('resize', setContent);
      setContent();
      // scrollBar.element.find('.scrollbar-content').on('scroll', function () {
      //   hideMenuTip();
      // });
    }
};
en4.core.languageAbstract = function(){
  var name = 'language';
  this.options = {
    locale : 'en',
    defaultLocale : 'en'
  }
  var data = {

  }

  this.initialize = function(options, data) {
    // b/c
    if(typeof options == 'object' ) {
      if(typeof options.lang !== "undefined") {
        this.addData(options.lang);
        delete options.lang;
      }
      if(typeof options.data !== "undefined") {
        this.addData(options.data);
        delete options.data;
      }
      this.setOptions(options);
    }
    if(typeof data == 'object' ) {
      this.setData(data);
    }
  }

  this.getName = function() {
    return this.name;
  }

  this.setLocale = function(locale) {
    this.options.locale = locale;
    return this;
  }

  this.getLocale = function() {
    return this.options.locale;
  }

  this.translate = function() {
    //try {
      if( arguments.length < 1 ) {
        return '';
      }

      // Process arguments
      var locale = this.options.locale;
      var messageId = arguments[0];
      var options = new Array();
      if( arguments.length > 1 ) {
        for( var i = 1, l = arguments.length; i < l; i++ ) {
          options.push(arguments[i]);
        }
      }

      // Check plural
      var plural = false;
      var number = 1;
      if(typeof messageId == 'object' ) {
        if( messageId.length > 2 ) {
          number = messageId.pop();
          plural = messageId;
        }
        messageId = messageId[0];
      }

      // Get message
      var message;
      if(typeof (this.data) !== "undefined" && typeof (this.data[messageId]) !== "undefined") {
        message = this.data[messageId];
      } else if( plural ) {
        message = plural;
        locale = this.options.defaultLocale;
      } else {
        message = messageId;
      }
      
      // Get correct message from plural
      if(typeof message == 'object') {
        var rule = this.getPlural(locale, number);
        if(typeof message[rule] !== "undefined") {
          message = message[rule];
        } else {
          message = message[0];
        }
      }

      if( options.length <= 0 ) {
        return message;
      }
      return message.vsprintf(options);
    // } catch( e ) {
    //   alert(e);
    // } 
  }
  function setData(data) {
    if(typeof data != 'object' && typeof data != 'hash' ) {
      return this;
    }
    this.data = data;
    return this;
  }

  this.addData = function(data) {
    if(typeof data != 'object' && typeof data != 'hash' ) {
      return this;
    }
    this.data = scriptJquery.extend(this.data, data);
    return this;
  }

  this.getData = function(data) {
    return this.data;
  }


  this.getPlural = function(locale, number) {

    if(typeof locale != 'string' ) {
      return 0;
    }

    if( locale == "pt_BR" ) {
      locale = "xbr";
    }

    if( locale.length > 3 ) {
      locale = locale.substring(0, locale.indexOf('_'));
    }

    switch( locale ) {
      case 'bo': case 'dz': case 'id': case 'ja': case 'jv': case 'ka':
      case 'km': case 'kn': case 'ko': case 'ms': case 'th': case 'tr':
      case 'vi':
        return 0;
        break;

      case 'af': case 'az': case 'bn': case 'bg': case 'ca': case 'da':
      case 'de': case 'el': case 'en': case 'eo': case 'es': case 'et':
      case 'eu': case 'fa': case 'fi': case 'fo': case 'fur': case 'fy':
      case 'gl': case 'gu': case 'ha': case 'he': case 'hu': case 'is':
      case 'it': case 'ku': case 'lb': case 'ml': case 'mn': case 'mr':
      case 'nah': case 'nb': case 'ne': case 'nl': case 'nn': case 'no':
      case 'om': case 'or': case 'pa': case 'pap': case 'ps': case 'pt':
      case 'so': case 'sq': case 'sv': case 'sw': case 'ta': case 'te':
      case 'tk': case 'ur': case 'zh': case 'zu':
        return (number == 1) ? 0 : 1;
        break;

      case 'am': case 'bh': case 'fil': case 'fr': case 'gun': case 'hi':
      case 'ln': case 'mg': case 'nso': case 'xbr': case 'ti': case 'wa':
        return ((number == 0) || (number == 1)) ? 0 : 1;
        break;

      case 'be': case 'bs': case 'hr': case 'ru': case 'sr': case 'uk':
        return ((number % 10 == 1) && (number % 100 != 11)) ? 0 :
          (((number % 10 >= 2) && (number % 10 <= 4) && ((number % 100 < 10)
          || (number % 100 >= 20))) ? 1 : 2);

      case 'cs': case 'sk':
        return (number == 1) ? 0 : (((number >= 2) && (number <= 4)) ? 1 : 2);

      case 'ga':
        return (number == 1) ? 0 : ((number == 2) ? 1 : 2);

      case 'lt':
        return ((number % 10 == 1) && (number % 100 != 11)) ? 0 :
          (((number % 10 >= 2) && ((number % 100 < 10) ||
          (number % 100 >= 20))) ? 1 : 2);

      case 'sl':
        return (number % 100 == 1) ? 0 : ((number % 100 == 2) ? 1 :
          (((number % 100 == 3) || (number % 100 == 4)) ? 2 : 3));

      case 'mk':
        return (number % 10 == 1) ? 0 : 1;

      case 'mt':
        return (number == 1) ? 0 :
          (((number == 0) || ((number % 100 > 1) && (number % 100 < 11))) ? 1 :
          (((number % 100 > 10) && (number % 100 < 20)) ? 2 : 3));

      case 'lv':
        return (number == 0) ? 0 : (((number % 10 == 1) &&
          (number % 100 != 11)) ? 1 : 2);

      case 'pl':
        return (number == 1) ? 0 : (((number % 10 >= 2) && (number % 10 <= 4) &&
          ((number % 100 < 10) || (number % 100 > 29))) ? 1 : 2);

      case 'cy':
        return (number == 1) ? 0 : ((number == 2) ? 1 : (((number == 8) ||
          (number == 11)) ? 2 : 3));

      case 'ro':
        return (number == 1) ? 0 : (((number == 0) || ((number % 100 > 0) &&
          (number % 100 < 20))) ? 1 : 2);

      case 'ar':
        return (number == 0) ? 0 : ((number == 1) ? 1 : ((number == 2) ? 2 :
          (((number >= 3) && (number <= 10)) ? 3 : (((number >= 11) &&
          (number <= 99)) ? 4 : 5))));

      default:
        return 0;
    }
  }
};


en4.core.language = new en4.core.languageAbstract();

/**
 * ReCaptcha scripts
 */
en4.core.reCaptcha = {
  lodedJs: [],
  render: function () {
    scriptJquery('.g-recaptcha').each(function (e) {
      let $el = scriptJquery(this);
      if ($el.data('recaptcha-loaded')) {
        return;
      }
      $el.empty();
      grecaptcha.render($el[0], {
        sitekey: $el.attr('data-sitekey'),
        theme: $el.attr('data-theme'),
        type: $el.attr('data-type'),
        tabindex: $el.attr('data-tabindex'),
        size: $el.attr('data-size'),
      });
      $el.data('recaptcha-loaded', true);
    });
  },
  loadJs: function(js) {
    if (this.lodedJs.indexOf(js) != -1) {
      return;
    }
    this.lodedJs.push(js);
    scriptJquery.getScript(js);
  }
};

window.en4CoreReCaptcha = function () {
  en4.core.reCaptcha.render();
};

})(); // END NAMESPACE


//Check upload file size.
scriptJquery(document).on('change',"input[type='file']",function() {
  if(this.files.length > 0) {
    var FileSize = this.files[0].size; // in byte
    if(FileSize > post_max_size) {
      alert("The size of the file exceeds the limits set on the server.");
      scriptJquery(this).val('');
    } else {
      if(scriptJquery(this).data('function')){
        eval(scriptJquery(this).data('function')+"()");
      }
    }
  }
});
