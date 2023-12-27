////// FUNCTION FOR CREATING A FAVOURITE OR UNFAVOURITE.
function seaocore_content_type_favourites(resource_id, resource_type) {

    content_type_undefined = 0;
    var content_type = resource_type;
    if (resource_type == '') {
        content_type_undefined = 1;
        var content_type = resource_type;
    }

    // SENDING REQUEST TO AJAX
    var request = seaocore_content_create_favourite( resource_id, resource_type, content_type, content_type_undefined );


}

function seaocore_content_create_favourite( resource_id, resource_type, content_type, content_type_undefined ) {
    
        if (scriptJquery('#'+content_type + '_favourite_' + resource_id).length) {
            var favourite_id = scriptJquery('#'+content_type + '_favourite_' + resource_id)[0].value
        }
    
    var request = scriptJquery.ajax({
        url: en4.core.baseUrl + 'seaocore/favourite/favourite',
        method: "GET",
        data: {
            format: 'json',
            'resource_id': resource_id,
            'resource_type': resource_type,
            'favourite_id': favourite_id
        },
        success: function(responseJSON) {

            if (content_type_undefined == 0) {
                if (responseJSON.favourite_id) {

                    if (scriptJquery('#'+content_type + '_favourite_' + resource_id).length)
                        scriptJquery('#'+content_type + '_favourite_' + resource_id)[0].value = responseJSON.favourite_id;
                    if (scriptJquery('#'+content_type + '_num_of_favourite_' + resource_id).length) {
                        scriptJquery('#'+content_type + '_num_of_favourite_' + resource_id).html( responseJSON.num_of_favourite );
                    }
                    scriptJquery('a.' + content_type + '_most_favourites_' + resource_id).each(function ( ttttt, el) {
                        el.style.display = 'none';
                    });
                    scriptJquery('a.' + content_type + '_unfavourites_' + resource_id).each(function ( ttttt, el) {
                        el.style.display = 'inline-block';
                    });

                } else {

                    if (scriptJquery('#'+content_type + '_favourite_' + resource_id).length)
                        scriptJquery('#'+content_type + '_favourite_' + resource_id)[0].value = 0;
                    if (scriptJquery('#'+content_type + '_num_of_favourite_' + resource_id).length) {
                        scriptJquery('#'+content_type + '_num_of_favourite_' + resource_id).html( responseJSON.num_of_favourite );
                    }
                    scriptJquery('a.' + content_type + '_most_favourites_' + resource_id).each(function ( ttttt, el) {
                        el.style.display = 'inline-block';
                    });
                    scriptJquery('a.' + content_type + '_unfavourites_' + resource_id).each(function ( ttttt, el) {
                        el.style.display = 'none';
                    });

                }
            }

        }
    });
    //request.send();
    return request;
}
