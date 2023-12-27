/* $Id: usercontacts.js 2010-08-17 9:40:21Z SocialEngineAddOns Copyright 2009-2010 BigStep Technologies Pvt. Ltd. $ */

var aaf_main_page_invite = false;
var fbappid;
var invite_mainpage_url;
var semoduletype = ''; 
var invitepage_id = '';
var semoduletype_id = '';
var semoduletype_type = '';
//THIS FUNCTION IS USED TO SHOW THE ALL GOOGLE CONTACTS IN PARSING MODE.DEFAULT WE ARE SHOWING ONLY THOSE CONTACTS WHICH ARE SITE MEMBERS BUT NOT USER'S FRIENDS.
function show_contacts_google (id) { 
  scriptJquery('#id_nonsite_success_mess').attr('style','display:none');
  if (scriptJquery('#id_success_frequ').length)
	   scriptJquery('#id_success_frequ').attr('style','display:none');
	scriptJquery('#show_contacts').attr('style','display:none');
	if (id == 1) { //PARAMETER ID REFERS TO THAT CHILD WINDOW IS OPEN OR NOT
		var child_window = window.open (en4.core.baseUrl + 'seaocore/usercontacts/getgooglecontacts?redirect_uri=' + invite_callbackURl ,'mywindow','width=500,height=500');
	}
	if (window.opener!= null) {
		if (id == 0) { //HERE PARAMETER ID IS USED TO DETERMINE THAT WE ARE REDIRECTED FROM THE GOOGLE APP IT SELF
			var href = window.location.href;
		  var access_token = href.split('#access_token='); //ACCESS TOKEN IS PROVIDED IN THE RETURN URL
		if (typeof access_token[1] == 'undefined' ) {
		  var token = getQuerystring('token', href);
		  var redirect_href = href;
		}
		else { 
		  var redirect_href = access_token[0];
		  var access_token = access_token[1].split('&token_type');
		  var token = access_token[0];
		  redirect_href = redirect_href + '?token=' + token;
		   
		}
			
			if (window.opener.aaf_main_page_invite)
			   window.opener.location.href = redirect_href;
			else 
			  window.opener.get_contacts_google (token);
			
			close();
		}
	}
}
//CALLING THIS FUNCTION FROM CHILD WINDOW BEFORE CLOSING THE CHILD WINDOW.WHICH GETS THE GOOGLE CONTACTS.
function get_contacts_google (token) { 
	if (token == '') {
    return;
  }
	Smoothbox.open("<div style='height:30px;'><center><b>" + en4.core.language.translate('Importing Contacts') + "</b><br /><img src='application/modules/Seaocore/externals/images/loadings.gif' alt='' /></center></div>");
	
	var postData = {
		'token' : token,
		'task' : 'get_googlecontacts',
		'moduletype': semoduletype
	};
	
	en4.core.request.send(scriptJquery.ajax( {
		url : en4.core.baseUrl + 'seaocore/usercontacts/getgooglecontacts',
		method : 'post',
		data : postData,
		success : function(responseObject)
		{ 
			scriptJquery('#network_friends').attr('style','display:block'); 
			if( scriptJquery('#skipinviterlink') ){ scriptJquery('#skipinviterlink').attr('style','display:block'); }
			scriptJquery('#show_contacts').attr('style','display:block');
			scriptJquery('#show_contacts').html( responseObject );
			window.location.hash ='show_contacts';
			Smoothbox.close();
		}
	}), {
	 'force':true
	});
}

  
//THIS FUNCTION IS USED TO SHOW THE ALL YAHOO CONTACTS IN PARSING MODE.DEFAULT WE ARE SHOWING ONLY THOSE CONTACTS WHICH ARE SITE MEMBERS BUT NOT USER'S FRIENDS.
function show_contacts_yahoo (id) {
	scriptJquery('#id_nonsite_success_mess').attr('style','display:none');
	if (scriptJquery('#id_success_frequ').length) 	   scriptJquery('#id_success_frequ').attr('style','display:none');
	scriptJquery('#show_contacts').attr('style','display:none');
	if (id == 1) { 
		var child_window = window.open (en4.core.baseUrl + 'seaocore/usercontacts/getyahoocontacts?redirect_uri=' + invite_callbackURl ,'mywindow','width=500,height=500');
	}
	
	if (window.opener!= null) {
		if (id == 0) {
		var href = window.location.href;
		var oauth_verifier = getQuerystring('oauth_verifier', href);
		if (window.opener.aaf_main_page_invite)
			 window.opener.location.href = href;
		else {
		  window.opener.get_contacts_yahoo(oauth_verifier);
		}
		close();
		}
	}
}
//CALLING THIS FUNCTION FROM CHILD WINDOW BEFORE CLOSING THE CHILD WINDOW.WHICH GETS ALL YAHOO CONTACTS.
function get_contacts_yahoo (oauth_verifier) { 
	if (oauth_verifier == '') {
    return;
  }
	Smoothbox.open("<div style='height:30px;'><center><b>" + en4.core.language.translate('Importing Contacts') + "</b><br /><img src='application/modules/Seaocore/externals/images/loadings.gif' alt='' /></center></div>");
	var postData = {
		'oauth_verifier' : oauth_verifier,
		'task' : 'get_yahoocontact',
		'moduletype': semoduletype
	};

	en4.core.request.send(scriptJquery.ajax({
		url : en4.core.baseUrl + 'seaocore/usercontacts/getyahoocontacts',
		method : 'post',
		data : postData,
		success : function(responseObject)
		{ 
			scriptJquery('#network_friends').attr('style','display:block');
			if( scriptJquery('#skipinviterlink') ){ scriptJquery('#skipinviterlink').attr('style','display:block'); }
			scriptJquery('#show_contacts').attr('style','display:block');
			scriptJquery('#show_contacts').html( responseObject );
			window.location.hash ='show_contacts';
			Smoothbox.close();
		}
	}), {
	 'force':true
	});
}



//THIS FUNCTION IS USED TO SHOW THE ALL YAHOO CONTACTS IN PARSING MODE.DEFAULT WE ARE SHOWING ONLY THOSE CONTACTS WHICH ARE SITE MEMBERS BUT NOT USER'S FRIENDS.
function show_contacts_linkedin (id) {
	scriptJquery('#id_nonsite_success_mess').attr('style','display:none');
	if (scriptJquery('#id_success_frequ').length) 	   scriptJquery('#id_success_frequ').attr('style','display:none');
	scriptJquery('#show_contacts').attr('style','display:none');
	if (id == 1) { 
		var child_window = window.open (en4.core.baseUrl + 'seaocore/usercontacts/getlinkedincontacts?redirect_uri=' + invite_callbackURl ,'mywindow','width=500,height=500');
	}

	if (window.opener!= null) {
		if (id == 0) {
		var href = window.location.href;
		var oauth_verifier = getQuerystring('oauth_verifier', href);
		if (window.opener.aaf_main_page_invite)
			 window.opener.location.href = href;
		else { 
		  window.opener.get_contacts_linkedin(oauth_verifier);
		}
		close();
		}
	}
}
//CALLING THIS FUNCTION FROM CHILD WINDOW BEFORE CLOSING THE CHILD WINDOW.WHICH GETS ALL YAHOO CONTACTS.
function get_contacts_linkedin (oauth_verifier) { 
	if (oauth_verifier == '') {
    return;
  }
	Smoothbox.open("<div style='height:30px;'><center><b>" + en4.core.language.translate('Importing Contacts') + "</b><br /><img src='application/modules/Seaocore/externals/images/loadings.gif' alt='' /></center></div>");
	var postData = {
		'oauth_verifier' : oauth_verifier,
		'task' : 'get_yahoocontact',
		'moduletype': semoduletype
	};

	en4.core.request.send(scriptJquery.ajax({
		url : en4.core.baseUrl + 'seaocore/usercontacts/getlinkedincontacts',
		method : 'post',
		data : postData,
		success : function(responseObject)
		{ 
			scriptJquery('#network_friends').attr('style','display:block');
			if( scriptJquery('#skipinviterlink') ){ scriptJquery('#skipinviterlink').attr('style','display:block'); }
			scriptJquery('#show_contacts').attr('style','display:block');
			scriptJquery('#show_contacts').html( responseObject );
			window.location.hash ='show_contacts';
			Smoothbox.close();
		}
	}), {
	 'force':true
	});
}
  
  
//THIS FUNCTION IS USED TO SHOW THE ALL WINDOW LIVE CONTACTS IN PARSING MODE.DEFAULT WE ARE SHOWING ONLY THOSE CONTACTS WHICH ARE SITE MEMBERS BUT NOT USER'S FRIENDS.
function show_contacts_windowlive (id) {
	scriptJquery('#id_nonsite_success_mess').attr('style','display:none');
	if (scriptJquery('#id_success_frequ').length) 	   scriptJquery('#id_success_frequ').attr('style','display:none');
	scriptJquery('#show_contacts').attr('style','display:none');
	if (id == 1) { 
		var child_window = window.open (en4.core.baseUrl + 'seaocore/usercontacts/getwindowlivecontacts?redirect_uri=' + invite_callbackURl ,'mywindow','width=900,height=900');
    
    if (window.parent.aaf_main_page_invite) { 
       var href = window.location.href;
			 window.parent.location.href = invite_callbackURl;
    }
	}
	
	if (window.opener!= null) {
		if (id == 0) {
		var href = window.location.href;
		var oauth_verifier = getQuerystring('oauth_verifier', href);
		window.opener.get_contacts_windowlive(oauth_verifier);
		close();
		}
	}
}
 
//CALLING THIS FUNCTION FROM CHILD WINDOW BEFORE CLOSING THE CHILD WINDOW.WHICH GETS ALL WINDOW LIVE CONTACTS.
function get_contacts_windowlive (oauth_verifier) {
  if (typeof oauth_verifier == 'undefined') {
    oauth_verifier = getParameterByName('oauth_verifier');    
  }
	if (oauth_verifier == '') {
    return;
  }
	Smoothbox.open("<div style='height:30px;'><center><b>" + en4.core.language.translate('Importing Contacts') + "</b><br /><img src='application/modules/Seaocore/externals/images/loadings.gif' alt='' /></center></div>");
	var postData = {
		'task' : 'get_windowcontact',
		'moduletype': semoduletype,
		'oauth_verifier' : oauth_verifier
	};

	en4.core.request.send(scriptJquery.ajax({
		url : en4.core.baseUrl + 'seaocore/usercontacts/getwindowlivecontacts',
		method : 'post',
		data : postData,
		success : function(responseObject)
		{ 
			scriptJquery('#network_friends').attr('style','display:block');
			if( scriptJquery('#skipinviterlink') ){ scriptJquery('#skipinviterlink').attr('style','display:block'); }
			scriptJquery('#show_contacts').attr('style','display:block');
			scriptJquery('#show_contacts').html( responseObject );
			window.location.hash ='show_contacts';
			Smoothbox.close();
		}
	}), {
	 'force':true
	});
}

//THIS FUNCTION IS USED TO SHOW THE ALL AOL CONTACTS IN PARSING MODE.DEFAULT WE ARE SHOWING ONLY THOSE CONTACTS WHICH ARE SITE MEMBERS BUT NOT USER'S FRIENDS.
function show_contacts_aol (id) {
	scriptJquery('#id_nonsite_success_mess').attr('style','display:none');
	if (scriptJquery('#id_success_frequ').length) 	   scriptJquery('#id_success_frequ').attr('style','display:none');
	scriptJquery('#show_contacts').attr('style','display:none');
	if (id == 1) {
		var child_window = window.open (en4.core.baseUrl + 'seaocore/usercontacts/aollogin?redirect_uri=' + invite_callbackURl ,'mywindow','width=500,height=500');
	}
	if (window.opener!= null) {
		if (id == 0) { 
		  if (window.opener.aaf_main_page_invite)
			   window.opener.location.href = window.location.href;
			else {
			   window.opener.get_contacts_aol();
			}  
			close();
		}
	}
}
//CALLING THIS FUNCTION FROM CHILD WINDOW BEFORE CLOSING THE CHILD WINDOW.WHICH GETS THE AOL CONTACTS.
function get_contacts_aol () {
	Smoothbox.open("<div style='height:30px;'><center><b>" + en4.core.language.translate('Importing Contacts') + "</b><br /><img src='application/modules/Seaocore/externals/images/loadings.gif' alt='' /></center></div>");
	var postData = {
		'task' : 'get_aolcontacts',
		'moduletype': semoduletype
	};
	
	en4.core.request.send(scriptJquery.ajax( {
		url : en4.core.baseUrl + 'seaocore/usercontacts/getaolcontacts',
		method : 'post',
		data : postData,
		success : function(responseObject)
		{ 
			scriptJquery('#network_friends').attr('style','display:block');
			if( scriptJquery('#skipinviterlink') ){ scriptJquery('#skipinviterlink').attr('style','display:block'); }
			scriptJquery('#show_contacts').attr('style','display:block');
			scriptJquery('#show_contacts').html( responseObject );
			window.location.hash ='show_contacts';
			Smoothbox.close();
		}
	}), {
	 'force':true
	});
}

//RETURNING THE QUERY STRING .
function getQuerystring(key, href) {
	key = key.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
	var regex = new RegExp("[\\?&]"+key+"=([^&#]*)");
	var qs = regex.exec(href);
	if(qs == null)
		return '';
	else
		return qs[1];
}

//THIS FUNCTION IS USED TO SET AND UNSET ALL CHECKBOX IN CASE OF ADD FRIENDS.
function checkedAll () {
	if (scriptJquery('#select_all').checked)
		checked = true;
	else
		checked = false;
	var total_contacts = scriptJquery('#total_contacts').value;

	for (var i =1; i <= total_contacts; i++) 
	{
		scriptJquery('#contact_' + i).checked = checked;
	}
}

//SENDING USER SELECTED USERS TO ADD AS A FRIEND REQUEST.
function sendFriendRequests() {
	var sitemembers = new Array ();
	var checked = false;
	var total_contacts = scriptJquery('#total_contacts').value;
	for (var i =1; i <= total_contacts; i++) 
	{
		if (scriptJquery('#contact_' + i).checked) {
			checked = true;
			sitemembers [i] = scriptJquery('#contact_' + i).value;
		}
	}
	if (checked) {
		Smoothbox.open("<div style='height:30px;'><center><b>" + en4.core.language.translate('Sending Request') + "</b><br /><img src='application/modules/Seaocore/externals/images/loadings.gif' alt='' /></center></div>");
		var postData = { 
		  'format': 'json',
			'sitemembers' : sitemembers,
			'task' : 'friend_requests'
		};
		
		en4.core.request.send(scriptJquery.ajax({
			url : en4.core.baseUrl + 'seaocore/usercontacts/addtofriend',
			method : 'post',
			data : postData,
			success : function(responseJSON)
			{ 
				
        if (scriptJquery('#id_success_frequ').length) 
				  scriptJquery('#id_success_frequ').attr('style','display:block');
				scriptJquery('#show_sitefriend').attr('style','display:none');
				scriptJquery('#show_nonsitefriends').attr('style','display:block');
				Smoothbox.close();
			}
		}));
	}
	else {
		en4.core.showError("Please select at least one friend to add");
	}
}

//THIS FUNCTION IS USED TO SET AND UNSET ALL CHECKBOX IN CASE OF INVITE FRIENDS.
function nonsitecheckedAll () {
	if (scriptJquery('#nonsiteselect_all').checked)
		checked = true;
	else
		checked = false;
	var total_contacts = scriptJquery('#nonsitetotal_contacts').value;
	for (var i =1; i <= total_contacts; i++) 
	{
		scriptJquery('#nonsitecontact_' + i).checked = checked;
	}
}

//THIS FUNCTION IS USED TO HIDE THE ADD FRINEDS LIST AND SHOWING THE NONSITE MEMBERS LIST.
function skip_addtofriends () {
  if (scriptJquery('#nonsitetotal_contacts').length) {
		var total_contacts = scriptJquery('#nonsitetotal_contacts').value;
		for (var i =1; i <= total_contacts; i++) 
		{
			scriptJquery('#nonsitecontact_' + i).checked = true;
		}
		scriptJquery('#show_sitefriend').attr('style','display:none');
		scriptJquery('#show_nonsitefriends').attr('style','display:block');
	}
	else {
   document.id_myform_temp.submit();
	}
}

//WHEN USER CLICKED ON THE SKIP BUTTON OF NONSITE MEMBERS LIST.
function skipinvites () {
	document.id_myform_temp.submit();
}

//WHEN USER CLICKS ON INVITE TO JOIN ON FIND FRIENDS PAGE
function inviteFriends (socialtype) { 
  if (scriptJquery('#id_nonsite_success_mess').length)scriptJquery('#id_nonsite_success_mess').attr('style','display:none');
  parent.Smoothbox.close();
 	var nonsitemembers = new Array ();
	var checked = false;
	var total_checked = 0;
	var total_contacts = scriptJquery('#nonsitetotal_contacts').value;
	for (var i =1; i <= total_contacts; i++) 
	{
		if (scriptJquery('#nonsitecontact_' + i).checked) {
			total_checked++; 
			checked = true;
      //SPLIT NAME AND EMAIL:     
			nonsitemembers [i] = scriptJquery('#nonsitecontact_' + i).value;
      if (socialtype == 'twitter' && total_contacts > 20) {
        scriptJquery('#user_' + scriptJquery('#nonsitecontact_' + i).value).attr('style','display:none');
        scriptJquery('#nonsitecontact_' + i).checked = false;
        count_twit_request_sent++;
      }
		}
    
    
	}

	if (checked) { 
    Smoothbox.open("<div style='height:30px;'><center><b>" + en4.core.language.translate('Sending Request') + "</b><br /><img src='application/modules/Seaocore/externals/images/loadings.gif' alt='' /></center></div>");
		var postData = {
		  'nonsitemembers' : nonsitemembers,
			'task' : 'join_network',
			'socialtype': socialtype,
			'moduletype':semoduletype,
			'invitepage_id': semoduletype_id,
      'resource_type': semoduletype_type,
      'custom_message': scriptJquery('#custom_message') !=  null ? scriptJquery('#custom_message').value : ''
		};
    
    if (typeof occurrence_id != 'undefined') {
      postData = scriptJquery.extend(postData, {'occurrence_id' : occurrence_id});
    }
		
		en4.core.request.send(scriptJquery.ajax({
			url : en4.core.baseUrl + 'seaocore/usercontacts/invitetosite',
			method : 'post',
			data : postData,
			success : function(responseObject) { 
  			if (scriptJquery('#id_success_frequ').length)scriptJquery('#id_success_frequ').attr('style','display:none');
  				if ( responseObject == en4.core.language.translate('Some problem occured while sending invitation to your followers. Please try again later.')) {
  				  scriptJquery('#show_nonsitefriends').html( responseObject );
  				}
  				else { 
            if (socialtype == 'twitter' && total_contacts > 20) {
              count_twiteuser = 0;
            }
            else {
              scriptJquery('#show_nonsitefriends').html( '' );
            }
  				  scriptJquery('#id_nonsite_success_mess').attr('style','display:block');
            
  				  if(socialtype == 'twitter' && count_twit_request_sent >= total_contacts){
              scriptJquery('#show_contacts').attr('style','display:none');
            }
            
  				}
  				if( scriptJquery('#id_csvcontacts').css( 'display' ) == 'none' ) { 
            if (scriptJquery('#skipinviterlink').length ) {
  					   scriptJquery('#skipinviterlink').attr('style','display:block');
  					}
  				}
      
				Smoothbox.close();
			}
		}));
	}
	else {
		en4.core.showError("Please select at least one friend to invite");
	}
}

//ReSend Pending Invitations:

var reSendInvite = function(socialtype, friend_id) {
  
 	var nonsitemembers = new Array ();
	var checked = true;
	var total_checked = 0;
	//var total_contacts = scriptJquery( '#' + 'nonsitetotal_contacts').value;
//	for (var i =1; i <= total_contacts; i++) 
//	{
//		if (scriptJquery( '#' + 'nonsitecontact_' + i).checked) {
//			total_checked++; 
//			checked = true;
//			nonsitemembers [i] = scriptJquery( '#' + 'nonsitecontact_' + i).value;
//		}
//	}
	if (checked) { 
    nonsitemembers [0] = 
    Smoothbox.open("<div style='height:30px;'><center><b>" + en4.core.language.translate('Sending Request') + "</b><br /><img src='application/modules/Seaocore/externals/images/loadings.gif' alt='' /></center></div>");
		var postData = {
		  'nonsitemembers' : friend_id,
			'task' : 'join_network',
			'socialtype': socialtype,
			'moduletype':semoduletype,
			'invitepage_id': invitepage_id
		};
		
		en4.core.request.send(scriptJquery.ajax({
			url : en4.core.baseUrl + 'seaocore/usercontacts/invitetosite',
			method : 'post',
			data : postData,
			success : function(responseObject) { 
  			//if (scriptJquery( '#' + 'id_success_frequ').length)scriptJquery( '#' + 'id_success_frequ').attr('style','display:none');
  				if ( responseObject == en4.core.language.translate('Some problem occured while sending invitation to your followers. Please try again later.')) {
  				  en4.core.showError(responseObject);
  				}
  				else {
  				  scriptJquery('#show_successmsg').attr('style','display:block');
  				  //scriptJquery( '#' + 'show_nonsitefriends').innerHTML = '';
  				}
//  				if( scriptJquery( '#' + 'id_csvcontacts').css( 'display' ) == 'none' ) { 
//            if (scriptJquery( '#' + 'skipinviterlink') ) {
//  					   scriptJquery( '#' + 'skipinviterlink').attr('style','display:block');
//  					}
//  				}
      
				Smoothbox.close();
			}
		}));
	}
	else {
		en4.core.showError("Please select at least one friend to invite");
	}
  
}

var fileext = false;

//HERE WE ARE UPLOADING THE FILE ON SELECTING FILE FROM BROWSE BUTTON.
function savefilepath() {
	scriptJquery('#id_nonsite_success_mess').attr('style','display:none');
	if (scriptJquery('#id_success_frequ').length) 	   scriptJquery('#id_success_frequ').attr('style','display:none');	
	scriptJquery('#id_csvformate_error_mess').attr('style','display:none');	
	scriptJquery('#show_contacts_csv').attr('style','display:none');	
	scriptJquery('#id_csvformate_error_mess').attr('style','display:none');	
	scriptJquery('#show_contacts_csv').attr('style','display:none');			
	var filename = scriptJquery('#Filedata').value;
  if (checkext(trim(filename)) == true) { 
		fileext = true;
		Smoothbox.open("<div style='height:30px;'><center><b>" + en4.core.language.translate('Uploading file') + "</b><br /><img src='application/modules/Seaocore/externals/images/loadings.gif' alt='' /></center></div>");
		//scriptJquery('#suggestion_file_upload').attr('style','display:block');
		scriptJquery('#csvmasssubmit').attr('style','display:block');
		window.setTimeout("document.csvimport.submit()",1);
	}
	else {
			fileext = false;
			scriptJquery('#id_csvformate_error_mess').attr('style','display:block');
			//scriptJquery('#suggestion_file_upload').attr('style','display:block');
			scriptJquery('#csvmasssubmit').attr('style','display:none');
	}
}

//GETTING ALL CSV FILE CONTACTS.
function getcsvcontacts (filename) { 
	
	if (fileext) {  
	  if (scriptJquery('#uccess_fileupload_parent_sugg').length)
	     scriptJquery('#uccess_fileupload_parent_sugg').attr('style','display:none');
	  if (aaf_main_page_invite) { 
	   var filename = scriptJquery('#file_upload').value;
	    window.location.href = invite_mainpage_url + '?csv_filename=' + filename;
	  }
	  else { 
	    if (filename == '')
	      var filename = scriptJquery('#Filedata').value;
  		Smoothbox.open("<div style='height:30px;'><center><b>" + en4.core.language.translate('Importing Contacts') + "</b><br /><img src='application/modules/Seaocore/externals/images/loadings.gif' alt='' /></center></div>");
  		var postData = {
  			'task' : 'join_network',
  			'filename': filename,
		    'moduletype': semoduletype
  		};
  			
  		en4.core.request.send(scriptJquery.ajax({
  			url : en4.core.baseUrl + 'seaocore/usercontacts/getcsvcontacts',
  			method : 'post',
  			data : postData,
  			success : function(responseObject)
  			{ 
  				scriptJquery('#Filedata').value = '';
  				fileext = false;
  				Smoothbox.close();
  				scriptJquery('#csvmasssubmit').attr('style','display:none');
  				scriptJquery('#id_csvformate_error_mess').attr('style','display:none');	
  				scriptJquery('#csv_friends').attr('style','display:block');
  				scriptJquery('#show_contacts_csv').attr('style','display:block');	
  				scriptJquery('#show_contacts_csv').html( responseObject );
  				if( scriptJquery('#skipinviterlink') ){ scriptJquery( '#' + 'skipinviterlink').attr('style','display:block'); }
  				window.location.hash ='show_contacts_csv';
  			}
  
  		}), {
	 'force':true
	});
	  }	
		
	}
	else { 
		if (scriptJquery('#id_success_frequ').length) 	   scriptJquery('#id_success_frequ').attr('style','display:none');
		scriptJquery('#id_nonsite_success_mess').attr('style','display:none');
		scriptJquery('#csv_friends').attr('style','display:none');
		scriptJquery('#id_csvformate_error_mess').attr('style','display:block');	
 		
	}
}

function checkext(fis)
{
	var s,ext ;
    s=fis.length;
    ext=fis.substring(s-4,s);
	if((ext.toUpperCase()=='.CSV' || ext.toUpperCase()=='.TXT'))
	 {
		 return true;
	}
	else
	return false;
	
}

function showhide (hide_div, show_div) {
	 if (scriptJquery('#show_contacts').length) {
		scriptJquery('#show_contacts').html('');
	}
	if (scriptJquery('#show_contacts_csv').length) {
		scriptJquery('#show_contacts_csv').html('');
   }
	scriptJquery('#'+hide_div).attr('style','display:none');
	scriptJquery('#'+show_div).attr('style','display:block');
	scriptJquery('#network_friends').attr('style','display:none');
	scriptJquery('#id_nonsite_success_mess').attr('style','display:none');
	if (scriptJquery('#id_success_frequ').length) 	   scriptJquery('#id_success_frequ').attr('style','display:none');
	scriptJquery('#csv_friends').attr('style','display:none');
	scriptJquery('#id_csvformate_error_mess').attr('style','display:none');
}

function showhideinviter (hide_div, show_div, calling_from) {

	if( calling_from == 1 ) {
		scriptJquery('#sub-title').attr('style','display:none');
		scriptJquery('#sub-txt').attr('style','display:none');
		scriptJquery('#webacc-logos').attr('style','display:none');
		scriptJquery('#skipinviterlink').attr('style','display:none');
		scriptJquery('#invite_info').attr('style','display:none');
		scriptJquery('#header_title').attr('style','display:none');

		scriptJquery('#inviter_form1').attr('style','display:block');
		scriptJquery('#inviter_form2').attr('style','display:block');

		scriptJquery('#help_link2').attr('style','display:none');
		scriptJquery('#help_link1').attr('style','display:block');

		scriptJquery('#network_friends').attr('style','display:none');
		scriptJquery('#id_nonsite_success_mess').attr('style','display:none');
		if (scriptJquery('#id_success_frequ').length) 	   scriptJquery('#id_success_frequ').attr('style','display:none');
		scriptJquery('#csv_friends').attr('style','display:none');
		scriptJquery('#id_csvformate_error_mess').attr('style','display:none');
	}else if( calling_from == 2 ) { 
		scriptJquery('#'+hide_div).attr('style','display:none');
		scriptJquery('#'+show_div).attr('style','display:block');

		scriptJquery('#sub-title').attr('style','display:none');
		scriptJquery('#sub-txt').attr('style','display:none');
		scriptJquery('#webacc-logos').attr('style','display:none');
		scriptJquery('#skipinviterlink').attr('style','display:none');
		scriptJquery('#invite_info').attr('style','display:none');
		scriptJquery('#header_title').attr('style','display:none');

		scriptJquery('#inviter_form1').attr('style','display:block');
		scriptJquery('#inviter_form2').attr('style','display:block');

		scriptJquery('#help_link2').attr('style','display:none');
		scriptJquery('#help_link1').attr('style','display:block');
		

		scriptJquery('#network_friends').attr('style','display:none');
		scriptJquery('#id_nonsite_success_mess').attr('style','display:none');
		if (scriptJquery('#id_success_frequ').length) 	   scriptJquery('#id_success_frequ').attr('style','display:none');
		scriptJquery('#csv_friends').attr('style','display:none');
		scriptJquery('#id_csvformate_error_mess').attr('style','display:none');
	}else {
		if (scriptJquery('#show_contacts').length) {
			scriptJquery('#show_contacts').html('');
		}
		if (scriptJquery('#show_contacts_csv').length) {
			scriptJquery('#show_contacts_csv').html('');
		}
		scriptJquery('#'+hide_div).attr('style','display:none');
		scriptJquery('#'+show_div).attr('style','display:block');
		scriptJquery('#network_friends').attr('style','display:none');
		scriptJquery('#id_nonsite_success_mess').attr('style','display:none');
		if (scriptJquery('#id_success_frequ').length) 	   scriptJquery('#id_success_frequ').attr('style','display:none');
		scriptJquery('#csv_friends').attr('style','display:none');
		scriptJquery('#id_csvformate_error_mess').attr('style','display:none');
		scriptJquery('#sub-title').attr('style','display:block');
		scriptJquery('#sub-txt').attr('style','display:block');
		scriptJquery('#webacc-logos').attr('style','display:block');
		if( calling_from == 3 ) {
			scriptJquery('#skipinviterlink').attr('style','display:none');
		}else if( calling_from == 4 ) {
			scriptJquery('#skipinviterlink').attr('style','display:block');
		}
		scriptJquery('#invite_info').attr('style','display:block');
		scriptJquery('#header_title').attr('style','display:block');

		scriptJquery('#inviter_form1').attr('style','display:none');
		scriptJquery('#inviter_form2').attr('style','display:none');

		scriptJquery('#help_link2').attr('style','display:block');
		scriptJquery('#help_link1').attr('style','display:none');
		
	}
}



/**



*  Javascript trim, ltrim, rtrim
*  http://www.webtoolkit.info/
*
**/
 
function trim(str, chars) {
	return ltrim(rtrim(str, chars), chars);
}
 
function ltrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
}
 
function rtrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}


//THIS FUNCTION IS USED TO SHOW THE ALL GOOGLE CONTACTS IN PARSING MODE.DEFAULT WE ARE SHOWING ONLY THOSE CONTACTS WHICH ARE SITE MEMBERS BUT NOT USER'S FRIENDS.
function show_contacts_Facebook(id) {

FB.ui({
  method: 'send',
  link:document.location.origin+en4.core.baseUrl,
},function (response) {
	if(response!=null){
		Smoothbox.open("<div style='height:30px;'><center><b>" + en4.core.language.translate('You have successfully sent invites.') + "</b><br /></center></div>");            
             setTimeout("Smoothbox.close();", 1000);
             setTimeout("location.reload();", 1000);
	}
	
}); 

//    FB.ui({method: 'apprequests',
//         message: fbinvitemessage
//     }, function (response) {
        
//         var ids = response.to;

// if(ids){
//         FB.ui({method: 'apprequests',
//             message: fbinvitemessage,
//             to: ids
//         }, function (response) {

//             Smoothbox.open("<div style='height:30px;'><center><b>" + en4.core.language.translate(['You have successfully sent %s invite.','You have successfully sent %s invites.'], response.to.length) + "</b><br /></center></div>");            
//             setTimeout("Smoothbox.close();", 1000);
//             setTimeout("location.reload();", 1000); 
//         });
//         }else{
//             alert('Please select atleast 1 contact to send invite.');
//         }
//     });
  

//  scriptJquery( '#' + 'id_nonsite_success_mess').attr('style','display:none');
//	if (scriptJquery( '#' + 'id_success_frequ').length) 	   scriptJquery( '#' + 'id_success_frequ').attr('style','display:none');
//	scriptJquery( '#' + 'show_contacts').attr('style','display:none');
//	if (id == 1) {
//		var child_window = window.open (en4.core.baseUrl + 'seaocore/usercontacts/getfacebookcontacts?redirect_uri=' + invite_callbackURl ,'mywindow','width=580,height=450');
//	}
//	if (window.opener!= null) {
//		if (id == 0) {
//			var href = window.location.href;
//			if (window.opener.aaf_main_page_invite)
//			   window.opener.location.href = href;
//			else { 
//       
//        if (en4.user.viewer.guid)
//           window.opener.location.href = href;
//        else
//          window.opener.get_contacts_Facebook();
//			}
//			close();
//		}
//	}
}

function get_contacts_Facebook () {  
if(scriptJquery('#id_nonsite_success_mess').length)
    scriptJquery('#id_nonsite_success_mess').css( 'display', 'none' );
  
  if(typeof seaocoreFBParams != 'undefined') {
    if (seaocoreFBParams.method == 'apprequests') {

      var params = {method: 'apprequests',
        message: seaocoreFBParams.message,
      };
      if (seaocoreFBParams.exclude_ids != null && seaocoreFBParams.exclude_ids.length > 0)
        params = scriptJquery.extend({'exclude_ids': seaocoreFBParams.exclude_ids}, params);
      
      FB.ui(params, fbInviteRequestCallBack);
    } else {
      FB.ui({method: 'send',
        link: seaocoreFBParams.link

      }, fbInviteRequestCallBack);

    }
  } else {
    Smoothbox.open("<div style='height:30px;'><center><b>" + en4.core.language.translate('Importing Contacts') + "</b><br /><img src='application/modules/Seaocore/externals/images/loadings.gif' alt='' /></center></div>");
	var postData = {
		'task' : 'get_facebookcontacts',
		'redirect_uri': invite_callbackURl,	
		'moduletype':semoduletype,
		'invitepage_id': semoduletype_id,
    'resource_type': semoduletype_type
	};
	
	en4.core.request.send(scriptJquery.ajax( {
		url : en4.core.baseUrl + 'seaocore/usercontacts/getfacebookcontacts',
		method : 'POST',
		data : postData,
		success : function(responseJSON) { 
	    if ($type(responseJSON) && typeof responseJSON.message != 'undefined' && responseJSON.message != '') {
  	    FB.ui({ method: 'send', 
           
           link: responseJSON.link
          
           
          
           
         }, fbInviteRequestCallBack);
		  }
		  else {
		    en4.core.showError(en4.core.language.translate('No such Facebook friends found.'));
		  }
			Smoothbox.close();
		}
	}), {
	 'force':true
	});
  }
}



function fbInviteRequestCallBack (response) {
  if(typeof response == 'undefined' || response == null) return;
       if (response && typeof response.error_message != 'undefined') {
         alert('Error occurred.');
       } 
       else if(typeof response.to != 'undefined' || response.success)
        { 
         if(typeof seaocoreFBParams != 'undefined' && seaocoreFBParams.method == 'apprequests') {
           
            var postData = {
        		'ids' : response.to
        	};
        	
        	en4.core.request.send(scriptJquery.ajax( {
        		url : en4.core.baseUrl + 'seaocore/auth/save-fb-inviter',
        		method : 'post',
        		data : postData,        		
        		
        	}));
         }
         if(scriptJquery('#id_nonsite_success_mess').length)
  				 scriptJquery('#id_nonsite_success_mess').attr('style','display:block');
  				if(scriptJquery('#show_nonsitefriends').length)
  				  scriptJquery('#show_nonsitefriends').html( '' );
  				if( scriptJquery('#id_csvcontacts').css( 'display' ) == 'none' ) { 
            if (scriptJquery('#skipinviterlink').length ) {
  					   scriptJquery('#skipinviterlink').attr('style','display:block');
  					}
  				}
      
		      
       }
}


//THIS FUNCTION IS USED TO SHOW THE ALL TWITTER CONTACTS IN PARSING MODE. DEFAULT WE ARE SHOWING ONLY THOSE CONTACTS WHICH ARE SITE MEMBERS BUT NOT USER'S FRIENDS.
function show_contacts_Twitter (id) { 
  scriptJquery('#id_nonsite_success_mess').attr('style','display:none');
	if (scriptJquery('#id_success_frequ').length) 	   scriptJquery('#id_success_frequ').attr('style','display:none');
	scriptJquery('#show_contacts').attr('style','display:none');
	if (id == 1) {
		var child_window = window.open (en4.core.baseUrl + 'seaocore/usercontacts/gettwittercontacts?redirect_uri=' + invite_callbackURl ,'mywindow','width=500,height=500');
	}
	if (window.opener!= null) {
		if (id == 0) {
			var redirect_href = window.location.href;
		
			
			if (window.opener.aaf_main_page_invite)
			   window.opener.location.href = redirect_href;
			else {
			  window.opener.get_contacts_twitter ();
			}
			close();
		}
	}
}
//CALLING THIS FUNCTION FROM CHILD WINDOW BEFORE CLOSING THE CHILD WINDOW.WHICH GETS THE GOOGLE CONTACTS.
function get_contacts_twitter () { 
	Smoothbox.open("<div style='height:30px;'><center><b>" + en4.core.language.translate('Importing Contacts') + "</b><br /><img src='application/modules/Seaocore/externals/images/loadings.gif' alt='' /></center></div>");
	var postData = {
		'task' : 'get_twiitercontacts',
		'moduletype': semoduletype,
    'format':'html'
	};
	
	en4.core.request.send(scriptJquery.ajax( {
		url : en4.core.baseUrl + 'seaocore/usercontacts/gettwittercontacts',
		method : 'post',
		data : postData,
		success : function(responseHTML) {
		 
			
			scriptJquery('#network_friends').attr('style','display:block'); 
			if( scriptJquery('#skipinviterlink') ){ scriptJquery('#skipinviterlink').attr('style','display:block'); }
			scriptJquery('#show_contacts').attr('style','display:block');
			scriptJquery('#show_contacts').html( responseHTML );
      en4.core.runonce.trigger();
			window.location.hash ='show_contacts';
			Smoothbox.close();
		}
	}), {
	 'force':true
	});
}

function invitePreview (servicetype) {
	var checked = false;
	var total_contacts = scriptJquery('#nonsitetotal_contacts').value;
	for (var i =1; i <= total_contacts; i++) 
	{
		if (scriptJquery('#nonsitecontact_' + i).checked) {
			checked = true;
		}
	}
	if (checked) { 
    if (typeof occurrence_id != 'undefined') 
      url = en4.core.baseUrl + semoduletype +'invite/index/invitepreview?' + semoduletype + '_id=' + semoduletype_id + '&servicetype=' + servicetype + '&occurrence_id=' + occurrence_id;
    else
      url = en4.core.baseUrl + semoduletype +'invite/index/invitepreview?' + semoduletype + '_id=' + semoduletype_id + '&servicetype=' + servicetype;
    Smoothbox.open(url);
	}
	else { 
	  en4.core.showError("Please select at least one friend to invite.");
		
	}
}

function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}
