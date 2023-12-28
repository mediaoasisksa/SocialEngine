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

////// FUNCTION FOR CREATING A FAVOURITE OR UNFAVOURITE.
function seaocore_content_type_favourites(resource_id, resource_type) {

    content_type_undefined = 0;
    /*if (seaocore_content_type) {
        var content_type = seaocore_content_type;
    }
    else {*/
        var content_type = resource_type;
   // }

    if (resource_type == '') {
        content_type_undefined = 1;
        var content_type = resource_type;
    }

    // SENDING REQUEST TO AJAX
    var request = seaocore_content_create_favourite(resource_id, resource_type, content_type);

    // RESPONCE FROM AJAX
    request.addEvent('complete', function (responseJSON) {
        if (content_type_undefined == 0) {
            if (responseJSON.favourite_id) {

                if ($(content_type + '_favourite_' + resource_id))
                    $(content_type + '_favourite_' + resource_id).value = responseJSON.favourite_id;
                if ($(content_type + '_num_of_favourite_' + resource_id)) {
                    $(content_type + '_num_of_favourite_' + resource_id).innerHTML = responseJSON.num_of_favourite;
                }
                $$('a.' + content_type + '_most_favourites_' + resource_id).each(function (el) {
                    el.style.display = 'none';
                });
                $$('a.' + content_type + '_unfavourites_' + resource_id).each(function (el) {
                    el.style.display = 'inline-block';
                });

            } else {

                if ($(content_type + '_favourite_' + resource_id))
                    $(content_type + '_favourite_' + resource_id).value = 0;
                if ($(content_type + '_num_of_favourite_' + resource_id)) {
                    $(content_type + '_num_of_favourite_' + resource_id).innerHTML = responseJSON.num_of_favourite;
                }
                $$('a.' + content_type + '_most_favourites_' + resource_id).each(function (el) {
                    el.style.display = 'inline-block';
                });
                $$('a.' + content_type + '_unfavourites_' + resource_id).each(function (el) {
                    el.style.display = 'none';
                });

            }
        }
    });
}

function seaocore_content_create_favourite(resource_id, resource_type, content_type) {
    
        if ($(content_type + '_favourite_' + resource_id)) {
            var favourite_id = $(content_type + '_favourite_' + resource_id).value
        }
    
    var request = new Request.JSON({
        url: en4.core.baseUrl + 'seaocore/favourite/favourite',
        method: "GET",
        data: {
            format: 'json',
            'resource_id': resource_id,
            'resource_type': resource_type,
            'favourite_id': favourite_id
        }
    });
    request.send();
    return request;
}
