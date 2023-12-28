
function inArray(needle, haystack) {
  var length = haystack.length;
  for(var i = 0; i < length; i++) {
    if(haystack[i] == needle) return true;
  }
  return false;
} 
en4.module = {
  globalSettingsUrl: function(name) {
    var settingsModule = name;
    if(inArray(name, ['siteeventticket', 'siteeventrepeat', 'siteeventemail', 'siteeventinvite', 'siteeventdocument'])) {
      settingsModule = 'siteevent';
    }
    if(name == 'sitereviewlistingtype' || name == 'sitereviewpaidlisting') {
      settingsModule = 'sitereview';
    }
    if(name == 'sitevideointegration') {
      settingsModule = 'sitevideo';
    }
    if(name == 'documentintegration') {
      settingsModule = 'document';
    }
    var url = en4.core.baseUrl + "admin/" + settingsModule + "/settings";
    if(settingsModule == 'sitegroupinvite' || settingsModule == 'sitepageinvite') {
      url = en4.core.baseUrl + "admin/" + settingsModule + "/global/global";
    }
    if(settingsModule == 'facebooksefeed') {
      url = en4.core.baseUrl + "admin/" + settingsModule + "/settings/feedsetting";
    }
    if(settingsModule == 'poke') {
      url = en4.core.baseUrl + "admin/" + settingsModule + "/pokesettings";
    }
    return url;  
  }
}; 

var selectedItemLength = 0;
function upgradeSelectedSeaoPlugins() {
  var ready = confirm("are you ready to upgarde?");
  if(ready == true) {
    elements = $$('.seaocore_multi-upgrade:checked');
        
    if(elements.length < 1) return ;
    selectedItemLength = elements.length - 1;

    $('seaocore_light').style.display = 'block';
    $('seaocore_fade').style.display = 'block';
    sendUpgradeRequestSeao(elements[selectedItemLength]);
        
  }
}

function sendUpgradeRequestSeao(item){
            
  var request = new Request.JSON({
    'url' : item.get('data-url'),
    'method':'post',
    'data' : {
      'format' : 'json',
    },
    'onComplete' : function(responseJSON) {
      if (selectedItemLength < 1 ) {
        window.location = en4.core.baseUrl + 'install/manage/select';
      } else {
        selectedItemLength--;
        sendUpgradeRequestSeao(elements[selectedItemLength]);
      }
    }
  });
  request.send();
}

function checkUncheckSeaoAll(element){
  if(element.checked) {
    $$('.seaocore_multi-upgrade').each(function(item, index){ 
      item.checked = true;
    });    
  }else {
    $$('.seaocore_multi-upgrade').each(function(item, index){ 
      item.checked = false;
    });
  }   
}
