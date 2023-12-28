## SocialEngine REST API

**What is REST API?** Simply, REST API is the set of functions to which the developers can perform requests and receive responses. The interaction is performed via the HTTP/HTTPS protocol. An advantage of such an approach is the wide usage of HTTP/HTTPS that's why REST API can be used practically for any programming language.

Below are the characteristics of the SocialEngineAddOns REST API:

* When a request is sent to the REST API server, the server will return a response that contains either the data you requested, or the status code, or both.
* **oauth_consumer_key / oauth_consumer_secret?**  The `Consumer Key` and `Consumer Secret Key` are used to make a connection between API client and server. These keys are also used for creating `OAuth Tokens` for site members to access server resources without sharing their credentials.

```
Accept: application/json
oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb
```
* **oauth_token / oauth_secret?** The `OAuth Token` and `OAuth Secret` are used for identifying site members.

```
Accept: application/json
oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb
oauth_token: 276ed9327d0478c2606adea438f4fd15
oauth_secret: ffaff6754b5d4ca5f2fc4cc2a3a75478
```

### Base URL
All requests to this REST API should have the base URL:
[https://example.com/api/rest/]

### HTTP Verbs
In REST API, there are four verbs that are used to manage resources: GET, POST, PUT, and DELETE. You can get the contents of a data using GET, delete the data using DELETE, and create or update the data using POST/PUT.

### Supported Features

#### Official SocialEngine Plugins
* Blogs Plugin
* Photo Albums Plugin
* Classifieds Plugin
* Groups Plugin
* Events Plugin
* Forums Plugin
* Polls Plugin
* Video Sharing Plugin
* Music Sharing Plugin

#### Core SocialEngine Features
* Search
* Member Links
* Member Log-in
* Member Sign-up With Profile Fields
* Edit Member Profile Fields
* Activity Feeds
* Notifications
* Friend Requests
* Messages
* Member Settings
* Likes and Comments
* Easy Implementation of Photo Lightbox.
* Footer Menus

#### SocialEngineAddOns Plugins:
* Advanced Activity Feeds / Wall Plugin
* Geo-Location, Geo-Tagging, Check-Ins & Proximity Search Plugin [only for Advanced Activity Feed's location features]


## Group Advanced Activity Feeds / Wall Plugin
These are the APIs for the SocialEngineAddOns Plugin: [Advanced Activity Feeds / Wall Plugin](http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin).

### Get Activity Feeds [/advancedactivity/feeds{?limit}{&maxid}{&post_elements}{&subject_type}{&subject_id}{&minid}{&feed_count_only}{&action_id}{&feed_filter}{&feed_type}{&subject_info}{&object_info}]
#### Get Activity Feeds [GET]
Get the activity feeds for member home, user profile, or content profile. Activity feeds can also be obtained based on feed_type. Count of new activity feeds can also be obtained.

To get `New feeds count` call this API after some time interval [example: in every 30 second] with `minid` and `feed_count_only`, and the available new feeds count will be returned in response.

In case of pagination, we need to use 2 parameters `limit` and `maxid`. Use `limit` to set the number of feeds in response and use `maxid` for the feed id after which `limit` number of feeds are to be fetched. Note that as feeds are shown in descending order of ids, feeds returned in response will have ids lesser than `maxid`.

+ Parameters    
    + post_elements (optional, boolean, `post_elements=0`)  ... get list of feed elements that can be posted like: status, emoticons, photo, checkin, withtags, userprivacy, etc. Default value is 1.
    + subject_type (optional, string, `subject_type=blog`)  ... subject type. Required to get activity feeds of a content or user profile.
    + subject_id (optional, integer, `subject_id=128`)  ... subject id. ID of content / user.
    + minid (optional, integer, `minid=75`)  ... first feed's id. Note that in a response set of feeds, `minid` will be greater than `maxid` as feeds are shown in descending order of ids. `minid` is used to get the count of new feeds.
    + feed_count_only (optional, boolean, `feed_count_only=1`)  ... get only feed count in response. Required to get count of new feeds. Default value is 0.
    + action_id (optional, integer, `action_id=167`)  ... feed id. Get information of a single activity feed.
    + feed_filter (optional, boolean, `boolean=1`)  ... get list of available feed filters in response. Default value is 0.
    + feed_type (optional, string, `feed_type=all`)  ... filter the activity feeds response to get feeds of a particular type ().
    + subject_info (optional, boolean, `subject_info=1`)  ... get subject array with every feed in response. Default value is 0.
    + object_info (optional, boolean, `object_info=1`)  ... get object array with every feed in response. Default value is 0.
    + maxid (optional, integer, `maxid=128`)  ... last feed's id. Required in pagination as explained above.
    + limit (optional, integer, `limit=20`)  ... number of feeds. Default limit is 15.
    + hashtag (optional, integer, `hashtag=#awesome`) ... shows all the feeds which contains this hashtag.

+ Request
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb


+ Response 200
    + Headers

            Content-Type: application/json

    + Body

            {
                "activity_feed": [
                   {  
                      "data": {
                         "action_id": 668,
                         "type": "sitetagcheckin_post_self",
                         "subject_type": "user",
                         "subject_id": 3,
                         "object_type": "user",
                         "object_id": 3,
                         "body": "He Hugh",
                         "params": {  
                            "0": "[]",
                            "checkin": {  
                               "resource_guid": "0",
                               "type": "place",
                               "id": "sitetagcheckin_0",
                               "label": "Franklin Institute, North 20th Street, Philadelphia, PA, United States",
                               "place_id": "ChIJ05tdhjTGxokReHRD0wXejyE",
                               "latitude": "39.9582109",
                               "vicinity": "The Franklin Institute, 222 North 20th Street, Philadelphia, PA 19103, USA",
                               "longitude": "-75.1731347",
                               "prefixadd": "in"
                            }
                         },
                         "date": "2015-03-19 07:30:52",
                         "attachment_count": 1,
                         "comment_count": 1,
                         "like_count": 1,
                         "privacy": "everyone",
                         "commentable": 1,
                         "shareable": 1,
                         "user_agent": "seiosnativeapp/1 CFNetwork/711.0.6 Darwin/14.0.0",
                         "time_value": 1426750252,
                         "feed_title": "Robert Desuza\r\nHe Hugh",
                         "action_type_body": "{item:$subject}\r\n{body:$body}",
                         "action_type_body_params": [  
                            {  
                               "search": "{item:$subject}",
                               "label": "Robert Desuza",
                               "type": "user",
                               "id": 3
                            },
                            {  
                               "search": "{body:$body}",
                               "label": "He Hugh"
                            }
                         ],
                         "feed_icon": "http://example.com/public/user/0c/000c_1719.png",
                         "tags": [  
                            {  
                               "tagmap_id": 91,
                               "resource_type": "activity_action",
                               "resource_id": 668,
                               "tagger_type": "user",
                               "tagger_id": 3,
                               "tag_type": "user",
                               "tag_id": 1,
                               "creation_date": "2015-03-19 07:30:52",
                               "extra": null,
                               "tag_obj": {  
                                  "user_id": 1,
                                  "username": "mikealen",
                                  "displayname": "M Mike",
                                  "photo_id": 0,
                                  "status": "Bmcjgfigf",
                                  "status_date": "2015-03-10 08:11:45",
                                  "locale": "auto",
                                  "language": "en_US",
                                  "timezone": "US/Pacific",
                                  "search": 1,
                                  "show_profileviewers": 1,
                                  "level_id": 1,
                                  "enabled": 1,
                                  "verified": 1,
                                  "approved": 1,
                                  "creation_date": "2014-11-15 08:27:34",
                                  "modified_date": "2015-03-10 08:11:45",
                                  "lastlogin_date": "2015-03-18 10:18:31",
                                  "update_date": null,
                                  "member_count": 27,
                                  "view_count": 187,
                                  "location": "",
                                  "image_icon": ""
                               }
                            }
                         ],
                         "attachment": [  
                            {  
                               "title": "",
                               "body": "",
                               "attachment_type": "album_photo",
                               "attachment_id": 11,
                               "likes_count": 1,
                               "comment_count": 1,
                               "is_like": 1,
                               "image_main": {  
                                  "src": "http://example.com/public/album_photo/31/03/032e_bf05.png",
                                  "size": {  
                                     "width": 640,
                                     "height": 480
                                  }
                               },
                               "image_icon": {  
                                  "src": "http://example.com/public/album_photo/31/03/032e_bf05.png",
                                  "size": {  
                                     "width": 640,
                                     "height": 480
                                  }
                               },
                               "image_profile": {  
                                  "src": "http://example.com/public/album_photo/31/03/032e_bf05.png",
                                  "size": {  
                                     "width": 640,
                                     "height": 480
                                  }
                               },
                               "image_normal": {  
                                  "src": "http://example.com/public/album_photo/32/03/032f_252d.png",
                                  "size": {  
                                     "width": 140,
                                     "height": 105
                                  }
                               },
                               "image_medium": {  
                                  "src": "http://example.com/public/album_photo/33/03/0330_3768.png",
                                  "size": {  
                                     "width": 375,
                                     "height": 281
                                  }
                               },
                               "mode": 1
                            }
                         ],
                         "photo_attachment_count": 1
                      },
                      "can_comment": true,
                      "is_like": 1,
                      "can_share": 1,
                      "feed_menus": {  
                         "like": {  
                            "name": "unlike",
                            "label": "Unlike",
                            "url": "unlike",
                            "urlParams": {  
                               "action_id": 668
                            }
                         },
                         "share": {  
                            "name": "share",
                            "label": "Share",
                            "url": "activity/share",
                            "urlParams": {  
                               "type": "album_photo",
                               "id": 89
                            }
                         },
                         "save_feed": {  
                            "name": "update_save_feed",
                            "label": "Save Feed",
                            "url": "advancedactivity/update-save-feed",
                            "urlParams": {  
                               "action_id": 668
                            }
                         },
                         "delete": {  
                            "name": "delete_feed",
                            "label": "Delete Feed",
                            "url": "advancedactivity/delete",
                            "urlParams": {  
                               "action_id": 668
                            }
                         },
                         "disable_comment": {  
                            "name": "disable_comment",
                            "label": "Disable Comments",
                            "url": "advancedactivity/update-commentable",
                            "urlParams": {  
                               "action_id": 668
                            }
                         },
                         "lock_feed": {  
                            "name": "lock_this_feed",
                            "label": "Lock this Feed",
                            "url": "advancedactivity/update-shareable",
                            "urlParams": {  
                               "action_id": 668
                            }
                         }
                      }
                   }
                ],
                "activityCount": 1,
                "enable_composer": true,
                "enable_composer_photo": true,
                "maxid": 667,
                "minid": 669,
                "feed_post_menu": {  
                   "status": 1,
                   "emotions": 1,
                   "withtags": 1,
                   "photo": 1,
                   "checkin": 1,
                   "userprivacy": {  
                      "everyone": "Everyone",
                      "networks": "Friends & Networks",
                      "friends": "Friends Only",
                      "onlyme": "Only Me"
                   }
                }
            }


+ Response 401
    + Body

            {
                "status_code": 401,
                "error": true,
                "error_code": "unauthorized",
                "message": "User does not have access to this resource."
            }


### Post New Feed [/advancedactivity/feeds/post]
#### Post New Feed [POST]
Post a new update in activity feeds. Share a new post from status update box.

To post the feed photo, send the file in `multipart / form-data` format with the `photo` API request parameters.

To tag a friend, call `Tag Friend in Feed` API.

In this API request the parameter `composer` is dependent on the "[Geo-Location, Geo-Tagging, Check-Ins & Proximity Search Plugin](http://www.socialengineaddons.com/socialengine-geo-location-geo-tagging-checkins-proximity-search-plugin)". So, this parameter is not required to be sent if this plugin is not installed on the website.


| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `body`     | yes      | string | Save Earth | status update post's content
| `toValues`     | no      | string | 2,5,6 | comma-separated IDs of tagged friends
| `auth_view`     | no      | string | everyone | authorization permission (privacy) to view feed / status update
| `composer`     | no      | array | {"checkin": {...}} | location information for checkin


+ Request valid
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb

    + Body

            {
                "body": "Merry Christmas",
                "toValues": "2,5,6",
                "auth_view": "everyone",                
                "composer": {
                    "checkin": {
                        "id": "sitetagcheckin_3",
                        "label": "Alexandria, VA, United States",
                        "place_id": "ChIJ8aukkz5NtokRLAHB24Ym9dc",
                        "prefixadd": "in",
                        "resource_guid": 0,
                        "type": "place"
                    }
                }
            }

+ Response 201

+ Response 401
    + Headers

            Content-Type: application/json
    + Body

            {
                "status_code": 401,
                "error": true,
                "error_code": "unauthorized",
                "message": "User does not have access to this resource."
            }


### Post Feed Comment [/advancedactivity/comment]
#### Post Feed Comment [POST]
Post a comment on an activity feed.

| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `action_id`     | yes      | integer | 562 | activity feed id
| `body`     | yes      | string | great job!! | comment body


+ Request valid
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb
            oauth_token: 276ed9327d0478c2606adea438f4fd15
            oauth_secret: ffaff6754b5d4ca5f2fc4cc2a3a75478

    + Body

            {
                "action_id": 562,
                "body": "great job!!"
            }

+ Response 200
    + Headers

            Content-Type: application/json

    + Body

            {  
                "action_id":195,
                "comment_id":28,
                "image":"http://localhost/socialEngineApi/application/modules/User/externals/images/nophoto_user_thumb_profile.png",
                "image_normal":"http://localhost/socialEngineApi/application/modules/User/externals/images/nophoto_user_thumb_profile.png",
                "image_profile":"http://localhost/socialEngineApi/application/modules/User/externals/images/nophoto_user_thumb_profile.png",
                "image_icon":"http://localhost/socialEngineApi/application/modules/User/externals/images/nophoto_user_thumb_icon.png",
                "content_url":"http://example.com/profile/michal",
                "author_title":"M Michael",
                "comment_body":"great job!!",
                "comment_date":"2015-06-22 10:56:54",
                "delete":{  
                    "name":"delete",
                    "label":"Delete",
                    "url":"comment-delete",
                    "urlParams":{  
                        "action_id":195,
                        "subject_type":"activity_action",
                        "subject_id":195,
                        "comment_id":28
                    }
                },
                "like":{  
                    "name":"like",
                    "label":"Like",
                    "url":"like",
                    "urlParams":{  
                        "action_id":195,
                        "subject_type":"activity_action",
                        "subject_id":195,
                        "comment_id":28
                    }
                }
            }

+ Response 401
    + Body

            {
                "status_code": 401,
                "error": true,
                "error_code": "unauthorized",
                "message": "User does not have access to this resource."
            }


### Save / Unsave a Feed [/advancedactivity/update-save-feed{?action_id}]
#### Save / Unsave a Feed [POST]
Save or Unsave a feed.

| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `action_id`     | yes      | integer | 562 | activity feed id

+ Request valid
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb
            oauth_token: 276ed9327d0478c2606adea438f4fd15
            oauth_secret: ffaff6754b5d4ca5f2fc4cc2a3a75478

    + Body

            {
                "action_id": "562"
            }


+ Response 200
    + Headers

            Content-Type: application/json

    + Body

            1


+ Response 401
    + Body

            {
                "status_code": 401,
                "error": true,
                "error_code": "unauthorized",
                "message": "User does not have access to this resource."
            }

### Delete a Feed [/advancedactivity/delete]
#### Delete Feed [DELETE]
Delete an activity feed.

| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `action_id`     | yes      | integer | 562 | activity feed id

+ Request valid
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb
            oauth_token: 276ed9327d0478c2606adea438f4fd15
            oauth_secret: ffaff6754b5d4ca5f2fc4cc2a3a75478

    + Body

            {
                "action_id": 562
            }


+ Response 204

+ Response 401
    + Body

            {
                "status_code": 401,
                "error": true,
                "error_code": "unauthorized",
                "message": "User does not have access to this resource."
            }

### Delete a Feed Comment [/advancedactivity/delete]
#### Delete Feed Comment [DELETE]
Delete an activity feed's comment.

| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `comment_id`     | yes      | integer | 122 | activity feed comment id

+ Request valid
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb
            oauth_token: 276ed9327d0478c2606adea438f4fd15
            oauth_secret: ffaff6754b5d4ca5f2fc4cc2a3a75478

    + Body

            {
                "comment_id": 122
            }


+ Response 204

+ Response 401
    + Body

            {
                "status_code": 401,
                "error": true,
                "error_code": "unauthorized",
                "message": "User does not have access to this resource."
            }

### Enable / Disable Comments [/advancedactivity/update-commentable]
#### Enable / Disable Comments [POST]
Enable or Disable comments commenting on an activity feed.

| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `action_id`     | yes      | integer | 562 | activity feed id


+ Request valid
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb
            oauth_token: 276ed9327d0478c2606adea438f4fd15
            oauth_secret: ffaff6754b5d4ca5f2fc4cc2a3a75478

    + Body

            {
                "action_id": 562
            }

+ Response 200
    + Headers

            Content-Type: application/json

    + Body

            1


+ Response 401
    + Body

            {
                "status_code": 401,
                "error": true,
                "error_code": "unauthorized",
                "message": "User does not have access to this resource."
            }


### Lock / Unlock Feed [/advancedactivity/update-shareable]
#### Lock / Unlock Feed [POST]
Lock or Unlock an activity feed. When an activity feed is locked, then it cannot be shared.

| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `action_id`     | yes      | integer | 562 | activity feed id

+ Request valid
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb
            oauth_token: 276ed9327d0478c2606adea438f4fd15
            oauth_secret: ffaff6754b5d4ca5f2fc4cc2a3a75478

    + Body

            {
                "action_id": 562
            }

+ Response 200
    + Headers

            Content-Type: application/json

    + Body

            0


+ Response 401
    + Body

            {
                "status_code": 401,
                "error": true,
                "error_code": "unauthorized",
                "message": "User does not have access to this resource."
            }

### Like a Feed [/advancedactivity/like]
#### Like a Feed [POST]
Like an activity feed.

| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `action_id`     | yes      | integer | 562 | activity feed id

+ Request valid
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb
            oauth_token: 276ed9327d0478c2606adea438f4fd15
            oauth_secret: ffaff6754b5d4ca5f2fc4cc2a3a75478

    + Body

            {
                "action_id": 562
            }

+ Response 204
    
+ Response 401
    + Body

            {
                "status_code": 401,
                "error": true,
                "error_code": "unauthorized",
                "message": "User does not have access to this resource."
            }

### Like a Feed Comment [/advancedactivity/like]
#### Like a Feed Comment [POST]
Like an activity feed comment.

| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `comment_id`     | yes      | integer | 122 | feed comment id

+ Request valid
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb
            oauth_token: 276ed9327d0478c2606adea438f4fd15
            oauth_secret: ffaff6754b5d4ca5f2fc4cc2a3a75478

    + Body

            {
                "comment_id": 871
            }

+ Response 204
    
+ Response 401
    + Body

            {
                "status_code": 401,
                "error": true,
                "error_code": "unauthorized",
                "message": "User does not have access to this resource."
            }

### Unlike a Feed [/advancedactivity/unlike]
#### Unlike a Feed [POST]
Unlike an activity feed.

| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `action_id`     | yes      | integer | 562 | activity feed id

+ Request valid
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb
            oauth_token: 276ed9327d0478c2606adea438f4fd15
            oauth_secret: ffaff6754b5d4ca5f2fc4cc2a3a75478

    + Body

            {
                "action_id": 562
            }

+ Response 204
    
+ Response 401
    + Body

            {
                "status_code": 401,
                "error": true,
                "error_code": "unauthorized",
                "message": "User does not have access to this resource."
            }

### Unlike a Feed Comment [/advancedactivity/unlike]
#### Unlike a Feed Comment [POST]
Unlike an activity feed comment.

| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `comment_id`     | yes      | integer | 122 | feed comment id

+ Request valid
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb
            oauth_token: 276ed9327d0478c2606adea438f4fd15
            oauth_secret: ffaff6754b5d4ca5f2fc4cc2a3a75478

    + Body

            {
                "comment_id": 871
            }

+ Response 204
    
+ Response 401
    + Body

            {
                "status_code": 401,
                "error": true,
                "error_code": "unauthorized",
                "message": "User does not have access to this resource."
            }

### Hide Feed(s) [/advancedactivity/feeds/hide-item]
#### Hide Feed(s) [POST]
Hide activity feeds. Either a particular activity feed can be hidden, or all the activity feeds of a particular user can be hidden for the logged-in user.

| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `type`     | yes      | integer | activity_action | table item type
| `id`     | yes      | integer | 122 | activity feed id
| `hide_report`     | no      | boolean | 1 | Set only in case of `Hide all by <User Name>`. Default value is 0


+ Request valid
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb
            oauth_token: 276ed9327d0478c2606adea438f4fd15
            oauth_secret: ffaff6754b5d4ca5f2fc4cc2a3a75478

    + Body

            {
                "type": "activity_action",
                "id": 122
            }

+ Response 200
    + Headers

            Content-Type: application/json

    + Body

            {
              "undo": {  
                "name": "undo",
                "label": "This story is now hidden from your Activity Feed.",
                "url": "advancedactivity/feeds/un-hide-item",
                "urlParams": {  
                  "type": "activity_action",
                  "id": "671"
                }
              },
              "hide_all": {  
                "name": "hide_all",
                "label": "Hide all by Robert De Souza",
                "url": "advancedactivity/feeds/hide-item",
                "urlParams": {  
                  "type": "user",
                  "id": 3
                }
              }
            }

+ Response 401
    + Body

            {
                "status_code": 401,
                "error": true,
                "error_code": "unauthorized",
                "message": "User does not have access to this resource."
            }

### Undo Feed Hiding [/advancedactivity/feeds/un-hide-item]
#### Undo Feed Hiding [POST]
Un-hide activity feeds.

| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `type`     | yes      | integer | activity_action | table item type
| `id`     | yes      | integer | 122 | activity feed id


+ Request valid
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb
            oauth_token: 276ed9327d0478c2606adea438f4fd15
            oauth_secret: ffaff6754b5d4ca5f2fc4cc2a3a75478

    + Body

            {
                "type": "activity_action",
                "id": 122
            }


+ Response 204

+ Response 401
    + Body

            {
                "status_code": 401,
                "error": true,
                "error_code": "unauthorized",
                "message": "User does not have access to this resource."
            }


### Get Likes and Comments [/advancedactivity/feeds/likes-comments{?action_id}{&comment_id}{&viewAllLikes}{&viewAllComments}]
#### Get Likes and Comments [GET]
Get the paginated list of likes and comments respect of any feed or feed-comment.

| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `action_id`     | yes      | integer | 122 | activity feed id
| `comment_id`     | no      | integer | 89 | activity feed comment id
| `viewAllLikes`     | no      | integer | 1 | get list of users, who like to this feed
| `viewAllComments`     | no      | integer | 1 | get list of users, who comment to this feed

+ Request
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb

+ Response 200
    + Headers

            Content-Type: application/json

    + Body

            {
                "viewAllLikesBy": [  
                    {
                        "user_id": 1,
                        "username": "admin",
                        "displayname": "Jennifer Aniston",
                        "photo_id": 0,
                        "status": "Bmcjgfigf",
                        "status_date": "2015-03-10 08:11:45",
                        "locale": "auto",
                        "language": "en_US",
                        "timezone": "US/Pacific",
                        "search": 1,
                        "show_profileviewers": 1,
                        "level_id": 1,
                        "enabled": 1,
                        "verified": 1,
                        "approved": 1,
                        "creation_date": "2014-11-15 08:27:34",
                        "modified_date": "2015-03-10 08:11:45",
                        "lastlogin_date": "2015-03-20 06:12:08",
                        "update_date": null,
                        "member_count": 2,
                        "view_count": 87,
                        "location": "",
                        "url": "http://example.com/profile/admin",
                        "photo_url": "http://dev1.bigsteptech.in"
                    }
                ],
                  "viewAllComments": [  
                    {  
                        "action_id": "670",
                        "comment_id": 50,
                        "author_photo": "http://dev1.bigsteptech.in",
                        "author_title": "Jennifer Aniston",
                        "comment_body": "my first comment",
                        "comment_date": "2015-03-20 09:59:31",
                        "delete": {  
                          "name": "delete",
                          "label": "Delete",
                          "url": "comment-delete",
                          "urlParams": {  
                              "action_id": "670",
                              "subject_type": "group",
                              "subject_id": 74,
                              "comment_id": 50
                          }
                        },
                        "like": {  
                            "name": "like",
                            "label": "Like",
                            "url": "like",
                            "urlParams": {  
                                "action_id": "670",
                                "subject_type": "group",
                                "subject_id": 74,
                                "comment_id": 50
                            },
                            "isLike": 0
                        }
                    },
                    {  
                        "action_id": "670",
                        "comment_id": 51,
                        "author_photo": "http://dev1.bigsteptech.in",
                        "author_title": "Jennifer Aniston",
                        "comment_body": "My second comment",
                        "comment_date": "2015-03-20 09:59:49",
                        "delete": {  
                            "name": "delete",
                            "label": "Delete",
                            "url": "comment-delete",
                            "urlParams": {  
                              "action_id": "670",
                              "subject_type": "group",
                              "subject_id": 74,
                              "comment_id": 51
                            }
                        },
                        "like": {  
                            "name": "like",
                            "label": "Like",
                            "url": "like",
                            "urlParams": {  
                              "action_id": "670",
                              "subject_type": "group",
                              "subject_id": 74,
                              "comment_id": 51
                            },
                            "isLike": 0
                        }
                    }
                ],
                "isLike": 0,
                "canComment": 1,
                "canDelete": 1,
                "getTotalComments": 2,
                "getTotalLikes": 1
            }


+ Response 401
    + Body

            {
                "status_code": 401,
                "error": true,
                "error_code": "unauthorized",
                "message": "User does not have access to this resource."
            }


### Edit Feed [/advancedactivity/editfeed]
#### Edit Feed [POST]
Edit the body of the feed

| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| action_id   |    yes   |  int   |   20             |          The action_id of the feed                         |
| body        |  yes     | string |  This is awesome |          The body of the feed                              |

+ Request
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb


+ Response 400
    + Body

            {
                "status_code": 400,
                "error": true,
                "error_code": "parameter_missing",
                "message": "Missing Parameters: action_id"
            }


+ Response 400
    + Body
            {
                "status_code": 400,
                "error": true,
                "error_code": "invalid_method",
                "message": "Incorrect method"
            }

+ Response 201
    + Body

            {
                "status_code": 204
            }


### Get All Activity Form [/advancedactivity/feelings/get-status-form]
#### Get All Form [GET]
Get all the form like Sell Post, Schedule Time, Target Audience.

+ Request
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb

+ Response 200
    + Headers

            Content-Type: application/json
    + Body

            {
                "form": 
                        {
                        "targetForm": [
                            {
                                "type": "Radio",
                                "name": "who",
                                "label": "Gender",
                                "description": "Target your audience to whom you want to show this post",
                                "multiOptions": {
                                    "": "All",
                                    "male": "Male",
                                    "female": "Female"
                                },
                                "value": ""
                            },
                            {
                                "type": "Select",
                                "name": "min_age",
                                "label": "Min age",
                                "multiOptions": {
                                    "0": "Min age",
                                    "14": 14,
                                    "15": 15,
                                    "16": 16,
                                    "17": 17,
                                    "18": 18,
                                    "19": 19,
                                    "20": 20,
                                    "21": 21,
                                    "22": 22,
                                    "23": 23,
                                    "24": 24,
                                    "25": 25,
                                    "26": 26,
                                    "27": 27,
                                    "28": 28,
                                    "29": 29,
                                    "30": 30,
                                    "31": 31,
                                    "32": 32,
                                    "33": 33,
                                    "34": 34,
                                    "35": 35,
                                    "36": 36,
                                    "37": 37,
                                    "38": 38,
                                    "39": 39,
                                    "40": 40,
                                    "41": 41,
                                    "42": 42,
                                    "43": 43,
                                    "44": 44,
                                    "45": 45,
                                    "46": 46,
                                    "47": 47,
                                    "48": 48,
                                    "49": 49,
                                    "50": 50,
                                    "51": 51,
                                    "52": 52,
                                    "53": 53,
                                    "54": 54,
                                    "55": 55,
                                    "56": 56,
                                    "57": 57,
                                    "58": 58,
                                    "59": 59,
                                    "60": 60,
                                    "61": 61,
                                    "62": 62,
                                    "63": 63,
                                    "64": 64,
                                    "65": 65,
                                    "66": 66,
                                    "67": 67,
                                    "68": 68,
                                    "69": 69,
                                    "70": 70,
                                    "71": 71,
                                    "72": 72,
                                    "73": 73,
                                    "74": 74,
                                    "75": 75,
                                    "76": 76,
                                    "77": 77,
                                    "78": 78,
                                    "79": 79,
                                    "80": 80,
                                    "81": 81,
                                    "82": 82,
                                    "83": 83,
                                    "84": 84,
                                    "85": 85,
                                    "86": 86,
                                    "87": 87,
                                    "88": 88,
                                    "89": 89,
                                    "90": 90,
                                    "91": 91,
                                    "92": 92,
                                    "93": 93,
                                    "94": 94,
                                    "95": 95,
                                    "96": 96,
                                    "97": 97,
                                    "98": 98,
                                    "99": 99,
                                    "100": 100,
                                    "101": 101
                                },
                                "value": 0,
                                "hasValidator": true
                            },
                            {
                                "type": "Select",
                                "name": "max_age",
                                "label": "Max age",
                                "multiOptions": {
                                    "0": "Max age",
                                    "14": 14,
                                    "15": 15,
                                    "16": 16,
                                    "17": 17,
                                    "18": 18,
                                    "19": 19,
                                    "20": 20,
                                    "21": 21,
                                    "22": 22,
                                    "23": 23,
                                    "24": 24,
                                    "25": 25,
                                    "26": 26,
                                    "27": 27,
                                    "28": 28,
                                    "29": 29,
                                    "30": 30,
                                    "31": 31,
                                    "32": 32,
                                    "33": 33,
                                    "34": 34,
                                    "35": 35,
                                    "36": 36,
                                    "37": 37,
                                    "38": 38,
                                    "39": 39,
                                    "40": 40,
                                    "41": 41,
                                    "42": 42,
                                    "43": 43,
                                    "44": 44,
                                    "45": 45,
                                    "46": 46,
                                    "47": 47,
                                    "48": 48,
                                    "49": 49,
                                    "50": 50,
                                    "51": 51,
                                    "52": 52,
                                    "53": 53,
                                    "54": 54,
                                    "55": 55,
                                    "56": 56,
                                    "57": 57,
                                    "58": 58,
                                    "59": 59,
                                    "60": 60,
                                    "61": 61,
                                    "62": 62,
                                    "63": 63,
                                    "64": 64,
                                    "65": 65,
                                    "66": 66,
                                    "67": 67,
                                    "68": 68,
                                    "69": 69,
                                    "70": 70,
                                    "71": 71,
                                    "72": 72,
                                    "73": 73,
                                    "74": 74,
                                    "75": 75,
                                    "76": 76,
                                    "77": 77,
                                    "78": 78,
                                    "79": 79,
                                    "80": 80,
                                    "81": 81,
                                    "82": 82,
                                    "83": 83,
                                    "84": 84,
                                    "85": 85,
                                    "86": 86,
                                    "87": 87,
                                    "88": 88,
                                    "89": 89,
                                    "90": 90,
                                    "91": 91,
                                    "92": 92,
                                    "93": 93,
                                    "94": 94,
                                    "95": 95,
                                    "96": 96,
                                    "97": 97,
                                    "98": 98,
                                    "99": 99,
                                    "100": 100,
                                    "101": 101
                                },
                                "value": 0,
                                "hasValidator": true
                            }
                        ],
                        "scheduleForm": [
                            {
                                "type": "date",
                                "name": "schedule_time",
                                "label": "Schedule Your Post",
                                "description": "Select date and time on which you want to publish your post",
                                "hasValidator": true
                            }
                        ],
                        "sellingForm": [
                            {
                                "type": "Text",
                                "name": "title",
                                "label": "What to sell?",
                                "hasValidator": true
                            },
                            {
                                "type": "Select",
                                "name": "currency",
                                "label": "Currency",
                                "multiOptions": {
                                    "ADP": "ADP",
                                    "AED": "AED",
                                    "AFA": "AFA",
                                    "AFN": "AFN",
                                    "ALK": "ALK",
                                    "ALL": "ALL",
                                    "AMD": "AMD",
                                    "ANG": "ANG",
                                    "AOA": "AOA",
                                    "AOK": "AOK",
                                    "AON": "AON",
                                    "AOR": "AOR",
                                    "ARA": "ARA",
                                    "ARL": "ARL",
                                    "ARM": "ARM",
                                    "ARP": "ARP",
                                    "ARS": "ARS",
                                    "ATS": "ATS",
                                    "AUD": "AUD",
                                    "AWG": "AWG",
                                    "AZM": "AZM",
                                    "AZN": "AZN",
                                    "BAD": "BAD",
                                    "BAM": "BAM",
                                    "BAN": "BAN",
                                    "BBD": "BBD",
                                    "BDT": "BDT",
                                    "BEC": "BEC",
                                    "BEF": "BEF",
                                    "BEL": "BEL",
                                    "BGL": "BGL",
                                    "BGM": "BGM",
                                    "BGN": "BGN",
                                    "BGO": "BGO",
                                    "BHD": "BHD",
                                    "BIF": "BIF",
                                    "BMD": "BMD",
                                    "BND": "BND",
                                    "BOB": "BOB",
                                    "BOL": "BOL",
                                    "BOP": "BOP",
                                    "BOV": "BOV",
                                    "BRB": "BRB",
                                    "BRC": "BRC",
                                    "BRE": "BRE",
                                    "BRL": "BRL",
                                    "BRN": "BRN",
                                    "BRR": "BRR",
                                    "BRZ": "BRZ",
                                    "BSD": "BSD",
                                    "BTN": "BTN",
                                    "BUK": "BUK",
                                    "BWP": "BWP",
                                    "BYB": "BYB",
                                    "BYR": "BYR",
                                    "BZD": "BZD",
                                    "CAD": "CAD",
                                    "CDF": "CDF",
                                    "CHE": "CHE",
                                    "CHF": "CHF",
                                    "CHW": "CHW",
                                    "CLE": "CLE",
                                    "CLF": "CLF",
                                    "CLP": "CLP",
                                    "CNX": "CNX",
                                    "CNY": "CNY",
                                    "COP": "COP",
                                    "COU": "COU",
                                    "CRC": "CRC",
                                    "CSD": "CSD",
                                    "CSK": "CSK",
                                    "CUC": "CUC",
                                    "CUP": "CUP",
                                    "CVE": "CVE",
                                    "CYP": "CYP",
                                    "CZK": "CZK",
                                    "DDM": "DDM",
                                    "DEM": "DEM",
                                    "DJF": "DJF",
                                    "DKK": "DKK",
                                    "DOP": "DOP",
                                    "DZD": "DZD",
                                    "ECS": "ECS",
                                    "ECV": "ECV",
                                    "EEK": "EEK",
                                    "EGP": "EGP",
                                    "ERN": "ERN",
                                    "ESA": "ESA",
                                    "ESB": "ESB",
                                    "ESP": "ESP",
                                    "ETB": "ETB",
                                    "EUR": "EUR",
                                    "FIM": "FIM",
                                    "FJD": "FJD",
                                    "FKP": "FKP",
                                    "FRF": "FRF",
                                    "GBP": "GBP",
                                    "GEK": "GEK",
                                    "GEL": "GEL",
                                    "GHC": "GHC",
                                    "GHS": "GHS",
                                    "GIP": "GIP",
                                    "GMD": "GMD",
                                    "GNF": "GNF",
                                    "GNS": "GNS",
                                    "GQE": "GQE",
                                    "GRD": "GRD",
                                    "GTQ": "GTQ",
                                    "GWE": "GWE",
                                    "GWP": "GWP",
                                    "GYD": "GYD",
                                    "HKD": "HKD",
                                    "HNL": "HNL",
                                    "HRD": "HRD",
                                    "HRK": "HRK",
                                    "HTG": "HTG",
                                    "HUF": "HUF",
                                    "IDR": "IDR",
                                    "IEP": "IEP",
                                    "ILP": "ILP",
                                    "ILR": "ILR",
                                    "ILS": "ILS",
                                    "INR": "INR",
                                    "IQD": "IQD",
                                    "IRR": "IRR",
                                    "ISJ": "ISJ",
                                    "ISK": "ISK",
                                    "ITL": "ITL",
                                    "JMD": "JMD",
                                    "JOD": "JOD",
                                    "JPY": "JPY",
                                    "KES": "KES",
                                    "KGS": "KGS",
                                    "KHR": "KHR",
                                    "KMF": "KMF",
                                    "KPW": "KPW",
                                    "KRH": "KRH",
                                    "KRO": "KRO",
                                    "KRW": "KRW",
                                    "KWD": "KWD",
                                    "KYD": "KYD",
                                    "KZT": "KZT",
                                    "LAK": "LAK",
                                    "LBP": "LBP",
                                    "LKR": "LKR",
                                    "LRD": "LRD",
                                    "LSL": "LSL",
                                    "LTL": "LTL",
                                    "LTT": "LTT",
                                    "LUC": "LUC",
                                    "LUF": "LUF",
                                    "LUL": "LUL",
                                    "LVL": "LVL",
                                    "LVR": "LVR",
                                    "LYD": "LYD",
                                    "MAD": "MAD",
                                    "MAF": "MAF",
                                    "MCF": "MCF",
                                    "MDC": "MDC",
                                    "MDL": "MDL",
                                    "MGA": "MGA",
                                    "MGF": "MGF",
                                    "MKD": "MKD",
                                    "MKN": "MKN",
                                    "MLF": "MLF",
                                    "MMK": "MMK",
                                    "MNT": "MNT",
                                    "MOP": "MOP",
                                    "MRO": "MRO",
                                    "MTL": "MTL",
                                    "MTP": "MTP",
                                    "MUR": "MUR",
                                    "MVP": "MVP",
                                    "MVR": "MVR",
                                    "MWK": "MWK",
                                    "MXN": "MXN",
                                    "MXP": "MXP",
                                    "MXV": "MXV",
                                    "MYR": "MYR",
                                    "MZE": "MZE",
                                    "MZM": "MZM",
                                    "MZN": "MZN",
                                    "NAD": "NAD",
                                    "NGN": "NGN",
                                    "NIC": "NIC",
                                    "NIO": "NIO",
                                    "NLG": "NLG",
                                    "NOK": "NOK",
                                    "NPR": "NPR",
                                    "NZD": "NZD",
                                    "OMR": "OMR",
                                    "PAB": "PAB",
                                    "PEI": "PEI",
                                    "PEN": "PEN",
                                    "PES": "PES",
                                    "PGK": "PGK",
                                    "PHP": "PHP",
                                    "PKR": "PKR",
                                    "PLN": "PLN",
                                    "PLZ": "PLZ",
                                    "PTE": "PTE",
                                    "PYG": "PYG",
                                    "QAR": "QAR",
                                    "RHD": "RHD",
                                    "ROL": "ROL",
                                    "RON": "RON",
                                    "RSD": "RSD",
                                    "RUB": "RUB",
                                    "RUR": "RUR",
                                    "RWF": "RWF",
                                    "SAR": "SAR",
                                    "SBD": "SBD",
                                    "SCR": "SCR",
                                    "SDD": "SDD",
                                    "SDG": "SDG",
                                    "SDP": "SDP",
                                    "SEK": "SEK",
                                    "SGD": "SGD",
                                    "SHP": "SHP",
                                    "SIT": "SIT",
                                    "SKK": "SKK",
                                    "SLL": "SLL",
                                    "SOS": "SOS",
                                    "SRD": "SRD",
                                    "SRG": "SRG",
                                    "SSP": "SSP",
                                    "STD": "STD",
                                    "SUR": "SUR",
                                    "SVC": "SVC",
                                    "SYP": "SYP",
                                    "SZL": "SZL",
                                    "THB": "THB",
                                    "TJR": "TJR",
                                    "TJS": "TJS",
                                    "TMM": "TMM",
                                    "TMT": "TMT",
                                    "TND": "TND",
                                    "TOP": "TOP",
                                    "TPE": "TPE",
                                    "TRL": "TRL",
                                    "TRY": "TRY",
                                    "TTD": "TTD",
                                    "TWD": "TWD",
                                    "TZS": "TZS",
                                    "UAH": "UAH",
                                    "UAK": "UAK",
                                    "UGS": "UGS",
                                    "UGX": "UGX",
                                    "USD": "USD",
                                    "USN": "USN",
                                    "USS": "USS",
                                    "UYI": "UYI",
                                    "UYP": "UYP",
                                    "UYU": "UYU",
                                    "UZS": "UZS",
                                    "VEB": "VEB",
                                    "VEF": "VEF",
                                    "VND": "VND",
                                    "VNN": "VNN",
                                    "VUV": "VUV",
                                    "WST": "WST",
                                    "XAF": "XAF",
                                    "XAG": "XAG",
                                    "XAU": "XAU",
                                    "XBA": "XBA",
                                    "XBB": "XBB",
                                    "XBC": "XBC",
                                    "XBD": "XBD",
                                    "XCD": "XCD",
                                    "XDR": "XDR",
                                    "XEU": "XEU",
                                    "XFO": "XFO",
                                    "XFU": "XFU",
                                    "XOF": "XOF",
                                    "XPD": "XPD",
                                    "XPF": "XPF",
                                    "XPT": "XPT",
                                    "XRE": "XRE",
                                    "XSU": "XSU",
                                    "XTS": "XTS",
                                    "XUA": "XUA",
                                    "XXX": "XXX",
                                    "YDD": "YDD",
                                    "YER": "YER",
                                    "YUD": "YUD",
                                    "YUM": "YUM",
                                    "YUN": "YUN",
                                    "YUR": "YUR",
                                    "ZAL": "ZAL",
                                    "ZAR": "ZAR",
                                    "ZMK": "ZMK",
                                    "ZMW": "ZMW",
                                    "ZRN": "ZRN",
                                    "ZRZ": "ZRZ",
                                    "ZWD": "ZWD",
                                    "ZWL": "ZWL",
                                    "ZWR": "ZWR"
                                },
                                "value": "USD",
                                "hasValidator": true
                            },
                            {
                                "type": "Text",
                                "name": "price",
                                "label": "What is price?",
                                "hasValidator": true
                            },
                            {
                                "type": "Text",
                                "name": "location",
                                "label": "Where to sell?",
                                "hasValidator": true
                            },
                            {
                                "type": "Textarea",
                                "name": "description",
                                "label": "Product description"
                            },
                            {
                                "type": "File",
                                "name": "photo",
                                "label": "Add Photo"
                            }
                        ]
                }
        }



### Feed Decoration and Word Styling Values [/advancedactivity/feeds/feed-decoration]
#### Feed Decoration and Word Styling Values [GET]
Get all the Feed Decoration and Word Styling Values from Admin Panel.

+ Request
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb

+ Response 200
    + Headers

            Content-Type: application/json
    + Body
            {
                "feed_docoration_setting": {
                    "char_length": 50,
                    "font_size": 30,
                    "font_color": "#ed2626",
                    "font_style": "normal",
                    "banner_feed_length": 100,
                    "banner_count": 10,
                    "banner_order": "random"
                },
                "word_styling": [
                    {
                        "word_id": 3,
                        "title": "Happy New Year",
                        "color": "#4f39e3",
                        "background_color": "#FFFFFF",
                        "style": "normal",
                        "params": {
                            "animation": "background-happy-new-year",
                            "bg_enabled": 0
                        }
                    },
                    {
                        "word_id": 4,
                        "title": "Happy Birthday",
                        "color": "#09961e",
                        "background_color": "#ffffff",
                        "style": "normal",
                        "params": {
                            "animation": "background-happy-birthday",
                            "bg_enabled": 0
                        }
                    },
                    {
                        "word_id": 5,
                        "title": "Merry Christmas",
                        "color": "#a1361f",
                        "background_color": "#FFFFFF",
                        "style": "normal",
                        "params": {
                            "animation": "background-merry-christmas",
                            "bg_enabled": 0
                        }
                    },
                    {
                        "word_id": 6,
                        "title": "Congratulations",
                        "color": "#0fd159",
                        "background_color": "#FFFFFF",
                        "style": "normal",
                        "params": {
                            "animation": "background-congratulations",
                            "bg_enabled": 0
                        }
                    },
                    {
                        "word_id": 7,
                        "title": "Happy Easter",
                        "color": "#0bb0b3",
                        "background_color": "#FFFFFF",
                        "style": "normal",
                        "params": {
                            "animation": "background-happy-easter",
                            "bg_enabled": 0
                        }
                    },
                    {
                        "word_id": 8,
                        "title": "Happy Thanksgiving",
                        "color": "#b5aa09",
                        "background_color": "#FFFFFF",
                        "style": "normal",
                        "params": {
                            "animation": "background-happy-thanksgiving",
                            "bg_enabled": 0
                        }
                    }
                ]
               
    }


### Feelings Types [/advancedactivity/feelings]
#### Get All Feelling Type [GET]
Get all the Feelings.

+ Request
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb

+ Response 200
    + Headers

            Content-Type: application/json
    + Body
            
            {
                "parent": [
                    {
                        "parent_id": 27,
                        "title": "feeling",
                        "photo": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/81/fa/01/847a3e830794ee5c90be490f8cdb208d.png",
                        "url": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/81/fa/01/847a3e830794ee5c90be490f8cdb208d.png",
                        "tagline": "How are you feeling?"
                    },
                    {
                        "parent_id": 26,
                        "title": "eating",
                        "photo": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/51/fa/01/fe7148025bf341639b1f08c4564b24f7.png",
                        "url": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/51/fa/01/fe7148025bf341639b1f08c4564b24f7.png",
                        "tagline": "What are you eating?"
                    },
                    {
                        "parent_id": 23,
                        "title": "celebrating",
                        "photo": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/1a/f9/01/70e200c1b672c15c13069342b4b7a4c1.png",
                        "url": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/1a/f9/01/70e200c1b672c15c13069342b4b7a4c1.png",
                        "tagline": " What are you celebrating?"
                    },
                    {
                        "parent_id": 22,
                        "title": "attending",
                        "photo": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/ef/f8/01/08a6f0706dc77b16bfee82a7bdd42933.png",
                        "url": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/ef/f8/01/08a6f0706dc77b16bfee82a7bdd42933.png",
                        "tagline": "What are you attending?"
                    },
                    {
                        "parent_id": 28,
                        "title": "getting",
                        "photo": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/70/fb/01/8328982761b6fb77c74e9731697e5bce.png",
                        "url": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/70/fb/01/8328982761b6fb77c74e9731697e5bce.png",
                        "tagline": "What are you getting?"
                    },
                    {
                        "parent_id": 29,
                        "title": "looking for",
                        "photo": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/96/fb/01/429401946d5c382ec6ba42b278783e93.png",
                        "url": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/96/fb/01/429401946d5c382ec6ba42b278783e93.png",
                        "tagline": "What are you looking for?"
                    },
                    {
                        "parent_id": 30,
                        "title": "making",
                        "photo": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/ef/fc/01/2c300f77e4e34436ed5b9dda865b1990.png",
                        "url": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/ef/fc/01/2c300f77e4e34436ed5b9dda865b1990.png",
                        "tagline": "What are you making?"
                    },
                    {
                        "parent_id": 25,
                        "title": "drinking",
                        "photo": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/2b/fa/01/1b8731a9785165d102478bce03a89a38.png",
                        "url": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/2b/fa/01/1b8731a9785165d102478bce03a89a38.png",
                        "tagline": "What are you drinking?"
                    },
                    {
                        "parent_id": 33,
                        "title": "thinking about",
                        "photo": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/7c/fd/01/b5b48258d919b5d043f0b03fda992efe.png",
                        "url": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/7c/fd/01/b5b48258d919b5d043f0b03fda992efe.png",
                        "tagline": "Which are you thinking about?"
                    },
                    {
                        "parent_id": 31,
                        "title": "meeting",
                        "photo": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/1e/fd/01/fd23615a11aef225974fe7183830eef4.png",
                        "url": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/1e/fd/01/fd23615a11aef225974fe7183830eef4.png",
                        "tagline": "Who are you meeting?"
                    },
                    {
                        "parent_id": 32,
                        "title": "rememberring",
                        "photo": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/62/fd/01/369a9a58cf9d8c5a04a4e29abb07091a.png",
                        "url": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/62/fd/01/369a9a58cf9d8c5a04a4e29abb07091a.png",
                        "tagline": "What are you remembering?"
                    },
                    {
                        "parent_id": 24,
                        "title": "custom",
                        "photo": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/05/fa/01/e30dd4e1d6c8edcd1e069a009d4580f3.png",
                        "url": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/05/fa/01/e30dd4e1d6c8edcd1e069a009d4580f3.png",
                        "tagline": "What are you doing?"
                    }
                ],
                "child": {
                    "22": [
                        {
                            "child_id": 268,
                            "title": "a birthday party",
                            "photo": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/ef/f8/01/08a6f0706dc77b16bfee82a7bdd42933.png",
                            "url": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/ef/f8/01/08a6f0706dc77b16bfee82a7bdd42933.png",
                            "type": ""
                        },
                        {
                            "child_id": 269,
                            "title": "a businees meeting",
                            "photo": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/f1/f8/01/21ad07c5f532ed376bf16b178c95bf5a.png",
                            "url": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/f1/f8/01/21ad07c5f532ed376bf16b178c95bf5a.png",
                            "type": ""
                        }
                ],
                "23": [
                        {
                            "child_id": 289,
                            "title": "july",
                            "photo": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/1a/f9/01/70e200c1b672c15c13069342b4b7a4c1.png",
                            "url": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/1a/f9/01/70e200c1b672c15c13069342b4b7a4c1.png",
                            "type": ""
                        },
                        {
                            "child_id": 290,
                            "title": "Another year of life",
                            "photo": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/1c/f9/01/217ade3a256a298eed998bc1abfb7a37.png",
                            "url": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/1c/f9/01/217ade3a256a298eed998bc1abfb7a37.png",
                            "type": ""
                        }
                    ]
            }
        }

### Feeling Activity Post [/advancedactivity/feeds/post]
#### Post Feeling Activity [POST]
Post a new update in activity feeds. Share a new post from status update box.


| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `body`     | yes      | string | Alone | status update post's content
| `toValues`     | no      | string | 2,5,6 | comma-separated IDs of tagged friends
| `auth_view`     | no      | string | everyone | authorization permission (privacy) to view feed / status update
| `composer`     | yes      | array | {"feeling": {...}} | N/A


+ Request valid
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb

    + Body

        {
            "body": "Alone",
            "auth_view": "everyone",                
            "composer": {"feeling":
                            {
                                "parent" :"27",
                                "child" :"470",
                                "type":"",
                                "childtitle":"alone"
                                }
                        }
        }

+ Response 201

+ Response 401
    + Headers

            Content-Type: application/json
    + Body

            {
                "status_code": 401,
                "error": true,
                "error_code": "unauthorized",
                "message": "User does not have access to this resource."
            }



### Get All Banner [/advancedactivity/feelings/banner]
#### Get All Banner [GET]
Get all the Banner.

+ Request
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb

+ Response 200
    + Headers

            Content-Type: application/json
    + Body
                [
                {
                    "backgroundImage": "url(http://mobiledemodevelopment.s3.amazonaws.com/public/system/c8/fd/01/57c491727e9eb8dc7b10fe043bf53ed1.png)",
                    "backgroundImageurl": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/c8/fd/01/57c491727e9eb8dc7b10fe043bf53ed1.png",
                    "backgroundColor": "#1dcc92",
                    "color": "#ffffff",
                    "highlighted": 0
                },
                {
                    "backgroundImage": "url(http://mobiledemodevelopment.s3.amazonaws.com/public/system/ca/fd/01/1b4891b922e4c5e3c3f810ee3733651b.jpg)",
                    "backgroundImageurl": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/ca/fd/01/1b4891b922e4c5e3c3f810ee3733651b.jpg",
                    "backgroundColor": "#1dcc92",
                    "color": "#c70eab",
                    "highlighted": 0
                },
                {
                    "backgroundImage": "url(http://mobiledemodevelopment.s3.amazonaws.com/public/system/c4/fd/01/37fee372934f46049cc7ed5d59f02651.jpg)",
                    "backgroundImageurl": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/c4/fd/01/37fee372934f46049cc7ed5d59f02651.jpg",
                    "backgroundColor": "#1dcc92",
                    "color": "#ffffff",
                    "highlighted": 0
                },
                {
                    "backgroundImage": "url(http://mobiledemodevelopment.s3.amazonaws.com/public/system/cc/fd/01/239a0a928963aee7d72e382e48346f4f.png)",
                    "backgroundImageurl": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/cc/fd/01/239a0a928963aee7d72e382e48346f4f.png",
                    "backgroundColor": "#1dcc92",
                    "color": "#e30707",
                    "highlighted": 0
                },
                {
                    "backgroundImage": "url(http://mobiledemodevelopment.s3.amazonaws.com/public/system/d2/fd/01/89ec8ee2d61eca438a738959d03068fe.jpg)",
                    "backgroundImageurl": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/d2/fd/01/89ec8ee2d61eca438a738959d03068fe.jpg",
                    "backgroundColor": "#1dcc92",
                    "color": "#ffffff",
                    "highlighted": 0
                },
                {
                    "backgroundImage": "url(http://mobiledemodevelopment.s3.amazonaws.com/public/system/c6/fd/01/67334a048b4cf5f6c8d56886abbac39e.png)",
                    "backgroundImageurl": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/c6/fd/01/67334a048b4cf5f6c8d56886abbac39e.png",
                    "backgroundColor": "#1dcc92",
                    "color": "#ffffff",
                    "highlighted": 0
                },
                {
                    "backgroundImage": "url(http://mobiledemodevelopment.s3.amazonaws.com/public/system/ce/fd/01/b278d6735e58ec437000911b556fe6f8.jpg)",
                    "backgroundImageurl": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/ce/fd/01/b278d6735e58ec437000911b556fe6f8.jpg",
                    "backgroundColor": "#1dcc92",
                    "color": "#ffffff",
                    "highlighted": 0
                },
                {
                    "backgroundImage": "url(http://mobiledemodevelopment.s3.amazonaws.com/public/system/d0/fd/01/957216df2b7fb174380e3b1573241e09.jpg)",
                    "backgroundImageurl": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/d0/fd/01/957216df2b7fb174380e3b1573241e09.jpg",
                    "backgroundColor": "#1dcc92",
                    "color": "#ffffff",
                    "highlighted": 0
                },
                {
                    "backgroundImage": "url(http://mobiledemodevelopment.s3.amazonaws.com/public/system/d4/fd/01/bfefd25f6a1046cbcdde1f3831abedce.jpg)",
                    "backgroundImageurl": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/d4/fd/01/bfefd25f6a1046cbcdde1f3831abedce.jpg",
                    "backgroundColor": "#1dcc92",
                    "color": "#ffffff",
                    "highlighted": 0
                },
                {
                    "backgroundImage": "url(http://mobiledemodevelopment.s3.amazonaws.com/public/system/d6/fd/01/9c1d1940c9f6c5fe33641aac00c6fcdb.png)",
                    "backgroundImageurl": "http://mobiledemodevelopment.s3.amazonaws.com/public/system/d6/fd/01/9c1d1940c9f6c5fe33641aac00c6fcdb.png",
                    "backgroundColor": "#1dcc92",
                    "color": "#ffffff",
                    "highlighted": 0
                }
            ]


### Post Status With Banner [/advancedactivity/feeds/post]
#### Status with Banner [POST]
Post a new update in activity feeds. Share a new post from status update box.


| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `body`     | yes      | string | Happpy Birthday | status update post's content
| `post_attach`     | no      | int | 1 | It will indicate posting status with attachment or not
| `toValues`     | no      | string | 2,5,6 | comma-separated IDs of tagged friends
| `auth_view`     | no      | string | everyone | authorization permission (privacy) to view feed / status update
| `composer`     | yes      | array | {"feeling": {...}} | N/A


+ Request valid
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb

    + Body

        {
            "body": "Happpy Birthday",
            "post_attach":0,
            "auth_view": "everyone",                
            "composer": {"banner":
                            {
                                    "image":" url(/restapiupgrade/public/system/cd/06/648a43f1927d912aaf6213525e434ad5.jpg)",
                                "color":"#ffffff",
                                "background-color" : "#1dcc92"
                            }
                        }
        }

+ Response 201

+ Response 401
    + Headers

            Content-Type: application/json
    + Body

            {
                "status_code": 401,
                "error": true,
                "error_code": "unauthorized",
                "message": "User does not have access to this resource."
            }


### Post Feed With Target User [/advancedactivity/feeds/post]
#### Target User [POST]
Post a new update in activity feeds. Share a new post from status update box.


| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `body`     | yes      | Happy | Happpy Birthday | status update post's content
| `post_attach`     | no      | int | 1 | It will indicate posting status with attachment or not
| `max_age`     | yes      | int | 35 | Maximum user age
| `min_age`     | yes     | int | 18 | Minimum user age
| `who`     | yes      | string | male | Target User male or female
| `auth_view`     | no      | string | everyone | authorization permission (privacy) to view feed / status update



+ Request valid
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb

    + Body

        {
            "who":male
            "min_age":13
            "max_age":40
            "body":target user
            "post_attach":0
        }

+ Response 201

+ Response 401
    + Headers

            Content-Type: application/json
    + Body

            {
                "status_code": 401,
                "error": true,
                "error_code": "unauthorized",
                "message": "User does not have access to this resource."
            }


### Sell Post [/advancedactivity/feeds/post]
#### Sell Post [POST]
Post a new update in activity feeds. Share a new post from status update box.


| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `type`     | yes      | string | sell | This is attachment type
| `post_attach`     | yes      | int | 1 | It will indicate posting status with attachment or not
| `title`     | yes      | string |Bike | you can give here product name
| `description`     | no     | string | old Bike | Discription of product.
| `currency`     | yes      | string | USD | Currency Type
|`price`     | yes      | int | 50000 | Price of product
| `location`     | yes     | string | New York | Where do you want to sell 
| `photo`     | no      | file | abg.jpeg | Photos of product
| `auth_view`     | no      | string | everyone | authorization permission (privacy) to view feed / status update



+ Request valid
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb

    + Body

        {
            type:sell
            title:Shell Phone
            description:my phone
            currency:USD
            price:10
            location:Gurgaon
            post_attach:1
            photo:
        }

+ Response 201

+ Response 401
    + Headers

            Content-Type: application/json
    + Body

            {
                "status_code": 401,
                "error": true,
                "error_code": "unauthorized",
                "message": "User does not have access to this resource."
            }


### Sticker Post [/advancedactivity/feeds/post]
#### Sticker Post [POST]
Post a new update in activity feeds. Share a new post from status update box.


| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `type`     | yes      | string | sell | This is attachment type
| `post_attach`     | yes      | int | 1 | It will indicate posting status with attachment or not
| `sticker_guid`     | yes      | string |Bike | you can give here product name
|`body`              |no        | string       |Write something
| `auth_view`     | no      | string | everyone | authorization permission (privacy) to view feed / status update



+ Request valid
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb

    + Body

        {
            type:sell
            sticker_guid:sitereaction_sticker_38
            post_attach:1
        }

+ Response 201

+ Response 401
    + Headers

            Content-Type: application/json
    + Body

            {
                "status_code": 401,
                "error": true,
                "error_code": "unauthorized",
                "message": "User does not have access to this resource."
            }

### Pin Unpin Post [/advancedactivity/pin-unpin]
#### Pin Unpin Post [POST]
Post a new update in activity feeds. Share a new post from status update box.


| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `time`     | yes      | int | 3 | show this post top of the feed at 3 days
| `action_id`     | yes      | int | 4661 | feed action id
| `type`     | yes      | string |sitegroup_group_97 | type of feed



+ Request valid
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb

    + Body

        {
            type:sitegroup_group_97
            action_id:1238
            time:7
        }

+ Response 201

+ Response 401
    + Headers

            Content-Type: application/json
    + Body

            {
                "status_code": 401,
                "error": true,
                "error_code": "unauthorized",
                "message": "User does not have access to this resource."
            }


### Turn on of Notification   [/advancedactivity/turn-on-off-notification]
#### On Off Notification  [POST]
Post a new update in activity feeds. Share a new post from status update box.


| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |

| `action_id`     | yes      | int | 15465 | feed action id




+ Request valid
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb

    + Body

        {
           
            "action_id":1238
           
        }

+ Response 201

+ Response 401
    + Headers

            Content-Type: application/json
    + Body

            {
                "status_code": 401,
                "error": true,
                "error_code": "unauthorized",
                "message": "User does not have access to this resource."
            }

