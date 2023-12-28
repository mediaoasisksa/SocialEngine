/*
 ---
 name: SEATips
 description: Class for creating nice tips that follow the mouse cursor when hovering an element.
 
 Extends :Tips
 
 requires:
 - Core/Options
 - Core/Events
 - Core/Element.Event
 - Core/Element.Style
 - Core/Element.Dimensions
 - /MooTools.More
 
 provides: [Tips]
 
 ...
 */
window.addEvent('domready', function () {
  en4.seaocore.isDomReady = true;
  en4.seaocore.setDomElements();
});

(function () {
  this.SEATips = new Class({
    Extends: Tips,
    options: {
      canHide: true
    },
    hide: function (element) {
      if (!this.options.canHide)
        return;
      if (!this.tip)
        document.id(this);
      this.fireEvent('hide', [this.tip, element]);
    },
    position: function (event) {
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
      this.tip.setStyles(obj);
      Smoothbox.bind(this.tip);
    }
  });
})();

en4.seaocore = {
  headerElement: 'global_header',
  footerElement: 'global_footer',
  contentElement: 'global_content',
  contentWrapperElement: 'global_wrapper',

  isDomElementSet: false,
  isDomReady: false,

  setDomElements: function () {
    this.headerElement = $('se-header') ? 'se-header' : 'global_header';
    this.footerElement = $('se-footer') ? 'se-footer' : 'global_footer';
    this.contentElement = $('se-content') ? 'se-content' : 'global_content';
    this.contentWrapperElement = $('se-main') ? 'se-main' : 'global_wrapper';
    this.isDomElementSet = true;
  },

  setLayoutWidth: function (elementId, width) {
    var layoutColumn = null;
    if ($(elementId).getParent('.layout_left')) {
      layoutColumn = $(elementId).getParent('.layout_left');
    } else if ($(elementId).getParent('.layout_right')) {
      layoutColumn = $(elementId).getParent('.layout_right');
    } else if ($(elementId).getParent('.layout_middle')) {
      layoutColumn = $(elementId).getParent('.layout_middle');
    }
    if (layoutColumn) {
      layoutColumn.setStyle('width', width);
    }
    $(elementId).destroy();
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
    enableSignup: true,
    enableLogin: true,
    autoOpenLogin: false,
    autoOpenSignup: false,
    allowClose: true,
    openDelay: 100,
    popupVisibilty:0 // X numbers of time
  },
  init: function (params) {
    this.params = $merge(this.params, params);
    this.attachEvents(params);
    if ($('socialsignup_popup_div'))
      $('socialsignup_popup_div').addClass('socialsignup_popup_div');
    if ($('sociallogin_signup_popup'))
      $('sociallogin_signup_popup').addClass('sociallogin_signup_popup');
    
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
    $$('.user_signup_link').each(function (el) {
      el.addClass('seao_popup_user_signup_link').removeClass('user_signup_link');
    });
    $$('.user_auth_link').each(function (el) {
      el.addClass('seao_popup_user_auth_link').removeClass('user_auth_link');
      ;
    });
    if (params.enableSignup) {
      $$('.seao_popup_user_signup_link').removeEvent('click').addEvent('click', function (event) {
        this.setPopupContent(event, 'seaocore_login_signup_popup', 'seao_user_signup_popup');
        this.showPopupForm('seao_user_signup_popup');
      }.bind(this));
    }
    if (params.enableLogin) {
      $$('.seao_popup_user_auth_link').removeEvent('click').addEvent('click', function (event) {
        this.setPopupContent(event, 'seaocore_login_signup_popup', 'seao_user_auth_popup');
        this.showPopupForm('seao_user_auth_popup');
      }.bind(this));
    }
  },
  setPopupContent: function (event, contentId) {
    if (event) {
      event.stop();
    }
    SmoothboxSEAO.open({element: $(contentId).get('html'), class: 'seaocore_login_popup_wrapper', closable: this.params.allowClose});
    SmoothboxSEAO.setHtmlScroll("hidden");
    this.setLoginForm(SmoothboxSEAO.content.getElement('.seao_user_auth_popup form'));
    this.setSignupForm(SmoothboxSEAO.content.getElement('.seao_user_signup_popup form'));
    SmoothboxSEAO.content.getElements('ul._navigation > li').addEvent('click', function (event) {
      this.showPopupForm($(event.target).get('data-role'));
    }.bind(this));
  },
  setLoginForm: function (el) {
    if (el.hasClass('seaocore_popup_user_form_login')) {
      return;
    }
    el.addClass('seaocore_popup_user_form_login');
    var handelerOnFocus = function (event) {
      $(event.target).getParent('.form-wrapper').addClass('form-wapper-focus');
    };
    var handelerOnBlur = function (event) {
      $(event.target).getParent('.form-wrapper').removeClass('form-wapper-focus');
    };
    if (el.getElementById("twitter-wrapper") || el.getElementById("facebook-wrapper")) {
      var wrapperDiv = document.createElement("div");
      wrapperDiv.id = "seaocore_loginform_sociallinks";
      wrapperDiv.inject(el);
      if (el.getElementById("facebook-wrapper")) {
        el.getElementById("facebook-element").title = en4.core.language.translate("Login with Facebook");
        el.getElementById("facebook-wrapper").inject(wrapperDiv);
      }

      if (el.getElementById("twitter-wrapper")) {
        el.getElementById("twitter-element").title = en4.core.language.translate("Login with Twitter");
        el.getElementById("twitter-wrapper").inject(wrapperDiv);
      }
    }

    el.getElements('input').each(function (el) {
      var type = el.get('type');
      if (type == 'email') {
        el.getParent('.form-wrapper').addClass('form-email-wrapper');
      }
      if (el.get('id') == 'password') {
        var showHideEl = new Element('div', {
          'id': 'show-hide-password-element',
          'class': 'show-hide-password-form-element fa fa-eye'
        }).inject(el.getParent('.form-element'));
        showHideEl.addEvent('click', function () {
          if (el.get('type') == 'password') {
            showHideEl.addClass('fa-eye-slash').removeClass('fa-eye');
            el.set('type', 'text');
          } else {
            showHideEl.removeClass('fa-eye-slash').addClass('fa-eye');
            el.set('type', 'password');
          }
        });
        $("user_form_login").addEvent('submit', function () {
          el.set('type', 'password');
          showHideEl.removeClass('fa-eye-slash').addClass('fa-eye');
        });
      }
      if ((type == 'text' || type == 'email' || type == 'password') && el.getParent('.form-wrapper').getElement('label').get('html')) {
        el.set('placeholder', el.getParent('.form-wrapper').getElement('label').get('html'));
        el.getParent('.form-wrapper').addClass('_slpff');
        el.addEvent('focus', handelerOnFocus);
        el.addEvent('blur', handelerOnBlur);
      }
    });
  },

  setSignupForm: function (formEl) {
    if (formEl.hasClass('seaocore_popup_user_form_signup')) {
      return;
    }
    formEl.addClass('seaocore_popup_user_form_signup');
    var handelerOnFocus = function (event) {
      $(event.target).getParent('.form-wrapper').addClass('form-wapper-focus');
    };
    var handelerOnBlur = function (event) {
      $(event.target).getParent('.form-wrapper').removeClass('form-wapper-focus');
    };
    if (formEl.getElementById("twitter-wrapper") || formEl.getElementById("facebook-wrapper")) {
      var wrapperDiv = document.createElement("div");
      wrapperDiv.id = "seaocore_signupform_sociallinks";
      wrapperDiv.inject(formEl, 'top');
      if (formEl.getElementById("facebook-wrapper")) {
        var wrapperDiv = document.createElement("span");
        wrapperDiv.id = "facebook";
        wrapperDiv.innerHTML = "<div id='facebook-wrapper'><div id='facebook-element'><a href='" + en4.core.baseUrl + "user/auth/facebook'><img border='0' alt='Connect with Facebook' title = 'Login with Facebook' src='" + en4.core.baseUrl + "application/modules/User/externals/images/facebook-sign-in.gif'></a></div></div>";
        wrapperDiv.inject(wrapperDiv);
      }

      if (formEl.getElementById("twitter-wrapper")) {
        var wrapperDiv = document.createElement("span");
        wrapperDiv.id = "twitter";
        wrapperDiv.innerHTML = "<div id='twitter-wrapper'><div id='twitter-element'><a href='" + en4.core.baseUrl + "user/auth/twitter'><img border='0' alt='Connect with Twitter' title = 'Login with Twitter' src='" + en4.core.baseUrl + "application/modules/User/externals/images/twitter-sign-in.gif'></a></div></div>";
        wrapperDiv.inject(wrapperDiv);
      }
    }

    var className = 'seao_seaolightbox_signup';
    if (wrapperDiv && wrapperDiv.getElement('.plan_subscriptions_container')) {
      className = className + ' seaocore_seaolightbox_plan_subscriptions';
    }

    formEl.getElements('input').each(function (el) {
      var type = el.get('type');
      if (type == 'email') {
        el.getParent('.form-wrapper').addClass('form-email-wrapper');
      }
      if ((type == 'text' || type == 'email' || type == 'password') && el.getParent('.form-wrapper').getElement('label').get('html')) {
        el.set('placeholder', el.getParent('.form-wrapper').getElement('label').get('html'));
        el.getParent('.form-wrapper').addClass('_sspff');
        el.addEvent('focus', handelerOnFocus);
        el.addEvent('blur', handelerOnBlur);
      }
    });
    if (formEl.getElementById('password-element') && formEl.getElementById('passconf-element')) {
      formEl.getElementById('password-element').getParent('.form-wrapper').addClass('_spfhf');
      formEl.getElementById('passconf-element').getParent('.form-wrapper').addClass('_spfhf');
    }
    var languageEl = formEl.getElementById('language-element'),
            timezoneEl= formEl.getElementById('timezone-element');
    var canMakeSmallFileds = !!languageEl && !!timezoneEl;
    if (timezoneEl && !formEl.getElementById('timezone-option-label')) {
      formEl.getElementById('timezone-wrapper').addClass('_spfhf');
      var el = formEl.getElementById('timezone');
      var options = new Element('option', {
        'id': 'timezone-option-label',
        'disabled': 'disabled',
        'class': '_sspff_option_label',
        'html': el.getParent('.form-wrapper').getElement('label').get('html')

      });
      options.inject(el, 'top');
      el.getParent('.form-wrapper').addClass('_sspff');
      if (canMakeSmallFileds) {
        el.getParent('.form-wrapper').addClass('_spfhf');
      }
    }
    if (languageEl && !formEl.getElementById('language-option-label')) {
      formEl.getElementById('language-wrapper').addClass('_spfhf')
      var el = formEl.getElementById('language');
      var options = new Element('option', {
        'id': 'language-option-label',
        'class': '_sspff_option_label',
        'disabled': 'disabled',
        'html': el.getParent('.form-wrapper').getElement('label').get('html')

      });
      options.inject(el, 'top');
      el.getParent('.form-wrapper').addClass('_sspff');
      if (canMakeSmallFileds) {
        el.getParent('.form-wrapper').addClass('_spfhf');
      }
    }
    if (formEl.getElementById('profile_type') && formEl.getElementById('profile_type').get('type') != 'hidden' && !formEl.getElementById('profile_type-option-label')) {
      var el = formEl.getElementById('profile_type');
      var addedFields = false;
      el.getElements('option').each(function (el) {
        if (!el.get('value').trim()) {
          el.set('html', el.getParent('.form-wrapper').getElement('label').get('html')).addClass('_sspff_option_label');
          addedFields = true;
        }
      });
      if (!addedFields) {
        var options = new Element('option', {
          'id': 'profile_type-option-label',
          'class': '_sspff_option_label',
          'disabled': 'disabled',
          'html': el.getParent('.form-wrapper').getElement('label').get('html')

        });
        options.inject(el, 'top');
      }
      el.getParent('.form-wrapper').addClass('_sspff seaocore_popup_profile_type_form_field');
    }
  },
  showPopupForm: function (elementId) {
    SmoothboxSEAO.content.getElements('ul._navigation > li').removeClass('active');
    SmoothboxSEAO.content.getElements('ul._navigation > li[data-role=' + elementId + ']').addClass('active');
    SmoothboxSEAO.content.getElements('._form_wapper ._form_cont').hide();
    SmoothboxSEAO.content.getElement('.' + elementId).show();
    en4.core.reCaptcha.render();
    //SmoothboxSEAO.doAutoResize();
  }
};
/**
 * likes
 */
en4.seaocore.likes = {
  like: function (type, id, show_bottom_post, comment_id) {
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'seaocore/comment/like',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: 0,
        show_bottom_post: show_bottom_post
      },
      onSuccess: function (responseJSON) {
        if ($type(responseJSON) == 'object' && $type(responseJSON.status)) {
          if ($(type + '_' + id + 'like_link'))
            $(type + '_' + id + 'like_link').style.display = "none";
          if ($(type + '_' + id + 'unlike_link'))
            $(type + '_' + id + 'unlike_link').style.display = "inline-block";
        }
      }
    }), {
      'element': $('comments' + '_' + type + '_' + id)
        //      "force":true
    });
  },
  unlike: function (type, id, show_bottom_post, comment_id) {
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'seaocore/comment/unlike',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id,
        show_bottom_post: show_bottom_post
      },
      onSuccess: function (responseJSON) {
        if ($type(responseJSON) == 'object' && $type(responseJSON.status)) {
          if ($(type + '_' + id + 'unlike_link'))
            $(type + '_' + id + 'unlike_link').style.display = "none";
          if ($(type + '_' + id + 'like_link'))
            $(type + '_' + id + 'like_link').style.display = "inline-block";
        }
      }
    }), {
      'element': $('comments' + '_' + type + '_' + id)
        //      "force":true
    });
  }
};

en4.seaocore.comments = {
  loadComments: function (type, id, page, show_bottom_post) {
    en4.core.request.send(new Request.HTML({
      url: en4.core.baseUrl + 'seaocore/comment/list',
      data: {
        format: 'html',
        type: type,
        id: id,
        page: page,
        show_bottom_post: show_bottom_post
      }
    }), {
      'element': $('comments' + '_' + type + '_' + id),
      "force": true
    });
  },
  attachCreateComment: function (formElement, type, id, show_bottom_post) {
    var bind = this;
    if (show_bottom_post == 1) {
      formElement.addEvent((Browser.Engine.trident || Browser.Engine.webkit) ? 'keydown' : 'keypress', function (event) {
        if (event.shift && event.key == 'enter') {
        } else if (event.key == 'enter') {
          event.stop();
          var form_values = formElement.toQueryString();
          form_values += '&format=json';
          form_values += '&id=' + formElement.identity.value;
          form_values += '&show_bottom_post=' + show_bottom_post;
          formElement.style.display = "none";
          if ($("comment-form-loading-li_" + type + '_' + id))
            $("comment-form-loading-li_" + type + '_' + id).style.display = "block";
          en4.core.request.send(new Request.JSON({
            url: en4.core.baseUrl + 'seaocore/comment/create',
            data: form_values,
            type: type,
            id: id,
            show_bottom_post: show_bottom_post
          }), {
            'element': $('comments' + '_' + type + '_' + id),
            "force": true
          });

        }
      });

      // add blur event
      formElement.body.addEvent('blur', function () {
        formElement.style.display = "none";
        if ($("comment-form-open-li_" + type + '_' + id))
          $("comment-form-open-li_" + type + '_' + id).style.display = "block";
      });
    }
    formElement.addEvent('submit', function (event) {
      event.stop();
      var form_values = formElement.toQueryString();
      form_values += '&format=json';
      form_values += '&id=' + formElement.identity.value;
      form_values += '&show_bottom_post=' + show_bottom_post;
      en4.core.request.send(new Request.JSON({
        url: en4.core.baseUrl + 'seaocore/comment/create',
        data: form_values,
        type: type,
        id: id,
        show_bottom_post: show_bottom_post
      }), {
        'element': $('comments' + '_' + type + '_' + id),
        "force": true
      });
    })
  },
  comment: function (type, id, body, show_bottom_post) {
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'seaocore/comment/create',
      data: {
        format: 'json',
        type: type,
        id: id,
        body: body,
        show_bottom_post: show_bottom_post
      }
    }), {
      'element': $('comments' + '_' + type + '_' + id),
      "force": true
    });
  },
  like: function (type, id, show_bottom_post, comment_id) {
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'seaocore/comment/like',
      data: {
        format: 'json',
        type: type,
        id: id,
        page: pageComment,
        comment_id: comment_id,
        show_bottom_post: show_bottom_post
      },
      onSuccess: function (responseJSON) {
        if ($type(responseJSON) == 'object' && $type(responseJSON.status)) {
          if ($(type + '_' + id + 'like_link'))
            $(type + '_' + id + 'like_link').style.display = "none";
          if ($(type + '_' + id + 'unlike_link'))
            $(type + '_' + id + 'unlike_link').style.display = "inline-block";
        }
      }
    }), {
      'element': $('comments' + '_' + type + '_' + id),
      "force": true
    });
  },
  unlike: function (type, id, show_bottom_post, comment_id) {
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'seaocore/comment/unlike',
      data: {
        format: 'json',
        type: type,
        id: id,
        page: pageComment,
        comment_id: comment_id,
        show_bottom_post: show_bottom_post
      },
      onSuccess: function (responseJSON) {
        if ($type(responseJSON) == 'object' && $type(responseJSON.status)) {
          if ($(type + '_' + id + 'unlike_link'))
            $(type + '_' + id + 'unlike_link').style.display = "none";
          if ($(type + '_' + id + 'like_link'))
            $(type + '_' + id + 'like_link').style.display = "inline-block";
        }
      }
    }), {
      'element': $('comments' + '_' + type + '_' + id),
      "force": true
    });
  },
  showLikes: function (type, id, show_bottom_post) {
    en4.core.request.send(new Request.HTML({
      url: en4.core.baseUrl + 'seaocore/comment/list',
      data: {
        format: 'html',
        type: type,
        id: id,
        viewAllLikes: true,
        show_bottom_post: show_bottom_post
      }
    }), {
      'element': $('comments' + '_' + type + '_' + id),
      "force": true
    });
  },
  deleteComment: function (type, id, comment_id) {
    if (!confirm(en4.core.language.translate('Are you sure you want to delete this?'))) {
      return;
    }
    (new Request.JSON({
      url: en4.core.baseUrl + 'seaocore/comment/delete',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id
      },
      onComplete: function () {
        if ($('comment-' + comment_id)) {
          $('comment-' + comment_id).destroy();
        }
        try {
          var commentCount = $$('.comments_options span')[0];
          var m = commentCount.get('html').match(/\d+/);
          var newCount = (parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 1 ? parseInt(m[0]) - 1 : 0);
          commentCount.set('html', commentCount.get('html').replace(m[0], newCount));
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
      var catarea = $(en4.seaocore.footerElement);
      if (catarea == null) {
        catarea = $(en4.seaocore.contentElement);
      }
      if (catarea != null && (typeof $('fb-root') == 'undefined' || $('fb-root') == null)) {
        var newdiv = document.createElement('div');
        newdiv.id = 'fb-root';
        newdiv.inject(catarea, 'after');
        var e = document.createElement('script');
        e.async = true;
        if (typeof local_language != 'undefined' && $type(local_language)) {
          e.src = document.location.protocol + '//connect.facebook.net/' + local_language + '/all.js';
        } else {
          e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
        }
        document.getElementById('fb-root').appendChild(e);
      }
    }());

  }

};

en4.seaocore.advlightbox = {
  createDefaultContent: function () {

  }
}
//window.addEvent('load', function() {
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

  en4.core.request.send(new Request.HTML({
    method: 'post',
    'url': en4.core.baseUrl + 'seaocore/feed/addfriendrequest',
    'data': {
      format: 'html',
      'resource_id': user_id
        //'action_id' : action_id,
    },
    onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
      var parent = el.getParent('div');
      var nextSibling = el.nextSibling;
      el.destroy();
      parent.insertBefore(new Element('span', {
        'html': responseHTML
      }), nextSibling);

    }
  }), {
    'force': true
  });
}



en4.seaocore.nestedcomments = {
  loadComments: function (type, id, page, order, parent_comment_id) {

    if ($('view_more_comments_' + parent_comment_id)) {
      $('view_more_comments_' + parent_comment_id).style.display = 'inline-block';
      $('view_more_comments_' + parent_comment_id).innerHTML = '<img src="application/modules/Seaocore/externals/images/core/loading.gif" alt="Loading" />';
    }
    if ($('view_previous_comments_' + parent_comment_id)) {
      $('view_previous_comments_' + parent_comment_id).style.display = 'inline-block';
      $('view_previous_comments_' + parent_comment_id).innerHTML = '<img src="application/modules/Seaocore/externals/images/core/loading.gif" alt="Loading" />';
    }
    if ($('view_later_comments_' + parent_comment_id)) {
      $('view_later_comments_' + parent_comment_id).style.display = 'inline-block';
      $('view_later_comments_' + parent_comment_id).innerHTML = '<img src="application/modules/Seaocore/externals/images/core/loading.gif" alt="Loading" />';
    }
    en4.core.request.send(new Request.HTML({
      url: en4.core.baseUrl + 'seaocore/nestedcomment/list',
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
      'element': $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
    });
  },
  loadcommentssortby: function (type, id, order, parent_comment_id) {
    if ($('sort' + '_' + type + '_' + id + '_' + parent_comment_id)) {
      $('sort' + '_' + type + '_' + id + '_' + parent_comment_id).style.display = 'inline-block';
      $('sort' + '_' + type + '_' + id + '_' + parent_comment_id).innerHTML = '<img src="application/modules/Seaocore/externals/images/core/loading.gif" alt="Loading" />';
    }
    en4.core.request.send(new Request.HTML({
      url: en4.core.baseUrl + 'seaocore/nestedcomment/list',
      data: {
        format: 'html',
        type: type,
        id: id,
        order: order,
        parent_div: 1,
        parent_comment_id: parent_comment_id
      }
    }), {
      'element': $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
    });
  },
  attachCreateComment: function (formElement, type, id, parent_comment_id) {
    var bind = this;
    formElement.addEvent('submit', function (event) {
      event.stop();
      if (formElement.body.value == '')
        return;
      if ($('seaocore_comment_image_' + type + '_' + id + '_' + parent_comment_id))
        $('seaocore_comment_image_' + type + '_' + id + '_' + parent_comment_id).destroy();
      var divEl = new Element('div', {
        'class': '',
        'html': '<img src="application/modules/Seaocore/externals/images/core/loading.gif" alt="Loading">',
        'id': 'seaocore_comment_image_' + type + '_' + id + '_' + parent_comment_id,
        'styles': {
          'display': 'inline-block'
        }
      });

      divEl.inject(formElement);
      var form_values = formElement.toQueryString();
      form_values += '&format=json';
      form_values += '&id=' + formElement.identity.value;

      en4.core.request.send(new Request.JSON({
        url: en4.core.baseUrl + 'seaocore/nestedcomment/create',
        data: form_values,
        type: type,
        id: id,
        onComplete: function (e) {
          if (parent_comment_id == 0)
            return;
          try {
            var replyCount = $$('.seaocore_replies_options span')[0];
            var m = replyCount.get('html').match(/\d+/);
            replyCount.set('html', replyCount.get('html').replace(m[0], e.commentsCount));
          } catch (e) {
          }
        }
      }), {
        'element': $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
      });
    })
  },
  comment: function (type, id, body, parent_comment_id) {
    if (body == '')
      return;
    var formElement = $('comments_form_' + type + '_' + id + '_' + parent_comment_id);
    if ($('seaocore_comment_image_' + type + '_' + id + '_' + parent_comment_id))
      $('seaocore_comment_image_' + type + '_' + id + '_' + parent_comment_id)
    var divEl = new Element('div', {
      'class': '',
      'html': '<img src="application/modules/Seaocore/externals/images/spinner.gif">',
      'id': 'seaocore_comment_image_' + type + '_' + id + '_' + parent_comment_id,
      'styles': {
        'display': 'inline-block'
      }
    });
    divEl.inject(formElement);
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'seaocore/nestedcomment/create',
      data: {
        format: 'json',
        type: type,
        id: id,
        body: body
      },
      onComplete: function (e) {
        if (parent_comment_id == 0)
          return;
        try {
          var replyCount = $$('.seaocore_replies_options span')[0];
          var m = replyCount.get('html').match(/\d+/);
          replyCount.set('html', replyCount.get('html').replace(m[0], e.commentsCount));
        } catch (e) {
        }
      }
    }), {
      'element': $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
    });
  },
  like: function (type, id, comment_id, order, parent_comment_id, option) {
    if ($('like_comments_' + comment_id) && (option == 'child')) {
      $('like_comments_' + comment_id).style.display = 'inline-block';
      $('like_comments_' + comment_id).innerHTML = '<img src="application/modules/Seaocore/externals/images/core/loading.gif" alt="Loading" />';
    }
    if ($('like_comments') && (option == 'parent')) {
      $('like_comments').style.display = 'inline-block';
      $('like_comments').innerHTML = '<img src="application/modules/Seaocore/externals/images/core/loading.gif" alt="Loading" />';
    }
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'seaocore/nestedcomment/like',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id,
        order: order,
        parent_comment_id: parent_comment_id
      },
      onComplete: function (e) {
        if ($('sitereview_most_likes_' + id)) {
          $('sitereview_most_likes_' + id).style.display = 'none';
        }
        if ($('sitereview_unlikes_' + id)) {
          $('sitereview_unlikes_' + id).style.display = 'block';
        }

        if ($(type + '_like_' + id))
          $(type + '_like_' + id).value = 1;
        if ($(type + '_most_likes_' + id))
          $(type + '_most_likes_' + id).style.display = 'none';
        if ($(type + '_unlikes_' + id))
          $(type + '_unlikes_' + id).style.display = 'inline-block';

      }
    }), {
      'element': $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
    });
  },
  unlike: function (type, id, comment_id, order, parent_comment_id, option) {
    if ($('unlike_comments_' + comment_id) && (option == 'child')) {
      $('unlike_comments_' + comment_id).style.display = 'inline-block';
      $('unlike_comments_' + comment_id).innerHTML = '<img src="application/modules/Seaocore/externals/images/core/loading.gif" alt="Loading" />';
    }
    if ($('unlike_comments') && (option == 'parent')) {
      $('unlike_comments').style.display = 'inline-block';
      $('unlike_comments').innerHTML = '<img src="application/modules/Seaocore/externals/images/core/loading.gif" alt="Loading" />';
    }
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'seaocore/nestedcomment/unlike',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id,
        order: order,
        parent_comment_id: parent_comment_id
      },
      onComplete: function (e) {
        if ($('sitereview_most_likes_' + id)) {
          $('sitereview_most_likes_' + id).style.display = 'block';
        }
        if ($('sitereview_unlikes_' + id)) {
          $('sitereview_unlikes_' + id).style.display = 'none';
        }

        if ($(type + '_like_' + id))
          $(type + '_like_' + id).value = 0;
        if ($(type + '_most_likes_' + id))
          $(type + '_most_likes_' + id).style.display = 'inline-block';
        if ($(type + '_unlikes_' + id))
          $(type + '_unlikes_' + id).style.display = 'none';

      }
    }), {
      'element': $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
    });
  },
  showLikes: function (type, id, order, parent_comment_id) {
    en4.core.request.send(new Request.HTML({
      url: en4.core.baseUrl + 'seaocore/nestedcomment/list',
      data: {
        format: 'html',
        type: type,
        id: id,
        viewAllLikes: true,
        order: order,
        parent_comment_id: parent_comment_id
      }
    }), {
      'element': $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
    });
  },
  deleteComment: function (type, id, comment_id, order, parent_comment_id) {
    if (!confirm(en4.core.language.translate('Are you sure you want to delete this?'))) {
      return;
    }
    if ($('comment-' + comment_id)) {
      $('comment-' + comment_id).destroy();
    }
    (new Request.JSON({
      url: en4.core.baseUrl + 'seaocore/nestedcomment/delete',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id,
        order: order,
        parent_comment_id: parent_comment_id
      },
      onComplete: function (e) {
        try {
          var replyCount = $$('.seaocore_replies_options span')[0];
          var m = replyCount.get('html').match(/\d+/);
          var newCount = (parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 1 ? parseInt(m[0]) - 1 : 0);
          replyCount.set('html', replyCount.get('html').replace(m[0], e.commentsCount));
        } catch (e) {
        }
      }
    })).send();
  }
};

var ScrollToTopSeao = function (topElementId, buttonId) {
  window.addEvent('scroll', function () {
    var element = $(buttonId);
    if (element) {
      if ($(topElementId)) {
        var elementPostionY = 0;
        if (typeof ($(topElementId).offsetParent) != 'undefined') {
          elementPostionY = $(topElementId).offsetTop;
        } else {
          elementPostionY = $(topElementId).y;
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
    var scroll = new Fx.Scroll(document.getElement('body').get('id'), {
      wait: false,
      duration: 750,
      offset: {
        'x': -200,
        'y': -100
      },
      transition: Fx.Transitions.Quad.easeInOut
    });

    $(buttonId).addEvent('click', function (event) {
      event = new Event(event).stop();
      scroll.toElement(topElementId);
    });
  });

};


ActivitySEAOUpdateHandler = new Class({
  Implements: [Events, Options],
  options: {
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
  state: true,
  activestate: 1,
  fresh: true,
  lastEventTime: false,
  title: document.title,
  //loopId : false,

  initialize: function (options) {
    this.setOptions(options);
  },
  start: function () {
    this.state = true;

    // Do idle checking
    this.idleWatcher = new IdleWatcher(this, {timeout: this.options.idleTimeout});
    this.idleWatcher.register();
    this.addEvents({
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
  stop: function () {
    this.state = false;
  },
  checkFeedUpdate: function (action_id, subject_guid) {
    if (en4.core.request.isRequestActive())
      return;

    function getAllElementsWithAttribute(attribute) {
      var matchingElements = [];
      var values = [];
      var allElements = document.getElementsByTagName('*');
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

    var req = new Request.HTML({
      url: en4.core.baseUrl + 'widget/index/name/seaocore.feed',
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
      'element': $('activity-feed'),
      'updateHtmlMode': 'prepend'
    }
    );



    req.addEvent('complete', function () {
      (function () {
        if (this.options.showImmediately && $('feed-update').getChildren().length > 0) {
          $('feed-update').setStyle('display', 'none');
          $('feed-update').empty();
          this.getFeedUpdate(this.options.next_id);
        }
      }).delay(50, this);
    }.bind(this));



    // Start LOCAL STORAGE STUFF   
    if (localStorage) {
      var pageTitle = document.title;
      //@TODO Refill Locally Stored Activity Feed

      // For each activity-item, get the item ID number Data attribute and add it to an array
      var feed = document.getElementById('activity-feed');
      // For every <li> in Feed, get the Feed Item Attribute and add it to an array
      var items = feed.getElementsByTagName("li");
      var itemObject = {};
      // Loop through each item in array to get the InnerHTML of each Activity Feed Item
      var c = 0;
      for (var i = 0; i < items.length; ++i) {
        if (items[i].getAttribute('data-activity-feed-item') != null) {
          var itemId = items[i].getAttribute('data-activity-feed-item');
          itemObject[c] = {id: itemId, content: document.getElementById('activity-item-' + itemId).innerHTML};
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
  getFeedUpdate: function (last_id) {
    if (en4.core.request.isRequestActive())
      return;
    var min_id = this.options.last_id + 1;
    this.options.last_id = last_id;
    document.title = this.title;
    var req = new Request.HTML({
      url: en4.core.baseUrl + 'widget/index/name/seaocore.feed',
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
      'element': $('activity-feed'),
      'updateHtmlMode': 'prepend'
    });
    return req;
  },
  loop: function () {
    this._log('activity update loop start');

    if (!this.state) {
      this.loop.delay(this.options.delay, this);
      return;
    }

    try {
      this.checkFeedUpdate().addEvent('complete', function () {
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
  _log: function (object) {
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
});

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


          if ($('region')) {
            var regionCurrentLocation = $('region').innerHTML;
            $('region').innerHTML = '<div class="seaocore_content_loader"></div>';
          }

          window.locationsParamsSEAO.latitude = position.coords.latitude;
          window.locationsParamsSEAO.longitude = position.coords.longitude;

          var myLocationDetails = {'latitude': position.coords.latitude, 'longitude': position.coords.longitude, 'location': '', 'locationmiles': params.locationmiles};
          self.setLocationCookies(myLocationDetails);

          self.setLocationField(position, params);
          params.locationSetInCookies = true;
          params.requestParams = $merge(params.requestParams, window.locationsParamsSEAO);
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
        params.requestParams = $merge(params.requestParams, window.locationsParamsSEAO);
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
        params.requestParams = $merge(params.requestParams, window.locationsParamsSEAO);
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
    var request = new Request.HTML({
      url: url,
      data: $merge(params.requestParams, {
        format: 'html',
        subject: en4.core.subject.guid,
        is_ajax_load: true
      }),
      evalScripts: true,
      onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
        if ($(params.responseContainer)) {
          $(params.responseContainer).innerHTML = '';
          Elements.from(responseHTML).inject($(params.responseContainer));
        }
        en4.core.runonce.trigger();
        Smoothbox.bind(params.responseContainer);
      }
    });
    request.send();

  },
  setLocationCookies: function (params, pageReload) {

    var myLocationDetails = {'latitude': params.latitude, 'longitude': params.longitude, 'location': params.location, 'locationmiles': params.locationmiles};

    if (typeof (params.changeLocationWidget) != 'undefined' && params.changeLocationWidget) {
      Cookie.write('seaocore_myLocationDetails', JSON.stringify(myLocationDetails), {duration: 30, path: en4.core.baseUrl});
    } else {
      en4.core.request.send(new Request.JSON({
        url: en4.core.baseUrl + 'seaocore/location/get-specific-location-setting',
        data: {
          format: 'json',
          location: params.location,
          updateUserLocation: params.updateUserLocation
        },
        onSuccess: function (responseJSON) {
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
      var mapDetect = new google.maps.Map(new Element('div'), {
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
      if (typeof (params.fieldName) != 'undefined' && params.fieldName != null && document.getElementById(params.fieldName)) {
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
  if (cont.getElement('.facebook_container')) {
    if (!document.getElementById('fb-root'))
      new Element('div', {'id': 'fb-root'}).inject($(en4.seaocore.contentElement), 'top');
    (function (d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id))
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

  if (cont.getElement('.linkedin_container')) {
    new Element('script', {'type': 'IN/Share', 'data-counter': 'top'}).inject(cont.getElement('.linkedin_container'));
    new Element('script', {'src': ("https:" == document.location.protocol ? "https://" : "http://") + 'platform.linkedin.com/in.js'}).inject($(en4.seaocore.contentElement), 'before');

  }
  if (cont.getElement('.twitter_container')) {
    new Element('script', {'src': ("https:" == document.location.protocol ? "https://" : "http://") + 'platform.twitter.com/widgets.js'}).inject($(en4.seaocore.contentElement), 'before');

  }
  if (cont.getElement('.google_container')) {
    new Element('script', {'src': 'https://apis.google.com/js/plusone.js', 'async': true}).inject($(en4.seaocore.contentElement), 'before');

  }

  if (!params.leftValue) {
    params.leftValue = 15;
  }
  wrapper.inject($(en4.seaocore.contentElement), 'top');
  var top = wrapper.getStyle('top');
  if (!params.type) {
    params.type = 'left';
  }

  if (params.type === 'left') {
    params.leftValue = params.leftValue + parseInt(wrapper.clientWidth);
    cont.setStyle('left', '-' + params.leftValue + 'px');
    $(en4.seaocore.contentElement).addClass('seao_share_buttons_left_content');
  } else {
    params.leftValue = params.leftValue + parseInt($(en4.seaocore.contentElement).clientWidth);
    cont.setStyle('left', params.leftValue + 'px');
    $(en4.seaocore.contentElement).addClass('seao_share_buttons_right_content');
  }
  (function () {
    wrapper.setStyles({width: '1px', visibility: 'visible'});
  }).delay(1500);
  window.addEvent('scroll', function (e) {
    var descripY = parseInt($(en4.seaocore.contentElement).getOffsets().y) - 20, scrollY = $(window).getScroll().y, footerY = parseInt($(en4.seaocore.footerElement).getOffsets().y), height = parseInt(wrapper.getStyle('height')), fixedShare = wrapper.getStyle('position') === 'fixed';

    if (scrollY < descripY && fixedShare) {
      wrapper.setStyles({
        position: 'absolute',
        top: top
      });
    } else if (scrollY > descripY && (scrollY + 20 + height) > footerY) {
      wrapper.setStyles({
        position: 'absolute',
        top: (footerY - height - 90)
      });
    } else if (scrollY > descripY && !fixedShare) {
      wrapper.setStyles({
        position: 'fixed',
        top: 20
      });
    }
  });
  $$('.generic_layout_container.layout_seaocore_social_share_buttons')[0].style.display = 'none';
};

var SmoothboxSEAO = {
  overlay: null,
  wrapper: null,
  content: null,
  contentHTML: null,
  scrollPosition: {
    left: 0,
    top: 0
  },
  addScriptFiles: [],
  addStylesheets: [],
  active: false,
  closable: true,
  build: function () {
    SmoothboxSEAO.overlay = new Element('div', {
      'class': 'seao_smoothbox_lightbox_overlay'
    }).inject($(en4.seaocore.contentWrapperElement));
    SmoothboxSEAO.wrapper = new Element('div', {
      'class': 'seao_smoothbox_lightbox_content_wrapper'
    }).inject($(en4.seaocore.contentWrapperElement));
    SmoothboxSEAO.attach();
    SmoothboxSEAO.hide();
  },
  attach: function () {
    if (!SmoothboxSEAO.wrapper || !SmoothboxSEAO.closable)
      return;
    SmoothboxSEAO.wrapper.removeEvents('click').addEvent('click', function (event) {
      var el = $(event.target);
      if (el.hasClass('seao_smoothbox_lightbox_content') || el.getParent('.seao_smoothbox_lightbox_content'))
        return;
      SmoothboxSEAO.close();
    });
  },
  bind: function (selector) {
    // All children of element
    var elements;
    if ($type(selector) == 'element') {
      elements = selector.getElements('a.seao_smoothbox');
    } else if ($type(selector) == 'string') {
      elements = $$(selector);
    } else {
      elements = $$("a.seao_smoothbox");
    }

    elements.each(function (el)
    {
      if (el.get('tag') != 'a' || !SmoothboxSEAO.hasLink(el) || el.retrieve('smoothboxed', false))
      {
        return;
      }


      el.addEvent('click', function (event) {
        event.stop();
        if (el.get('data-SmoothboxSEAOType') == 'iframe') {
          SmoothboxSEAO.open({
            class: el.get('data-SmoothboxSEAOClass'),
            iframe: {
              src: el.href
            }
          });
        } else {
          SmoothboxSEAO.open({
            class: el.get('data-SmoothboxSEAOClass'),
            request: {
              url: el.href
            }
          });
        }
      });
      el.store('smoothboxed', true);
    });


  },
  hasLink: function (element) {
    return (
      !element.onclick &&
      element.href &&
      !element.href.match(/^(javascript|[#])/));
  },
  open: function (params) {
    if (!params)
      return;
    if (!SmoothboxSEAO.wrapper) {
      if ((typeof params.closable) === 'boolean' && params.closable === false) {
        SmoothboxSEAO.closable = false;
      }
      SmoothboxSEAO.build();
    } else {
      SmoothboxSEAO.wrapper.empty();
    }
    if ((typeof params) === 'string') {
      if (params.length < 4000 && (params.substring(0, 1) == '/' ||
        params.substring(0, 1) == '.' ||
        params.substring(0, 4) == 'http' ||
        !params.match(/[ <>"'{}|^~\[\]`]/)
        )
        ) {

        params = {request: {
            url: params
          }};
      } else {
        params = {element: params};
      }

    } else if ($type(params) === 'element') {
      params = {element: params};
    }

    SmoothboxSEAO.content = new Element('div', {
      'class': 'seao_smoothbox_lightbox_content'
    }).inject(SmoothboxSEAO.wrapper);
    // SmoothboxSEAO.content.setStyle('width', 'auto');
    SmoothboxSEAO.contentHTML = new Element('div', {
      'class': 'seao_smoothbox_lightbox_content_html'
    }).inject(SmoothboxSEAO.content);
    if (params.class)
      SmoothboxSEAO.content.addClass(params.class);

    if (params.element && (typeof params.element) === 'string')
      SmoothboxSEAO.contentHTML.innerHTML = params.element;
    else if (params.element)
      params.element.inject(SmoothboxSEAO.contentHTML);
    else if (params.request && params.request.url)
      SmoothboxSEAO.sendReq(params.request);
    else if (params.iframe && params.iframe.src)
      SmoothboxSEAO.iframeReq(params.iframe);
    else if (params.embed && params.embed.code)
      SmoothboxSEAO.embed(params.embed);
    SmoothboxSEAO.show();
    $$(".seao_smoothbox_lightbox_close").addEvent('click', function (event) {
      event.stopPropagation();
      SmoothboxSEAO.close();
    });

    SmoothboxSEAO.doAutoResize();
    //  this.fireEvent('open', this);
  },
  embed: function (options) {
    var elementWapper = new Element('div', {
      'class': 'seao_smoothbox_iframe_wapper ' + (options.wapperClass ? options.wapperClass : ''),
      html: '<a class="seao_smoothbox_lightbox_close _close" href="javascript:void();"><i class="fa fa-close"></i></a>'
    });
    var elementContent = new Element('div', {
      'class': 'seao_smoothbox_iframe_content ' + (options.contentClass ? options.contentClass : ''),
      html: options.code
    });
    elementContent.inject(elementWapper);
    elementWapper.inject(SmoothboxSEAO.contentHTML);
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
    var elementWapper = new Element('div', {
      'class': 'seao_smoothbox_iframe_wapper ' + (options.wapperClass ? options.wapperClass : ''),
      html: '<a class="seao_smoothbox_lightbox_close _close" href="javascript:void();"><i class="fa fa-close"></i></a>'
    });
    var elementContent = new Element('div', {
      'class': 'seao_smoothbox_iframe_content ' + (options.contentClass ? options.contentClass : ''),
    });
    var target = SmoothboxSEAO._getEmbedIframeTarget(options.src);
    SmoothboxSEAO.frame = new IFrame({
      src: target.src,
      name: 'SmoothboxSEAO_iframe',
      frameborder: 0,
      webkitallowfullscreen: true,
      mozallowfullscreen: true,
      allowfullscree: true
    });
    SmoothboxSEAO.frame.inject(elementContent);
    elementContent.inject(elementWapper);
    elementWapper.inject(SmoothboxSEAO.contentHTML);
    SmoothboxSEAO.content.addClass('seao_smoothbox_iframe seao_smoothbox_iframe_' + target.type);
    SmoothboxSEAO.wrapper.addClass('seao_smoothbox_wapper-iframe');
  },
  doAutoResize: function () {
    //return;
    var size = Function.attempt(function () {
      return SmoothboxSEAO.contentHTML.getScrollSize();
    }, function () {
      return SmoothboxSEAO.contentHTML.getSize();
    }, function () {
      return {
        x: SmoothboxSEAO.contentHTML.scrollWidth,
        y: SmoothboxSEAO.contentHTML.scrollHeight
      }
    });

    // if (size.x) {
    var winSize = window.getSize();
    if (size.x > (winSize.x - 30)) {
      size.x = winSize.x - 30;
    }
    var marginTop = 10;
    SmoothboxSEAO.content.setStyle('width', size.x);
    if (size.y < winSize.y) {
      marginTop = (winSize.y - size.y) / 2;
    }

    if (marginTop < 10)
      marginTop = 10;
    size.x = size.x + 10;
    SmoothboxSEAO.content.setStyles({
      'width': size.x,
      'marginTop': marginTop,
      'marginBottom': 20
    });
    //  }
  },
  show: function () {
    SmoothboxSEAO.overlay.show();
    SmoothboxSEAO.wrapper.show();
    if ($('arrowchat_base'))
      $('arrowchat_base').style.display = 'none';
    if ($('wibiyaToolbar'))
      $('wibiyaToolbar').style.display = 'none';
    SmoothboxSEAO.scrollPosition.top = window.getScrollTop();
    SmoothboxSEAO.scrollPosition.left = window.getScrollLeft();
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
    if ($('arrowchat_base'))
      $('arrowchat_base').style.display = 'block';
    if ($('wibiyaToolbar'))
      $('wibiyaToolbar').style.display = 'block';
    // this.fireEvent('close', this);
  },
  setHtmlScroll: function (cssCode) {
    $$('html').setStyle('overflow', cssCode);
  },
  sendReq: function (params) {
    var container = SmoothboxSEAO.contentHTML;
    container.empty();
    new Element('div', {
      'class': 'seao_smoothbox_lightbox_loading'
    }).inject(container);

    if (!params.requestParams)
      params.requestParams = {};
    SmoothboxSEAO.addScriptFiles = [];
    SmoothboxSEAO.addStylesheets = [];

    var request = new Request.HTML({
      url: params.url,
      method: 'get',
      data: $merge(params.requestParams, {
        format: 'html',
        seaoSmoothbox: true
      }),
      evalScripts: true,
      onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
        var onLoadContent = function () {
          container.empty();
          Elements.from(responseHTML).inject(container);
          en4.core.runonce.trigger();
          SmoothboxSEAO.doAutoResize();
          Smoothbox.bind(container);
          SmoothboxSEAO.bind(container);
          $$(".seao_smoothbox_lightbox_close").addEvent('click', function (event) {
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
          Asset.javascript(SmoothboxSEAO.addScriptFiles[i], {
            onLoad: function () {
              succes++;
              if (succes === totalFiles)
                onLoadContent();
            }});
        }
        SmoothboxSEAO.addScriptFiles = [];
        for (i = 0; i < StyleSheetCount; i++) {
          Asset.css(SmoothboxSEAO.addStylesheets[i], {
            onLoad: function () {
              succes++;
              if (succes === totalFiles)
                onLoadContent();
            }});
        }
        SmoothboxSEAO.addStylesheets = [];

      }
    });
    request.send();
  }
};

en4.seaocore.socialService = {
  clickHandler: function (el) {
    var request = new Request.JSON({
      url: en4.core.baseUrl + 'siteshare/index/social-service-click',
      method: 'post',
      data: {
        format: 'json',
        shareUrl: $(el).get('data-url'),
        serviceType: $(el).get('data-service')
      },
      onSuccess: function () {

      }
    });
    request.send();
  }
};
window.addEvent('domready', function ()
{
  SmoothboxSEAO.bind();
});

window.addEvent('load', function ()
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
      params = $merge(requestParams, params);
    var request = new Request.HTML({
      url: url,
      method: 'get',
      data: params,
      onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
        container.empty();
        Elements.from(responseHTML).inject(container);
        en4.core.runonce.trigger();
        Smoothbox.bind(container);
      }
    });
    request.send();
  }
};
var communityad_likeinfo = function (ad_id, resource_type, resource_id, owner_id, widgetType, core_like) {
  // SENDING REQUEST TO AJAX
  var request = createLike(ad_id, resource_type, resource_id, owner_id, widgetType, core_like);
  // RESPONCE FROM AJAX
  request.addEvent('complete', function (responseJSON) {
    if (responseJSON.like_id)
    {
      $(widgetType + '_likeid_info_' + ad_id).value = responseJSON.like_id;
      $(resource_type + '_' + widgetType + '_most_likes_' + ad_id).style.display = 'none';
      $(resource_type + '_' + widgetType + '_unlikes_' + ad_id).style.display = 'block';
    } else
    {
      $(widgetType + '_likeid_info_' + ad_id).value = 0;
      $(resource_type + '_' + widgetType + '_most_likes_' + ad_id).style.display = 'block';
      $(resource_type + '_' + widgetType + '_unlikes_' + ad_id).style.display = 'none';
    }
  });
}
/* $Id: core.js 2011-02-16 9:40:21Z SocialEngineAddOns Copyright 2009-2011 BigStep Technologies Pvt. Ltd. $ */

// Use: Ads Display.
// Function Call: When click on cross of any advertisment.
function adCancel(div_id, widgetType) {
  $(widgetType + '_ad_cancel_' + div_id).style.display = 'block';
  $(widgetType + '_ad_' + div_id).style.display = 'none';
}

// Use: Ads Display.
// Function Call: After click on cross of any ads then show option of 'undo' if click on the 'undo'.
function adUndo(div_id, widgetType) {
  $(widgetType + '_ad_cancel_' + div_id).style.display = 'none';
  // $(widgetType + '_ad_' + div_id).style.display = 'block';
  if ($(widgetType + '_other_' + div_id).checked) {
    $(widgetType + '_other_' + div_id).checked = false;
    $(widgetType + '_other_text_' + div_id).style.display = 'none';
    $(widgetType + '_other_text_' + div_id).value = 'Type your reason here...';
    $(widgetType + '_other_button_' + div_id).style.display = 'none';
  }
}

// Use: Ads Display.
// Function Call: After click on cross of any ads then show radio button if click on 'other' type radio button.
function otherAdCannel(adRadioValue, div_id, widgetType) {
  // Condition: When click on 'other radio button'.
  if (adRadioValue == 4) {
    $(widgetType + '_other_text_' + div_id).style.display = 'block';
    $(widgetType + '_other_button_' + div_id).style.display = 'block';
  }
}

// Use: Ads Display
// Function Call: When save entry in data base.
function adSave(adCancelReasion, adsId, divId, widgetType) {
  var adDescription = 0;
  // Condition: Find out 'Description' if select other options from radio button.

  if (adCancelReasion == 'Other') {
    if ($(widgetType + '_other_text_' + divId).value != 'Type your reason here...') {
      adDescription = $(widgetType + '_other_text_' + divId).value;
    }
  }
  $(widgetType + '_ad_cancel_' + divId).innerHTML = '<center><img src="application/modules/Seaocore/externals/images/core/loading.gif" alt=""></center>';
  en4.core.request.send(new Request.HTML({
    url: en4.core.baseUrl + 'communityad/display/adsave',
    data: {
      format: 'html',
      adCancelReasion: adCancelReasion,
      adDescription: adDescription,
      adsId: adsId
    }
  }), {
    'element': $(widgetType + '_ad_cancel_' + divId)
  })
}

// Function: For 'Advertisment' liked or unliked.
function createLike(ad_id, resource_type, resource_id, owner_id, widgetType, core_like)
{
  var like_id = $(widgetType + '_likeid_info_' + ad_id).value;
  var request = new Request.JSON({
    url: en4.core.baseUrl + 'communityad/display/globallikes',
    data: {
      format: 'json',
      'ad_id': ad_id,
      'resource_type': resource_type,
      'resource_id': resource_id,
      'owner_id': owner_id,
      'like_id': like_id,
      'core_like': core_like
    }
  });
  request.send();
  return request;
}
/*  Community Ad Plugin JS End here*/

function locationAutoSuggest(countrycities, location_field, city_field) {

  if (city_field && $(city_field)) {

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

    var autocomplete = new google.maps.places.Autocomplete($(city_field), options);
  }

  if (location_field && $(location_field)) {

    if (countrycities) {
      var options = {
        //types: [''],//We are not passing any values here for showing all results of some specific country.
        componentRestrictions: {country: countrycities}
      };
    } else {
      var options = {

      };
    }

    var autocomplete = new google.maps.places.Autocomplete($(location_field), options);
  }

}

//WHEN CONTENT ON THE PAGE LOAD FROM THE AJAX IN THAT CASE SMOOTHBOX CLASS DOES NOT WORK THEN WE USE BELOW FUNCTION
function openSmoothbox(thisobj) {
  var Obj_Url = thisobj.href;
  Smoothbox.open(Obj_Url);
}

function showShareLinks(val) {
  $(document.body).addEvent('click', showHideToggleShareLinks);
  $$('.siteevent_share_links_toggle').removeEvents('click').addEvent('click', function (event) {
    event.stop();
    //showHideToggleShareLinks();
    $(this).getParent('.siteevent_grid_footer').getElement('.siteevent_share_links').toggle();

    if (typeof val == 'undefined') {
      $(this).toggle();
    } else {
      $(this).show();
    }
  });
}

function showHideToggleShareLinks() {
  $$('.siteevent_share_links_toggle').show();
  $$('.siteevent_share_links_toggle').getParent('.siteevent_grid_footer').getElement('.siteevent_share_links').hide();
}

function showReviewShareLinks(val) {
  $(document.body).addEvent('click', showHideToggleReviewShareLinks);
  $$('.sitereview_share_links_toggle').removeEvents('click').addEvent('click', function (event) {
    event.stop();
    //showHideToggleShareLinks();
    $(this).getParent('.sitereview_grid_footer').getElement('.sitereview_share_links').toggle();

    if (typeof val == 'undefined') {
      $(this).toggle();
    } else {
      $(this).show();
    }
  });
}

function showHideToggleReviewShareLinks() {
  $$('.sitereview_share_links_toggle').show();
  $$('.sitereview_share_links_toggle').getParent('.sitereview_grid_footer').getElement('.sitereview_share_links').hide();
}

function showPageShareLinks(val) {
  $(document.body).addEvent('click', showHideTogglePageShareLinks);
  $$('.sitepage_share_links_toggle').removeEvents('click').addEvent('click', function (event) {
    event.stop();
    //showHideToggleShareLinks();
    $(this).getParent('.sitepage_grid_footer').getElement('.sitepage_share_links').toggle();

    if (typeof val == 'undefined') {
      $(this).toggle();
    } else {
      $(this).show();
    }
  });
}

function showHideTogglePageShareLinks() {
  $$('.sitepage_share_links_toggle').show();
  $$('.sitepage_share_links_toggle').getParent('.sitepage_grid_footer').getElement('.sitepage_share_links').hide();
}

function showForumShareLinks(val) {
  $(document.body).addEvent('click', showForumHideToggleShareLinks);
  $$('.siteforum_share_links_toggle').removeEvents('click').addEvent('click', function (event) {
    event.stop();
    //showHideToggleShareLinks();
    $(this).getParent('.siteforum_grid_footer').getElement('.siteforum_share_links').toggle();

    if (typeof val == 'undefined') {
      $(this).toggle();
    } else {
      $(this).show();
    }
  });
}

function showForumHideToggleShareLinks() {
  $$('.siteforum_share_links_toggle').show();
  $$('.siteforum_share_links_toggle').getParent('.siteforum_grid_footer').getElement('.siteforum_share_links').hide();
}


function showGroupShareLinks(val) {
  $(document.body).addEvent('click', showHideToggleGroupShareLinks);
  $$('.sitegroup_share_links_toggle').removeEvents('click').addEvent('click', function (event) {
    event.stop();
    //showHideToggleShareLinks();
    $(this).getParent('.sitegroup_grid_footer').getElement('.sitegroup_share_links').toggle();

    if (typeof val == 'undefined') {
      $(this).toggle();
    } else {
      $(this).show();
    }
  });
}

function showHideToggleGroupShareLinks() {
  $$('.sitegroup_share_links_toggle').show();
  $$('.sitegroup_share_links_toggle').getParent('.sitegroup_grid_footer').getElement('.sitegroup_share_links').hide();
}
en4.seaocore.uploaderInstance = new Hash();
var callbacks = {};
en4.seaocore.initSeaoFancyUploaderQueue = {
  request: [],

  add: function (params, callbacks) {
    this.request.push({params: params, callbacks: callbacks});
  },

  trigger: function () {
    if (this.executing)
      return;
    this.executing = true;
    var data;
    while ((data = this.request.shift())) {
      en4.seaocore.initSeaoFancyUploader(data.params, data.callbacks);
    }
    this.request = [];
    this.executing = false;
  }
};
en4.seaocore.initSeaoFancyUploader = function (params, callbacks) {
  if (typeof SeaoFancyUploader == 'undefined') {
    en4.seaocore.initSeaoFancyUploaderQueue.add(params, callbacks);
    if (en4.seaocore.initSeaoFancyUploaderQueue.request.lenght > 1) {
      return false;
    }
    options = {prependPath: en4.core.staticBaseUrl + 'externals/seao-fancy-uploader/'};
    var self = this;
    loadDependingAssets = function () {
      self.loadAssets([{type: 'javascript', src: 'Uploader.HTML5.js'}], function () {
        en4.seaocore.initSeaoFancyUploaderQueue.trigger();
      }, options);
    }
    this.loadAssets([
      {type: 'javascript', src: 'Request.Blob.js'},
      {type: 'javascript', src: 'Uploader.js'},
      {type: 'css', src: 'uploader.css'}
    ], loadDependingAssets, options);
    return false;
  }

  var wrapper = $(params.name + '-wrapper');
  var defaultParams = {
    ui_button: wrapper && wrapper.getElement('.upload-link'),
    ui_list: wrapper && wrapper.getElement('.uploaded-files-list'),
    ui_drop_area: wrapper && wrapper.getElement('.drop-area'),
    clear_list: wrapper && wrapper.getElement('.clear-list'),
    remote_input: wrapper && wrapper.getElement('input.remote-url'),
    remote_button: wrapper && wrapper.getElement('button.remote-fetch'),
    name: 'file',
    submitElement: true,
    responseParamId: 'photo_id',
    autosubmit: false,
    view: 'grid',
    debugMode: false,
    dropAreaClick: true,
    wrapperClass: '',
    populateFiles: {},
    onActivate: function () {
      var self = this;
      this.debug = this.options.debugMode || en4.core.environment == 'development';
      if (!this.options.autostart) {
        this.startUploadLink = wrapper.getElement('.start-upload-link');
        this.startUploadLink.addEvent('click', function (event) {
          event.stop()
          self.upload();
          event.target.hide();
        })
      }
      this.clearListLink = $(this.options.clear_list);
      if (this.clearListLink) {
        this.clearListLink.hide();
        this.clearListLink.addEvent('click', function () {
          wrapper.getElements('.seao-fancy-uploader-item .file-remove, .file-error').each(function (link) {
            link.click();
          })
          if (self.submitWrapper)
            self.submitWrapper.hide();
          self.progressBar.setStyle('width', '0px').set('html', '');
        })
      }

      // HIDE DROP AREA AND REMOTE FILE WRAPPER ON CLICK OF UI BUTTON
      this.uiButton.addEvent('click', function () {
        if (self.uiButton != self.uiDropArea)
          self.uiDropArea.addClass('dnone');
        self.remoteWrapper && self.remoteWrapper.addClass('dnone');
      });

      // BIND CLICK EVENT TO DROP AREA
      if (this.uiDropArea) {
        if (wrapper.getElement('.drop-link')) {
          wrapper.getElement('.drop-link').addEvent('click', function () {
            self.uiDropArea.toggleClass('dnone');
            if (self.remoteWrapper && !self.uiDropArea.hasClass('dnone')) {
              self.remoteWrapper.addClass('dnone');
            }
          });
        }
        if (this.options.dropAreaClick) {
          this.uiDropArea.addEvent('click', function (e) {
            e.stop();
            this.lastInput.click();
          }.bind(this));
        }
      }

      this.language = en4.core.language;
      this.limitFiles = this.options.multiple ? this.options.limitFiles : 1;
      this.paramId = this.options.responseParamId;
      this.submitWrapper = null;
      if ($(this.options.submitElement + '-wrapper')) {
        this.submitWrapper = $(this.options.submitElement + '-wrapper');
      }

      // ADD REQUIRED ERROR MESSAGES
      this.setErrorMessages();
      this.setErrorMessage('maxfilesize', 'File Exceeded Maximum File Size or Remaining Storage Quota of (' + this._convertSize(this.options.max_file_size) + ') - %s ( %s )');

      this.progressBar = wrapper.getElement('.progress-bar');

      // ADD CLASSES FOR VIEW - GRID / LIST / CAROUSEL
      this.view = this.options.view;
      this.uiList.addClass('uploader-' + this.view + '-view');
      wrapper.addClass('seao-fancy-uploader-wrapper');
      wrapper.addClass('uploader-' + this.view + '-view-wrapper');
      wrapper.addClass(this.options.wrapperClass);

      // INJECT FILE IDS ELEMENT IN FORM IF IT IS NOT IN FORM
      this.fileIdsElement = $(this.options.fileIdsElement) || wrapper.getElement('input.file-ids');
      this.form = wrapper.getParent('form');
      if (!this.form && (this.form = $(this.options.formId))) {
        this.fileIdsElement.inject(this.form);
      }

      this.remoteLink = wrapper && wrapper.getElement('.remote-link');
      this.remoteWrapper = wrapper && wrapper.getElement('.remote-wrapper');
      if (this.remoteLink && this.remoteWrapper) {
        this.remoteLink.addEvent('click', function () {
          self.remoteWrapper.toggleClass('dnone');
          if (self.uiDropArea && !self.remoteWrapper.hasClass('dnone')) {
            self.uiDropArea.addClass('dnone');
          }
        });
        this.remoteWrapper.getElement('.loading').hide();
      }

      // ADD SCROLLBAR TO UPLOADED FILE LIST
      this.scrollContainer = this.uiList.getParent('div.scrollbars');
      this.maxHeight = {grid: 270, list: 400};
      if (this.scrollContainer) {
        this.scrollContainer.setStyle('height', '0px');
        this.scrollContainer.scrollbars({
          scrollBarSize: 10,
          fade: true,
          barOverContent: true
        });
      }

      this.totalWidth = Math.min(this.getTotalWidth(), 400);
      this._log('onActivate');
      this.fireEvent('onActivateCustom');
      this._populateItems();
    },
    onAddFiles: function (num) {
      if (!num)
        return;
      if (!this.options.autostart) {
        this.startUploadLink.show();
      }
      if (this.clearListLink)
        this.clearListLink.show();
      this.uiList.getParent().getElements('.file-error').destroy();
    },
    onItemAdded: function (el, file, imagedata) {
      var self = this;
      el.addClass('uploader-file')
        .adopt(new Element('div', {'class': 'file-info-wrapper'})
          .adopt(new Element('div', {'class': 'file-info'})
            .adopt(new Element('span', {'class': 'file-name', 'title': file.name, 'html': file.name.truncate(60)}))
            .adopt(new Element('span', {'class': 'file-type', 'html': file.type}))
            .adopt(new Element('span', {'class': 'file-size', 'html': file.is_url ? '' : this._convertSize(file.size)}))
            )
          .adopt(new Element('div', {'class': 'file-progress-list'}).set('tween', {duration: 200}))
          )
        .adopt(new Element('div', {'class': 'file-preview'}))
        .adopt(new Element('div', {'class': 'file-progress'}).setStyle('left', 0))
        .adopt(new Element('div', {'class': 'file-remove', 'title': this.language.translate('Remove')}).addEvent('click', function (e) {
          e.stop();
          self.cancel(file.id, el)
        }));

      this.updateUiList();
      this._log('onItemAdded - ' + file.name);
      if (!file.type)
        return;
      if (file.type.match('image') && imagedata) {
        el.addClass('image');
        el.getElement('.file-preview').adopt(new Element('img', {'src': imagedata}));
      } else if (file.type.match('audio') || file.type.match('flac')) {
        el.addClass('audio');
      } else if (file.type.match('video')) {
        el.addClass('video');
      }
    },
    // UPDATE SCROLL BARS
    onUpdateUiList: function () {
      if (!this.scrollContainer)
        return;
      var self = this;
      (function () {
        height = Math.min(self.maxHeight[self.view], self.uiList.getSize().y && self.uiList.getSize().y + 10);
        self.scrollContainer.setStyle('height', height + 'px');
        self.scrollContainer.retrieve('scrollbars').updateScrollBars();
      }).delay(50);
    },
    onItemUploadStart: function (el, file) {
      el.addClass('file-uploading');
      this._log('onItemUploadStart - ' + file.name);
    },
    onItemProgress: function (el, perc) {
      el.getElement('.file-progress-list').tween('width', Math.round(this.getItemWidth() * perc / 100));
      el.getElement('.file-progress').setStyle('left', Math.floor(perc) + '%');
    },
    onItemComplete: function (el, file, response) {
      el.removeClass('file-uploading').addClass('file-success');
      el.getElement('.file-progress-list').tween('width', this.getItemWidth());
      el.getElement('.file-progress').setStyle('left', '100%');
      el.set('data-file_id', response[this.paramId]);
      src = response['src'] || response['url'] || response['imgSrc'];
      src && el.getElement('.file-preview img').set('src', src);

      value = this.fileIdsElement.get('value') + response[this.paramId] + ' ';
      this.fileIdsElement.set('value', value);

      if (this.options.autosubmit && this._uploadedMaxFiles() && this.form) {
        typeof this.form.submit == 'function' ? this.form.submit() : this.form.submit.click();
      }
      this._log('onItemComplete - ' + file.name);
    },
    onItemCancel: function (el) {
      file_id = el.get('data-file_id');
      el.destroy();

      if (this.uiList.getElements('.seao-fancy-uploader-item').length == 0) {
        if (this.clearListLink)
          this.clearListLink.hide();
        if (!this.options.autostart) {
          this.startUploadLink.hide();
        }
      }

      if (!this._uploadedMaxFiles()) {
        this.uiButton.show();
        this.lastInput.set('disabled', false);
      }

      this.updateUiList();
      if (!file_id)
        return;

      if (this.fileIdsElement) {
        value = this.fileIdsElement.get('value').replace(file_id, '');
        this.fileIdsElement.set('value', value);
      }

      if (this.options.deleteUrl) {
        data = {};
        data['isajax'] = 1;
        data[this.paramId] = file_id;
        request = new Request.JSON({
          'format': 'json',
          'url': this.options.deleteUrl,
          'data': $merge(this.options.vars, data),
          'onSuccess': function (responseJSON) {
            return false;
          }
        });
        request.send();
      }
      this._log('onItemCancel');
    },
    onItemError: function (el, file, response) {
      el.removeClass('file-uploading').addClass('file-failed');
      el.adopt(new Element('div', {class: 'file-error', html: response.error}));
      this._log('onItemError - ' + file.name);
    },
    onUploadStart: function () {
      if (!this.fileList.length)
        return;
      if (this.submitWrapper)
        this.submitWrapper.hide();
      if (this.progressBar && !this.uploadingStatus) {
        this.uploadingStatus = true;
        this.progressBar.tween('width', 40);
        this.progressBar.set('html', '1%');
      }
      this._log('onUploadStart');
    },
    onUploadProgress: function (perc) {
      if (!perc)
        return;
      this.progressBar.tween('width', this.getTotalWidth() * perc / 100);
      this.progressBar.set('html', Math.floor(perc) + '%');
      this._log('onUploadProgress - ' + Math.floor(perc) + '%');
    },
    onUploadComplete: function (num) {
      if (num > 0) {
        this.progressBar.tween('width', this.getTotalWidth());
      } else {
        this.progressBar.setStyle('width', 0);
      }
      if (this.lastInput) {
        this.lastInput.destroy();
      }
      if (this.submitWrapper)
        this.submitWrapper.show();
      this.uploadingStatus = this.isUploading;
      this._log('onUploadComplete: Uploaded Files - ' + num);
    },
    onSelectError: function (type, filename, filesize) {
      errorMessage = this.getErrorMessage(type, filename, filesize);
      var uploadedFile = new Element('div', {
        'class': 'file-error',
        'html': '<span class="validation-error" title="' + this.language.translate('Click to remove this entry.') + '">' + 'Error - ' + errorMessage + '</span>',
        events: {
          click: function () {
            this.destroy();
          }
        }
      }).inject(this.uiList, 'before');
      this.scrollContainer.setStyle('height', 'auto');
      this.scrollContainer.retrieve('scrollbars').updateScrollBars();

      if (type == 'limitFilesExceeded') {
        this.uiButton.hide();
        this.lastInput.set('disabled', true);
      }
    },
    onRemoteFetchStart: function (url) {
      this.remoteButton.hide();
      if (!this.remoteWrapper)
        return;
      this.remoteWrapper.getElement('.error-message').hide();
      this.remoteWrapper.getElement('.success-message').hide();
      this.remoteWrapper.getElement('.loading').show();
      this._log('onRemoteFetchStart - ' + url);
    },
    onRemoteFetchComplete: function (url) {
      if (!this.remoteWrapper)
        return;
      this.remoteButton.show();
      this.remoteWrapper.getElement('.success-message').show();
      this.remoteWrapper.getElement('.loading').hide();
      this._log('onRemoteFetchComplete - ' + url);
    },
    onRemoteFetchError: function (type, url) {
      this.remoteButton.show();
      if (!this.remoteWrapper)
        return;
      this.remoteWrapper.getElement('.loading').hide();
      this.remoteWrapper.getElement('.error-message').show();
      this.remoteWrapper.getElement('.error-message').set('html', this.language.translate(this.getErrorMessage(type)));
      this._log('onRemoteFetchError - ' + type);
    },
    onItemPopulated: function (el, file) {
      file_id = file.id || file[this.responseParamId];
      el.set('data-file_id', file_id);
      if (typeof file !== 'object')
        return;
      el.addClass('uploader-file file-success')
        .adopt(new Element('div', {'class': 'file-info-wrapper'})
          .adopt(new Element('div', {'class': 'file-info'})
            .adopt(new Element('span', {'class': 'file-name', 'title': file.name, 'html': file.name && file.name.truncate(60)}))
            ))
        .adopt(new Element('div', {'class': 'file-preview'}))
        .adopt(new Element('div', {'class': 'file-remove', 'title': this.language.translate('Remove')}).addEvent('click', function (e) {
          e.stop();
          self.cancel(file_id, el)
        }));

      if (this.options.fileType === 'default' && file.src) {
        el.addClass('image');
        el.getElement('.file-preview').adopt(new Element('img', {'src': file.src}));
      } else {
        el.addClass(this.options.fileType);
      }
      this._log('onItemPopulated - File id - ' + file_id);
    },
    onPopulateFiles: function (num) {
      if (!num)
        return;
      if (this.clearListLink)
        this.clearListLink.show();
    }
  };
  params = $merge(defaultParams, params, callbacks);

  uploader = new SeaoFancyUploader(params);
  this.uploaderInstance.set(params.name, uploader);
  return uploader;
};


en4.seaocore.miniMenu = {
  init: function () {
    if (en4.user.viewer.id) {
      setInterval(en4.seaocore.miniMenu.checkUpdates, 30000);
    }
    $(document.body).addEvent('click', en4.seaocore.miniMenu.pullDownCloseHandler);
    $$('.seaocore_mimi_menu_pulldown_item').addEvent('click', en4.seaocore.miniMenu.pullDownOpenHandler);
  },
  pullDownCloseHandler: function (event) {
    var el = $(event.target);
    if (el.hasClass('seaocore_mimi_menu_pulldown_item') || el.getParent('.seaocore_mimi_menu_pulldown_item')) {
      return;
    }
    en4.seaocore.miniMenu.pullDownClose();
  },
  pullDownOpenHandler: function (event) {
    var el = $(event.target);
    if (el.hasClass('seaocore_pulldown_wrapper') || el.getParent('.seaocore_pulldown_wrapper')) {
      return;
    }
    if (!el.hasClass('seaocore_mimi_menu_pulldown_item')) {
      el = el.getParent('.seaocore_mimi_menu_pulldown_item');
    }
    if (el.hasClass('_pulldown_item_active')) {
      en4.seaocore.miniMenu.pullDownClose();
      return;
    }
    en4.seaocore.miniMenu.pullDownClose();
    el.addClass('_pulldown_item_active');
    el.getElement('._count_bubble').hide();
    var actionUrl = el.getElement('.seaocore_pulldown_wrapper').get('data-action');
    if (!actionUrl) {
      return;
    }
    var newContentRequest = new Request.HTML({
      url: actionUrl,
      method: 'POST',
      onRequest: function () {
      },
      data: {
        format: 'html',
        page: 1,
        noOfUpdates: 10,
        isajax: 1,
      },
      onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript)
      {
        el.getElement('.seaocore_pulldown_contents').set('html', responseHTML);
        en4.core.runonce.trigger();

      }
    });
    newContentRequest.send();
  },
  pullDownClose: function () {
    $$('.seaocore_mimi_menu_pulldown_item').removeClass('_pulldown_item_active');
  },
  notifications: {
    markAsRead: function () {
      $("notifications_main").getElements('li.notifications_unread').each(function (el) {
        el.removeClass("notifications_unread");
      });
      en4.seaocore.miniMenu.pullDownClose();
      en4.core.request.send(new Request.JSON({
        url: en4.core.baseUrl + 'seaocore/mini-menu/mark-notifications-as-read',
        method: 'POST',
        data: {
          format: 'json'
        },
        onSuccess: function (responseJSON) {
        }
      }), {'force': true});
    },
    onClick: function (event) {
      var current_link = event.target;
      var notification_li = $(current_link).getParent('li');
      var forward_link;
      if (current_link.get('href')) {
        forward_link = current_link.get('href');
      } else {
        forward_link = $(current_link).getElements('a:last-child').get('href');
        if (forward_link == '' || $(current_link).get('tag') == 'img') {
          var a_el = $(current_link).getParent('a');
          if (a_el)
            forward_link = $(current_link).getParent('a').get('href');
        }
        if (forward_link == '' || $(current_link).get('tag') == 'span') {
          //      if($(notification_li) && $(notification_li).getElements('a:last-child') && $(notification_li).getElements('a:last-child')[0])
          //        forward_link = $(notification_li).getElements('a:last-child')[0].get('href');
          forward_link = $(notification_li).getElements('a:last-child').get('href');
        }
      }
      if (forward_link) {
        en4.core.request.send(new Request.JSON({
          url: en4.core.baseUrl + 'activity/notifications/markread',
          data: {
            format: 'json',
            'actionid': notification_li.get('value')
          },
          onSuccess: window.location = forward_link
        }), {'force': true});
      }
    }
  },
  messages: {
    markReadUnread: function (message_id)
    {
      var is_message_read;
      // Mark Unread
      if ($("message_conversation_" + message_id).hasClass("seocore_pulldown_item_list_new"))
      {
        $("message_conversation_" + message_id).removeClass("seocore_pulldown_item_list_new");
        $("seaocore_message_icon_" + message_id).setAttribute("title", en4.core.language.translate("Mark as Read"));
        is_message_read = 1;
      } else {
        $("message_conversation_" + message_id).addClass("seocore_pulldown_item_list_new");
        $("seaocore_message_icon_" + message_id).setAttribute("title", en4.core.language.translate("Mark as Unread"));
        is_message_read = 0;
      }

      en4.core.request.send(new Request.JSON({
        url: en4.core.baseUrl + 'seaocore/mini-menu/mark-message-read-unread',
        method: 'POST',
        data: {
          format: 'json',
          messgae_id: message_id,
          is_read: is_message_read
        },
        onSuccess: function (responseJSON)
        {
        }
      }), {'force': true});
    }
  },
  checkUpdates: function () {
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'seaocore/mini-menu/check-new-updates',
      method: 'POST',
      data: {
        format: 'json'
      },
      onSuccess: function (responseJSON)
      {
        if (responseJSON.newFriendRequest)
        {
          $$('.seaocore_mini_menu_items li span._count_bubble_friend_request_updates').set('html', responseJSON.newFriendRequest).show();
        } else {
          $$('.seaocore_mini_menu_items li span._count_bubble_friend_request_updates').set('html', 0).hide();
        }
        if (responseJSON.newMessage)
        {
          $$('.seaocore_mini_menu_items li span._count_bubble_message_updates').set('html', responseJSON.newMessage).show();
        } else {
          $$('.seaocore_mini_menu_items li span._count_bubble_message_updates').set('html', 0).hide();
        }
        if (responseJSON.newNotification) {
          $$('.seaocore_mini_menu_items li span._count_bubble_activity_updates').set('html', responseJSON.newNotification).show();
        } else {
          $$('.seaocore_mini_menu_items li span._count_bubble_activity_updates').set('html', 0).hide();
        }
      }
    }), {'force': true});
  }
};
