/* $Id: pinboard.js 6590 2013-04-01 00:00:00Z SocialEngineAddOns Copyright 2012-2013 BigStep Technologies Pvt. Ltd. $ */
if(!window.locationsParamsSEAO){
  window.locationsParamsSEAO = {
    latitude:0, 
    longitude:0
  };
  window.locationsDetactSEAO = false;
  window.locationsDetactedSEAO = false;
  window.locationCallBack = [];
}
var PinBoardSeao = function(Events,Options){

  this.widgets = new Array(),
  this.options = {
  };
  this.currentIndex = 0;
  this.currentPage = 1;
  this.currentActive = false;
  this.viewMoreEl = null;
  this.layout = 'middle';

  this.add = function(params){
    scriptJquery('#'+params.contentId).closest('.'+params.responseContainerClass).attr('id','pinboard_wrapper_'+params.widgetId);
    
    params.requestParams.content_id = params.widgetId;
    params.responseContainer = scriptJquery('#pinboard_wrapper_'+params.widgetId);

    if(params.requestParams.noOfTimes != 0 && params.totalCount > (params.requestParams.noOfTimes * params.requestParams.itemCount))
      params.totalCount = (params.requestParams.noOfTimes * params.requestParams.itemCount);
    params.active = false;
    this.widgets.push(params);
    if(!this.viewMoreEl){
      this.viewMoreEl =  scriptJquery.crtEle('div',{
        'class':'dnone'
      });
    }
    if(!this.loading){
      this.loading =  scriptJquery.crtEle('div',{
        'class':'dnone'
      });
    }
    this.loading.insertAfter(params.responseContainer);
    this.viewMoreEl.insertAfter(params.responseContainer);
  };
  this.start = function(){
    if(this.currentActive)
      return;
    if(this.widgets.length > this.currentIndex){
      this.currentActive = true;
      var params = this.widgets[this.currentIndex];
      params.currentIndex = this.currentInde;
      params.requestParams.contentpage = this.currentPage;
      this.startReq(params);
      if(params.totalCount <= params.requestParams.itemCount * this.currentPage){
        this.currentIndex++;
        this.currentPage = 1;
      }else{
        this.currentPage++;
      }
    }
  },
  this.viewMore = function(){

    if(!this.viewMoreEl)
      return;
    var self=this;
    var elementPostionY = 0;
    if( typeof( this.viewMoreEl.offsetParent ) != 'undefined' ) {
      elementPostionY = this.viewMoreEl.offsetTop;
    }else{
      elementPostionY = this.viewMoreEl.y; 
    }
    if(elementPostionY <= scriptJquery(window).scrollTop()+(window.innerHeight -10)){
      self.start();  
    }
    
  },
  this.callBackLocation = function(){
    var fn;
    while( (fn = window.locationCallBack.shift()) ){
      $try(function(){
        fn();
      });
    }
    window.locationCallBack = [];

  },
  this.startReq = function(params){
    var self=this;
    
    params.callBack=this.callBackLocation;

    //window.locationCallBack.push(function(){
        params.requestParams= scriptJquery.extend(params.requestParams,window.locationsParamsSEAO);
        self.sendReq(params)
    //});
    en4.seaocore.locationBased.startReq(params);
   
  },
  this.sendReq = function(params){
    console.log('params',params);
    var self=this;
    // params.responseContainer.empty();
    this.loading.removeClass('dnone');
    var url = en4.core.baseUrl+'widget';
   
    if(params.requestUrl)
      url= params.requestUrl;
    en4.core.request.send(scriptJquery.ajax({
      url : url,
      dataType : 'html',
      data : scriptJquery.extend(params.requestParams,{
        format : 'html',
        subject: en4.core.subject.guid,
        is_ajax_load:true
      }),
      evalScripts : true,
      async : false,
      success : function(responseHTML) {

        self.loading.addClass('dnone');
        if(scriptJquery('#'+params.contentId))
        scriptJquery('#'+params.contentId).remove();

        if(!self.loading.hasClass('seaocore_loading')){
          self.loading.addClass('seaocore_loading');
          scriptJquery.crtEle('img', {
            src: en4.core.staticBaseUrl + 'application/modules/Seaocore/externals/images/core/loading.gif'
          }).appendTo(self.loading);
        }
        scriptJquery(responseHTML).appendTo(params.responseContainer);
        
        if(params.requestParams.contentpage == 1){
          var elem = scriptJquery.crtEle('div',{
            id : 'seaocore_board_wrapper_'+params.widgetId,
            'class' : 'seaocore_board_wrapper'
          }).appendTo(params.responseContainer);
          params.responseContainer.find('.seaocore_list_wrapper').appendTo(elem);
          params.responseContainer = elem;
          params.active = true;
          self.widgets[params.currentIndex] = params;  
        }
        for (var i=0; i<= 10;i++){
          setTimeout(function(){
            self.setMasonry(params);
          },(500*i));
        }
       
        en4.core.runonce.trigger();
        Smoothbox.bind(params.responseContainer);
        scriptJquery(".pb_ch_wd").on('click',self.childWindowOpen.bind(this));
        self.currentActive=false;
      }
    }));
  },
  this.childWindowOpen = function(event){
    var element =  scriptJquery(event.target);
    if((
      element.prop("tagName").toLowerCase() == 'a' &&
      !element.onclick &&
      element.href &&
      !element.href.match(/^(javascript|[#])/)
      )){
      event.preventDefault();
      open(element.href,element.html(),'width=700,height=350,resizable,toolbar,status');
    }
  },
  this.setMasonry = function(params){
    
    if(!params.active)return;
    // params.responseContainer.pinBoardSeaoMasonry({
    //   columnWidth: params.requestParams.itemWidth,  //224 columnWidth does not need to be set if singleMode is set to true.
    //   singleMode: true,
    //   itemSelector: '.seaocore_list_wrapper'
    // }); 
    params.responseContainer.masonry({
      itemSelector: '.seaocore_list_wrapper',
      //singleMode: true,
      // columnWidth: 160
    });
  },
  this.setAllPinBoardLayout = function(){
    if(this.widgets.length > 0){
      for (var i = 0; i < this.widgets.length;i++){
        var params = this.widgets[i];
        this.setMasonry(params);
      }
    }
  }
  
};
//**********
var PinBoardSeaoObject = new Array();
var PinBoardSeaoViewMoreObjects = new Array();
var PinBoardSeaoColumn = new Array('middle','left','right');
for(var i = 0;i < PinBoardSeaoColumn.length;i++)
  PinBoardSeaoObject[PinBoardSeaoColumn[i]] = new PinBoardSeao(PinBoardSeaoColumn[i]);
en4.core.runonce.add(function(){

  for(var i = 0; i < PinBoardSeaoColumn.length; i++){
    PinBoardSeaoObject[PinBoardSeaoColumn[i]].start();  
  }

  scriptJquery(window).on('scroll', function(){

    for(var i = 0; i < PinBoardSeaoColumn.length; i++){
      PinBoardSeaoObject[PinBoardSeaoColumn[i]].viewMore();
    }
    en4.seaocorepinboard.comments.setLayout(true);
  });
});

//********
PinBoardSeaoViewMore = function(options){

  //options = { },
  this.params = {},
  this.currentPage = 1,
  this.currentActive = false,
  this.viewMoreEl = null,
  this.layout = 'middle',
  this.active = false,

  this.initialize = function(params) {

    if(params.detactLocation != 'undefined') {
      params.callBack = function(params) {
        if(params.locationSetInCookies) {
          window.location.reload();
        }
      };
      en4.seaocore.locationBased.startReq(params); 
    }
    scriptJquery( '#'+params.contentId).closest('.'+params.responseContainerClass).attr('id','pinboard_wrapper_'+params.widgetId);
    scriptJquery( '#'+params.contentId).remove();
    params.requestParams.content_id =params.widgetId;
    params.responseContainer=scriptJquery('#pinboard_wrapper_'+params.widgetId);

    if(params.requestParams.noOfTimes !=0 && params.totalCount>(params.requestParams.noOfTimes * params.requestParams.itemCount)) {
      params.totalCount = (params.requestParams.noOfTimes * params.requestParams.itemCount);
    }
    
    this.params = params;
    this.viewMoreEl =  scriptJquery('#'+params.viewMoreId);
    this.loading = scriptJquery('#'+params.loadingId);
    
    var elem = scriptJquery.crtEle('div',{ id:'seaocore_board_wrapper_'+params.widgetId, 'class':'seaocore_board_wrapper' })
                         .appendTo(this.params.responseContainer);
    params.responseContainer.find('.seaocore_list_wrapper').appendTo(elem);
    params.responseContainer = elem;

    this.loading.injectSeaoCustom(params.responseContainer,'after');
    this.viewMoreEl.injectSeaoCustom(params.responseContainer,'after');

    this.viewMoreEl.on('click',this.start.bind(this));

    if(this.params.totalCount > (this.currentPage*this.params.requestParams.itemCount)){
      this.viewMoreEl.removeClass('dnone');
    }
    this.active=true;
    this.params=params;
    this.pinBoardLayout();
    scriptJquery(".pb_ch_wd").on('click',this.childWindowOpen.bind(this));

  },

  this.pinBoardLayout = function(){

    var self=this;
    if(!this.active)return;
    setTimeout(function(){

      self.params.responseContainer.masonry({
        itemSelector: '.seaocore_list_wrapper',
        //singleMode: true,
        // columnWidth: 160
      });
      /*self.params.responseContainer.pinBoardSeaoMasonry({
        columnWidth: self.params.requestParams.itemWidth,  //224 columnWidth does not need to be set if singleMode is set to true.
        singleMode: true,
        itemSelector: '.seaocore_list_wrapper'
      });*/
    },100);
  },
  this.start = function(){
    if(this.currentActive)
      return;
   
    this.currentActive = true;
    var params = this.params;
    if(params.totalCount < (this.currentPage*params.requestParams.itemCount))
      return;
 
    this.currentPage++;
    params.requestParams.contentpage = this.currentPage;
    this.sendReq(params);  
  },
  this.sendReq = function(params){
    var self=this;
    // params.responseContainer.empty();
    this.loading.removeClass('dnone');
    this.viewMoreEl.addClass('dnone');
    var url = en4.core.baseUrl+'widget';
   
    if(params.requestUrl)
      url = params.requestUrl;
    
    en4.core.request.send(scriptJquery.ajax({
      url : url,
      dataType : 'html',
      data : scriptJquery.extend(params.requestParams,{
        format : 'html',
        subject: en4.core.subject.guid,
        is_ajax_load:true
      }),
      evalScripts : true,
      success : function(responseHTML) {
        self.loading.addClass('dnone');
        if(self.params.totalCount > (self.currentPage*self.params.requestParams.itemCount)){
          self.viewMoreEl.removeClass('dnone');
        }
        responseHTML = scriptJquery(responseHTML);
        responseHTML.appendTo(params.responseContainer);
        setTimeout( function(){
          params.responseContainer.masonry( 'appended', responseHTML )
          //self.pinBoardLayout();
        },(500) );
        for (var i=0; i<= 10;i++){
        }
        en4.core.runonce.trigger();
        Smoothbox.bind(params.responseContainer);
        scriptJquery(".pb_ch_wd").on('click',self.childWindowOpen.bind(this));
        self.currentActive=false;
        
      }
    }));
  },
  this.childWindowOpen = function(event){
    
    var element=  scriptJquery(event.target);
    if((
      element.prop("tagName").toLowerCase() == 'a' &&
      !element.onclick &&
      element.href &&
      !element.href.match(/^(javascript|[#])/)
      )){
      event.preventDefault();
      open(element.href,element.html(),'width=700,height=350,resizable,toolbar,status');
    }
  }
  this.initialize( options );
};

/**
 * likes
 */
en4.seaocorepinboard = {
  masonryArray:new Array(),
  masonryWidgetAllow:new Array(),
  setMasonryLayout:function(){
    for(var i=0;i< en4.seaocorepinboard.masonryArray.length;i++){
      if(en4.seaocorepinboard.masonryArray[i].allowId && en4.seaocorepinboard.masonryWidgetAllow[en4.seaocorepinboard.masonryArray[i].allowId]){
        en4.seaocorepinboard.setMasonry(en4.seaocorepinboard.masonryArray[i]);
      }
    }
  },
  setMasonry:function(params){
    if(!params.responseContainer)
      return;
    params.responseContainer.masonry({
      itemSelector: '.seaocore_list_wrapper',
      //singleMode: true,
      // columnWidth: 160
    });
    //params.responseContainer.pinBoardSeaoMasonry(params); 
  }
}

en4.seaocorepinboard.comments = {
  setLayoutActive:false,

  setLayout:function(force){
    if(this.setLayoutActive) {
      return;
    }
    var delay=100;
    if(force==true){
      delay=1;
    }
    this.setLayoutActive = true;
    setTimeout(function(){
      for(var i=0;i<PinBoardSeaoColumn.length;i++){
        PinBoardSeaoObject[PinBoardSeaoColumn[i]]. setAllPinBoardLayout();
      }
      for(var i=0;i<PinBoardSeaoViewMoreObjects.length;i++){
        PinBoardSeaoViewMoreObjects[i].pinBoardLayout();
      }
      en4.seaocorepinboard.setMasonryLayout();
      en4.seaocorepinboard.comments.setLayoutActive=false;
    },delay);
  },
  addComment:function(elem_id){
    if(scriptJquery("#comment-form-open-li_"+elem_id).length)
      scriptJquery("#comment-form-open-li_"+elem_id).css( 'display', "none" );
    scriptJquery('#comment-form_'+elem_id).css( 'display', '' );
    scriptJquery('#comment-form_'+elem_id).find('#body').focus();
    en4.seaocorepinboard.comments.setLayout(true);
   
    if(!scriptJquery('#comment-form_'+elem_id).data('bodyheight',false))
      scriptJquery('#comment-form_'+elem_id).data('bodyheight',scriptJquery(scriptJquery('#comment-form_'+elem_id).body).offsetHeight); 
  
  
  if (typeof commentCaptcha != 'undefined' && commentCaptcha == 1) {
            
            scriptJquery('#comment-form_'+elem_id).find('.g-recaptcha').each(function (index, el) {
                grecaptcha.render(index, {'sitekey': sitekey});
            });
        }
        
  },
  loadComments : function(type, id, page,widget_id){
    en4.core.request.send(scriptJquery.ajax({
      url : en4.core.baseUrl + 'seaocore/pin-board-comment/list',
      dataType : 'html',
      data : {
        format : 'html',
        type : type,
        id : id,
        page : page,
        widget_id : widget_id
      },
      success : function(responseJSON) {
        en4.seaocorepinboard.comments.setLayout(); 
        
      }
    }), {
      'element' : scriptJquery('#comments'+'_'+type+'_'+id+'_'+widget_id),
      "force":true
    });
  },
  
  attachCreateComment : function(formElement,type,id,widget_id){
    var bind = this;
    formElement.on((en4.seaocore.browser.msieversion()) ? 'keydown':'keypress',function (event){
      if(formElement.data('bodyheight',false)&& scriptJquery(formElement.body).offsetHeight > formElement.data('bodyheight',false)){
        en4.seaocorepinboard.comments.setLayout(true);
      }
      if (event.shift && event.key == 'enter') {        
      } else if(event.key == 'enter') {
        if(formElement.body.value.replace(/\s/g, '')==''){
          return;
        }
        event.preventDefault();  
        bind.submit(formElement,type,id,widget_id);         
      }
    });
      
    //    // add blur event
    //    formElement.body.on('blur',function(){
    //      formElement.style.display = "none";
    //      if(scriptJquery( '#' + "comment-form-open-li_"+type+'_'+id+'_'+widget_id).length)
    //        scriptJquery( '#' + "comment-form-open-li_"+type+'_'+id+'_'+widget_id).css( 'display', "block" );
    //    } );
    formElement.on('submit', function(event){
      event.preventDefault();
      bind.submit(formElement,type,id,widget_id);
    });
  },
  submit:function(formElement,type,id,widget_id){
    var form_values  = jQuery(formElement).serialize();
    form_values += '&format=json';
    form_values += '&id='+id;
    form_values += '&widget_id='+widget_id;
    // if(formElement.body.replace(/\s/g, '')==''){
    //   return;
    // }
    if(scriptJquery("#comment-form-loading-li_"+type+'_'+id+'_'+widget_id).length)
      scriptJquery("#comment-form-loading-li_"+type+'_'+id+'_'+widget_id).css( 'display', "block" );
    scriptJquery(formElement).css("display","none");
    en4.seaocorepinboard.comments.setLayout(true);
    en4.core.request.send(scriptJquery.ajax({
      url : en4.core.baseUrl + 'seaocore/pin-board-comment/create',
      data : form_values,
      dataType:'json',
      method : 'post',
      success : function(responseJSON) {
        en4.seaocorepinboard.comments.setLayout();
        if(scriptJquery('#pin_comment_st_'+type+'_'+id+'_'+widget_id).length){
          setTimeout(function(){
            var commentCountHtml = scriptJquery('#comments'+'_'+type+'_'+id+'_'+widget_id).find('.comments_options span').html();
            scriptJquery('#pin_comment_st_'+type+'_'+id+'_'+widget_id).seaoset('html', commentCountHtml);
          },100);
        }
      }
    }), {
      'element' : scriptJquery('#comments'+'_'+type+'_'+id+'_'+widget_id),
      "force":true
    });
  },
  comment : function(type, id, body, widget_id){

    en4.core.request.send(scriptJquery.ajax({
      url : en4.core.baseUrl + 'seaocore/pin-board-comment/create',
      dataType:'json',
      method : 'post',
      data : {
        format : 'json',
        type : type,
        id : id,
        body : body,
        widget_id : widget_id
      },
      success : function(responseJSON) {
        en4.seaocorepinboard.comments.setLayout(); 
        if(scriptJquery('#pin_comment_st_'+type+'_'+id+'_'+widget_id).length){
          setTimeout(function(){
            var commentCountHtml = scriptJquery('#comments'+'_'+type+'_'+id+'_'+widget_id).find('.comments_options span')[0].html();
            scriptJquery('#pin_comment_st_'+type+'_'+id+'_'+widget_id).seaoset('html', commentCountHtml);
          },100);
        }
      }
    }), {
      'element' : scriptJquery('#comments'+'_'+type+'_'+id+'_'+widget_id),
      "force":true
    });
  },
  deleteComment : function(type, id, comment_id,widget_id) {
    if( !confirm(en4.core.language.translate('Are you sure you want to delete this?')) ) {
      return;
    }
    en4.core.request.send(scriptJquery.ajax({
      url : en4.core.baseUrl + 'seaocore/comment/delete',
      dataType : 'json',
      method : 'post',
      data : {
        format : 'json',
        type : type,
        id : id,
        comment_id : comment_id
      },
      complete: function() {
        scriptJquery('.comment-' + comment_id).each(function(element){
          try {
            var commentParent=scriptJquery(this).closest('.comments');
            var commentCount = commentParent.find('.comments_options span');
            var m = commentCount.html().match(/\d+/);
            var newCount = ( parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 1 ? parseInt(m[0]) - 1 : 0 );
            scriptJquery(this).remove();
            commentCount.seaoset('html', commentCount.html().replace(m[0], newCount));
            var pinStComment=commentParent.attr('id').replace('comments', 'pin_comment_st');
            if(scriptJquery( '#' + pinStComment).length){
              var commentCountHtml = commentCount.html();
              scriptJquery( '#' + pinStComment).seaoset('html', commentCountHtml);
            }
          } catch( e ) {}
        });
        en4.seaocorepinboard.comments.setLayout(); 
      }
    }));
  },
  like : function(type, id, widget_id, comment_id) {
    en4.core.request.send(scriptJquery.ajax({
      url : en4.core.baseUrl + 'seaocore/pin-board-comment/like',
      dataType : 'json',
      method : 'post',
      data : {
        format : 'json',
        type : type,
        id : id,
        comment_id : comment_id,
        widget_id : widget_id
      }
    }), {
      'element' : scriptJquery('#comments'+'_'+type+'_'+id+'_'+widget_id),
      "force":true
    });
  },
  
  unlike : function(type, id, widget_id, comment_id) {
    en4.core.request.send(scriptJquery.ajax({
      url : en4.core.baseUrl + 'seaocore/pin-board-comment/unlike',
      dataType : 'json',
      method : 'post',
      data : {
        format : 'json',
        type : type,
        id : id,
        comment_id : comment_id,
        widget_id : widget_id
      }
    }), {
      'element' : scriptJquery('#comments'+'_'+type+'_'+id+'_'+widget_id),
      "force":true
    });
  }
};

en4.seaocorepinboard.likes = {
  like : function(type, id) {
    en4.core.request.send(scriptJquery.ajax({
      url : en4.core.baseUrl + 'seaocore/comment/like',
      dataType : 'json',
      method : 'post',
      data : {
        format : 'json',
        type : type,
        id : id
      },
      success : function(responseJSON) {
        if( $type(responseJSON) == 'object' && $type(responseJSON.status)) {
          scriptJquery('.'+type+'_'+id+'like_link').css('display','none');
          scriptJquery('.'+type+'_'+id+'unlike_link').css('display','block');
        }
        
        scriptJquery('.pin_like_st_'+type+'_'+id).each(function(likeCount){
          try {
            var m = likeCount.html().match(/\d+/);
            var newCount = ( parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 0 ? parseInt(m[0]) + 1 : 1 );
            likeCount.seaoset('html', likeCount.html().replace(m[0], newCount));
          } catch( e ) {}
        });
      }
    }), {
      'element' : scriptJquery('#comments'+'_'+type+'_'+id)
    //      "force":true
    });
  },

  unlike : function(type, id) {
    en4.core.request.send(scriptJquery.ajax({
      url : en4.core.baseUrl + 'seaocore/comment/unlike',
      dataType : 'json',
      method : 'post',
      data : {
        format : 'json',
        type : type,
        id : id
      },
      success : function(responseJSON) {
        if( $type(responseJSON) == 'object' && $type(responseJSON.status)  ) {
          scriptJquery('.'+type+'_'+id+'unlike_link').css('display','none');
          scriptJquery('.'+type+'_'+id+'like_link').css('display','block');
        }
        scriptJquery('.pin_like_st_'+type+'_'+id).each(function(likeCount){
          try {
            var m = likeCount.html().match(/\d+/);
            var newCount = ( parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 1 ? parseInt(m[0]) - 1 : 0 );
            likeCount.seaoset('html', likeCount.html().replace(m[0], newCount));
          } catch( e ) {}
        });
      }
    }), {
      'element' : scriptJquery('#comments'+'_'+type+'_'+id)
    //      "force":true
    });
  }
};

