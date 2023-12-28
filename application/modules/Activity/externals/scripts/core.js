
/* $Id: core.js 10182 2014-04-29 23:52:40Z andres $ */



(function() { // START NAMESPACE
var $ = 'id' in document ? document.id : window.$;



en4.activity = {
  postLength: 1000,
  editComposers : {},
  load : function(next_id, subject_guid){
    if( en4.core.request.isRequestActive() ) return;
    scriptJquery('#feed_viewmore')[0].style.display = 'none';
    scriptJquery('#feed_loading')[0].style.display = '';
    en4.core.request.send(scriptJquery.ajax({
      url : en4.core.baseUrl + 'activity/widget/feed',
      method:'post',
      dataType:'json',
      data : {
        format : 'json',
        'maxid' : next_id,
        'feedOnly' : true,
        'nolayout' : true,
        'subject' : subject_guid
      }
    }), {
      'element' : scriptJquery('#activity-feed'),
      'updateHtmlMode' : 'append'
    });
  },

  like : function(action_id, comment_id) {
    en4.core.request.send(scriptJquery.ajax({
      url : en4.core.baseUrl + 'activity/index/like',
      method:'post',
      dataType:'json',
      data : {
        format : 'json',
        action_id : action_id,
        comment_id : comment_id,
        subject : en4.core.subject.guid
      }
    }), {
      'element' : scriptJquery('#comment-likes-activity-item-'+action_id),
      'updateHtmlMode': 'comments2'
    });
  },
  
  unlike : function(action_id, comment_id) {
    en4.core.request.send(scriptJquery.ajax({
      url : en4.core.baseUrl + 'activity/index/unlike',
      method:'post',
      dataType:'json',
      data : {
        format : 'json',
        action_id : action_id,
        comment_id : comment_id,
        subject : en4.core.subject.guid
      }
    }), {
      'element' :scriptJquery('#comment-likes-activity-item-'+action_id),
      'updateHtmlMode': 'comments2'
    });
  },

 comment : function(formData) {
    if( formData.body.trim() == '') return;
    scriptJquery('#activity-comment-submit-'+formData.action_id).after('<div class="comment_loading_overlay"></div>');
    en4.core.request.send(scriptJquery.ajax({
      url : en4.core.baseUrl + 'activity/index/comment',
      method:'post',
      dataType:'json',
      data : formData,
    }), {
      'element' : scriptJquery('#comment-likes-activity-item-' + formData.action_id),
      'updateHtmlMode': 'comments'
    });
  },

  viewComments : function(action_id){
    en4.core.request.send(scriptJquery.ajax({
      url : en4.core.baseUrl + 'activity/index/viewComment',
      method:'post',
      dataType:'json',
      data : {
        format : 'json',
        action_id : action_id,
        nolist : true
      }
    }), {
      'element' : scriptJquery('#activity-item-'+action_id),
      'updateHtmlMode': 'comments'
      //'element' : $('comment-likes-activity-item-'+action_id),
    });
  },

  viewLikes : function(action_id){
    en4.core.request.send(scriptJquery.ajax({
      url : en4.core.baseUrl + 'activity/index/viewLike',
      method:'post',
      dataType:'json',
      data : {
        format : 'json',
        action_id : action_id,
        nolist : true
      }
    }), {
      'element' : scriptJquery('activity-item-'+action_id),
      'updateHtmlMode': 'comments'
    });
  },

  hideNotifications : function(reset_text) {
    en4.core.request.send(scriptJquery.ajax({
      'url' : en4.core.baseUrl + 'activity/notifications/hide'
    }));
    scriptJquery('#updates_toggle').removeClass('new_updates');
    if(scriptJquery('#update_count').length)
    scriptJquery('#update_count').removeClass('minimenu_update_count_bubble_active');
    /*
    var notify_link = $('core_menu_mini_menu_updates_count').clone();
    $('new_notification').destroy();
    notify_link.setAttribute('id', 'core_menu_mini_menu_updates_count');
    notify_link.innerHTML = "0 updates";
    notify_link.inject($('core_menu_mini_menu_updates'));
    $('core_menu_mini_menu_updates').setAttribute('id', '');
    */
    if(scriptJquery('#notifications_main').length){
      var notification_children = scriptJquery('#notifications_main').children('li');
      notification_children.each(function(el){
        scriptJquery(this).attr('class', '');
      });
    }

    if(scriptJquery('#notifications_menu').length){
      var notification_children = scriptJquery('#notifications_menu').children('li');
      notification_children.each(function(el){
        scriptJquery(this).attr('class', '');
      });
    }
    //$('core_menu_mini_menu_updates').setStyle('display', 'none');
  },

  updateNotifications : function() {
    var self = this;
    if(en4.core.request.isRequestActive() ) return;
    en4.core.request.send(scriptJquery.ajax({
      url : en4.core.baseUrl + 'activity/notifications/update',
      method:'post',
      dataType:'json',
      data : {
        format : 'json'
      },
      success : function(){
        self.showNotifications.bind(self);
      },
    }));
  },

  showNotifications : function(responseJSON){
    if (responseJSON.notificationCount>0){
      scriptJquery('#updates_toggle').addClass('new_updates');
    }
  },

  markRead : function (action_id){
    en4.core.request.send(scriptJquery.ajax({
      url : en4.core.baseUrl + 'activity/notifications/test',
      method:'post',
      dataType:'json',
      data : {
        format     : 'json',
        'actionid' : action_id
      }
    }));
  },

  cometNotify : function(responseObject){
    scriptJquery('#core_menu_mini_menu_updates')[0].style.display = '';
    scriptJquery('#core_menu_mini_menu_updates_count')[0].innerHTML = responseObject.text;
  },
  alignHtml : function(){
      scriptJquery(".feed_item_body_content > .feed_item_bodytext").each(function(){
        var element = scriptJquery(this);
        element.clone().insertAfter(scriptJquery(this).closest(".feed_item_body_content"));
        element.remove();
      });
  },
  post : function (composeInstance, event) {
    event.preventDefault();
    var formElement = composeInstance.getForm();
    var formData = {};
    formElement.serializeArray().forEach((item)=>{
        formData[item.name] = item.value;
    });
    formData['format'] = 'json';
    en4.core.request.send(scriptJquery.ajax({
      url: formElement.attr('action'),
      data: formData,
      dataType: 'json',
      method : 'post',
    }),{
      force: true,
      successCallBack : function (resp) {
        if(!resp.status) {
          scriptJquery('#fail_msg').css("display","block");
          return;
        }
        if(resp.flood){
          scriptJquery('#flood_msg').css("display","block");
          scriptJquery('#flood_msg_cnt').html(resp.floodMessage);
          return;
        }else{
            scriptJquery('#flood_msg').css("display","none");
            scriptJquery('#flood_msg_cnt').html("");
        }

        if (!scriptJquery('#activity-feed').length) {
          scriptJquery.crtEle('ul', {
            'id': 'activity-feed',
            'class': 'feed'
          }).appendTo(scriptJquery("body").find('.layout_activity_feed'));
          scriptJquery('#no-feed-tip').remove();
        }

        if (window._activityUpdateHandler !== undefined) {
          window._activityUpdateHandler.options.next_id = resp.action_id;
          window._activityUpdateHandler.getFeedUpdate(window._activityUpdateHandler.options.next_id, true);
        } else {
          new ActivityUpdateHandler({
            'baseUrl' : en4.core.baseUrl,
            'basePath' : en4.core.basePath,
            'identity' : 4,
            'subject_guid' : en4.core.subject.guid
          }).getFeedUpdate(resp.action_id, true);
        }

        scriptJquery('#token').val(resp.formToken);
        // Remove the params values after submit
        scriptJquery('#compose-menu').next().html('');
        Object.entries(composeInstance.plugins).forEach(function([key,plugin]) {
          plugin.reset();
        });
        composeInstance.reset();
      }
    });
  },
  bindEditFeed: function(action_id, composerOptions) {
    if(scriptJquery('#activity-item-' + action_id).find('.compose-container').length) {
      this.bindEditLink(action_id);
      return;
    }
    var editComposeInstance = new Composer('#feed-edit-body-' + action_id, {
      lang: composerOptions.lang,
      hashtagEnabled : composerOptions.hashtagEnabled,
      hideSubmitOnBlur: false,
      allowEmptyWithoutAttachment: composerOptions.allowEmptyWithoutAttachment,
      submitCallBack: function(composeInstance, event) {
        event.preventDefault();
        var params =  {};
        editComposeInstance.getForm().serializeArray().forEach((item)=>{
            params[item.name] = item.value;
        });
        en4.core.request.send(scriptJquery.ajax({
          url: editComposeInstance.getForm().attr('action'),
          data: scriptJquery.extend({
            format: 'json',
            subject: en4.core.subject.guid
          },params),
          method: 'POST',
          beforeSend: function() {
            editComposeInstance.getForm().find('#fieldset-buttons').css('display', 'none');
            en4.core.loader.appendTo(editComposeInstance.getForm().find('#buttons-wrapper'));
          },
        }), {
          'force': true,
          'element': scriptJquery('#activity-item-' + action_id),
          'updateHtmlMode': '#comments',
          'successCallBack' : function() {
            scriptJquery('#activity-item-' + action_id).find('.compose-container').remove();
          }
        });
      }
    });
    this.editComposers[action_id] = editComposeInstance;
    if( editComposeInstance._supportsContentEditable() ) {
      editComposeInstance.setContent(editComposeInstance.elements.body.html().replace(/(\r\n?|\n)/ig, "<br>"));
    }
    scriptJquery(document).data('editComposeInstanceActivity' + action_id, editComposeInstance);
    
    this.bindEditLink(action_id);
    scriptJquery('#activity-item-' + action_id).find('.feed-edit-content-cancel').on('click', function(event) {
      var el = scriptJquery(event.target);
      var parent = el.parents('.activity-item');
      parent.find('.feed_item_body_edit_content').css('display', 'none');
      parent.find('.feed_item_body_content').css('display', 'block');
    });
    
    //Edit Post case
    scriptJquery(".feed_item_body_content > .feed_item_bodytext").each(function(){
      var element = scriptJquery(this);
      element.clone().insertAfter(scriptJquery(this).closest(".feed_item_body_content"));
      element.remove();
    });
  },
  bindEditLink : function (action_id) {
    if (!scriptJquery('#activity-item-' + action_id).find('.feed_item_option_edit').length) {
      return;
    }
    var self = this;
    scriptJquery('#activity-item-' + action_id).find('.feed_item_option_edit').on('click', function(event) {
      var el = scriptJquery(event.target);
      var parent = el.closest('.activity-item');
      parent.find('.feed_item_body_content').css('display', 'none');
      parent.find('.feed_item_body_edit_content').css('display', 'block');
      self.editComposers[action_id].focus();
      self.editComposers[action_id].placeCaretAtEnd();
    });
    if(self.postLength > 0){
      self.editComposers[action_id].elements.textCounter = scriptJquery.crtEle('div', {
        'class' : 'compose-content-counter',
      }).css({
          'display' : 'none'
      }).insertAfter(self.editComposers[action_id].elements.textarea, 'after');
      
      scriptJquery('#activity-item-' + action_id).find('.compose-content').on('input', function(e) {
          self.checkPostLength(e,self,action_id);
      });
    }
  },
  checkPostLength: function(e,self,action_id) {
    var content = self.editComposers[action_id].getContent();
    content = content.replace(/&nbsp;/g, ' ');
    content = content.replace(/&amp;/g, '&'); 
    content = content.replace(/&lt;/g, '<'); 
    content = content.replace(/&gt;/g, '>');
    if(self.postLength < content.length){
      content = content.substr(0,self.postLength);
      self.editComposers[action_id].setContent(content);
      self.editComposers[action_id].setCaretPos(content.length);
    }
    self.editComposers[action_id].elements.textCounter.css("display","block");
    self.editComposers[action_id].elements.textCounter.html(self.postLength-content.length);
    return;
  },
};

NotificationUpdateHandler = class {
  options = {
      debug : false,
      baseUrl : '/',
      identity : false,
      delay : 5000,
      minDelay : 5000,
      maxDelay : 600000,
      delayFactor : 1.5,
      admin : false,
      idleTimeout : 600000,
      last_id : 0,
      subject_guid : null
  }
  state = true;

  activestate = 1;

  fresh = true;

  lastEventTime = false;

  title = document.title;

  constructor(options) {
    this.options = scriptJquery.extend(this.options,options);
    this.options.minDelay = this.options.delay;
  }

  start() {
    this.state = true;
    this.loop();
  }

  stop() {
    this.state = false;
  }

  updateNotifications() {
    if( en4.core.request.isRequestActive()) return;
    en4.core.request.send(scriptJquery.ajax({
      url : en4.core.baseUrl + 'activity/notifications/update',
      method : 'post',
      dataType : 'json',
      data : {
        format : 'json'
      },
    })
    ,{
      successCallBack : this.showNotifications.bind(this)
    });
  }

  showNotifications(responseJSON){
    if (responseJSON.notificationCount > 0){
      this.options.delay = this.options.minDelay;
        if (!scriptJquery('#updates_toggle').length) {
            return;
        }
      //$('updates_toggle').set('html', responseJSON.text).addClass('new_updates');
      if(scriptJquery('#update_count').length)
        scriptJquery('#update_count').html(responseJSON.notificationCount).addClass('minimenu_update_count_bubble_active');
    } else {
      this.options.delay = Math.min(this.options.maxDelay, this.options.delayFactor * this.options.delay);
    }
  }

  loop() {
    if( !this.state) {
      setTimeout(this.loop.bind(this),this.options.delay);
      return;
    }
    try {
      this.updateNotifications().complete(function() {
        setTimeout(this.loop.bind(this),this.options.delay);
      }.bind(this));
    } catch( e ) {
      setTimeout(this.loop.bind(this),this.options.delay);
      this._log(e);
    }
  }
  // Utility
  _log(object) {
    if( !this.options.debug ) {
      return;
    }
    // Firefox is dumb and causes problems sometimes with console
    try {
      if( typeof(console) && $type(console) ) {
        console.log(object);
      }
    } catch( e ) {
      // Silence
    }
  }
};

//(function(){

  en4.activity.compose = {

    composers : {},

    register : function(object){
      name = object.getName();
      this.composers[name] = object;
    },

    deactivate : function(){
      for( var x in this.composers ){
        this.composers[x].deactivate();
      }
      return this;
    }
  };
  class en4_activity_compose_icompose {
    name = false;
    element = false;
    options = {};

    constructor(element, options){
      this.element = $(element);
      this.setOptions(options);
    }

    getName(){
      return this.name;
    }

    activate(){
      en4.activity.compose.deactivate();
    }

    deactivate(){

    }
  };

//})();

ActivityUpdateHandler = function(options){

  //Implements : [Events, Options],
  this.options = {
      debug : true,
      baseUrl : '/',
      identity : false,
      delay : 500,
      admin : false,
      idleTimeout : 600000,
      last_id : 0,
      next_id : null,
      subject_guid : null,
      showImmediately : false
  };

  this.state = true;
  this.activestate = 1;
  this.fresh = true

  this.lastEventTime = false

  this.title = document.title

  //loopId : false,

  this.initialize = function(options) {
    this.options = scriptJquery.extend(this.options,options);
  }

  this.start = function() {
    this.state = true;
    this.loop();
  };

  this.stop = function() {
    this.state = false;
  };

  this.checkFeedUpdate = function(action_id, subject_guid){
    if( en4.core.request.isRequestActive() ) return;

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
    this.options.last_id = Math.max.apply( Math, list );
    min_id = parseInt(this.options.last_id) + 1;

    var req = scriptJquery.ajax({
      url : en4.core.baseUrl + 'widget/index/name/activity.feed',
      dataType : 'html',
      method: 'post',
      data : {
        'format' : 'html',
        'minid' : min_id,
        'feedOnly' : true,
        'nolayout' : true,
        'subject' : this.options.subject_guid,
        'getUpdate' : true
      },
    });
    var self = this;
    en4.core.request.send(req, {
      'element' : scriptJquery('#activity-feed'),
      'updateHtmlMode' : 'prepend',
      'successCallBack' : function(){
          setTimeout(function() {
            if(this.options.showImmediately && scriptJquery('#feed-update').children().length > 0 ) {
              scriptJquery('#feed-update').css('display', 'none');
              scriptJquery('#feed-update').empty();
              this.getFeedUpdate(this.options.next_id);
              }
            }.bind(self),50)
        },
      }
    );

   // Start LOCAL STORAGE STUFF
   if(localStorage) {
     var pageTitle = document.title;
     //@TODO Refill Locally Stored Activity Feed

     // For each activity-item, get the item ID number Data attribute and add it to an array
     var feed  = document.getElementById('activity-feed');
     // For every <li> in Feed, get the Feed Item Attribute and add it to an array
     var items = feed ? feed.getElementsByTagName("li") : [];
     var itemObject = { };
     // Loop through each item in array to get the InnerHTML of each Activity Feed Item
     var c = 0;
     for (var i = 0; i < items.length; ++i) {
       if(items[i].getAttribute('data-activity-feed-item') != null){
         var itemId = items[i].getAttribute('data-activity-feed-item');
         itemObject[c] = {id: itemId, content : document.getElementById('activity-item-'+itemId).innerHTML };
         c++;
         }
       }
     // Serialize itemObject as JSON string
     var activityFeedJSON = JSON.stringify(itemObject);
     localStorage.setItem(pageTitle+'-activity-feed-widget', activityFeedJSON);
   }


   // Reconstruct JSON Object, Find Highest ID
   if(localStorage.getItem(pageTitle+'-activity-feed-widget')) {
     var storedFeedJSON = localStorage.getItem(pageTitle+'-activity-feed-widget');
     var storedObj = eval ("(" + storedFeedJSON + ")");

    // Highest Feed ID
    // @TODO use this at min_id when fetching new Activity Feed Items
   }
   // END LOCAL STORAGE STUFF
   return req;
  }

  this.getFeedUpdate = function(last_id, force){
    if( !force && en4.core.request.isRequestActive() ) return;
    var min_id = parseInt(this.options.last_id) + 1;
    this.options.last_id = last_id;
    document.title = this.title;
    var req = scriptJquery.ajax({
      url : en4.core.baseUrl + 'widget/index/name/activity.feed',
      dataType:'html',
      method : 'post',
      data : {
        'format' : 'html',
        'minid' : min_id,
        'feedOnly' : true,
        'nolayout' : true,
        'getUpdate' : true,
        'subject' : this.options.subject_guid
      }
    });
    en4.core.request.send(req, {
      'element' : scriptJquery('#activity-feed'),
      'updateHtmlMode' : 'prepend',
      force: !!force
    });
    req.complete(function() {
      setTimeout(function() {
          en4.activity.alignHtml();
        }.bind(this),100);
    }.bind(this));
    return req;
  }

  this.loop = function() {
    this._log('activity update loop start');
    if(!this.state ) {
      setTimeout(this.loop.bind(this),this.options.delay);
      return;
    }
    try {
      this.checkFeedUpdate().complete(function() {
        try {
          this._log('activity loop req complete');
          setTimeout(this.loop.bind(this),this.options.delay);
        } catch( e ) {
          setTimeout(this.loop.bind(this),this.options.delay);
          this._log(e);
        }
      }.bind(this));
    } catch( e ) {
      setTimeout(this.loop.bind(this),this.options.delay);
      this._log(e);
    }

    this._log('activity update loop stop');
  }

  // Utility
  this._log = function(object) {
    if( !this.options.debug ) {
      return;
    }
    try {
      if( 'console' in window && typeof(console) && 'log' in console ) {
        console.log(object);
      }
    } catch( e ) {
      // Silence
    }
  }
  this.initialize(options);
};

})(); // END NAMESPACE
