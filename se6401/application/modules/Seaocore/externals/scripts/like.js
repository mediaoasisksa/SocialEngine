////// FUNCTION FOR CREATING A LIKE OR UNLIKE.
function seaocore_content_type_likes(resource_id, resource_type) {

    content_type_undefined = 0;
    if (typeof seaocore_content_type != 'undefined') {
        var content_type = seaocore_content_type;
        if (resource_type) {
          content_type = resource_type;
        }
    }
    else {
        var content_type = resource_type;
    }

    if (resource_type == '') {
        content_type_undefined = 1;
        var content_type = resource_type;
    }

    // SENDING REQUEST TO AJAX
    var request = seaocore_content_create_like(resource_id, resource_type, content_type);
    // RESPONCE FROM AJAX
    request.success(function(responseJSON) {
        if (content_type_undefined == 0) {
            if (responseJSON.like_id) {
                if (scriptJquery('#'+content_type + '_like_' + resource_id).length)
                    scriptJquery('#'+content_type + '_like_' + resource_id).val(responseJSON.like_id);
                if (scriptJquery('#'+content_type + '_most_likes_' + resource_id).length)
                    scriptJquery('#'+content_type + '_most_likes_' + resource_id).css('display','none');
                if (scriptJquery('#'+content_type + '_unlikes_' + resource_id).length)
                    scriptJquery('#'+content_type + '_unlikes_' + resource_id).css('display', 'inline-block');
                if (scriptJquery('#'+content_type + '_num_of_like_' + resource_id).length) {
                    scriptJquery('#'+content_type + '_num_of_like_' + resource_id).html(responseJSON.num_of_like);
                }
                if (scriptJquery('.' + content_type + '_num_of_like_' + resource_id).length) {
                    scriptJquery('.' + content_type + '_num_of_like_' + resource_id).each(function (id,el) {
                    scriptJquery(el).html(responseJSON.num_of_like);
                    });
                }
                scriptJquery('.' + content_type + '_most_likes_' + resource_id).each(function (id,el) {
                    scriptJquery(el).css('display', 'none');
                });
                scriptJquery('.' + content_type + '_unlikes_' + resource_id).each(function (id,el) {
                    scriptJquery(el).css('display','inline-block');
                });

            } else {
                if (scriptJquery('#'+content_type + '_like_' + resource_id).length)
                    scriptJquery('#'+content_type + '_like_' + resource_id).val(0);
                if (scriptJquery('#'+content_type + '_most_likes_' + resource_id).length)
                    scriptJquery('#'+content_type + '_most_likes_' + resource_id).css('display', 'inline-block');
                if (scriptJquery('#'+content_type + '_unlikes_' + resource_id).length)
                    scriptJquery('#'+content_type + '_unlikes_' + resource_id).css('display', 'none');
                if (scriptJquery('#'+content_type + '_num_of_like_' + resource_id).length) {
                    scriptJquery('#'+content_type + '_num_of_like_' + resource_id).html(responseJSON.num_of_like);
                }
                if (scriptJquery('.' + content_type + '_num_of_like_' + resource_id).length) {
                    scriptJquery('.' + content_type + '_num_of_like_' + resource_id).each(function (id,el) {
                    scriptJquery(el).html(responseJSON.num_of_like);
                    });
                }
                scriptJquery('.' + content_type + '_most_likes_' + resource_id).each(function (id,el) {
                    scriptJquery(el).css('display', 'inline-block');
                });
                scriptJquery('.' + content_type + '_unlikes_' + resource_id).each(function (id,el) {
                    scriptJquery(el).css('display', 'none');
                });
            }
        }
    });
}

function seaocore_content_create_like(resource_id, resource_type, content_type) {
    if (scriptJquery('#'+content_type + '_like_' + resource_id).length) {
        var like_id = scriptJquery('#'+content_type + '_like_' + resource_id).val()
    }
    var request = scriptJquery.ajax({
        url: en4.core.baseUrl + 'seaocore/like/like',
        dataType:'json',
        method: 'post',
        data: {
            format: 'json',
            'resource_id': resource_id,
            'resource_type': resource_type,
            'like_id': like_id
        }
    });
    // request.send();
    return request;
}
//FUNCTION FOR LIKE OR UNLIKE.
