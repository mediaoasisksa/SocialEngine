function isBody(element){
  if ( !(element instanceof scriptJquery) ) {
    element = scriptJquery(element)
  }
  return (/^(?:body|html)$/i).test(element.prop("tagName"));
}

var window, document = null;

(function(){
  var window = this;
  var document = this.document;
  document.window = this;
  document.html = document.documentElement;
})();

function getCompatElement(element){
  var doc = element.getDocument();
  return (!doc.compatMode || doc.compatMode == 'CSS1Compat') ? doc.html : doc.body;
}

scriptJquery(document).ready(function () {

  document.getDocument = function() {
    return this;
  }
  document.getWindow = function(){
    return this.window;
  }

  document.getScroll = window.getScroll = function(){
    var win = this.getWindow(), doc = getCompatElement(this);
    return {x: win.pageXOffset || doc.scrollLeft, y: win.pageYOffset || doc.scrollTop};
  }

  window.getDocument = function() {
    return this.document;
  }
  window.getWindow = function() {
    return this;
  }
  document.getSize = document.seaogetSize = window.getSize = window.seaogetSize = function() {
   return {x: scriptJquery(window).width(), y: scriptJquery(window).height()};
  }

  document.getScrollSize = window.getScrollSize = function(){
    var doc = getCompatElement(this),
      min = this.getSize(),
      body = this.getDocument().body;
    return {x: Math.max(doc.scrollWidth, body.scrollWidth, min.x), y: Math.max(doc.scrollHeight, body.scrollHeight, min.y)};
  }
  document.getPosition = window.getPosition = function() {
    return {x: 0, y: 0};
  }
  document.getCoordinates = window.getCoordinates = function(){
    var size = this.getSize();
    return {top: 0, left: 0, bottom: size.y, right: size.x, height: size.y, width: size.x};
  }

  document.getHeight = window.getHeight = scriptJquery.fn.getHeight = function() {
    return this.getSize().y;
  }
  document.getWidth = window.getWidth = scriptJquery.fn.getWidth = function() {
    return this.getSize().x;
  }

  scriptJquery.fn.getScroll = function(){
    if (isBody(this)) return this.getWindow().getScroll();
    return {x: this.scrollLeft, y: this.scrollTop};
  }
  scriptJquery.fn.getScrollSize = function(){
    if (isBody(this)) return this.getWindow().getScrollSize();
    return {x: this.scrollWidth, y: this.scrollHeight};
  }
  scriptJquery.fn.getDocument = function() {
    return ( this.length > 0 ) ? this[0].ownerDocument : null;
  }
  scriptJquery.fn.getWindow = function(){
    return window;
  }

  window.getScrollTop = document.getScrollTop = scriptJquery.fn.getScrollTop = function() {
    return this.getScroll().x;
  }
  window.getScrollLeft = document.getScrollLeft = scriptJquery.fn.getScrollLeft = function() {
    return this.getScroll().y;
  }

  scriptJquery.fn.getPosition = scriptJquery.fn.seaogetPosition = function(relative){
    if ( this.length != 1 ) { return; }
    var offset = this.offset();
    var position = {
      x: offset.left - this.scrollLeft(),
      y: offset.top - this.scrollTop()
    };
    if ( relative && ( relative.length == 1 ) ){
      var relativePosition = relative.seaogetPosition();
      return {
        x: position.left - relativePosition.x - relative.css('border-left-width'),
        y: position.top - relativePosition.y - relative.css('border-top-width')
      };
    }
    return position;
  }

  scriptJquery.fn.autogrow = function() {
      this.each(function () {
        this.setAttribute("style", "height:" + (this.scrollHeight) + "px;overflow-y:hidden;");
      }).on("input", function () {
        this.style.height = 0;
        this.style.height = (this.scrollHeight) + "px";
      });
  }

  scriptJquery.fn.getSize = scriptJquery.fn.seaogetSize = function() {
    // It work on class or id elements not on scriptJquery(window)
    if ( this.length == 0 ) {
      return;
    }
    if (isBody(this)) {
      return {x: scriptJquery(window).width(), y: scriptJquery(window).height()};
    }
    // no problem with any element then body
    return {x: this[0].offsetWidth, y: this[0].offsetHeight};
  }

  scriptJquery.fn.seaoset = function( task, htmlContent ) {
    if ( task == 'html' ) {
      this.html(htmlContent);
    }
  }

  scriptJquery.fn.getCoordinates = scriptJquery.fn.seaoGetCoordinates = function() {

    var offSet = this.offset();
    obj = {};
    obj.left = (offSet.left).seaotoInt() - (this.scrollLeft()).seaotoInt();
    obj.top = (offSet.top).seaotoInt() - (this.scrollTop()).seaotoInt();
    obj.width = this[0].offsetWidth;
    obj.height = this[0].offsetHeight;
    obj.right = obj.left + obj.width;
    obj.bottom = obj.top + obj.height;
    return obj;
  };

  scriptJquery.fn.seaoSerializeObject = function() {
    var o = {};
    var a = this.serializeArray();
    scriptJquery.each(a, function() {
      if (o[this.name] !== undefined) {
        if (!o[this.name].push) {
          o[this.name] = [o[this.name]];
        }
        o[this.name].push(this.value || '');
      } else {
        o[this.name] = this.value || '';
      }
    });
    return o;
  };
  scriptJquery.fn.injectSeaoCustom = function( element, position ) {
    if ( typeof element  == 'string' ) { element = scriptJquery( element ); }
    else if ( !( element instanceof scriptJquery ) ) { element = scriptJquery( element ); }
    var listPositions = [ 'bottom', 'top', 'after', 'before' ];
    if ( position == undefined || typeof position != 'string' || listPositions.indexOf( position ) == -1 ) {
      position = 'bottom';
    }
    if ( position == 'bottom' ) {
      this.appendTo(element)
    } else if( position == 'top' ) {
      this.prependTo(element)
    } else if( position == 'before' ) {
      this.insertBefore(element)
    } else if( position == 'after' ) {
      this.insertAfter(element)
    }
    return this;
  }

  String.prototype.parseQueryString = String.prototype.seaoParseQueryString = function(decodeKeys, decodeValues){ // parse query string
    if (decodeKeys == null) decodeKeys = true;
    if (decodeValues == null) decodeValues = true;
    var vars = this.split(/[&;]/),
    object = {};
    if (!vars.length) return object;

    vars.forEach(function(val){
      var index = val.indexOf('=') + 1,
        value = index ? val.substr(index) : '',
        keys = index ? val.substr(0, index - 1).match(/([^\]\[]+|(\B)(?=\]))/g) : [val],
        obj = object;
      if (!keys) return;
      if (decodeValues) value = decodeURIComponent(value);
      keys.forEach(function(key, i){
        if (decodeKeys) key = decodeURIComponent(key);
        var current = obj[key];
        if (i < keys.length - 1) obj = obj[key] = current || {};
        else if (scriptJquery.type(current) == 'array') current.push(value);
        else obj[key] = current != null ? [current, value] : value;
      });
    });
    return object;
  }

  // Add functions to Numbers here

  Number.prototype.limit = Number.prototype.seaolimit = function(min, max){
    return Math.min(max, Math.max(min, this));
  }
  Number.prototype.round = Number.prototype.seaoround = function(precision){
    precision = Math.pow(10, precision || 0).toFixed(precision < 0 ? -precision : 0);
    return Math.round(this * precision) / precision;
  }
  Number.prototype.times = Number.prototype.seaotimes = function(fn, bind){
    for (var i = 0; i < this; i++) fn.call(bind, i, this);
  }
  Number.prototype.toFloat = Number.prototype.seaotoFloat = function(){
    return parseFloat(this);
  }
  Number.prototype.toInt = Number.prototype.seaotoInt = function(base){
    return parseInt(this, base || 10);
  }
  String.prototype.test = String.prototype.seaotest = function(regex, params){
    return ( ( regex instanceof RegExp ) ? regex : new RegExp('' + regex, params)).test(this);
  }
  String.prototype.contains = String.prototype.seaocontains = function(string, separator){
    return (separator) ? (separator + this + separator).indexOf(separator + string + separator) > -1 : String(this).indexOf(string) > -1;
  }
  String.prototype.trim = String.prototype.seaotrim = function(){
    return String(this).replace(/^\s+|\s+$/g, '');
  }
  String.prototype.clean = String.prototype.seaoclean = function(){
    return String(this).replace(/\s+/g, ' ').trim();
  }
  String.prototype.camelCase = String.prototype.seaocamelCase = function(){
    return String(this).replace(/-\D/g, function(match){
      return match.charAt(1).toUpperCase();
    });
  }
  String.prototype.hyphenate = String.prototype.seaohyphenate = function(){
    return String(this).replace(/[A-Z]/g, function(match){
      return ('-' + match.charAt(0).toLowerCase());
    });
  }
  String.prototype.capitalize = String.prototype.seaocapitalize = function(){
    return String(this).replace(/\b[a-z]/g, function(match){
      return match.toUpperCase();
    });
  }
  String.prototype.escapeRegExp = String.prototype.seaoescapeRegExp = function(){
    return String(this).replace(/([-.*+?^${}()|[\]\/\\])/g, '\\$1');
  }
  String.prototype.toInt = String.prototype.seaotoInt = function(base){
    return parseInt(this, base || 10);
  }
  String.prototype.toFloat = String.prototype.seaotoFloat = function(){
    return parseFloat(this);
  }
  String.prototype.hexToRgb = String.prototype.seaohexToRgb = function(array){
    var hex = String(this).match(/^#?(\w{1,2})(\w{1,2})(\w{1,2})$/);
    return (hex) ? hex.slice(1).hexToRgb(array) : null;
  }
  String.prototype.rgbToHex = String.prototype.seaorgbToHex = function(array){
    var rgb = String(this).match(/\d{1,3}/g);
    return (rgb) ? rgb.rgbToHex(array) : null;
  }
  String.prototype.substitute = String.prototype.seaosubstitute = function(object, regexp){
    return String(this).replace(regexp || (/\\?\{([^{}]+)\}/g), function(match, name){
      if (match.charAt(0) == '\\') return match.slice(1);
      return (object[name] != null) ? object[name] : '';
    });
  }

});

window.addEventListener('DOMContentLoaded', function () {
  en4.seaocore.isDomReady = true;
  en4.seaocore.setDomElements();
});


(function () {

  this.SEATips = function( elements, options ) {

    if ( !elements.length ) {
      return;
    }

    for( i=0; i<elements.length; i++ ) {
      var element = scriptJquery(elements[i]);
      var tooltipElement = scriptJquery.crtEle('div', { 'class': options.className, }).appendTo( 'body' );
      var toolTipCss =  { position: 'absolute', background: 'white', border: '1px solid black', padding: '10px', zIndex: 999, display: 'none' }
      tooltipElement.html( element.html() );
      if ( !options.css ) {
        toolTipCss = scriptJquery.extend( toolTipCss, options.css );
      }
      tooltipElement.css( toolTipCss );
      tooltipElement.appendTo('body');

      element.mousemove(function(e){
        tooltipElement.css( { top: e.pageY + 30 + 'px', left: e.pageX + 30 + 'px' } );
      })
      .hover(function(){
          tooltipElement.show();
        }, function(){
          tooltipElement.hide();
        }
      );
    }

    /*this.Extends = Tips({}),
    options = {
      canHide: true
    },
    this.hide = function (element) {
      if (!this.options.canHide)
        return;
      if (!this.tip)
        document.id(this);
      this.fireEvent('hide', [this.tip, element]);
    },
    this.position = function (event) {
      if (!this.tip)
        document.id(this);
      var size = window.getSize(), scroll = window.getScroll(),
      tip = {
        x: this.tip.offsetWidth,
        y: this.tip.offsetHeight
      },
      props = {
        x: 'left',
        y: 'top'
      },
      bounds = {
        y: false,
        x2: false,
        y2: false,
        x: false
      },
      obj = {};
      for (var z in props) {
        obj[props[z]] = event.page[z] + this.options.offset[z];
        if (obj[props[z]] < 0)
          bounds[z] = true;
        if ((event.page[z] - scroll[z]) > size[z] - this.options.windowPadding[z]) {
          var extra = 1;
          if (z == 'x')
            extra = 51;
          obj[props[z]] = event.page[z] - tip[z] + extra;
          bounds[z + '2'] = true;
        }
      }

      this.fireEvent('bound', bounds);
      this.tip.css(obj);
      Smoothbox.bind(this.tip);
    }*/


  };
})();

en4.seaocore = {
  headerElement: 'global_header',
  footerElement: 'global_footer',
  contentElement: 'global_content',
  contentWrapperElement: 'global_wrapper',

  isDomElementSet: false,
  isDomReady: false,

  setDomElements: function () {
    this.headerElement = scriptJquery('#se-header').length > 0 ? 'se-header' : 'global_header';
    this.footerElement = scriptJquery('#se-footer').length > 0 ? 'se-footer' : 'global_footer';
    this.contentElement = scriptJquery('#se-content').length > 0 ? 'se-content' : 'global_content';
    this.contentWrapperElement = scriptJquery('#se-main').length > 0 ? 'se-main' : 'global_wrapper';
    this.isDomElementSet = true;
  },

  setLayoutWidth: function (elementId, width) {
    var layoutColumn = null;
    if (scriptJquery('#'+elementId).parents('.layout_left').length) {
      layoutColumn = scriptJquery('#'+elementId).parents('.layout_left');
    } else if (scriptJquery('#'+elementId).parents('.layout_right').length) {
      layoutColumn = scriptJquery('#'+elementId).parents('.layout_right');
    } else if (scriptJquery('#'+elementId).parents('.layout_middle').length) {
      layoutColumn = scriptJquery('#'+elementId).parents('.layout_middle');
    }
    if (layoutColumn) {
      layoutColumn.css('width', width);
    }
    scriptJquery('#'+elementId).remove();
  },
  // load multiple assets and run callback function on successful loading
  // Example - en4.seaocore.loadAssets([{type: 'javascript, src : 'script.js'} ], callback,{prependPath : en4.core.staticBaseUrl + 'required-path/'});
  loadAssets: function (assetsArray, callback, options) {
    assetIndex = 0;
    assetCount = assetsArray.length;
    prependPath = options && options.prependPath ? options.prependPath : '';
    assetsArray.each(function (asset) {
      Asset[asset.type](prependPath + asset.src, {
        onLoad: function () {
          if (++assetIndex == assetCount && typeof callback == 'function') {
            callback();
          }
        }
      })
    })
  },
  getDomElements: function (element) {
    if (!this.isDomElementSet || !this.isDomReady) {
      this.setDomElements();
    }
    if (element == 'header') {
      return this.headerElement;
    } else if (element == 'footer') {
      return this.footerElement;
    } else if (element == 'content') {
      return this.contentElement;
    } else if (element == 'contentWrapper') {
      return this.contentWrapperElement;
    }
  }
};
/**
 * SEAO core login & Signup
 */
en4.seaocore.popupLoginSignup = {
  params: {
    togglePasswordType: 'password', // manage password show hide
    confirmtogglePasswordType: 'password', // manage password show hide
    enableSignup: true,
    enableLogin: true,
    autoOpenLogin: false,
    autoOpenSignup: false,
    allowClose: true,
    openDelay: 100,
    popupVisibilty:0 // X numbers of time,
    
  },
  init: function (params) {
    this.params = scriptJquery.extend(this.params, params);
    this.attachEvents(params);
    if (scriptJquery('#socialsignup_popup_div').length)
      scriptJquery('#socialsignup_popup_div').addClass('socialsignup_popup_div');
    if (scriptJquery('#sociallogin_signup_popup').length)
      scriptJquery('#sociallogin_signup_popup').addClass('sociallogin_signup_popup');
    
    if (this.params.autoOpenLogin && this.ckeckForAutoOpen()) {
      setTimeout(function () {
        this.setPopupContent(null, 'seaocore_login_signup_popup', 'seao_user_auth_popup');
        this.showPopupForm('seao_user_auth_popup');
      }.bind(this), this.params.openDelay);
    } else if (this.params.autoOpenSignup && this.ckeckForAutoOpen()) {
      setTimeout(function () {
        this.setPopupContent(null, 'seaocore_login_signup_popup', 'seao_user_signup_popup');
        this.showPopupForm('seao_user_signup_popup');
      }.bind(this), this.params.openDelay);
    }
  },
  ckeckForAutoOpen: function () {
    var falg = true;
    if (this.params.popupVisibilty == 0) {
      return falg;
    }
    if (typeof (Storage) !== "undefined") {
      var popupClosedOn = localStorage.getItem("popupClosedOn");
      if (popupClosedOn == null || popupClosedOn == undefined) {
        localStorage.setItem("popupClosedOn", new Date());
      } else {
        var todayDate = new Date();
        var closedDate = new Date(popupClosedOn);
        var diffDays = Math.abs(todayDate.getTime() - closedDate.getTime()) / (1000 * 3600 * 24);
        if (this.params.popupVisibilty <= diffDays) {          
          localStorage.setItem("popupClosedOn", new Date());
        }else {
          falg = false;
        }
      }
    }
    return falg;
  },
  attachEvents: function (params) {
    scriptJquery('.user_signup_link').each(function (el) {
      scriptJquery(this).addClass('seao_popup_user_signup_link').removeClass('user_signup_link');
    });
    scriptJquery('.user_auth_link').each(function (el) {
      scriptJquery(this).addClass('seao_popup_user_auth_link').removeClass('user_auth_link');
      ;
    });
    if (params.enableSignup) {
      scriptJquery('.seao_popup_user_signup_link').off('click').on('click', function (event) {
        this.setPopupContent(event, 'seaocore_login_signup_popup', 'seao_user_signup_popup');
        this.showPopupForm('seao_user_signup_popup');
      }.bind(this));
    }
    if (params.enableLogin) {
      scriptJquery('.seao_popup_user_auth_link').off('click').on('click', function (event) {
        this.setPopupContent(event, 'seaocore_login_signup_popup', 'seao_user_auth_popup');
        this.showPopupForm('seao_user_auth_popup');
      }.bind(this));
    }
  },
  setPopupContent: function (event, contentId) {
    if (event) {
      //event.stop();
      event.preventDefault();
      event.stopPropagation();
    }
    if ( !seaocoreSignupLoginPopupContentStorage ) {
        seaocoreSignupLoginPopupContentStorage = scriptJquery('#seaocore_login_signup_popup_widget_parent_div').children();
    } else {
        seaocoreSignupLoginPopupContentStorage.appendTo( scriptJquery('#seaocore_login_signup_popup_widget_parent_div') );
    }
    Smoothbox.open(scriptJquery('#'+contentId),{element: scriptJquery('#'+contentId).css('display', 'block'), class: 'seaocore_login_popup_wrapper', closable: this.params.allowClose});
    if(!this.params.allowClose) {      
      Smoothbox.instance.close = function() {};
    }
    //Smoothbox.setHtmlScroll("hidden");
    this.setLoginForm(Smoothbox.instance.content.find('.seao_user_auth_popup form'));
    this.setSignupForm(Smoothbox.instance.content.find('.seao_user_signup_popup form'));
    Smoothbox.instance.content.find('ul._navigation').find('li').on('click', function (event) {
      this.showPopupForm(scriptJquery(event.target).attr('data-role'));
    }.bind(this));
  },
  setLoginForm: function (el) {
    // if (el.hasClass('seaocore_popup_user_form_login')) {
    //   return;
    // }
    el.addClass('seaocore_popup_user_form_login');
    var handelerOnFocus = function (event) {
      scriptJquery(event.target).closest('.form-wrapper').addClass('form-wapper-focus');
    };
    var handelerOnBlur = function (event) {
      scriptJquery(event.target).closest('.form-wrapper').removeClass('form-wapper-focus');
    };
    if (el.find("#twitter-wrapper").length || el.find("#facebook-wrapper").length) {
      var wrapperDiv = scriptJquery.crtEle("div", {
        id: "seaocore_loginform_sociallinks"
      });
      wrapperDiv.appendTo(el);
      if (el.find("#facebook-wrapper")[0]) {
        el.find("#facebook-element").attr('title', en4.core.language.translate("Login with Facebook"));
        el.find("#facebook-wrapper").appendTo(wrapperDiv);
      }

      if (el.find("#twitter-wrapper").length) {
        el.find("#twitter-element").attr('title', en4.core.language.translate("Login with Twitter"));
        el.find("#twitter-wrapper").appendTo(wrapperDiv);
      }
    }
    el.find('input').each(function (i, inputEl) {
      inputEl = scriptJquery(inputEl);
      var type = inputEl.attr('type');
      if (type == 'email') {
        inputEl.closest('.form-wrapper').addClass('form-email-wrapper');
      }
      if (inputEl.attr('id') == 'password') {
        // remove core show hide password
        if (el.find(".user_showhidepassword").length) {
          el.find(".user_showhidepassword").remove();
        }
        // remove repeating items
        inputEl.siblings("#show-hide-password-element").remove();
        var showHideEl = scriptJquery.crtEle('div', {
          'id': 'show-hide-password-element',
          'class': 'show-hide-password-form-element fa fa-eye'
        }).appendTo(inputEl.closest('.form-element'));
        // custom code
        if(inputEl.attr('type') != 'password') {
          showHideEl.addClass('fa-eye-slash').removeClass('fa-eye');
        }
        showHideEl.on('click', function () {
          if (inputEl.attr('type') == 'password') {
            showHideEl.addClass('fa-eye-slash').removeClass('fa-eye');
            inputEl.attr('type', 'text');
          } else {
            showHideEl.removeClass('fa-eye-slash').addClass('fa-eye');
            inputEl.attr('type', 'password');
          }
        });
        scriptJquery("#user_form_login").on('submit', function () {
          inputEl.attr('type', 'password');
          showHideEl.removeClass('fa-eye-slash').addClass('fa-eye');
        });
      }
      if ((type == 'text' || type == 'email' || type == 'password') && inputEl.closest('.form-wrapper').find('label').html()) {
        inputEl.attr('placeholder', inputEl.closest('.form-wrapper').find('label').html());
        inputEl.closest('.form-wrapper').addClass('_slpff');
        inputEl.on('focus', handelerOnFocus);
        inputEl.on('blur', handelerOnBlur);
      }
    });
  },

  setSignupForm: function (formEl) {
    // if (formEl.hasClass('seaocore_popup_user_form_signup')) {
    //   return;
    // }
    formEl.addClass('seaocore_popup_user_form_signup');
    var handelerOnFocus = function (event) {
      scriptJquery(event.target).closest('.form-wrapper').addClass('form-wapper-focus');
    };
    var handelerOnBlur = function (event) {
      scriptJquery(event.target).closest('.form-wrapper').removeClass('form-wapper-focus');
    };
    if (formEl.find("#twitter-wrapper").length || formEl.find("#facebook-wrapper").length) {
      var wrapperDiv = scriptJquery.crtEle("div", {
        id: "seaocore_signupform_sociallinks"
      });
      wrapperDiv.injectSeaoCustom(formEl, 'top');
      if (formEl.find("#facebook-wrapper").length) {
        var wrapperDiv = scriptJquery("span", {
          id: "facebook"
        }).html("<div id='facebook-wrapper'><div id='facebook-element'><a href='" + en4.core.baseUrl + "user/auth/facebook'><img border='0' alt='Connect with Facebook' title = 'Login with Facebook' src='" + en4.core.baseUrl + "application/modules/User/externals/images/facebook-sign-in.gif'></a></div></div>");
        wrapperDiv.appendTo(wrapperDiv);
      }

      if (formEl.find("#twitter-wrapper").length) {
        var wrapperDiv = scriptJquery.crtEle("span", {
          id: "twitter"
        }).html("<div id='twitter-wrapper'><div id='twitter-element'><a href='" + en4.core.baseUrl + "user/auth/twitter'><img border='0' alt='Connect with Twitter' title = 'Login with Twitter' src='" + en4.core.baseUrl + "application/modules/User/externals/images/twitter-sign-in.gif'></a></div></div>");
        wrapperDiv.appendTo(wrapperDiv);
      }
    }

    var className = 'seao_seaolightbox_signup';
    if (wrapperDiv && wrapperDiv.length && wrapperDiv.find('.plan_subscriptions_container')) {
      className = className + ' seaocore_seaolightbox_plan_subscriptions';
    }

    formEl.find('input').each(function (i, inputEl) {
      inputEl = scriptJquery(inputEl);
      if(inputEl.attr('icon') &&  (inputEl.attr('icon') == 'fa fa-user' || inputEl.attr('icon') == 'fa-user') ){
        inputEl.closest('.form-wrapper').addClass('form-user-wrapper');
      }
      var type = inputEl.attr('type');
      if (type == 'email') {
        inputEl.closest('.form-wrapper').addClass('form-email-wrapper');
      }
      if ((type == 'text' || type == 'email' || type == 'password') && inputEl.closest('.form-wrapper').find('label').html()) {
        inputEl.attr('placeholder', inputEl.closest('.form-wrapper').find('label').html());
        inputEl.closest('.form-wrapper').addClass('_sspff');
        inputEl.on('focus', handelerOnFocus);
        inputEl.on('blur', handelerOnBlur);
      }
    });
    if (formEl.find('#password-element').length && formEl.find('#passconf-element').length) {
      formEl.find('#password-element').closest('.form-wrapper').addClass('_spfhf');
      formEl.find('#passconf-element').closest('.form-wrapper').addClass('_spfhf');
    }
    var languageEl = formEl.find('#language-element'),
            timezoneEl= formEl.find('#timezone-element');
    var canMakeSmallFileds = !!languageEl.length && !!timezoneEl.length;
    if (timezoneEl.length && !formEl.find('#timezone-option-label').length) {
      formEl.find('#timezone-wrapper').addClass('_spfhf');
      var el = formEl.find('#timezone');
      var options = scriptJquery.crtEle('option', {
        'id': 'timezone-option-label',
        'disabled': 'disabled',
        'class': '_sspff_option_label'
      }).html(el.closest('.form-wrapper').find('label').html());
      options.injectSeaoCustom(el, 'top');
      el.closest('.form-wrapper').addClass('_sspff');
      if (canMakeSmallFileds) {
        el.closest('.form-wrapper').addClass('_spfhf');
      }
    }
    if (languageEl.length && !formEl.find('#language-option-label').length) {
      formEl.find('#language-wrapper').addClass('_spfhf')
      var el = formEl.find('#language');
      var options = scriptJquery.crtEle('option', {
        'id': 'language-option-label',
        'class': '_sspff_option_label',
        'disabled': 'disabled',
      }).html(el.closest('.form-wrapper').find('label').html());
      options.injectSeaoCustom(el, 'top');
      el.closest('.form-wrapper').addClass('_sspff');
      if (canMakeSmallFileds) {
        el.closest('.form-wrapper').addClass('_spfhf');
      }
    }
    if (formEl.find('#profile_type').length && formEl.find('#profile_type').attr('type') != 'hidden' && !formEl.find('#profile_type-option-label').length) {
      var el = formEl.find('#profile_type');
      var addedFields = false;
      el.find('option').each(function (i, optionEl) {
        optionEl = scriptJquery(optionEl);
        if (!optionEl.val().trim()) {
          optionEl.html(optionEl.closest('.form-wrapper').find('label').html()).addClass('_sspff_option_label');
          addedFields = true;
        }
      });
      if (!addedFields) {
        var options = scriptJquery.crtEle('option', {
          'id': 'profile_type-option-label',
          'class': '_sspff_option_label',
          'disabled': 'disabled'
        }).html(el.closest('.form-wrapper').find('label').html());
        options.injectSeaoCustom(el, 'top');
      }
      el.closest('.form-wrapper').addClass('_sspff seaocore_popup_profile_type_form_field');
    }

    // custom code, as toggle password is managed by core, content is shown in smoothbox
    // it's not changing the icon as same listener is calling again so prevent this
    if(togglePassword && scriptJquery(togglePassword).length) {
      const self = this;
      scriptJquery(togglePassword).off('click').on('click', function(event) {
        event.stopPropagation();
        if (formEl.find("#password").length) {
          var password = formEl.find("#password");
        } else if (formEl.find("#oldPassword").length) {
          var password = formEl.find("#oldPassword");
        } else {
          var password = formEl.find("#signup_password");
        }

        self.params.togglePasswordType =
          self.params.togglePasswordType == "password" ? "text" : "password";
        password.attr('type', self.params.togglePasswordType);

        self.params.togglePasswordType == "password"
          ? this.classList.remove("fa-eye-slash")
          : this.classList.add("fa-eye-slash");
      });
    }

    if(confirmtogglePassword && scriptJquery(confirmtogglePassword).length) {
      const self = this;
      scriptJquery(confirmtogglePassword).off('click').on('click', function(event) {
        event.stopPropagation();
        if (formEl.find("#passconf").length) {
          var password = formEl.find("#passconf");
        } else if (formEl.find("#passwordConfirm").length) {
          var password = formEl.find("#passwordConfirm");
        } else {
          var password = formEl.find("#password_confirm");
        }

        self.params.confirmtogglePasswordType =
          self.params.confirmtogglePasswordType == "password" ? "text" : "password";
        password.attr('type', self.params.confirmtogglePasswordType);

        self.params.confirmtogglePasswordType == "password"
          ? this.classList.remove("fa-eye-slash")
          : this.classList.add("fa-eye-slash");
      });      
    }
  },
  showPopupForm: function (elementId) {
    Smoothbox.instance.content.find('ul._navigation > li').removeClass('active');
    Smoothbox.instance.content.find('ul._navigation > li[data-role=' + elementId + ']').addClass('active');
    Smoothbox.instance.content.find('._form_wapper ._form_cont').hide();
    Smoothbox.instance.content.find('.' + elementId).show();
    en4.core.reCaptcha.render();
    // Smoothbox.instance.doAutoResize()
    // SmoothboxSEAO.doAutoResize();
  }
};
/**
 * likes
 */
en4.seaocore.likes = {
  like: function (type, id, show_bottom_post, comment_id) {
    en4.core.request.send(scriptJquery.ajax({
      url: en4.core.baseUrl + 'seaocore/comment/like',
      method: 'POST',
      dataType: 'json',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: 0,
        show_bottom_post: show_bottom_post
      },
      success: function (responseJSON) {

        if ($type(responseJSON) == 'object' && $type(responseJSON.status)) {
          if (scriptJquery('#'+type + '_' + id + 'like_link').length)
            scriptJquery('#'+type + '_' + id + 'like_link').css( 'display', "none" );
          if (scriptJquery('#'+type + '_' + id + 'unlike_link').length)
            scriptJquery('#'+type + '_' + id + 'unlike_link').css( 'display', "inline-block" );

        }
      }
    }), {
      'element': document.getElementById('comments' + '_' + type + '_' + id)
    });
  },
  unlike: function (type, id, show_bottom_post, comment_id) {

    en4.core.request.send(scriptJquery.ajax({
      url: en4.core.baseUrl + 'seaocore/comment/unlike',
      method: 'POST',
      dataType: 'json',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id,
        show_bottom_post: show_bottom_post
      },
      success: function (responseJSON) {
        if ($type(responseJSON) == 'object' && $type(responseJSON.status)) {
          if (scriptJquery('#'+type + '_' + id + 'unlike_link').length)
            scriptJquery('#'+type + '_' + id + 'unlike_link').css( 'display', "none" );
          if (scriptJquery('#'+type + '_' + id + 'like_link').length)
            scriptJquery('#'+type + '_' + id + 'like_link').css( 'display', "inline-block" );

        }
      }
    }), {
      'element': document.getElementById('comments' + '_' + type + '_' + id)
        //      "force":true
    });

  }
};

en4.seaocore.browser = {
  // true if Browser is IE otherwise false
  msieversion: function() {
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");
    // If Internet Explorer, return true
    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
      return true;
    } else {
      // If another browser, return 0 
      return false;
    }
  }
}

en4.seaocore.comments = {
  loadComments: function (type, id, page, show_bottom_post) {
    en4.core.request.send(scriptJquery.ajax({
      url: en4.core.baseUrl + 'seaocore/comment/list',
      method: 'POST',
      dataType: 'html',
      data: {
        format: 'html',
        type: type,
        id: id, 
        page: page,
        show_bottom_post: show_bottom_post
      }
    }), {
      'element': document.getElementById('comments' + '_' + type + '_' + id),
      "force": true
    });
  },
  attachCreateComment: function (formElement, type, id, show_bottom_post) {
    var bind = this;
    if (show_bottom_post == 1) {
      formElement.addEventListener((en4.seaocore.browser.msieversion()) ? 'keydown' : 'keypress', function (event) {
        if (event.shift && event.key == 'Enter') {
        } else if (event.key == 'Enter') {
          event.preventDefault();
          var form_values = '';
          scriptJquery(formElement).serializeArray().forEach((item) => {
            form_values += item.name+"="+item.value+"&";
          });
          form_values += 'format=json';
          form_values += '&id=' + formElement.identity.value;
          form_values += '&show_bottom_post=' + show_bottom_post;
          
          formElement.style.display = "none";
          if (scriptJquery("#comment-form-loading-li_" + type + '_' + id).length)
            scriptJquery("#comment-form-loading-li_" + type + '_' + id).css( 'display', "none" );

          en4.core.request.send(scriptJquery.ajax({
            url: en4.core.baseUrl + 'seaocore/comment/create',
            method: 'POST',
            dataType: 'json',
            method: 'post',
            data: form_values,
            type: type,
            id: id,
            show_bottom_post: show_bottom_post

          }), {
            'element': document.getElementById('comments' + '_' + type + '_' + id),
            "force": true
          });

        }
      });

      // add blur event
      formElement.body.addEventListener('blur', function () {
        formElement.style.display = "none";

        if (scriptJquery("#comment-form-open-li_" + type + '_' + id).length)
          scriptJquery("#comment-form-open-li_" + type + '_' + id).css( 'display', "block" );
      });
    }

    formElement.addEventListener('submit', function (event) {
      event.preventDefault();
      var form_values = '';
      scriptJquery(formElement).serializeArray().forEach((item)=>{
        form_values += item.name+"="+item.value+"&";
      });
      form_values += 'format=json';

      form_values += '&id=' + formElement.identity.value;
      form_values += '&show_bottom_post=' + show_bottom_post;
      en4.core.request.send(scriptJquery.ajax({
        url: en4.core.baseUrl + 'seaocore/comment/create',
        method: 'POST',
        dataType: 'json',
        data: form_values
      }), {
        'element': document.getElementById('comments' + '_' + type + '_' + id),
        "force": true
      });
    })
  },
  comment: function (type, id, body, show_bottom_post) {
    en4.core.request.send(scriptJquery.ajax({
      url: en4.core.baseUrl + 'seaocore/comment/create',
      method: 'POST',
      dataType: 'json',
      data: {
        format: 'json',
        type: type,
        id: id,
        body: body,
        show_bottom_post: show_bottom_post
      }
    }), {
      'element': document.getElementById('comments' + '_' + type + '_' + id),
      "force": true
    });
  },
  like: function (type, id, show_bottom_post, comment_id) {
    en4.core.request.send(scriptJquery.ajax({
      url: en4.core.baseUrl + 'seaocore/comment/like',
      method: 'POST',
      dataType: 'json',
      data: {
        format: 'json',
        type: type,
        id: id,
        page: pageComment,
        comment_id: comment_id,
        show_bottom_post: show_bottom_post
      },
      success: function (responseJSON) {
        if ($type(responseJSON) == 'object' && $type(responseJSON.status)) {
          if (scriptJquery('#'+type + '_' + id + 'like_link').length)
            scriptJquery('#'+type + '_' + id + 'like_link').css( 'display', "none" );
          if (scriptJquery('#'+type + '_' + id + 'unlike_link').length)
            scriptJquery('#'+type + '_' + id + 'unlike_link').css( 'display', "inline-block" );
        }
      }
    }), {
      'element': document.getElementById('comments' + '_' + type + '_' + id),
      "force": true
    });
  },
  unlike: function (type, id, show_bottom_post, comment_id) {
    en4.core.request.send(scriptJquery.ajax({
      url: en4.core.baseUrl + 'seaocore/comment/unlike',
      method: 'POST',
      dataType: 'json',
      data: {
        format: 'json',
        type: type,
        id: id,
        page: pageComment,
        comment_id: comment_id,
        show_bottom_post: show_bottom_post
      },
      success: function (responseJSON) {
        if ($type(responseJSON) == 'object' && $type(responseJSON.status)) {
          if (scriptJquery('#'+type + '_' + id + 'unlike_link').length)
            scriptJquery('#'+type + '_' + id + 'unlike_link').css( 'display', "none" );
          if (scriptJquery('#'+type + '_' + id + 'like_link').length)
            scriptJquery('#'+type + '_' + id + 'like_link').css( 'display', "inline-block" );
        }
      }
    }), {
      'element': document.getElementById('comments' + '_' + type + '_' + id),
      "force": true
    });
  },
  showLikes: function (type, id, show_bottom_post) {
    en4.core.request.send(scriptJquery.ajax({
      url: en4.core.baseUrl + 'seaocore/comment/list',
      method: 'POST',
      dataType: 'html',
      data: {
        format: 'html',
        type: type,
        id: id,
        viewAllLikes: true,
        show_bottom_post: show_bottom_post
      }
    }), {
      'element': document.getElementById('comments' + '_' + type + '_' + id),
      "force": true
    });
  },
  deleteComment: function (type, id, comment_id) {
    if (!confirm(en4.core.language.translate('Are you sure you want to delete this?'))) {
      return;
    }
    en4.core.request.send(scriptJquery.ajax({
      url: en4.core.baseUrl + 'seaocore/comment/delete',
      method: 'POST',
      dataType: 'json', 
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id
      },
      complete: function () {
        if (scriptJquery('#comment-' + comment_id).length) {
          scriptJquery('#comment-' + comment_id).remove();
        }
        try {
          var commentCount = scriptJquery('.comments_options span')[0];
          var m = commentCount.html().match(/\d+/);
          var newCount = (parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 1 ? parseInt(m[0]) - 1 : 0);
          commentCount.html(commentCount.html().replace(m[0], newCount));
        } catch (e) {
        }
      }
    }));
  }
};

en4.seaocore.nestedcomments = {

  loadComments: function (type, id, page, order, parent_comment_id) {
    if (scriptJquery('#view_more_comments_' + parent_comment_id).length) {
      scriptJquery('#view_more_comments_' + parent_comment_id).css( 'display', 'inline-block' );
      scriptJquery('#view_more_comments_' + parent_comment_id).html( '<img src="application/modules/Seaocore/externals/images/core/loading.gif" alt="Loading" />' );
    }
    if (scriptJquery('#view_previous_comments_' + parent_comment_id).length) {
      scriptJquery('#view_previous_comments_' + parent_comment_id).css( 'display', 'inline-block' );
      scriptJquery('#view_previous_comments_' + parent_comment_id).html( '<img src="application/modules/Seaocore/externals/images/core/loading.gif" alt="Loading" />' );
    }
    if (scriptJquery('#view_later_comments_' + parent_comment_id).length) {
      scriptJquery('#view_later_comments_' + parent_comment_id).css( 'display', 'inline-block' );
      scriptJquery('#view_later_comments_' + parent_comment_id).html( '<img src="application/modules/Seaocore/externals/images/core/loading.gif" alt="Loading" />' );
    }
    en4.core.request.send(scriptJquery.ajax({
      url: en4.core.baseUrl + 'seaocore/nestedcomment/list',
      method: 'POST',
      dataType: 'html',
      data: {
        format: 'html',
        type: type,
        id: id,
        page: page,
        order: order,
        parent_div: 1,
        parent_comment_id: parent_comment_id
      }
    }), {
      'element': document.getElementById('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
    });
  },
  loadcommentssortby: function (type, id, order, parent_comment_id) {
    if (scriptJquery('#sort' + '_' + type + '_' + id + '_' + parent_comment_id).length) {
      scriptJquery('#sort' + '_' + type + '_' + id + '_' + parent_comment_id).css( 'display', 'inline-block' );
      scriptJquery('#sort' + '_' + type + '_' + id + '_' + parent_comment_id).html( '<img src="application/modules/Seaocore/externals/images/core/loading.gif" alt="Loading" />' );
    }
    en4.core.request.send(scriptJquery.ajax({
      url: en4.core.baseUrl + 'seaocore/nestedcomment/list',
      method: 'POST',
      dataType: 'html',
      data: {
        format: 'html',
        type: type,
        id: id,
        order: order,
        parent_div: 1,
        parent_comment_id: parent_comment_id
      }
    }), {
      'element': document.getElementById('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
    });
  },
  attachCreateComment: function (formElement, type, id, parent_comment_id) {
    var bind = this;
    formElement.addEventListener('submit', function (event) {
      event.stopPropagation();
      event.preventDefault();
      if (formElement.body.value == ''){

        return;
      }
      if (scriptJquery('#seaocore_comment_image_' + type + '_' + id + '_' + parent_comment_id).length)
        scriptJquery('#seaocore_comment_image_' + type + '_' + id + '_' + parent_comment_id).remove();
      var divEl = scriptJquery.crtEle('div', {
        'class': '',
        'html': '<img src="application/modules/Seaocore/externals/images/core/loading.gif" alt="Loading">',
        'id': 'seaocore_comment_image_' + type + '_' + id + '_' + parent_comment_id,
        'styles': {
          'display': 'inline-block'
        }
      });
      divEl.injectSeaoCustom(formElement);
      // var form_values = formElement.parseQueryString()();
      // form_values += '&format=json';
      // form_values += '&id=' + formElement.identity.value;

      var form_values = '';
      scriptJquery(formElement).serializeArray().forEach((item)=>{
        form_values += item.name+"="+item.value+"&";
      });
      form_values += 'format=json';
      form_values += '&id=' + formElement.identity.value;


      en4.core.request.send(scriptJquery.ajax({
        url: en4.core.baseUrl + 'seaocore/nestedcomment/create?'+form_values,
        dataType: 'json',
        method:'post',
        data: form_values,
        type: type,
        id: id,
        success: function (e) {
          if (parent_comment_id == 0)
            return;
          try {
            var replyCount = scriptJquery('.seaocore_replies_options span')[0];
            var m = replyCount.html().match(/\d+/);
            replyCount.html(replyCount.html().replace(m[0], e.commentsCount));
          } catch (e) {
          }
        }
      }), {
        'element': document.getElementById('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
      });
    })
  },
  comment: function (type, id, body, parent_comment_id) {
    if (body == '')
      return;
    var formElement = document.getElementById('comments_form_' + type + '_' + id + '_' + parent_comment_id);
    if (scriptJquery('#seaocore_comment_image_' + type + '_' + id + '_' + parent_comment_id).length)
      scriptJquery('#seaocore_comment_image_' + type + '_' + id + '_' + parent_comment_id)
    var divEl = scriptJquery.crtEle('div', {
      'class': '',
      'html': '<img src="application/modules/Seaocore/externals/images/spinner.gif">',
      'id': 'seaocore_comment_image_' + type + '_' + id + '_' + parent_comment_id,
      'styles': {
        'display': 'inline-block'
      }
    });
    divEl.appendTo(formElement);
    en4.core.request.send(scriptJquery.ajax({
      url: en4.core.baseUrl + 'seaocore/nestedcomment/create',
      method: 'POST',
      dataType: 'json',
      data: {
        format: 'json',
        type: type,
        id: id,
        body: body
      },
      success: function (e) {
        if (parent_comment_id == 0)
          return;
        try {
          var replyCount = scriptJquery('.seaocore_replies_options span')[0];
          var m = replyCount.html().match(/\d+/);
          replyCount.html( replyCount.html().replace(m[0], e.commentsCount));
        } catch (e) {
        }
      }
    }), {
      'element': document.getElementById('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
    });
  },
  like: function (type, id, comment_id, order, parent_comment_id, option) {
    if (scriptJquery('#like_comments_' + comment_id).length && (option == 'child')) {
      scriptJquery('#like_comments_' + comment_id).css( 'display', 'inline-block' );
      scriptJquery('#like_comments_' + comment_id).html( '<img src="application/modules/Seaocore/externals/images/core/loading.gif" alt="Loading" />' );
    }
    if (scriptJquery('#like_comments').length && (option == 'parent')) {
      scriptJquery('#like_comments').css( 'display', 'inline-block' );
      scriptJquery('#like_comments').html( '<img src="application/modules/Seaocore/externals/images/core/loading.gif" alt="Loading" />' );
    }
    en4.core.request.send(scriptJquery.ajax({
      url: en4.core.baseUrl + 'seaocore/nestedcomment/like',
      method: 'POST',
      dataType: 'json',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id,
        order: order,
        parent_comment_id: parent_comment_id
      },
      success: function (e) {
        if (scriptJquery('#sitereview_most_likes_' + id).length) {
          scriptJquery('#sitereview_most_likes_' + id).css( 'display', 'none' );
        }
        if (scriptJquery('#sitereview_unlikes_' + id).length) {
          scriptJquery('#sitereview_unlikes_' + id).css( 'display', 'block' );
        }

        if (scriptJquery('#'+type + '_like_' + id).length)
          scriptJquery('#'+type + '_like_' + id).value = 1;
        if (scriptJquery('#'+type + '_most_likes_' + id).length)
          scriptJquery('#'+type + '_most_likes_' + id).css( 'display', 'none' );
        if (scriptJquery('#'+type + '_unlikes_' + id).length)
          scriptJquery('#'+type + '_unlikes_' + id).css( 'display', 'inline-block' );

      }
    }), {
      'element': document.getElementById('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
    });
  },
  unlike: function (type, id, comment_id, order, parent_comment_id, option) {
    if (scriptJquery('#unlike_comments_' + comment_id).length && (option == 'child')) {
      scriptJquery('#unlike_comments_' + comment_id).css( 'display', 'inline-block' );
      scriptJquery('#unlike_comments_' + comment_id).html( '<img src="application/modules/Seaocore/externals/images/core/loading.gif" alt="Loading" />' );
    }
    if (scriptJquery('#unlike_comments').length && (option == 'parent')) {
      scriptJquery('#unlike_comments').css( 'display', 'inline-block' );
      scriptJquery('#unlike_comments').html( '<img src="application/modules/Seaocore/externals/images/core/loading.gif" alt="Loading" />' );
    }
    en4.core.request.send(scriptJquery.ajax({
      url: en4.core.baseUrl + 'seaocore/nestedcomment/unlike',
      method: 'POST',
      dataType: 'json',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id,
        order: order,
        parent_comment_id: parent_comment_id
      },
      success: function (e) {
        if (scriptJquery('#sitereview_most_likes_' + id).length) {
          scriptJquery('#sitereview_most_likes_' + id).css( 'display', 'block' );
        }
        if (scriptJquery('#sitereview_unlikes_' + id).length) {
          scriptJquery('#sitereview_unlikes_' + id).css( 'display', 'none' );
        }

        if (scriptJquery('#'+type + '_like_' + id).length)
          scriptJquery('#'+type + '_like_' + id).value = 0;
        if (scriptJquery('#'+type + '_most_likes_' + id).length)
          scriptJquery('#'+type + '_most_likes_' + id).css( 'display', 'inline-block' );
        if (scriptJquery('#'+type + '_unlikes_' + id).length)
          scriptJquery('#'+type + '_unlikes_' + id).css( 'display', 'none' );

      }
    }), {
      'element': document.getElementById('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
    });
  },
  showLikes: function (type, id, order, parent_comment_id) {
    en4.core.request.send(scriptJquery.ajax({
      url: en4.core.baseUrl + 'seaocore/nestedcomment/list',
      method: 'POST',
      dataType: 'html',
      data: {
        format: 'html',
        type: type,
        id: id,
        viewAllLikes: true,
        order: order,
        parent_comment_id: parent_comment_id
      }
    }), {
      'element': document.getElementById('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
    });
  },
  deleteComment: function (type, id, comment_id, order, parent_comment_id) {
    if (!confirm(en4.core.language.translate('Are you sure you want to delete this?'))) {
      return;
    }
    if (scriptJquery('#comment-' + comment_id).length) {
      scriptJquery('#comment-' + comment_id).remove();
    }
    (scriptJquery.ajax({
      url: en4.core.baseUrl + 'seaocore/nestedcomment/delete',
      method: 'POST',
      dataType: 'json',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id,
        order: order,
        parent_comment_id: parent_comment_id
      },
      success: function (e) {
        try {
          var replyCount = scriptJquery('.seaocore_replies_options span');
          var m = replyCount.html().match(/\d+/);
          var newCount = (parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 1 ? parseInt(m[0]) - 1 : 0);
          replyCount.html(replyCount.html().replace(m[0], e.commentsCount));
        } catch (e) {
        }
      }
    })).send();
  }
};

en4.seaocore.facebook = {
  runFacebookSdk: function () {

    window.fbAsyncInit = function () {
      FB.JSON.stringify = function (value) {
        return JSON.encode(value);
      };
      FB.init({
        appId: fbappid,
        status: true, // check login status
        cookie: true, // enable cookies to allow the server to access the session
        xfbml: true  // parse XFBML
      });

      if (window.setFBContent) {

        setFBContent();
      }
    };
    (function () {
      en4.seaocore.setDomElements();
      var catarea = scriptJquery('#'+en4.seaocore.footerElement);
      if (!catarea.length) {
        catarea = scriptJquery('#'+en4.seaocore.contentElement);
      }
      if ((catarea.length > 0) && (typeof document.getElementById('fb-root') == 'undefined' || document.getElementById('fb-root') == null)) {
        var newdiv = document.createElement('div');
        newdiv.id = 'fb-root';
        scriptJquery(newdiv).appendTo(catarea);
        var e = document.createElement('script');
        e.async = true;
        if (typeof local_language != 'undefined' && typeof(local_language)) {
          e.src = document.location.protocol + '//connect.facebook.net/' + local_language + '/all.js';
        } else {
          e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
        }
        scriptJquery.find('#fb-root')[0].appendChild(e);
      }
    }());

  }

};

en4.seaocore.advlightbox = {
  createDefaultContent: function () {

  }
}
//window.addEventListener('load', function() {
//  if (typeof FB == 'undefined' && typeof fbappid != 'undefined')  {
//    en4.seaocore.facebook.runFacebookSdk (); 
//  }
//  
//});


en4.core.runonce.add(function () {

  // Reload The Page on Pop State Click (Back & Forward) Pop State Button
  var defaultlocationHref = window.location.href;
  var n = defaultlocationHref.indexOf('#');
  defaultlocationHref = defaultlocationHref.substring(0, n != -1 ? n : defaultlocationHref.length);
  window.addEventListener("popstate", function (e) {
    var url = window.location.href;
    var n = url.indexOf('#');
    url = url.substring(0, n != -1 ? n : url.length);
    if (e && e.state && url != defaultlocationHref) {
      window.location.reload(true);
    }
  });
// END
});

function addfriend(el, user_id) {

  en4.core.request.send(scriptJquery.ajax({
    method: 'post',
    'url': en4.core.baseUrl + 'seaocore/feed/addfriendrequest',
    'data': {
      dataType: 'html',
      'resource_id': user_id
        //'action_id' : action_id,
    },
    success: function (responseHTML) {
      var parent = el.parents('div');
      var nextSibling = el.nextSibling;
      el.remove();
      parent.insertBefore(scriptJquery.crtEle('span', {
        'html': responseHTML
      }), nextSibling);

    }
  }), {
    'force': true
  });
}


var ScrollToTopSeao = function (topElementId, buttonId) {
  window.addEventListener('scroll', function () {
    var element = scriptJquery('#'+buttonId);
    if (element) {
      if (scriptJquery('#'+topElementId).length) {
        var elementPostionY = 0;
        if (typeof (scriptJquery('#'+topElementId).offsetParent) != 'undefined') {
          elementPostionY = scriptJquery('#'+topElementId).offsetTop;
        } else {
          elementPostionY = scriptJquery('#'+topElementId).y;
        }
      }
      if (elementPostionY + window.getSize().y < window.getScrollTop()) {
        if (element.hasClass('Offscreen'))
          element.removeClass('Offscreen');
      } else if (!element.hasClass('Offscreen')) {
        element.addClass('Offscreen');
      }
    }
  });
  en4.core.runonce.add(function () {
    var scroll = new Fx.Scroll(document.getElement('body').attr('id'), {
      wait: false,
      duration: 750,
      offset: {
        'x': -200,
        'y': -100
      },
      transition: Fx.Transitions.Quad.easeInOut
    });

    scriptJquery('#'+buttonId).addEventListener('click', function (event) {
      event = new Event(event).stop();
      scroll.toElement(topElementId);
    });
  });

};


ActivitySEAOUpdateHandler = function(Options){

  this.options = {
    debug: true,
    baseUrl: '/',
    identity: false,
    delay: 5000,
    admin: false,
    idleTimeout: 600000,
    last_id: 0,
    next_id: null,
    subject_guid: null,
    showImmediately: false
  },
  this.state = true,
  this.activestate = 1,
  this.fresh = true,
  this.lastEventTime = false,
  this.title = document.title,
  //loopId : false,

  this.initialize = function (options) {
    this.options = scriptJquery.extend(options, this.options);
  },
  this.start = function () {
    this.state = true;

    // Do idle checking
    this.idleWatcher = new IdleWatcher(this, {timeout: this.options.idleTimeout});
    this.idleWatcher.register();
    this.addEventListeners({
      'onStateActive': function () {
        this._log('activity loop onStateActive');
        this.activestate = 1;
        this.state = true;
      }.bind(this),
      'onStateIdle': function () {
        this._log('activity loop onStateIdle');
        this.activestate = 0;
        this.state = false;
      }.bind(this)
    });
    this.loop();
    //this.loopId = this.loop.periodical(this.options.delay, this);
  },
  this.stop = function () {
    this.state = false;
  },
  this.checkFeedUpdate = function (action_id, subject_guid) {
    if (en4.core.request.isRequestActive())
      return;

    function getAllElementsWithAttribute(attribute) {
      var matchingElements = [];
      var values = [];
      var allElements = document.findsByTagName('*');
      for (var i = 0; i < allElements.length; i++) {
        if (allElements[i].getAttribute(attribute)) {
          // Element exists with attribute. Add to array.
          matchingElements.push(allElements[i]);
          values.push(allElements[i].getAttribute(attribute));
        }
      }
      return values;
    }
    var list = getAllElementsWithAttribute('data-activity-feed-item');
    this.options.last_id = Math.max.apply(Math, list);
    min_id = this.options.last_id + 1;

    var req = scriptJquery.ajax({
      url: en4.core.baseUrl + 'widget/index/name/seaocore.feed',
      method: 'POST',
      dataType: 'html',
      data: {
        'format': 'html',
        'minid': min_id,
        'feedOnly': true,
        'nolayout': true,
        'subject': this.options.subject_guid,
        'getUpdate': true
      }
    });
    en4.core.request.send(req, {
      'element': scriptJquery('#activity-feed'),
      'updateHtmlMode': 'prepend'
    }
    );


    req.addEventListener('complete', function () {
      (function () {
        if (this.options.showImmediately && scriptJquery('#feed-update').children().length > 0) {
          scriptJquery('#feed-update').css('display', 'none');
          scriptJquery('#feed-update').empty();
          this.getFeedUpdate(this.options.next_id);
        }
      }).delay(50, this);
    }.bind(this));



    // Start LOCAL STORAGE STUFF   
    if (localStorage) {
      var pageTitle = document.title;
      //@TODO Refill Locally Stored Activity Feed

      // For each activity-item, get the item ID number Data attribute and add it to an array
      var feed = document.findById('activity-feed');
      // For every <li> in Feed, get the Feed Item Attribute and add it to an array
      var items = feed.findsByTagName("li");
      var itemObject = {};
      // Loop through each item in array to get the InnerHTML of each Activity Feed Item
      var c = 0;
      for (var i = 0; i < items.length; ++i) {
        if (items[i].getAttribute('data-activity-feed-item') != null) {
          var itemId = items[i].getAttribute('data-activity-feed-item');
          itemObject[c] = {id: itemId, content: document.findById('activity-item-' + itemId).innerHTML};
          c++;
        }
      }
      // Serialize itemObject as JSON string
      var activityFeedJSON = JSON.stringify(itemObject);
      localStorage.setItem(pageTitle + '-activity-feed-widget', activityFeedJSON);
    }


    // Reconstruct JSON Object, Find Highest ID
    if (localStorage.getItem(pageTitle + '-activity-feed-widget')) {
      var storedFeedJSON = localStorage.getItem(pageTitle + '-activity-feed-widget');
      var storedObj = eval("(" + storedFeedJSON + ")");

      //alert(storedObj[0].id); // Highest Feed ID
      // @TODO use this at min_id when fetching new Activity Feed Items
    }
    // END LOCAL STORAGE STUFF


    return req;
  },
  this.getFeedUpdate = function (last_id) {
    if (en4.core.request.isRequestActive())
      return;
    var min_id = this.options.last_id + 1;
    this.options.last_id = last_id;
    document.title = this.title;
    var req = scriptJquery.ajax({
      url: en4.core.baseUrl + 'widget/index/name/seaocore.feed',
      method: 'POST',
      dataType: 'html',
      data: {
        'format': 'html',
        'minid': min_id,
        'feedOnly': true,
        'nolayout': true,
        'getUpdate': true,
        'subject': this.options.subject_guid
      }
    });
    en4.core.request.send(req, {
      'element': scriptJquery('#activity-feed'),
      'updateHtmlMode': 'prepend'
    });
    return req;
  },
  this.loop = function () {
    this._log('activity update loop start');

    if (!this.state) {
      this.loop.delay(this.options.delay, this);
      return;
    }

    try {
      this.checkFeedUpdate().addEventListener('complete', function () {
        try {
          this._log('activity loop req complete');
          this.loop.delay(this.options.delay, this);
        } catch (e) {
          this.loop.delay(this.options.delay, this);
          this._log(e);
        }
      }.bind(this));
    } catch (e) {
      this.loop.delay(this.options.delay, this);
      this._log(e);
    }

    this._log('activity update loop stop');
  },
  // Utility
  this._log = function (object) {
    if (!this.options.debug) {
      return;
    }

    try {
      if ('console' in window && typeof (console) && 'log' in console) {
        console.log(object);
      }
    } catch (e) {
      // Silence
    }
  }
};

en4.seaocore.locationBased = {
  startReq: function (params) {
    window.locationsParamsSEAO = {
      latitude: 0,
      longitude: 0
    };
    window.locationsDetactSEAO = false;
    params.isExucute = false;
    var self = this;
    var callBackFunction = self.sendReq;
    if (params.callBack) {
      callBackFunction = params.callBack;
    }

    if (params.detactLocation && !window.locationsDetactSEAO && navigator.geolocation) {

      if (typeof (Cookie.read('seaocore_myLocationDetails')) != 'undefined' && Cookie.read('seaocore_myLocationDetails') != "") {
        var readLocationsDetails = JSON.parse(Cookie.read('seaocore_myLocationDetails'));
      }

      if (typeof (readLocationsDetails) == 'undefined' || readLocationsDetails == null || typeof (readLocationsDetails.latitude) == 'undefined' || typeof (readLocationsDetails.longitude) == 'undefined') {

        navigator.geolocation.getCurrentPosition(function (position) {


          if (scriptJquery('#region').length) {
            var regionCurrentLocation = scriptJquery('#region').innerHTML;
            scriptJquery('#region').html( '<div class="seaocore_content_loader"></div>' );
          }

          window.locationsParamsSEAO.latitude = position.coords.latitude;
          window.locationsParamsSEAO.longitude = position.coords.longitude;

          var myLocationDetails = {'latitude': position.coords.latitude, 'longitude': position.coords.longitude, 'location': '', 'locationmiles': params.locationmiles};
          self.setLocationCookies(myLocationDetails);

          self.setLocationField(position, params);
          params.locationSetInCookies = true;
          params.requestParams = scriptJquery.extend(params.requestParams, window.locationsParamsSEAO);
          params.isExucute = true;
          if (typeof (params.noSendReq) == 'undefined' || params.noSendReq == null) {
            callBackFunction(params);
          }

        }, function () {
          params.isExucute = true;
          if (typeof (params.noSendReq) == 'undefined' || params.noSendReq == null) {
            callBackFunction(params);
          }

        });
      } else {
        window.locationsParamsSEAO.latitude = readLocationsDetails.latitude;
        window.locationsParamsSEAO.longitude = readLocationsDetails.longitude;
        params.requestParams = scriptJquery.extend(params.requestParams, window.locationsParamsSEAO);
        params.isExucute = true;
        if (typeof (params.noSendReq) == 'undefined' || params.noSendReq == null) {
          callBackFunction(params);
        }
      }

      window.locationsDetactSEAO = true;
      window.setTimeout(function () {
        if (params.isExucute)
          return;

        if (typeof (params.noSendReq) == 'undefined' || params.noSendReq == null) {
          callBackFunction(params);
        }

      }, 3000);
    } else {
      if (params.detactLocation && window.locationsDetactSEAO) {
        params.requestParams = scriptJquery.extend(params.requestParams, window.locationsParamsSEAO);
      }

      if (typeof (params.noSendReq) == 'undefined' || params.noSendReq == null) {
        callBackFunction(params);
      }
    }

  },
  sendReq: function (params) {

    var self = this;
    var url = en4.core.baseUrl + 'widget';

    if (params.requestUrl)
      url = params.requestUrl;
    en4.core.request.send(scriptJquery.ajax({
      url: url,
      data: scriptJquery.extend(params.requestParams, {
        dataType: 'html',
        subject: en4.core.subject.guid,
        is_ajax_load: true
      }),
      evalScripts: true,
      success: function (responseHTML) {
        if (scriptJquery('#'+params.responseContainer).length) {
          scriptJquery('#'+params.responseContainer).html( '' );
          scriptJquery(responseHTML).appendTo(scriptJquery('#'+params.responseContainer));
        }
        en4.core.runonce.trigger();
        Smoothbox.bind(params.responseContainer);
      }
    }));

  },
  setLocationCookies: function (params, pageReload) {

    var myLocationDetails = {'latitude': params.latitude, 'longitude': params.longitude, 'location': params.location, 'locationmiles': params.locationmiles};

    if (typeof (params.changeLocationWidget) != 'undefined' && params.changeLocationWidget) {
      Cookie.write('seaocore_myLocationDetails', JSON.stringify(myLocationDetails), {duration: 30, path: en4.core.baseUrl});
    } else {
      en4.core.request.send(scriptJquery.ajax({
        url: en4.core.baseUrl + 'seaocore/location/get-specific-location-setting',
      method: 'POST',
      dataType: 'json',
        data: {
          format: 'json',
          location: params.location,
          updateUserLocation: params.updateUserLocation
        },
        success: function (responseJSON) {
          if (responseJSON.saveCookies) {
            Cookie.write('seaocore_myLocationDetails', JSON.stringify(myLocationDetails), {duration: 30, path: en4.core.baseUrl});

            if (pageReload) {
              window.location.reload();
            }

          }
        }
      }), {force: true});
    }




  },
  setLocationField: function (position, params) {
    var self = this;
    if (!position.address) {
      var mapDetect = new google.maps.Map(scriptJquery.crtEle('div'), {
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        center: new google.maps.LatLng(0, 0)
      });
      var service = new google.maps.places.PlacesService(mapDetect);
      var request = {
        location: new google.maps.LatLng(position.coords.latitude, position.coords.longitude),
        radius: 500
      };
      service.search(request, function (results, status) {
        if (status == 'OK') {
          var index = 0;
          var radian = 3.141592653589793 / 180;
          var my_distance = 1000;
          var R = 6371; // km
          for (var i = 0; i < results.length; i++) {
            var lat2 = results[i].geometry.location.lat();
            var lon2 = results[i].geometry.location.lng();
            var dLat = (lat2 - position.coords.latitude) * radian;
            var dLon = (lon2 - position.coords.longitude) * radian;
            var lat1 = position.coords.latitude * radian;
            lat2 = lat2 * radian;
            var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.sin(dLon / 2) * Math.sin(dLon / 2) * Math.cos(lat1) * Math.cos(lat2);
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            var d = R * c;

            if (d < my_distance) {
              index = i;
              my_distance = d;
            }
          }

          if (typeof (params.fieldName) != 'undefined' && params.fieldName != null && document.getElementById(params.fieldName)) {
            document.getElementById(params.fieldName).value = (results[index].vicinity) ? results[index].vicinity : '';

            if (typeof (params.locationmilesFieldName) != 'undefined' && params.locationmilesFieldName != null && document.getElementById(params.locationmilesFieldName)) {
              document.getElementById(params.locationmilesFieldName).value = params.locationmiles;
            }
          }

          var cookiesLocation = (results[index].vicinity) ? results[index].vicinity : '';
          var myLocationDetails = {'latitude': position.coords.latitude, 'longitude': position.coords.longitude, 'location': cookiesLocation, 'locationmiles': params.locationmiles};

          var pageReload = 0;
          if (typeof (params.reloadPage) != 'undefined' && params.reloadPage != null) {
            pageReload = 1;
          }

          self.setLocationCookies(myLocationDetails, pageReload);

//          if (typeof(params.reloadPage) != 'undefined' && params.reloadPage != null) {
//            window.location.reload();
//          }

        }
      })
    } else {
      var delimiter = (position.address && position.address.street != '' && position.address.city != '') ? ', ' : '';
      var location = (position.address) ? (position.address.street + delimiter + position.address.city) : '';
      if (typeof (params.fieldName) != 'undefined' && params.fieldName != null && document.findById(params.fieldName)) {
        document.getElementById(params.fieldName).value = location;
      }

      var myLocationDetails = {'latitude': position.coords.latitude, 'longitude': position.coords.longitude, 'location': location, 'locationmiles': params.locationmiles};

      var pageReload = 0;
      if (typeof (params.reloadPage) != 'undefined' && params.reloadPage != null) {
        pageReload = 1;
      }

      self.setLocationCookies(myLocationDetails, pageReload);

//      if (typeof(params.reloadPage) != 'undefined' && params.reloadPage != null) {
//        window.location.reload();
//      }

    }

  },
};
en4.seaocore.setShareButtons = function (wrapper, cont, params) {
  en4.seaocore.setDomElements();
  if (cont.find('.facebook_container')) {
    if (!document.findById('fb-root'))
      scriptJquery.crtEle('div', {'id': 'fb-root'}).appendTo(scriptJquery('#'+en4.seaocore.contentElement), 'top');
    (function (d, s, id) {
      var js, fjs = d.findsByTagName(s)[0];
      if (d.findById(id))
        return;
      js = d.createElement(s);
      js.id = id;
      if (typeof local_language != 'undefined' && $type(local_language)) {
        js.src = "//connect.facebook.net/" + local_language + "/all.js#xfbml=1";
      } else {
        js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
      }
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
  }

  if (cont.find('.linkedin_container')) {
    scriptJquery.crtEle('script', {'type': 'IN/Share', 'data-counter': 'top'}).appendTo(cont.find('.linkedin_container'));
    scriptJquery.crtEle('script', {'src': ("https:" == document.location.protocol ? "https://" : "http://") + 'platform.linkedin.com/in.js'}).appendTo(scriptJquery('#'+en4.seaocore.contentElement), 'before');

  }
  if (cont.find('.twitter_container')) {
    scriptJquery.crtEle('script', {'src': ("https:" == document.location.protocol ? "https://" : "http://") + 'platform.twitter.com/widgets.js'}).appendTo(scriptJquery('#'+en4.seaocore.contentElement), 'before');

  }
  if (cont.find('.google_container')) {
    scriptJquery.crtEle('script', {'src': 'https://apis.google.com/js/plusone.js', 'async': true}).appendTo(scriptJquery('#'+en4.seaocore.contentElement), 'before');

  }

  if (!params.leftValue) {
    params.leftValue = 15;
  }
  wrapper.appendTo(scriptJquery('#'+en4.seaocore.contentElement), 'top');
  var top = wrapper.css('top');
  if (!params.type) {
    params.type = 'left';
  }

  if (params.type === 'left') {
    params.leftValue = params.leftValue + parseInt(wrapper.clientWidth);
    cont.css('left', '-' + params.leftValue + 'px');
    scriptJquery('#'+en4.seaocore.contentElement).addClass('seao_share_buttons_left_content');
  } else {
    params.leftValue = params.leftValue + parseInt(scriptJquery('#'+en4.seaocore.contentElement).clientWidth);
    cont.css('left', params.leftValue + 'px');
    scriptJquery('#'+en4.seaocore.contentElement).addClass('seao_share_buttons_right_content');
  }
  (function () {
    wrapper.css({width: '1px', visibility: 'visible'});
  }).delay(1500);
  window.addEventListener('scroll', function (e) {
    var descripY = parseInt(scriptJquery('#'+en4.seaocore.contentElement).getOffsets().y) - 20, scrollY = scriptJquery(window).scrollTop(), footerY = parseInt(scriptJquery('#'+en4.seaocore.footerElement).getOffsets().y), height = parseInt(wrapper.css('height')), fixedShare = wrapper.css('position') === 'fixed';

    if (scrollY < descripY && fixedShare) {
      wrapper.css({
        position: 'absolute',
        top: top
      });
    } else if (scrollY > descripY && (scrollY + 20 + height) > footerY) {
      wrapper.css({
        position: 'absolute',
        top: (footerY - height - 90)
      });
    } else if (scrollY > descripY && !fixedShare) {
      wrapper.css({
        position: 'fixed',
        top: 20
      });
    }
  });
  scriptJquery('.generic_layout_container.layout_seaocore_social_share_buttons')[0].style.display = 'none';
};

var SmoothboxSEAO = {

  overlay: null,
  wrapper: null,
  content: null,
  contentHTML: null,
  scrollPosition: { left: 0, top: 0 },
  addScriptFiles: [],
  addStylesheets: [],
  active: false,
  closable: true,
  build: function () {
    SmoothboxSEAO.overlay = scriptJquery.crtEle('div', {
      'class': 'seao_smoothbox_lightbox_overlay'
    }).appendTo(scriptJquery('#'+en4.seaocore.contentWrapperElement));
    SmoothboxSEAO.wrapper = scriptJquery.crtEle('div', {
      'class': 'seao_smoothbox_lightbox_content_wrapper'
    }).appendTo(scriptJquery('#'+en4.seaocore.contentWrapperElement));
    SmoothboxSEAO.attach();
    SmoothboxSEAO.hide();
  },
  attach: function () {
    if (!SmoothboxSEAO.wrapper || !SmoothboxSEAO.closable) {
      return;
    }
    scriptJquery(SmoothboxSEAO.wrapper).off('click').on( 'click', function (event) {
      var el = scriptJquery( event.target );
      if ( el.hasClass('seao_smoothbox_lightbox_content') || el.parents('.seao_smoothbox_lightbox_content').length > 0 )
        return;
      SmoothboxSEAO.close();
    } );
  },
  bind: function (selector) {
    // All children of element
    var elements;
    if ($type(selector) == 'element') {
      elements = selector.finds('a.seao_smoothbox');
    } else if ($type(selector) == 'string') {
      elements = scriptJquery(selector);
    } else {
      elements = scriptJquery('a.seao_smoothbox');
    }
    elements.each(function (i,el){
      if (scriptJquery(el).prop('tagName') != 'A' || !SmoothboxSEAO.hasLink(el) || jQuery(el).attr('data-smoothboxed') == "done" ){
        return;
      }
      el.addEventListener('click', function (event) {
        event.preventDefault();
        if (jQuery(el).attr('data-SmoothboxSEAOType') == 'iframe') {
          SmoothboxSEAO.open({ class: jQuery(el).attr('data-SmoothboxSEAOClass'), iframe: { src: el.href } });
        } else {
          SmoothboxSEAO.open({ class: jQuery(el).attr('data-SmoothboxSEAOClass'), request: { url: el.href }  });
        }
      });
      jQuery(el).attr('data-smoothboxed', "done");
    });
  },
  hasLink: function (element) {
    return (
      !element.onclick &&
      element.href &&
      !element.href.match(/^(javascript|[#])/));
  },
  open : function (params) {
    if (!SmoothboxSEAO.wrapper) {
      if ((typeof params.closable) === 'boolean' && params.closable === false) {
        SmoothboxSEAO.closable = false;
      }
      SmoothboxSEAO.build();
    } else {
      SmoothboxSEAO.wrapper.empty();
    }
    if ((typeof params) === 'string') {
      if (params.length < 4000 && ( params.substring(0, 1) == '/' ||
          params.substring(0, 1) == '.' ||
          params.substring(0, 4) == 'http' ||
          !params.match(/[ <>"'{}|^~\[\]`]/)
        )
      ) {
        params = { request: { url: params } };
      } else {
        params = {element: params};
      }
    } else if ($type(params) === 'element') {
      params = {element: params};
    }

    SmoothboxSEAO.content = scriptJquery.crtEle('div', {
      'class': 'seao_smoothbox_lightbox_content'
    }).appendTo(SmoothboxSEAO.wrapper);
    // SmoothboxSEAO.content.css('width', 'auto');
    SmoothboxSEAO.contentHTML = scriptJquery.crtEle('div', {
      'class': 'seao_smoothbox_lightbox_content_html'
    }).appendTo(SmoothboxSEAO.content);
    if (params.class)
      SmoothboxSEAO.content.addClass(params.class);
    if (params.element && (typeof params.element) === 'string')
      SmoothboxSEAO.contentHTML.html( params.element );
    else if (params.element)
      params.element.appendTo(SmoothboxSEAO.contentHTML);
    else if (params.request && params.request.src) {
      if ( params.request.src.search( "/") == 0 ) {
        params.request.src = window.location.origin + params.request.src;
      }
      var url = new URL( params.request.src );
      var search_params = url.searchParams;
      search_params.set('format', 'smoothbox');
      url.search = search_params.toString();
      params.request.src = url.toString();
      SmoothboxSEAO.sendReq(params.request);
    } else if (params.request && params.request.url) {
      if ( params.request.url.search( "/") == 0 ) {
        params.request.url = window.location.origin + params.request.url;
      }
      var url = new URL( params.request.url );
      var search_params = url.searchParams;
      search_params.set('format', 'smoothbox');
      url.search = search_params.toString();
      params.request.src = url.toString();
      SmoothboxSEAO.sendReq(params.request);

    } else if (params.iframe && params.iframe.src)
      SmoothboxSEAO.iframeReq(params.iframe);
    else if (params.embed && params.embed.code)
      SmoothboxSEAO.embed(params.embed);
    SmoothboxSEAO.show();
    scriptJquery(".seao_smoothbox_lightbox_close").on('click', function (event) {
      event.stopPropagation();
      SmoothboxSEAO.close();
    });

    SmoothboxSEAO.doAutoResize();
    //  this.fireEvent('open', this);
  },
  embed: function (options) {
    var elementWapper = scriptJquery.crtEle('div', {
      'class': 'seao_smoothbox_iframe_wapper ' + (options.wapperClass ? options.wapperClass : '')
    }).html('<a class="seao_smoothbox_lightbox_close _close" href="javascript:void();"><i class="fa fa-close"></i></a>');
    var elementContent = scriptJquery.crtEle('div', {
      'class': 'seao_smoothbox_iframe_content ' + (options.contentClass ? options.contentClass : '')
    }).html(options.code);
    elementContent.appendTo(elementWapper);
    elementWapper.appendTo(SmoothboxSEAO.contentHTML);
    SmoothboxSEAO.content.addClass('seao_smoothbox_iframe');
    SmoothboxSEAO.wrapper.addClass('seao_smoothbox_wapper-iframe');
  },
  _getEmbedIframeTarget: function (src) {
    var _youtubeRegex = /(youtube(-nocookie)?\.com|youtu\.be)\/(watch\?v=|v\/|u\/|embed\/?)?([\w-]{11})(.*)?/i;
    var _instagramRegex = /(instagr\.am|instagram\.com)\/p\/([a-zA-Z0-9_\-]+)\/?\??(.*)?/i;
    var _vimeoRegex = /(vimeo(pro)?\.com)\/(?:[^\d]+)?(\d+)\??(.*)?$/;
    var _facebookRegex = /(facebook\.com)\/([a-z0-9_-]*)\/videos\/([0-9]*)(.*)?$/i;
    var _goolgeDocRegex = /(\S*)(\.(pdf|docx?|xlsx?|pptx?|e?ps|od(t|s|p)|pages|ai|psd|ttf)(\?\S*)?$)/i;
    var _dailymotionRegex = /dailymotion\.com(?:\/embed)?\/video\/([a-z0-9_-]*)\??(.*)?/i;
    var _goolgeMapRegex = /((maps|www)\.)?google\.([^\/\?]+)\/?((maps\/?)?\?)(.*)/i;

    var target = {}, matches;
    matches = src.match(_youtubeRegex);
    if (matches) {
      target.type = 'youtube';
      target.src = 'https://www.youtube' + (matches[2] || '') + '.com/embed/' + matches[4] + '?autoplay=1';
      return target;
    }

    matches = src.match(_vimeoRegex);
    if (matches) {
      target.type = 'vimeo';
      target.src = 'https://player.vimeo.com/video/' + matches[3] + '?autoplay=1';
      return target;
    }

    matches = src.match(_facebookRegex);
    if (matches) {
      if (0 !== src.indexOf('http')) {
        src = 'https:' + src;
      }
      target.type = 'facebook';
      target.src = 'https://www.facebook.com/plugins/video.php?href=' + src + '&autoplay=1';
      return target;
    }

    matches = src.match(_instagramRegex);
    if (matches) {
      target.type = 'instagram';
      target.src = 'https://www.instagram.com/p/' + matches[2] + '/embed/';
      return target;
    }
    matches = src.match(_dailymotionRegex);
    if (matches) {
      target.type = 'dailymotion';
      target.src = 'https://www.dailymotion.com/embed/video/' + matches[1] + '?autoPlay=1';
      return target;
    }

    matches = src.match(_goolgeDocRegex);
    if (matches) {
      if (0 !== src.indexOf('http')) {
        src = 'http:' + src;
      }
      target.type = 'googledocsviewer';
      target.src = 'https://docs.google.com/viewer?embedded=true&url=' + escape(matches[1]) + '.' + matches[3];
      return target;
    }
    matches = src.match(_goolgeMapRegex);
    if (matches) {
      if (0 !== src.indexOf('http')) {
        src = 'http:' + src;
      }
      target.type = 'googlemap';
      target.src = 'https://www.google.' + matches[3] + '/maps?' + matches[6] + '&output=' + (matches[6].indexOf('layer=c') > 0 ? 'svembed' : 'embed');
      return target;
    }
    target.type = 'website';
    target.src = src;
    return target;
  },
  iframeReq: function (options) {
    console.log(options);
    var elementWapper = scriptJquery.crtEle('div', {
      'class': 'seao_smoothbox_iframe_wapper ' + (options.wapperClass ? options.wapperClass : ''),
    }).html('<a class="seao_smoothbox_lightbox_close _close" href="javascript:void();"><i class="fa fa-close"></i></a>');
    var elementContent = scriptJquery.crtEle('div', {
      'class': 'seao_smoothbox_iframe_content ' + (options.contentClass ? options.contentClass : ''),
    });
    var target = SmoothboxSEAO._getEmbedIframeTarget(options.src);
    SmoothboxSEAO.frame = jQuery( '<iframe>', { src: target.src, id:  'TB_iframeContent', name : 'TB_iframeContent', } );
    SmoothboxSEAO.frame.appendTo(elementContent);
    elementContent.appendTo(elementWapper);
    elementWapper.appendTo(SmoothboxSEAO.contentHTML);
    SmoothboxSEAO.content.addClass('seao_smoothbox_iframe seao_smoothbox_iframe_' + target.type);
    SmoothboxSEAO.wrapper.addClass('seao_smoothbox_wapper-iframe');
  },

  doAutoResize: function () {

    var size = { x: SmoothboxSEAO.contentHTML[0].scrollWidth, y: SmoothboxSEAO.contentHTML[0].scrollHeight }
    if (size.x > (scriptJquery(window).width() - 30)) {
      size.x = scriptJquery(window).width() - 30;
    }
    var marginTop = 10;
    SmoothboxSEAO.content.css('width', size.x);
    if (size.y < scriptJquery(window).height()) {
      marginTop = (scriptJquery(window).height() - size.y) / 2;
    }

    if (marginTop < 10)
      marginTop = 10;
    size.x = size.x + 10;
    SmoothboxSEAO.content.css({
      'width': size.x,
      'marginTop': marginTop,
      'marginBottom': 20
    });
  },
  show: function () {
    SmoothboxSEAO.overlay.show();
    SmoothboxSEAO.wrapper.show();
    if (scriptJquery('#arrowchat_base').length > 0)
      scriptJquery('#arrowchat_base').css('display','none');
    if (scriptJquery('#wibiyaToolbar').length > 0)
      scriptJquery('#wibiyaToolbar').css('display','none');
    SmoothboxSEAO.scrollPosition.top = scriptJquery(window).scrollTop();
    SmoothboxSEAO.scrollPosition.left = scriptJquery(window).scrollLeft();
    SmoothboxSEAO.setHtmlScroll("hidden");
    SmoothboxSEAO.active = true;
  },
  hide: function () {
    SmoothboxSEAO.overlay.hide();
    SmoothboxSEAO.wrapper.hide();
    SmoothboxSEAO.active = false;
  },
  close: function () {

    if (!SmoothboxSEAO.active)
      return;

    SmoothboxSEAO.hide();
    SmoothboxSEAO.wrapper.empty();
    SmoothboxSEAO.wrapper.removeClass('seao_smoothbox_wapper-iframe');
    SmoothboxSEAO.setHtmlScroll("auto");
    window.scroll(SmoothboxSEAO.scrollPosition.left, SmoothboxSEAO.scrollPosition.top);
    if (scriptJquery('#arrowchat_base').length > 0)
      scriptJquery('#arrowchat_base').css('display','block');
    if (scriptJquery('#wibiyaToolbar').length > 0)
      scriptJquery('#wibiyaToolbar').css('display','block');

  },

  setHtmlScroll: function (cssCode) {
    scriptJquery('html').css('overflow', cssCode);
  },

  sendReq: function (params) {

    var container = SmoothboxSEAO.contentHTML;
    container.empty();
    scriptJquery.crtEle('div', {
      'class': 'seao_smoothbox_lightbox_loading'
    }).appendTo(container);

    if (!params.requestParams)
      params.requestParams = {};
    SmoothboxSEAO.addScriptFiles = [];
    SmoothboxSEAO.addStylesheets = [];
    
    jQuery.ajax({
      dataType:'html',
      url: params.url,
      method: 'get',
      data: jQuery.extend(params.requestParams,{
        format: 'html',
        seaoSmoothbox: true
      }),
      evalScripts: true,
      success: function (responseHTML) {

        var onLoadContent = function () {

          container.empty();
          jQuery(responseHTML).appendTo(container);
          en4.core.runonce.trigger();
          SmoothboxSEAO.doAutoResize();
          Smoothbox.bind(container);
          SmoothboxSEAO.bind(container);
          scriptJquery(".seao_smoothbox_lightbox_close").on('click', function (event) {
            event.stopPropagation();
            SmoothboxSEAO.close();
          });
          if (params.callBack && (typeof params.callBack) === 'function') {
            params.callBack(container);
          }
        };
        var JSCount = SmoothboxSEAO.addScriptFiles.length;
        var StyleSheetCount = SmoothboxSEAO.addStylesheets.length;
        var totalFiles = JSCount + StyleSheetCount;
        var i = 0, succes = 0;
        if (succes === totalFiles)
          onLoadContent();
        for (i; i < JSCount; i++) {
          jQuery.ajax({
            url: SmoothboxSEAO.addScriptFiles[i],
            success: function() {
            succes++;
            if (succes === totalFiles)
              onLoadContent();
            }
          });
        }
        SmoothboxSEAO.addScriptFiles = [];
        for (i = 0; i < StyleSheetCount; i++) {
          jQuery.ajax({
            url: SmoothboxSEAO.addStylesheets[i],
            success: function() {
            succes++;
            if (succes === totalFiles)
              onLoadContent();
            }
          });
        }
        SmoothboxSEAO.addStylesheets = [];

      }
    });
  }
};

en4.seaocore.socialService = {
  clickHandler: function (el) {
    var request = en4.core.request.send(scriptJquery.ajax({
      url: en4.core.baseUrl + 'siteshare/index/social-service-click',
      method: 'POST',
      dataType: 'json',
      data: {
        format: 'json',
        shareUrl: scriptJquery('#'+el).attr('data-url'),
        serviceType: scriptJquery('#'+el).attr('data-service')
      },
      success: function () {

      }
    }));
  }
};
window.addEventListener('DOMContentLoaded', function ()
{
  SmoothboxSEAO.bind();
});

window.addEventListener('load', function ()
{
  SmoothboxSEAO.bind();
});

en4.seaocore.covertdateDmyToMdy = function (date) {
  starttime = date.split("/");
  date = starttime[1] + '/' + starttime[0] + '/' + starttime[2];
  return date;
};

/*  Community Ad Plugin JS Start here*/
en4.communityad = {
  sendReq: function (container, content_id, isAdboardPage, requestParams) {
    var url = en4.core.baseUrl + 'widget';
    var params = {
      format: 'html',
      is_ajax_load: 1,
      subject: en4.core.subject.guid,
      isAdboardPage: isAdboardPage
    };
    if (!content_id) {
      url = en4.core.baseUrl + 'widget/index/mod/communityad/name/ads';
    } else {
      params.content_id = content_id;
    }
    if (requestParams)
      params = scriptJquery.extend(requestParams, params);
    en4.core.request.send(scriptJquery.ajax({
      url: url,
      method: 'get',
      data: params,
      success: function (responseHTML) {
        container.empty();
        scriptJquery(responseHTML).appendTo(container);
        en4.core.runonce.trigger();
        Smoothbox.bind(container);
      }
    }));
  }
};
var communityad_likeinfo = function (ad_id, resource_type, resource_id, owner_id, widgetType, core_like) {
  // SENDING REQUEST TO AJAX
  var request = createLike(ad_id, resource_type, resource_id, owner_id, widgetType, core_like);
  // RESPONCE FROM AJAX
  request.addEventListener('complete', function (responseJSON) {
    if (responseJSON.like_id)
    {
      scriptJquery('#'+widgetType + '_likeid_info_' + ad_id).value = responseJSON.like_id;
      scriptJquery('#'+resource_type + '_' + widgetType + '_most_likes_' + ad_id).css( 'display', 'none' );
      scriptJquery('#'+resource_type + '_' + widgetType + '_unlikes_' + ad_id).css( 'display', 'block' );
    } else
    {
      scriptJquery('#'+widgetType + '_likeid_info_' + ad_id).value = 0;
      scriptJquery('#'+resource_type + '_' + widgetType + '_most_likes_' + ad_id).css( 'display', 'block' );
      scriptJquery('#'+resource_type + '_' + widgetType + '_unlikes_' + ad_id).css( 'display', 'none' );
    }
  });
}
/* $Id: core.js 2011-02-16 9:40:21Z SocialEngineAddOns Copyright 2009-2011 BigStep Technologies Pvt. Ltd. $ */

// Use: Ads Display.
// Function Call: When click on cross of any advertisment.
function adCancel(div_id, widgetType) {
  scriptJquery('#'+widgetType + '_ad_cancel_' + div_id).css( 'display', 'block' );
  scriptJquery('#'+widgetType + '_ad_' + div_id).css( 'display', 'none' );
}

// Use: Ads Display.
// Function Call: After click on cross of any ads then show option of 'undo' if click on the 'undo'.
function adUndo(div_id, widgetType) {
  scriptJquery('#'+widgetType + '_ad_cancel_' + div_id).css( 'display', 'none' );
  // scriptJquery( '#' + widgetType + '_ad_' + div_id).css( 'display', 'block' );
  if (scriptJquery('#'+widgetType + '_other_' + div_id).checked) {
    scriptJquery('#'+widgetType + '_other_' + div_id).checked = false;
    scriptJquery('#'+widgetType + '_other_text_' + div_id).css( 'display', 'none' );
    scriptJquery('#'+widgetType + '_other_text_' + div_id).value = 'Type your reason here...';
    scriptJquery('#'+widgetType + '_other_button_' + div_id).css( 'display', 'none' );
  }
}

// Use: Ads Display.
// Function Call: After click on cross of any ads then show radio button if click on 'other' type radio button.
function otherAdCannel(adRadioValue, div_id, widgetType) {
  // Condition: When click on 'other radio button'.
  if (adRadioValue == 4) {
    scriptJquery('#'+widgetType + '_other_text_' + div_id).css( 'display', 'block' );
    scriptJquery('#'+widgetType + '_other_button_' + div_id).css( 'display', 'block' );
  }
}

// Use: Ads Display
// Function Call: When save entry in data base.
function adSave(adCancelReasion, adsId, divId, widgetType) {
  var adDescription = 0;
  // Condition: Find out 'Description' if select other options from radio button.

  if (adCancelReasion == 'Other') {
    if (scriptJquery('#'+widgetType + '_other_text_' + divId).value != 'Type your reason here...') {
      adDescription = scriptJquery('#'+widgetType + '_other_text_' + divId).value;
    }
  }
  scriptJquery('#'+widgetType + '_ad_cancel_' + divId).html( '<center><img src="application/modules/Seaocore/externals/images/core/loading.gif" alt=""></center>' );
  en4.core.request.send(scriptJquery.ajax({
    url: en4.core.baseUrl + 'communityad/display/adsave',
      method: 'POST',
      dataType: 'html',
    data: {
      format: 'html',
      adCancelReasion: adCancelReasion,
      adDescription: adDescription,
      adsId: adsId
    }
  }), {
    'element': scriptJquery('#'+widgetType + '_ad_cancel_' + divId)
  })
}

// Function: For 'Advertisment' liked or unliked.
function createLike(ad_id, resource_type, resource_id, owner_id, widgetType, core_like)
{
  var like_id = scriptJquery('#'+widgetType + '_likeid_info_' + ad_id).value;
  var request = en4.core.request.send(scriptJquery.ajax({
    url: en4.core.baseUrl + 'communityad/display/globallikes',
      method: 'POST',
      dataType: 'json',
    data: {
      format: 'json',
      'ad_id': ad_id,
      'resource_type': resource_type,
      'resource_id': resource_id,
      'owner_id': owner_id,
      'like_id': like_id,
      'core_like': core_like
    }
  }));
  return request;
}
/*  Community Ad Plugin JS End here*/

function locationAutoSuggest(countrycities, location_field, city_field) {

  if (city_field && scriptJquery('#'+city_field)[0]) {

    if (countrycities) {
      var options = {
        types: ['(cities)'],
        componentRestrictions: {country: countrycities}
      };
    } else {
      var options = {
        types: ['(cities)']
      };
    }

    var autocomplete = new google.maps.places.Autocomplete(scriptJquery('#'+city_field)[0], options);
  }

  if (location_field && scriptJquery('#'+location_field)[0]) {

    if (countrycities) {
      var options = {
        //types: [''],//We are not passing any values here for showing all results of some specific country.
        componentRestrictions: {country: countrycities}
      };
    } else {
      var options = {

      };
    }
    var autocomplete = new google.maps.places.Autocomplete(scriptJquery('#'+location_field)[0], options);
  }

}

//WHEN CONTENT ON THE PAGE LOAD FROM THE AJAX IN THAT CASE SMOOTHBOX CLASS DOES NOT WORK THEN WE USE BELOW FUNCTION
function openSmoothbox(thisobj) {
  var Obj_Url = thisobj.href;
  Smoothbox.open(Obj_Url);
}

function showShareLinks(val) {
  scriptJquery(document.body).on('click', showHideToggleShareLinks);
  scriptJquery('.siteevent_share_links_toggle').off('click').on('click', function (event) {
    event.stopPropagation();
    //showHideToggleShareLinks();
    scriptJquery(this).parents('.siteevent_grid_footer').find('.siteevent_share_links').toggle();

    if (typeof val == 'undefined') {
      scriptJquery(this).toggle();
    } else {
      scriptJquery(this).show();
    }
  });
}

function showHideToggleShareLinks() {
  scriptJquery('.siteevent_share_links_toggle').show();
  scriptJquery('.siteevent_share_links_toggle').parents('.siteevent_grid_footer').find('.siteevent_share_links').hide();
}

function showReviewShareLinks(val) {
  scriptJquery(document.body).on('click', showHideToggleReviewShareLinks);
  scriptJquery('.sitereview_share_links_toggle').off('click').on('click', function (event) {
    event.stopPropagation();
    //showHideToggleShareLinks();
    scriptJquery(this).parents('.sitereview_grid_footer').find('.sitereview_share_links').toggle();
    if (typeof val == 'undefined') {
      scriptJquery(this).toggle();
    } else {
      scriptJquery(this).show();
    }
  });
}

function showHideToggleReviewShareLinks() {
  scriptJquery('.sitereview_share_links_toggle').show();
  scriptJquery('.sitereview_share_links_toggle').parents('.sitereview_grid_footer').find('.sitereview_share_links').hide();
}

function showPageShareLinks(val) {
  scriptJquery(document.body).on('click', showHideTogglePageShareLinks);
  scriptJquery('.sitepage_share_links_toggle').off('click').on('click', function (event) {
    event.stopPropagation();
    //showHideToggleShareLinks();
    scriptJquery(this).parents('.sitepage_grid_footer').find('.sitepage_share_links').toggle();

    if (typeof val == 'undefined') {
      scriptJquery(this).toggle();
    } else {
      scriptJquery(this).show();
    }
  });
}

function showHideTogglePageShareLinks() {
  scriptJquery('.sitepage_share_links_toggle').show();
  scriptJquery('.sitepage_share_links_toggle').parents('.sitepage_grid_footer').find('.sitepage_share_links').hide();
}

function showForumShareLinks(val) {
  scriptJquery(document.body).on('click', showForumHideToggleShareLinks);
  scriptJquery('.siteforum_share_links_toggle').off('click').on('click', function (event) {
    event.stopPropagation();
    //showHideToggleShareLinks();
    scriptJquery(this).parents('.siteforum_grid_footer').find('.siteforum_share_links').toggle();

    if (typeof val == 'undefined') {
      scriptJquery(this).toggle();
    } else {
      scriptJquery(this).show();
    }
  });
}

function showForumHideToggleShareLinks() {
  scriptJquery('.siteforum_share_links_toggle').show();
  scriptJquery('.siteforum_share_links_toggle').parents('.siteforum_grid_footer').find('.siteforum_share_links').hide();
}


function showGroupShareLinks(val) {
  scriptJquery('body').on('click', showHideToggleGroupShareLinks);
  scriptJquery('.sitegroup_share_links_toggle').off('click').on('click', function (event) {
    event.preventDefault()
    event.stopPropagation();
    //showHideToggleShareLinks();
    scriptJquery(this).parents('.sitegroup_grid_footer').find('.sitegroup_share_links').toggle();
    scriptJquery('.tall-group-box-menu').show();

    if (typeof val == 'undefined') {
      scriptJquery(this).toggle();
    } else {
      scriptJquery(this).show();
    }
  });
}

function showHideToggleGroupShareLinks() {
  scriptJquery('.sitegroup_share_links_toggle').show();
  scriptJquery('.tall-group-box-menu').show();
  scriptJquery('.sitegroup_share_links_toggle').parents('.sitegroup_grid_footer').find('.sitegroup_share_links').hide();
}
en4.seaocore.uploaderInstance = new Hash();
var callbacks = {};


en4.seaocore.miniMenu = {
  init: function () {
    if (en4.user.viewer.id) {
      setInterval(en4.seaocore.miniMenu.checkUpdates, 30000);
    }
    scriptJquery(document.body).on('click', en4.seaocore.miniMenu.pullDownCloseHandler);
    scriptJquery('.seaocore_mimi_menu_pulldown_item').on('click', en4.seaocore.miniMenu.pullDownOpenHandler);
  },
  pullDownCloseHandler: function (event) {
    var el = scriptJquery(event.target);
    if (el.hasClass('seaocore_mimi_menu_pulldown_item') || el.parents('.seaocore_mimi_menu_pulldown_item')) {
      return;
    }
    en4.seaocore.miniMenu.pullDownClose();
  },
  pullDownOpenHandler: function (event) {
    var el = scriptJquery(event.target);
    // if (el.hasClass('seaocore_pulldown_wrapper') || el.parents('.seaocore_pulldown_wrapper')) {
    //   return;
    // }
    if (!el.hasClass('seaocore_mimi_menu_pulldown_item')) {
      el = el.parents('.seaocore_mimi_menu_pulldown_item');
    }
    
    if (el.hasClass('_pulldown_item_active')) {
      en4.seaocore.miniMenu.pullDownClose();
      return;
    }
    
    en4.seaocore.miniMenu.pullDownClose();
    el.addClass('_pulldown_item_active');
    el.find('._count_bubble').hide();
    var actionUrl = el.find('.seaocore_pulldown_wrapper').attr('data-action');
    if (!actionUrl) {
      return;
    }
    var newContentRequest = scriptJquery.ajax({
      url: actionUrl,
      method: 'POST',
      dataType : 'html',
      request: function () {
      },
      data: {
        format: 'html',
        page: 1,
        noOfUpdates: 10,
        isajax: 1,
      },
      success: function (responseHTML)
      {
        el.find('.seaocore_pulldown_contents').html(responseText);
        en4.core.runonce.trigger();

      }
    });
  },
  pullDownClose: function () {
    scriptJquery('.seaocore_mimi_menu_pulldown_item').removeClass('_pulldown_item_active');
  },
  notifications: {
    markAsRead: function () {
      scriptJquery("#notifications_main").finds('li.notifications_unread').each(function (el) {
        el.removeClass("notifications_unread");
      });
      en4.seaocore.miniMenu.pullDownClose();
      en4.core.request.send(scriptJquery.ajax({
        url: en4.core.baseUrl + 'seaocore/mini-menu/mark-notifications-as-read',
      method: 'POST',
      dataType: 'json',
        data: {
          format: 'json'
        },
        success: function (responseJSON) {
        }
      }), {'force': true});
    },
    onClick: function (event) {
      var current_link = event.target;
      var notification_li = scriptJquery(current_link).parents('li');
      var forward_link;
      if (current_link.attr('href')) {
        forward_link = current_link.attr('href');
      } else {
        forward_link = scriptJquery('#'+current_link).finds('a:last-child').attr('href');
        if (forward_link == '' || scriptJquery('#'+current_link).prop("tagName").toLowerCase() == 'img') {
          var a_el = scriptJquery('#'+current_link).parents('a');
          if (a_el)
            forward_link = scriptJquery('#'+current_link).parents('a').attr('href');
        }
        if (forward_link == '' || scriptJquery('#'+current_link).prop("tagName").toLowerCase() == 'span') {
          //      if(scriptJquery( '#' + notification_li).length && scriptJquery( '#' + notification_li).finds('a:last-child') && scriptJquery( '#' + notification_li).finds('a:last-child')[0])
          //        forward_link = scriptJquery( '#' + notification_li).finds('a:last-child')[0].attr('href');
          forward_link = scriptJquery('#'+notification_li).finds('a:last-child').attr('href');
        }
      }
      if (forward_link) {
        en4.core.request.send(scriptJquery.ajax({
          url: en4.core.baseUrl + 'activity/notifications/markread',
      method: 'POST',
      dataType: 'json',
          data: {
            format: 'json',
            'actionid': notification_li.attr('value')
          },
          success: window.location = forward_link
        }), {'force': true});
      }
    }
  },
  messages: {
    markReadUnread: function (message_id)
    {
      var is_message_read;
      // Mark Unread
      if (scriptJquery("#message_conversation_" + message_id).hasClass("seocore_pulldown_item_list_new"))
      {
        scriptJquery("#message_conversation_" + message_id).removeClass("seocore_pulldown_item_list_new");
        scriptJquery("#seaocore_message_icon_" + message_id).setAttribute("title", en4.core.language.translate("Mark as Read"));
        is_message_read = 1;
      } else {
        scriptJquery("#message_conversation_" + message_id).addClass("seocore_pulldown_item_list_new");
        scriptJquery("#seaocore_message_icon_" + message_id).setAttribute("title", en4.core.language.translate("Mark as Unread"));
        is_message_read = 0;
      }

      en4.core.request.send(scriptJquery.ajax({
        url: en4.core.baseUrl + 'seaocore/mini-menu/mark-message-read-unread',
      method: 'POST',
      dataType: 'json',
        data: {
          format: 'json',
          messgae_id: message_id,
          is_read: is_message_read
        },
        success: function (responseJSON)
        {
        }
      }), {'force': true});
    }
  },
  checkUpdates: function () {
    en4.core.request.send(scriptJquery.ajax({
      url: en4.core.baseUrl + 'seaocore/mini-menu/check-new-updates',
      method: 'POST',
      dataType: 'json',
      data: {
        format: 'json'
      },
      success: function (responseJSON)
      {
        if (responseJSON.newFriendRequest)
        {
          scriptJquery('.seaocore_mini_menu_items li span._count_bubble_friend_request_updates').html( responseJSON.newFriendRequest).show();
        } else {
          scriptJquery('.seaocore_mini_menu_items li span._count_bubble_friend_request_updates').html(0).hide();
        }
        if (responseJSON.newMessage)
        {
          scriptJquery('.seaocore_mini_menu_items li span._count_bubble_message_updates').html(responseJSON.newMessage).show();
        } else {
          scriptJquery('.seaocore_mini_menu_items li span._count_bubble_message_updates').html(0).hide();
        }
        if (responseJSON.newNotification) {
          scriptJquery('.seaocore_mini_menu_items li span._count_bubble_activity_updates').html(responseJSON.newNotification).show();
        } else {
          scriptJquery('.seaocore_mini_menu_items li span._count_bubble_activity_updates').html(0).hide();
        }
      }
    }), {'force': true});
  }
};


function passwordRoutine(value){
      var pswd = value;
      // valid length
      if ( pswd.length < 6) {
        scriptJquery('passwordroutine_length').removeClass('valid').addClass('invalid');
      } else {
        scriptJquery('passwordroutine_length').removeClass('invalid').addClass('valid');
      }

      //validate special character
      if ( pswd.match(/[#?!@$%^&*-]/) ) {
          if ( pswd.match(/[\\\\:\/]/) ) {
              scriptJquery('passwordroutine_specialcharacters').removeClass('valid').addClass('invalid');
          } else {
              scriptJquery('passwordroutine_specialcharacters').removeClass('invalid').addClass('valid');
          }
      } else {
          scriptJquery('passwordroutine_specialcharacters').removeClass('valid').addClass('invalid');
      }

      //validate capital letter
      if ( pswd.match(/[A-Z]/) ) {
          scriptJquery('passwordroutine_capital').removeClass('invalid').addClass('valid');
      } else {
          scriptJquery('passwordroutine_capital').removeClass('valid').addClass('invalid');
      }

      //validate small letter
      if ( pswd.match(/[a-z]/) ) {
          scriptJquery('passwordroutine_lowerLetter').removeClass('invalid').addClass('valid');
      } else {
          scriptJquery('passwordroutine_lowerLetter').removeClass('valid').addClass('invalid');
      }

      //validate number
      if ( pswd.match(/\d{1}/) ) {
          scriptJquery('passwordroutine_number').removeClass('invalid').addClass('valid');
      } else {
          scriptJquery('passwordroutine_number').removeClass('valid').addClass('invalid');
      }
  }
