
/* $Id: core.js 9984 2013-03-20 00:00:04Z john $ */



(function() { // START NAMESPACE
en4.user = {
  viewer : {
    type : false,
    id : false
  },

  attachEmailTaken : function(element, callback)
  {
    var bind = this;
    scriptJquery(element).on('blur', function(){
      bind.checkEmailTaken(scriptJquery(this).val(), callback);
    });

    /*
    var lastElementValue = element.value;
    (function(){
      if( element.value != lastElementValue )
      {

        lastElementValue = element.value;
      }
    }).periodical(500, this);
    */
  },

  attachUsernameTaken : function(element, callback)
  {
    var bind = this;
    scriptJquery(element).on('blur', function(){
      bind.checkUsernameTaken(scriptJquery(this).val(), callback);
    });
    
    /*
    var lastElementValue = element.value;
    (function(){
      if( element.value != lastElementValue )
      {
        bind.checkUsernameTaken(element.value, callback);
        lastElementValue = element.value;
      }
    }).periodical(500, this);
    */
  },

  checkEmailTaken : function(email, callback)
  {
    en4.core.request.send(scriptJquery.ajax({
      url : en4.core.baseUrl + 'user/signup/taken',
      dataType:'json',
      method:'post',
      data : {
        format : 'json',
        email : email
      },
      success : function(responseObject)
      {
        if( $type(responseObject.taken) ){
          callback(responseObject.taken);
        }
      }
    }));
    return this;
  },

  checkUsernameTaken : function(username)
  {
    en4.core.request.send(scriptJquery.ajax({
      url : en4.core.baseUrl + 'user/signup/taken',
      dataType:'json',
      method:'post',
      data : {
        format : 'json',
        username : username
      },
      success : function(responseObject)
      {
        if( $type(responseObject.taken) ){
          callback(responseObject.taken);
        }
      }
    }));

    return this;
  },

  clearStatus : function() {
    var request = scriptJquery.ajax({
      url : en4.core.baseUrl + 'user/edit/clear-status',
      method : 'post',
      dataType: 'json',
      data : {
        format : 'json'
      }
    });
    if(scriptJquery('#user_profile_status_container').length) {
      scriptJquery('#user_profile_status_container').empty();
    }
    return request;
  },
  
  buildFieldPrivacySelector : function(elements, privacyExemptFields) {
    var idEx = {};
    privacyExemptFields = typeof (privacyExemptFields) !== 'object' ? {} : privacyExemptFields;
    // Clear when body click, if not inside selector
    scriptJquery(document).on('click', function(event) {
      let ele = scriptJquery(event.target);
      if(ele.hasClass('field-privacy-selector')) {
        return;
      } else if(ele.closest('.field-privacy-selector').length) {
        return;
      } else {
        scriptJquery('.field-privacy-selector').removeClass('active');
      }
    });
    
    // Register selectors
    elements.each(function(e) {
      let el = scriptJquery(this);
      if(el.prop('tagName').toLowerCase() == 'span') {
        return;
      }
      var fuid = el.attr('id');
      var tmp;
      if( (tmp = fuid.match(/^\d+_\d+_\d+/)) ) {
        fuid = tmp[0];
      }
      var id = el.attr('data-field-id');
      if( id in idEx ) {
        return;
      }
      if( Object.values(privacyExemptFields).indexOf(parseInt(id)) > -1 ) {
        return;
      }
      idEx[id] = true;
      var wrapperEl = el.parents('.form-wrapper');
      var privacyValue = el.attr('data-privacy');
      
      var selector = scriptJquery.crtEle('div', {
        'class' : 'field-privacy-selector',
        'data-privacy' : privacyValue || 'everyone',
      });
      selector = selector.html('\
                  <span class="icon"></span>\n\
                  <span class="caret"></span>\n\
                  <ul>\n\
                    <li data-value="everyone" class="field-privacy-option-everyone"><span class="icon"></span><span class="text">' 
                      + en4.core.language.translate('Everyone') + '</span></li>\n\
                    <li data-value="registered" class="field-privacy-option-registered"><span class="icon"></span><span class="text">' 
                      + en4.core.language.translate('All Members') + '</span></li>\n\
                    <li data-value="friends" class="field-privacy-option-friends"><span class="icon"></span><span class="text">' 
                      + en4.core.language.translate('Friends') + '</span></li>\n\
                    <li data-value="self" class="field-privacy-option-self"><span class="icon"></span><span class="text">' 
                      + en4.core.language.translate('Only Me') + '</span></li>\n\
                  </ul>\n\
                  <input type="hidden" name="privacy[' + fuid + ']" />');
      selector.appendTo(wrapperEl);
      selector.off().on('click', function(e) {
        var prevState = selector.hasClass('active');
        scriptJquery('.field-privacy-selector').removeClass('active');
        if(!prevState) {
          selector.addClass('active');
        }
      });
      selector.find('li').off().on('click', function(event) {
        var el = scriptJquery(event.target);
        if(el.prop('tagName').toLowerCase() != 'li' ) {
          el = el.parent();
        }
        var value = el.attr('data-value');
        selector.find('input').attr('value', value);
        selector.find('.active').removeClass('active');
        el.addClass('active');
        selector.attr('data-privacy', value);
      });
      selector.find('*[data-value="' + (privacyValue || 'everyone') + '"]').addClass('active');
      selector.find('input').attr('value', privacyValue || 'everyone');
    });
  }
  
};

en4.user.friends = {

  refreshLists : function(){
    
  },
  
  addToList : function(list_id, user_id){
    var request = scriptJquery.ajax({
      url : en4.core.baseUrl + 'user/friends/list-add',
      dataType : 'json',
      method : 'post',
      data : {
        format : 'json',
        friend_id : user_id,
        list_id : list_id
      }
    });
    return request;

  },

  removeFromList : function(list_id, user_id){
    var request = scriptJquery.ajax({
      url : en4.core.baseUrl + 'user/friends/list-remove',
      dataType : 'json',
      method : 'post',
      data : {
        format : 'json',
        friend_id : user_id,
        list_id : list_id
      }
    });
    return request;

  },

  createList : function(title, user_id){
    var request = scriptJquery.ajax({
      url : en4.core.baseUrl + 'user/friends/list-create',
      dataType : 'json',
      method : 'post',
      data : {
        format : 'json',
        friend_id : user_id,
        title : title
      }
    });
    return request;
  },

  deleteList : function(list_id){

    var bind = this;
    en4.core.request.send(scriptJquery.ajax({
      url : en4.core.baseUrl + 'user/friends/list-delete',
      dataType : 'json',
      method : 'post',
      data : {
        format : 'json',
        user_id : en4.user.viewer.id,
        list_id : list_id
      }
    }));

    return this;
  },


  showMenu : function(user_id){
    scriptJquery('#profile_friends_lists_menu_' + user_id).css("visibility",'visible');
    scriptJquery('#friends_lists_menu_input_' + user_id).trigger("focus");
    scriptJquery('#friends_lists_menu_input_' + user_id).trigger("select");
  },

  hideMenu : function(user_id){
    scriptJquery('#profile_friends_lists_menu_' + user_id).css("visibility",'hidden');
  },

  clearAddList : function(user_id){
    scriptJquery('#friends_lists_menu_input_' + user_id).val("");
  }

};

/*
* Multi Select
* */
window.addEventListener('DOMContentLoaded', function() {
    if (typeof scriptJquery != "undefined" && scriptJquery('#global_page_user-index-browse').length) {
        scriptJquery('.show_multi_select').closest('li').css('overflow',"visible");
        scriptJquery('.show_multi_select').selectize({});
    }
})

})(); // END NAMESPACE
