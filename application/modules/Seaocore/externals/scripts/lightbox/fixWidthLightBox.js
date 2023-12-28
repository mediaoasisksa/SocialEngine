en4.core.runonce.add(function() {
  if(typeof en4.core.staticBaseUrl == 'undefined')
    en4.core.staticBaseUrl ='';
  //HREF
  var href = '';  
  //ONLY OPEN LIGHTBOX WHEN FLAG IS ONE 
  if( flag != 0 ) {
    if(scriptJquery('.thumbs_photo').length) {
      scriptJquery('.thumbs_photo').each(function(el){ 
        var addEventClickFlag=true;
        if(el.prop('onclick') != null)
          addEventClickFlag=false;
        
        var parentEl=el.get('.generic_layout_container');
        if(parentEl) {
          parentEl.attr('class').split(' ').each(function(className){
            className = className.trim();
            
            if( className.match(/sitealbum/) ) {   
              addEventClickFlag=false;
            }
          });
        }
        if(addEventClickFlag){  
          el.off('click').addEventListener('click', function(e) { 
            var href = this.href;  
            if( href.match(/photo_id/) ) {   
              e.stop();
              openSeaocoreLightBox(href);
            }
          }); 
        }
      });
    }  
  }

  //SHOW IMAGES IN THE LIGHTBOX FOR THE ACTIVITY FEED
  if( activityfeed_lightbox != 0 ) {
    //DISPLAY ACTIVITY FEED IMAGES IN THE LIGHTBOX FOR THE GROUP 
    addSEAOPhotoOpenEvent(Array('feed_attachment_group_photo','feed_attachment_event_photo','feed_attachment_sitepage_photo','feed_attachment_list_photo','feed_attachment_recipe_photo','feed_attachment_sitepagenote_photo','feed_attachment_album_photo','feed_attachment_sitebusiness_photo','feed_attachment_sitebusinessnote_photo','feed_attachment_sitegroup_photo','feed_attachment_sitegroupnote_photo','feed_attachment_sitegroupevent_photo','feed_attachment_sitebusinessevent_photo','feed_attachment_sitepageevent_photo'));
  }
});

function addSEAOPhotoOpenEvent(classnames){
  classnames.each(function(classname) {    
    classname="."+classname;
    if(scriptJquery(classname)) {
      scriptJquery(classname).each(function(el) {       
        if(el.find('.thumb_profile')) {
          el.find('.thumb_profile').off('click').addEventListener('click', function(e) {
            e.stop();     
            href = openLightboxforActivityFeedHREF(el);
            openSeaocoreLightBox(href);
          });   
        } else {
          el.find('.thumb_normal').off('click').addEventListener('click', function(e) {
            e.stop();     
            href = openLightboxforActivityFeedHREF(el);
            openSeaocoreLightBox(href);
          });
        }

      });
    }
  });
}
/*  
  RETURN HREF
*/
function openLightboxforActivityFeedHREF(spanElement) {
  if(spanElement.find('.feed_item_thumb')) {
    href = spanElement.find('.feed_item_thumb');  
  } 
  else {
    var tagName = spanElement.getElementsByTagName('a'); 
    for (i = 0; i <= tagName.length-1; i++)
    {
      href = tagName[i];
    } 
  }
  return href;
}

/*  
  OPEN IMAGES IN LIGHTBOX
*/
var lightbox_communityads_hidden;
var locationHref = window.location.href,defaultLoad = true,defaultSEAOLBAlbumPhotoContent = '',fullmode_photo=false,addAgainscrollFalg=true,rightSidePhotoContent,canClosePhotoLB=true,scrollPosition = {
  left:0,
  top:0
},loadedAllPhotos = '',contentPhotoSizeSEAO={
  width:0,
  height:0
};


var createDefaultContentAdvLBSEAO=function(element){ 
  scriptJquery.crtEle('input', {
    'id' : 'canReloadSeaocore',
    'type' : 'hidden',
    'value' :0      
  }).appendTo(element);
  scriptJquery.crtEle('div', {      
    'class' : 'photo_lightbox_overlay'      
  }).appendTo(element);
  scriptJquery.crtEle('div', {
    'id' : 'photo_lightbox_close',
    'class' : 'photo_lightbox_close',
    'onclick' : "closeSEAOLightBoxAlbum()",
    'title':en4.core.language.translate("Press Esc to Close")        
  }).appendTo(element);
   
  var photoContentDiv = scriptJquery.crtEle('div', {
    'id' : 'white_content_default_sea_lightbox',
    'class' : 'photo_lightbox_content_wrapper'         
  });     
  var photolbCont= scriptJquery.crtEle('div', {      
    'class' : 'photo_lightbox_cont'         
  }).appendTo(photoContentDiv);
  if(en4.orientation=='ltr'){   
    var photolbLeft = scriptJquery.crtEle('div', {
      'id' : 'photo_lightbox_seaocore_left',
      'class' : 'photo_lightbox_left',
      'styles' : {
        'right' : '1px'       
      }
    }).appendTo(photolbCont);
  }else{
    var photolbLeft = scriptJquery.crtEle('div', {
      'id' : 'photo_lightbox_seaocore_left',
      'class' : 'photo_lightbox_left',
      'styles' : {
        'left' : '1px'       
      }
    }).appendTo(photolbCont);
  }
    
  var photolbLeftTable = scriptJquery.crtEle('table', {
    'width' : '100%',
    'height' : '100%'
  }).appendTo(photolbLeft); 
  var photolbLeftTableTr = scriptJquery.crtEle('tr', {    
    }).appendTo(photolbLeftTable); 
      
  var photolbLeftTableTrTd = scriptJquery.crtEle('td', {
    'width' : '100%',
    'height' : '100%',
    'valign':'middle'
  }).appendTo(photolbLeftTableTr);
    
  scriptJquery.crtEle('div', {
    'id' : 'media_image_div_seaocore',
    'class' : 'photo_lightbox_image'      
  }).appendTo(photolbLeftTableTrTd);
  scriptJquery.crtEle('div', {     
    'class' : 'lightbox_btm_bl'     
  }).appendTo(photoContentDiv);
  photoContentDiv.appendTo(element);    
  photoContentDiv.on('click', function(event) {
    event.stopPropagation();
  });
};

function openSeaocoreLightBox(href){ 
  if(!scriptJquery("#white_content_default_sea_lightbox").length){
    createDefaultContentAdvLBSEAO(scriptJquery("#seaocore_photo_lightbox"));    
  }

  if(document.getElementById('seaocore_photo_lightbox'))
    document.getElementById('seaocore_photo_lightbox').style.display = 'block';
  if(scriptJquery('#arrowchat_base').length)
    scriptJquery('#arrowchat_base').css( 'display', 'none' );
  if(scriptJquery('#wibiyaToolbar').length)
    scriptJquery('#wibiyaToolbar').css( 'display', 'none' );
  scrollPosition['top']=window.getScrollTop();
  scrollPosition['left']=window.getScrollLeft();
  setHtmlScroll("hidden");
  
  
  getSEAOCorePhoto(href,0);
}
/*  
  GET NEXT AND PREVIOUS PHOTO
*/

function photopaginationSocialenginealbum(href,params,imagepath) { 
  getSEAOCorePhoto(href,1,params,imagepath);
}
function getSEAOCorePhoto(href,isajax,params,imagepath){
 
  if (history.replaceState) {
    history.replaceState( {}, document.title, href );
  } else {
    window.location.hash = href;
  }
  if(isajax==0){
    document.getElementById('media_image_div_seaocore').innerHTML = "&nbsp;<img class='photo_lightbox_loader' src='"+en4.core.staticBaseUrl+'application/modules/Seaocore/externals/images/icons/loader-large.gif'+"'  />";
  }else{
    scriptJquery(".lightbox_btm_bl").each(function(el){
      el.innerHTML="<center><img src='"+en4.core.staticBaseUrl+"application/modules/Seaocore/externals/images/icons/loader-large.gif' style='height:30px;' /> </center>";
    }); 
  } 
    if (isajax) 
  document.getElementById('media_image_div_seaocore').innerHTML = "&nbsp;<img class='lightbox_photo' src=" + imagepath + " style='max-width: " + contentPhotoSizeSEAO['width'] + "px; max-height: " + contentPhotoSizeSEAO['height'] + "px;'  />";
  var remove_extra = 2;
  contentPhotoSizeSEAO['height'] = scriptJquery("#photo_lightbox_seaocore_left").getCoordinates().height - remove_extra;
  if(isajax == 0 )
    remove_extra = remove_extra + 289;
  contentPhotoSizeSEAO['width'] = scriptJquery("#photo_lightbox_seaocore_left").getCoordinates().width - remove_extra;

  addAgainscrollFalg = true;
  en4.core.request.send(scriptJquery.ajax({      
    method : 'get',
    'url' : href, 
    'data' : scriptJquery.extend(params,{
      dataType : 'html',
      'lightbox_type' : 'photo',
      //  module_name : modulename,
      // tab : tab_id,
      is_ajax_lightbox : isajax
    }),
    success : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      if(scriptJquery('#white_content_default_sea_lightbox').length){
        scriptJquery('#white_content_default_sea_lightbox').html( responseHTML );        
        switchFullModePhotoSEAO(fullmode_photo);
      }
      
      Smoothbox.bind(scriptJquery("#display_current_location"));
      en4.core.runonce.trigger();
    }
  }), {
    "force":true
  });
}



/*  
  CLOSE LIGHTBOX
*/
var closeSEAOLightBoxAlbum = function()
{
  if(fullScreenApi.isFullScreen()){
    fullScreenApi.cancelFullScreen()
  } else{
    defaultLoad = true;
    document.getElementById('seaocore_photo_lightbox').style.display = 'none';
    setHtmlScroll("auto");
    window.scroll(scrollPosition['left'],scrollPosition['top']); // horizontal and vertical scroll targets
    if(scriptJquery('#arrowchat_base').length)
      scriptJquery('#arrowchat_base').css( 'display', 'block' );
    if(scriptJquery('#wibiyaToolbar').length)
      scriptJquery('#wibiyaToolbar').css( 'display', 'block' );
    if (history.replaceState)
      history.replaceState( {}, document.title, locationHref );
    else{  
      window.location.hash = "0";
    }
    if($type(keyDownEventsSEAOCorePhoto))
      scriptJquery(document).off("keydown",keyDownEventsSEAOCorePhoto);
    if($type(keyUpLikeEventSEAOCorePhoto))
      scriptJquery(document).off("keyup" , keyUpLikeEventSEAOCorePhoto);  
    if(document.getElementById('canReloadSeaocore').value == 1){
      window.location.reload(true);
    }
    loadedAllPhotos = '';
    document.getElementById('seaocore_photo_lightbox').empty();
    fullmode_photo = false;
  }
};


/*  
  SET HTML SCROLLING
*/
function setHtmlScroll(cssCode) {
  scriptJquery('#html').css('overflow',cssCode);
}

/*  
  SET IMAGE SCROLLING
*/
function setImageScrollAlbum(cssCode) {
  scriptJquery('.photo_lightbox_white_content_wrapper').css('overflow',cssCode);
}


/*  
  OPEN URLS IN SMOOTHBOX
*/
function showSmoothBox(url)
{
  Smoothbox.open(url);
  parent.Smoothbox.close;
}
function saveEditDescriptionPhotoSEAO(photo_id,resourcetype)
{
    
  var str = document.getElementById('editor_seaocore_description').value.replace('/\n/g','<br />');
  var str_temp = document.getElementById('editor_seaocore_description').value;
   
  if(document.getElementById('seaocore_description_loading'))
    document.getElementById('seaocore_description_loading').style.display="";
  document.getElementById('edit_seaocore_description').style.display="none";
  en4.core.request.send(scriptJquery.ajax({
    url :en4.core.baseUrl +'seaocore/photo/edit-description',
    data : {
      dataType : 'html',
      text_string : str_temp,
      photo_id : photo_id,
      resource_type : resourcetype
    },
    success : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      if(str=='')
        str_temp=en4.core.language.translate('Add a caption');
      document.getElementById('seaocore_description').innerHTML=str_temp.replace(/\n/gi, "<br /> \n");
      showeditDescriptionSEAO();
    }
  }), {
    "force":true
  });
}
/*  
 EDIT THE DESCRIPTION
*/
function showeditDescriptionSEAO(){
  if(document.getElementById('edit_seaocore_description')){
    if(document.getElementById('link_seaocore_description').style.display=="block"){
      document.getElementById('link_seaocore_description').style.display="none";
      document.getElementById('edit_seaocore_description').style.display="block";
      scriptJquery( '#' + 'editor_seaocore_description').focus();
    } else{
      document.getElementById('link_seaocore_description').style.display="block";
      document.getElementById('edit_seaocore_description').style.display="none";
    }

    if(document.getElementById('seaocore_description_loading'))
      document.getElementById('seaocore_description_loading').style.display="none";
  }
}

/*  
 EDIT THE TITLE
*/
function showeditPhotoTitleSEAO(){
  if(document.getElementById('edit_seaocore_title')){
    if(document.getElementById('link_seaocore_title').style.display=="block"){
      document.getElementById('link_seaocore_title').style.display="none";
      document.getElementById('edit_seaocore_title').style.display="block";
      scriptJquery('#editor_seaocore_title').focus();
    } else{
      document.getElementById('link_seaocore_title').style.display="block";
      document.getElementById('edit_seaocore_title').style.display="none";
    }

    if(document.getElementById('seaocore_title_loading'))
      document.getElementById('seaocore_title_loading').style.display="none";
  }
}

function saveEditTitlePhotoSEAO(photo_id,resourcetype)
{
   
  var str = document.getElementById('editor_seaocore_title').value.replace('/\n/g','<br />');
  var str_temp = document.getElementById('editor_seaocore_title').value;   
  if(document.getElementById('seaocore_title_loading'))
    document.getElementById('seaocore_title_loading').style.display="";
  document.getElementById('edit_seaocore_title').style.display="none";
  en4.core.request.send(scriptJquery.ajax({
    url :en4.core.baseUrl+'seaocore/photo/edit-title',
    data : {
      dataType : 'html',
      text_string : str_temp,
      photo_id : photo_id,
      resource_type : resourcetype
    },
    success : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      if(str=='')
        str_temp=en4.core.language.translate('Add a title');
      document.getElementById('seaocore_title').innerHTML=str_temp;
      showeditPhotoTitleSEAO();
    }
  }), true);
}  

//---------------------------------------------------------
var // Close the All Photo Contener
closeAllPhotoContener=function(){  
  scriptJquery("#all_photos").css( 'height', "0px" );
  scriptJquery("#close_all_photos").css( 'height', "0px" );
  scriptJquery("#close_all_photos_btm").css( 'height', "0px" );
},
// View all photos of the album in bottom
showAllSEAOPhotoContener = function(subjectguid,photo_id,count_photo){
  var onePhotoSizeW = 144,onePhotoSizeH = 112;
  heightContent = onePhotoSizeH + 60;
  var inOneRow = Math.ceil((window.getSize().x/(onePhotoSizeW+40)));
  if(count_photo > inOneRow){    
    heightContent = heightContent + onePhotoSizeH -2;    
  }
 
  scriptJquery("#all_photos").css( 'height', heightContent )+"px";
  scriptJquery("#close_all_photos").css( 'height', "100%" ); 
  scriptJquery("#close_all_photos_btm").css( 'height', "60px" ); 
  scriptJquery("#photos_contener").css("max-height",(heightContent-40)+"px")
  if(loadedAllPhotos !=''){
    scriptJquery("#photos_contener").empty();
    scriptJquery(loadedAllPhotos).appendTo(scriptJquery("#photos_contener"));
    onclickPhotoThumb(scriptJquery("#lb-all-thumbs-photo-" + photo_id)); 
    if(addAgainscrollFalg) {
      SEAOMooVerticalScroll('main_photos_contener', 'photos_contener', {} );
      addAgainscrollFalg = false;
    }
  }else{
    en4.core.request.send(scriptJquery.ajax({
      url : en4.core.baseUrl+'seaocore/photo/get-all-photos',
      data : {
        dataType : 'html',
        subjectguid : subjectguid
      // photo_id : photo_id
      },
      success : function(responseHTML) {
        scriptJquery("#photos_contener").empty();
        loadedAllPhotos=responseHTML;
        scriptJquery(responseHTML).appendTo(scriptJquery("#photos_contener"));
        onclickPhotoThumb(scriptJquery("#lb-all-thumbs-photo-"+photo_id)); 
        SEAOMooVerticalScroll('main_photos_contener', 'photos_contener',{});
        addAgainscrollFalg=false;
      }
    }), {
      "force":true
    });
  }
},
// Selected the Thumb
onclickPhotoThumb =function (element){
  if( element.prop('tagName').toLowerCase() == 'a' ) {
    element = element.closest('li');
  }
  var myContainer = element.closest('.lb_photos_contener').parent(); 
  myContainer.find('ul > li').removeClass('sea_val_photos_thumbs_selected');          
  element.addClass('sea_val_photos_thumbs_selected');     
   
},
showPhotoToggleContent=function (element_id){
  var el = scriptJquery(element_id);
  el.toggleClass('sea_photo_box_open');
  el.toggleClass('sea_photo_box_closed');
},// Hide and Show Right Side Box
switchFullModePhotoSEAO=function(fullmode){
  if(!scriptJquery("#full_screen_display_captions_on_image").length)
    return;
  if(fullmode){
    fullScreenApi.requestFullScreen(document.body);
    if(scriptJquery("#photo_owner_lb_fullscreen").length)
      scriptJquery("#photo_owner_lb_fullscreen").css( 'display', 'block' );
    if(scriptJquery("#photo_owner_titile_lb_fullscreen").length)
      scriptJquery("#photo_owner_titile_lb_fullscreen").css( 'display', 'block' );
    if(scriptJquery("#photo_owner_titile_lb_fullscreen_sep").length)
      scriptJquery("#photo_owner_titile_lb_fullscreen_sep").css( 'display', 'block' );
    scriptJquery("#full_screen_display_captions_on_image").css( 'display', 'block' );
    scriptJquery("#photo_lightbox_right_content").css( 'width', '1px' ); 
    scriptJquery("#photo_lightbox_right_content").css( 'visibility', 'hidden' ); 
    if(en4.orientation=='ltr')
      scriptJquery("#photo_lightbox_seaocore_left").css( 'right', '1px' );
    else
      scriptJquery("#photo_lightbox_seaocore_left").css( 'left', '1px' );
    scriptJquery("#full_mode_photo_button").css( 'display', 'none' );
    scriptJquery("#comment_count_photo_button").css( 'display', 'block' ); 
    if(scriptJquery("#full_screen_display_captions_on_image_dis").length){      
      (function(){
        if(!scriptJquery("#media_photo").length)return;
        var width_ln=  scriptJquery("#media_photo").getCoordinates().width;
        var total_char=2 *(width_ln/6).toInt();
        if(total_char <= 100 ) total_char=100;
        var str = scriptJquery("#full_screen_display_captions_on_image_dis").innerHTML;
        if(str.length >total_char){
          scriptJquery("#full_screen_display_captions_on_image_dis").html( str.substr(0,(total_char-3))+"..." );
        }
      }).delay(50);
    }
  }else{     
    if(scriptJquery("#photo_owner_lb_fullscreen").length)
      scriptJquery("#photo_owner_lb_fullscreen").css( 'display', 'none' );
    if(scriptJquery("#photo_owner_titile_lb_fullscreen").length)
      scriptJquery("#photo_owner_titile_lb_fullscreen").css( 'display', 'none' );
    if(scriptJquery("#photo_owner_titile_lb_fullscreen_sep").length)
      scriptJquery("#photo_owner_titile_lb_fullscreen_sep").css( 'display', 'none' );
    scriptJquery("#full_screen_display_captions_on_image").css( 'display', 'none' );
    scriptJquery("#photo_lightbox_right_content").css( 'width', '300px' );
    scriptJquery("#photo_lightbox_right_content").css( 'visibility', 'visible' ); 
    if(en4.orientation=='ltr')
      scriptJquery("#photo_lightbox_seaocore_left").css( 'right', '300px' );
    else
      scriptJquery("#photo_lightbox_seaocore_left").css( 'left', '300px' );
    scriptJquery("#full_mode_photo_button").css( 'display', 'block' );
    scriptJquery("#comment_count_photo_button").css( 'display', 'none' );        
  }
    
  fullmode_photo = fullmode; 
  contentPhotoSizeSEAO['height'] = scriptJquery("#photo_lightbox_seaocore_left").getCoordinates().height -2;      
  contentPhotoSizeSEAO['width'] = scriptJquery("#photo_lightbox_seaocore_left").getCoordinates().width - 2;    
  setPhotoContentSEAO();
}, 

setPhotoContentSEAO =function(){
  if(scriptJquery("#media_photo").length){
      
    scriptJquery("#media_photo").css("max-width",contentPhotoSizeSEAO['width'] + "px");
    scriptJquery("#media_photo").css("max-height",contentPhotoSizeSEAO['height'] + "px");
    scriptJquery("#media_photo_next").css("max-width",contentPhotoSizeSEAO['width'] + "px");
    scriptJquery("#media_photo_next").css("max-height",contentPhotoSizeSEAO['height'] + "px");  
    setTimeout("getTaggerInstanceSEAO()",1250);
  }
}; 
// ADD Fullscreen api
(function() {  
  var api = {  
    supportsFullScreen: false,  
    isFullScreen: function() {
      return false;
    },  
    requestFullScreen: function() {},  
    cancelFullScreen: function() {},  
    fullScreenEventName: '',  
    prefix: ''  
  },  
    
  browserPrefixes = 'webkit moz o ms khtml'.split(' ');  
    
  // Check for native support.  
  if (typeof document.cancelFullScreen != 'undefined') {  
    api.supportsFullScreen = true;  
  } else {  
    // Check for fullscreen support by browser prefix.  
    for (var i = 0, il = browserPrefixes.length; i < il; i++ ) {  
      api.prefix = browserPrefixes[i];  
      functionName = api.prefix + 'CancelFullScreen';  
        
      if (typeof document[functionName] != 'undefined') {  
        api.supportsFullScreen = true;  
        break;  
      }  
    }  
  }  
    
  // Update methods.  
  if (api.supportsFullScreen) {  
    api.fullScreenEventName = api.prefix + 'fullscreenchange';  
      
    api.isFullScreen = function() {  
      switch (this.prefix) {  
        case '':
          return document.fullScreen;  
        case 'webkit':
          return document.webkitIsFullScreen;  
        default:
          return document[this.prefix + 'FullScreen'];  
      }  
    }  
    api.requestFullScreen = function(el) {  
      switch (this.prefix) {  
        case '':
          return el.requestFullScreen();
        case 'webkit':
          /* @TODO:: INPUT KEYS (A-I) NOT WORKING*/
          return /*el.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT)*/;
        default:
          return el[this.prefix + 'RequestFullScreen']();
      } 

    }  
    api.cancelFullScreen = function(el) {  
      if (this.prefix === '') {  
        return document.cancelFullScreen();  
      } else {  
        return document[this.prefix + 'CancelFullScreen']();  
      }  
    }  
  }  
    
  // Export api.  
  window.fullScreenApi = api;  
})();  
  
if(fullScreenApi.supportsFullScreen===true){
  document.addEventListener(fullScreenApi.fullScreenEventName, function(e) {
    if(document.getElementById('seaocore_photo_lightbox').style.display != 'block')
      return;
    switchFullModePhotoSEAO(fullScreenApi.isFullScreen());
    var html_titile=en4.core.language.translate("Press Esc to Close");
    if(fullScreenApi.isFullScreen()){
      html_titile=en4.core.language.translate("Press Esc to exit Full-screen");
    }
    scriptJquery("#photo_lightbox_close").title=html_titile;
    resetPhotoContentSEAO();
    if(typeof rightSidePhotoContent != 'undefined')
      rightSidePhotoContent.update();
  },true);
}
  
var resetPhotoContentSEAO=function(){
  if(scriptJquery('#ads').length){
    if(scriptJquery('#ads_hidden').length){
      if(scriptJquery('#ads').getCoordinates().height < 30){
        scriptJquery('#ads').empty();
      }
      adsinnerHTML= scriptJquery('#ads').innerHTML;      
    }else{
      scriptJquery('#ads').html( adsinnerHTML );
    }
    (function(){
      if(!scriptJquery('#ads').length) return;
      scriptJquery('#ads').style.bottom="0px";
      if(scriptJquery('#photo_lightbox_right_content').getCoordinates().height < (scriptJquery('#photo_right_content').getCoordinates().height+scriptJquery('#ads').getCoordinates().height+10)){
        scriptJquery('#ads').empty();
        scriptJquery('#main_right_content_area').style.height= scriptJquery('#photo_lightbox_right_content').getCoordinates().height -2 +"px";
        scriptJquery('#main_right_content').style.height= scriptJquery('#photo_lightbox_right_content').getCoordinates().height -2 +"px";
      }else{
        scriptJquery('#main_right_content_area').style.height= scriptJquery('#photo_lightbox_right_content').getCoordinates().height - (scriptJquery('#ads').getCoordinates().height+10)+"px";
        scriptJquery('#main_right_content').style.height= scriptJquery('#photo_lightbox_right_content').getCoordinates().height - (scriptJquery('#ads').getCoordinates().height+10)+"px";
      }
    }).delay(1000);
  } 
};
var featuredPhoto=function(subject_guid)
{
  en4.core.request.send(scriptJquery.ajax({
    method : 'post',
    'url' : en4.core.baseUrl + 'sitealbum/photo/featured',
    'data' : {
      format : 'html',
      'subject' : subject_guid
    },
    success : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      if(scriptJquery('#featured_sitealbum_photo').style.display=='none'){
        scriptJquery('#featured_sitealbum_photo').style.display="";
        scriptJquery('#un_featured_sitealbum_photo').style.display="none";
      }else{
        scriptJquery('#un_featured_sitealbum_photo').style.display="";
        scriptJquery('#featured_sitealbum_photo').style.display="none";
      }
    }
  }), true);

  return false;

},

featuredpagealbumPhoto = function(photo_id)
{
  en4.core.request.send(scriptJquery.ajax({
    method : 'post',
    'url' : en4.core.baseUrl + 'sitepage/photo/featured',
    'data' : {
      format : 'html',
      'photo_id' : photo_id
    },
    success : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      if(scriptJquery('#featured_sitepagealbum_photo').style.display=='none'){
        scriptJquery('#featured_sitepagealbum_photo').style.display="";
        scriptJquery('#un_featured_sitepagealbum_photo').style.display="none";
      }else{
        scriptJquery('#un_featured_sitepagealbum_photo').style.display="";
        scriptJquery('#featured_sitepagealbum_photo').style.display="none";
      }
    }
  }), true);

  return false;
};

featuredgroupalbumPhoto = function(photo_id)
{
  en4.core.request.send(scriptJquery.ajax({
    method : 'post',
    'url' : en4.core.baseUrl + 'sitegroup/photo/featured',
    'data' : {
      format : 'html',
      'photo_id' : photo_id
    },
    success : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      if(scriptJquery('#featured_sitegroupalbum_photo').style.display=='none'){
        scriptJquery('#featured_sitegroupalbum_photo').style.display="";
        scriptJquery('#un_featured_sitegroupalbum_photo').style.display="none";
      }else{
        scriptJquery('#un_featured_sitegroupalbum_photo').style.display="";
        scriptJquery('#featured_sitegroupalbum_photo').style.display="none";
      }
    }
  }), true);

  return false;
};

featuredbusinessalbumPhoto = function(photo_id)
{
  en4.core.request.send(scriptJquery.ajax({
    method : 'post',
    'url' : en4.core.baseUrl + 'sitebusiness/photo/featured',
    'data' : {
      format : 'html',
      'photo_id' : photo_id
    },
    success : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      if(scriptJquery('#featured_sitebusinessalbum_photo').style.display=='none'){
        scriptJquery('#featured_sitebusinessalbum_photo').style.display="";
        scriptJquery('#un_featured_sitebusinessalbum_photo').style.display="none";
      } else{
        scriptJquery('#un_featured_sitebusinessalbum_photo').style.display="";
        scriptJquery('#featured_sitebusinessalbum_photo').style.display="none";
      }
    }
  }), true);

  return false;
};


/*  
  FUNCTION FOR ROTATING AND FLIPING THE IMAGES
*/
en4.photoadvlightbox= {
  rotate : function(photo_id, angle,resourcetype) {
    request = scriptJquery.ajax({
      url : en4.core.baseUrl + 'seaocore/photo/rotate',
      data : {
        format : 'json',
        photo_id : photo_id,
        angle : angle,
        resource_type : resourcetype
      },
      onComplete: function(response) {
        // Check status
        if( $type(response) == 'object' &&
          $type(response.status) &&
          response.status == false ) {
          en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
          return;
        } else if( $type(response) != 'object' ||
          !$type(response.status) ) {
          en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
          return;
        }

        // Ok, let's refresh the page I guess
        scriptJquery('#canReloadSeaocore').value=1;
        scriptJquery('#media_photo').src=response.href;
        scriptJquery('#media_photo').style.marginTop="0px";     
      }
    });
    request.send();
    return request;
  },

  flip : function(photo_id, direction,resourcetype) {
    request = scriptJquery.ajax({
      url : en4.core.baseUrl + 'seaocore/photo/flip',
      data : {
        format : 'json',
        photo_id : photo_id,
        direction : direction,
        resource_type : resourcetype
      },
      onComplete: function(response) {
        // Check status
        if( $type(response) == 'object' &&
          $type(response.status) &&
          response.status == false ) {
          en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
          return;
        } else if( $type(response) != 'object' ||
          !$type(response.status) ) {
          en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
          return;
        }

        // Ok, let's refresh the page I guess     
        scriptJquery('#canReloadSeaocore').value=1;
        scriptJquery('#media_photo').src=response.href;
        scriptJquery('#media_photo').style.marginTop="0px";
      }
    });
    request.send();
    return request;
  }
};