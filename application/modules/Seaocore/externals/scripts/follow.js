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

////// FUNCTION FOR CREATING A LIKE OR UNLIKE.
function seaocore_content_type_follows(resource_id, resource_type) {

  content_type_undefined = 0;
	var content_type = seaocore_content_type;
	if (seaocore_content_type == '') { 
		content_type_undefined = 1;
		var content_type = resource_type;
	}
	
	// SENDING REQUEST TO AJAX
	var request = seaocore_content_create_follow(resource_id, resource_type, content_type);
	
	// RESPONCE FROM AJAX
	request.complete(function(responseJSON) {
		var responseJSON = responseJSON.responseJSON
		if (content_type_undefined == 0) {
			if(responseJSON.follow_id )	{
				scriptJquery('#'+content_type+'_follow_'+ resource_id).val(responseJSON.follow_id);
				scriptJquery('#'+content_type+'_most_follows_'+ resource_id).css('display','none');
				scriptJquery('#'+content_type+'_unfollows_'+ resource_id).css('display','inline-block');

				if(scriptJquery('#'+content_type+'_num_of_follow_'+ resource_id)) {
					scriptJquery('#'+content_type + '_num_of_follow_'+ resource_id).html(responseJSON.follow_count);
				}
				
				if(scriptJquery('#'+content_type+'_num_of_follows_'+ resource_id)) { 
					scriptJquery('#'+content_type + '_num_of_follows_'+ resource_id).html(responseJSON.follow_count);
				}
			}	else	{
				scriptJquery('#'+content_type+'_follow_'+ resource_id).val(0);
				scriptJquery('#'+content_type+'_most_follows_'+ resource_id).css('display','inline-block');
				scriptJquery('#'+content_type+'_unfollows_'+ resource_id).css('display','none');
				
				if(scriptJquery('#'+content_type+'_num_of_follow_'+ resource_id)) {
					scriptJquery('#'+content_type + '_num_of_follow_'+ resource_id).html(responseJSON.follow_count);
				}
				
				if(scriptJquery('#'+content_type+'_num_of_follows_'+ resource_id)) {
					scriptJquery('#'+content_type + '_num_of_follows_'+ resource_id).html(responseJSON.follow_count);
				}
			}
		}
	});
}

function seaocore_content_create_follow( resource_id, resource_type, content_type ) {
	if(scriptJquery('#'+content_type + '_follow_'+ resource_id).length) {
		var follow_id = scriptJquery('#'+content_type + '_follow_'+ resource_id).val()
	}
	var request = scriptJquery.ajax({
		url : en4.core.baseUrl + 'seaocore/follow/global-follows',
		'method': 'post',
		data : {
			format : 'json',
			'resource_id' : resource_id,
			'resource_type' : resource_type,	
			'follow_id' : follow_id
		}
	});
	return request;
}
//FUNCTION FOR FOLLOW OR UNFOLLOW.
