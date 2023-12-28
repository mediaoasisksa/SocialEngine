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
* Advanced Comments Plugin - Nested Comments, Replies, Voting & Attachments



## Advanced Comments Plugin - Nested Comments, Replies, Voting & Attachments

These are the APIs for the SocialEngineAddOns Plugin: [Advanced Comments Plugin - Nested Comments, Replies, Voting & Attachments](http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin).


### Get Likes and Comments [/advancedcomments/likes-comments{?subject_id}{&subject_type}{&view_all_comments}{&view_all_likes}]
#### Get Likes and Comments [GET]
Get the paginated list of likes and comments respect of any feed or feed-comment.

| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `subject_type`     | yes      | string | event | type of content
| `subject_id`     | yes      | integer | 89 | content id
| `view_all_comments`     | no      | integer | 1 | get list of users, who like to this content
| `view_all_likes`     | no      | integer | 1 | get list of users, who comment to this content

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
    "reply_on_comment"[
        "8": [
            {
                "subject_id": 2,
                "comment_id": 9,
                "author_image": "http://xyz.com/public/user/dc/06/85e52612611096313d6d0e8dbf253e01.jpg",
                "author_image_normal": "http://xyz.com/public/user/de/06/4eabde367e5fc8e74d8debf6d6c1c178.jpg",
                "author_image_profile": "http://xyz.com/public/user/dd/06/7ecbb0669fdc23d5b282b9ad82e11e39.jpg",
                "author_image_icon": "http://xyz.com/public/user/df/06/e3d4104559ee5ed67f7a51d1a753b7fd.jpg",
                "content_url": "http://xyz.com/profile/Sanjeet",
                "author_title": "Sanjeet",
                "user_id": 1,
                "comment_body": "every good event",
                "comment_date": "2017-12-04 07:43:02",
                "params": null,
                "delete": {
                    "name": "delete",
                    "label": "Delete",
                    "url": "comment-delete",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 9
                    }
                },
                "like_count": 0,
                "like": {
                    "name": "like",
                    "label": "Like",
                    "url": "like",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 9
                    },
                    "isLike": 0
                }
            }
        ],
        "9": [
            {
                "subject_id": 2,
                "comment_id": 29,
                "author_image": "http://xyz.com/public/user/dc/06/85e52612611096313d6d0e8dbf253e01.jpg",
                "author_image_normal": "http://xyz.com/public/user/de/06/4eabde367e5fc8e74d8debf6d6c1c178.jpg",
                "author_image_profile": "http://xyz.com/public/user/dd/06/7ecbb0669fdc23d5b282b9ad82e11e39.jpg",
                "author_image_icon": "http://xyz.com/public/user/df/06/e3d4104559ee5ed67f7a51d1a753b7fd.jpg",
                "content_url": "http://xyz.com/profile/Sanjeet",
                "author_title": "Sanjeet",
                "user_id": 1,
                "comment_body": "ggodd",
                "comment_date": "2017-12-11 10:28:20",
                "params": null,
                "delete": {
                    "name": "delete",
                    "label": "Delete",
                    "url": "comment-delete",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 29
                    }
                },
                "like_count": 0,
                "like": {
                    "name": "like",
                    "label": "Like",
                    "url": "like",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 29
                    },
                    "isLike": 0
                }
            }
        ],
        "18": [
            {
                "subject_id": 2,
                "comment_id": 19,
                "author_image": "http://xyz.com/public/user/eb/06/77e10e0217f2b11224aac5b822233e99.jpg",
                "author_image_normal": "http://xyz.com/public/user/ed/06/fb213e3006956ba55a74fa77a976e394.jpg",
                "author_image_profile": "http://xyz.com/public/user/ec/06/6e8cc0dcf56d8049f135189f941e6ea5.jpg",
                "author_image_icon": "http://xyz.com/public/user/ee/06/6e7c75463334313402b9daed2a4ff67a.jpg",
                "content_url": "http://xyz.com/profile/test08",
                "author_title": "Shyam Kumar",
                "user_id": 4,
                "comment_body": "nice",
                "comment_date": "2017-12-07 11:29:43",
                "params": null,
                "delete": {
                    "name": "delete",
                    "label": "Delete",
                    "url": "comment-delete",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 19
                    }
                },
                "like_count": 0,
                "like": {
                    "name": "like",
                    "label": "Like",
                    "url": "like",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 19
                    },
                    "isLike": 0
                }
            }
        ],
        "22": [
            {
                "subject_id": 2,
                "comment_id": 38,
                "author_image": "http://xyz.com/public/user/dc/06/85e52612611096313d6d0e8dbf253e01.jpg",
                "author_image_normal": "http://xyz.com/public/user/de/06/4eabde367e5fc8e74d8debf6d6c1c178.jpg",
                "author_image_profile": "http://xyz.com/public/user/dd/06/7ecbb0669fdc23d5b282b9ad82e11e39.jpg",
                "author_image_icon": "http://xyz.com/public/user/df/06/e3d4104559ee5ed67f7a51d1a753b7fd.jpg",
                "content_url": "http://xyz.com/profile/Sanjeet",
                "author_title": "Sanjeet",
                "user_id": 1,
                "comment_body": "repy one",
                "comment_date": "2017-12-11 10:54:47",
                "params": null,
                "delete": {
                    "name": "delete",
                    "label": "Delete",
                    "url": "comment-delete",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 38
                    }
                },
                "like_count": 0,
                "like": {
                    "name": "like",
                    "label": "Like",
                    "url": "like",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 38
                    },
                    "isLike": 0
                }
            }
        ],
        "29": [
            {
                "subject_id": 2,
                "comment_id": 30,
                "author_image": "http://xyz.com/public/user/dc/06/85e52612611096313d6d0e8dbf253e01.jpg",
                "author_image_normal": "http://xyz.com/public/user/de/06/4eabde367e5fc8e74d8debf6d6c1c178.jpg",
                "author_image_profile": "http://xyz.com/public/user/dd/06/7ecbb0669fdc23d5b282b9ad82e11e39.jpg",
                "author_image_icon": "http://xyz.com/public/user/df/06/e3d4104559ee5ed67f7a51d1a753b7fd.jpg",
                "content_url": "http://xyz.com/profile/Sanjeet",
                "author_title": "Sanjeet",
                "user_id": 1,
                "comment_body": "dsdss",
                "comment_date": "2017-12-11 10:28:32",
                "params": null,
                "delete": {
                    "name": "delete",
                    "label": "Delete",
                    "url": "comment-delete",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 30
                    }
                },
                "like_count": 0,
                "like": {
                    "name": "like",
                    "label": "Like",
                    "url": "like",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 30
                    },
                    "isLike": 0
                }
            }
        ],
        "30": [
            {
                "subject_id": 2,
                "comment_id": 31,
                "author_image": "http://xyz.com/public/user/dc/06/85e52612611096313d6d0e8dbf253e01.jpg",
                "author_image_normal": "http://xyz.com/public/user/de/06/4eabde367e5fc8e74d8debf6d6c1c178.jpg",
                "author_image_profile": "http://xyz.com/public/user/dd/06/7ecbb0669fdc23d5b282b9ad82e11e39.jpg",
                "author_image_icon": "http://xyz.com/public/user/df/06/e3d4104559ee5ed67f7a51d1a753b7fd.jpg",
                "content_url": "http://xyz.com/profile/Sanjeet",
                "author_title": "Sanjeet",
                "user_id": 1,
                "comment_body": "niddkd",
                "comment_date": "2017-12-11 10:28:52",
                "params": null,
                "delete": {
                    "name": "delete",
                    "label": "Delete",
                    "url": "comment-delete",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 31
                    }
                },
                "like_count": 0,
                "like": {
                    "name": "like",
                    "label": "Like",
                    "url": "like",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 31
                    },
                    "isLike": 0
                }
            },
            {
                "subject_id": 2,
                "comment_id": 32,
                "author_image": "http://xyz.com/public/user/dc/06/85e52612611096313d6d0e8dbf253e01.jpg",
                "author_image_normal": "http://xyz.com/public/user/de/06/4eabde367e5fc8e74d8debf6d6c1c178.jpg",
                "author_image_profile": "http://xyz.com/public/user/dd/06/7ecbb0669fdc23d5b282b9ad82e11e39.jpg",
                "author_image_icon": "http://xyz.com/public/user/df/06/e3d4104559ee5ed67f7a51d1a753b7fd.jpg",
                "content_url": "http://xyz.com/profile/Sanjeet",
                "author_title": "Sanjeet",
                "user_id": 1,
                "comment_body": "niddkd",
                "comment_date": "2017-12-11 10:28:54",
                "params": null,
                "delete": {
                    "name": "delete",
                    "label": "Delete",
                    "url": "comment-delete",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 32
                    }
                },
                "like_count": 0,
                "like": {
                    "name": "like",
                    "label": "Like",
                    "url": "like",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 32
                    },
                    "isLike": 0
                }
            }
        ],
        "31": [
            {
                "subject_id": 2,
                "comment_id": 33,
                "author_image": "http://xyz.com/public/user/dc/06/85e52612611096313d6d0e8dbf253e01.jpg",
                "author_image_normal": "http://xyz.com/public/user/de/06/4eabde367e5fc8e74d8debf6d6c1c178.jpg",
                "author_image_profile": "http://xyz.com/public/user/dd/06/7ecbb0669fdc23d5b282b9ad82e11e39.jpg",
                "author_image_icon": "http://xyz.com/public/user/df/06/e3d4104559ee5ed67f7a51d1a753b7fd.jpg",
                "content_url": "http://xyz.com/profile/Sanjeet",
                "author_title": "Sanjeet",
                "user_id": 1,
                "comment_body": "nicd",
                "comment_date": "2017-12-11 10:30:49",
                "params": null,
                "delete": {
                    "name": "delete",
                    "label": "Delete",
                    "url": "comment-delete",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 33
                    }
                },
                "like_count": 0,
                "like": {
                    "name": "like",
                    "label": "Like",
                    "url": "like",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 33
                    },
                    "isLike": 0
                }
            }
        ],
        "33": [
            {
                "subject_id": 2,
                "comment_id": 34,
                "author_image": "http://xyz.com/public/user/dc/06/85e52612611096313d6d0e8dbf253e01.jpg",
                "author_image_normal": "http://xyz.com/public/user/de/06/4eabde367e5fc8e74d8debf6d6c1c178.jpg",
                "author_image_profile": "http://xyz.com/public/user/dd/06/7ecbb0669fdc23d5b282b9ad82e11e39.jpg",
                "author_image_icon": "http://xyz.com/public/user/df/06/e3d4104559ee5ed67f7a51d1a753b7fd.jpg",
                "content_url": "http://xyz.com/profile/Sanjeet",
                "author_title": "Sanjeet",
                "user_id": 1,
                "comment_body": "tesintgdlksd",
                "comment_date": "2017-12-11 10:52:01",
                "params": null,
                "delete": {
                    "name": "delete",
                    "label": "Delete",
                    "url": "comment-delete",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 34
                    }
                },
                "like_count": 0,
                "like": {
                    "name": "like",
                    "label": "Like",
                    "url": "like",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 34
                    },
                    "isLike": 0
                }
            }
        ],
        "34": [
            {
                "subject_id": 2,
                "comment_id": 35,
                "author_image": "http://xyz.com/public/user/dc/06/85e52612611096313d6d0e8dbf253e01.jpg",
                "author_image_normal": "http://xyz.com/public/user/de/06/4eabde367e5fc8e74d8debf6d6c1c178.jpg",
                "author_image_profile": "http://xyz.com/public/user/dd/06/7ecbb0669fdc23d5b282b9ad82e11e39.jpg",
                "author_image_icon": "http://xyz.com/public/user/df/06/e3d4104559ee5ed67f7a51d1a753b7fd.jpg",
                "content_url": "http://xyz.com/profile/Sanjeet",
                "author_title": "Sanjeet",
                "user_id": 1,
                "comment_body": "nidldlsl",
                "comment_date": "2017-12-11 10:53:12",
                "params": null,
                "delete": {
                    "name": "delete",
                    "label": "Delete",
                    "url": "comment-delete",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 35
                    }
                },
                "like_count": 0,
                "like": {
                    "name": "like",
                    "label": "Like",
                    "url": "like",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 35
                    },
                    "isLike": 0
                }
            },
            {
                "subject_id": 2,
                "comment_id": 36,
                "author_image": "http://xyz.com/public/user/dc/06/85e52612611096313d6d0e8dbf253e01.jpg",
                "author_image_normal": "http://xyz.com/public/user/de/06/4eabde367e5fc8e74d8debf6d6c1c178.jpg",
                "author_image_profile": "http://xyz.com/public/user/dd/06/7ecbb0669fdc23d5b282b9ad82e11e39.jpg",
                "author_image_icon": "http://xyz.com/public/user/df/06/e3d4104559ee5ed67f7a51d1a753b7fd.jpg",
                "content_url": "http://xyz.com/profile/Sanjeet",
                "author_title": "Sanjeet",
                "user_id": 1,
                "comment_body": "nidldlsl",
                "comment_date": "2017-12-11 10:53:14",
                "params": null,
                "delete": {
                    "name": "delete",
                    "label": "Delete",
                    "url": "comment-delete",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 36
                    }
                },
                "like_count": 0,
                "like": {
                    "name": "like",
                    "label": "Like",
                    "url": "like",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 36
                    },
                    "isLike": 0
                }
            },
            {
                "subject_id": 2,
                "comment_id": 37,
                "author_image": "http://xyz.com/public/user/dc/06/85e52612611096313d6d0e8dbf253e01.jpg",
                "author_image_normal": "http://xyz.com/public/user/de/06/4eabde367e5fc8e74d8debf6d6c1c178.jpg",
                "author_image_profile": "http://xyz.com/public/user/dd/06/7ecbb0669fdc23d5b282b9ad82e11e39.jpg",
                "author_image_icon": "http://xyz.com/public/user/df/06/e3d4104559ee5ed67f7a51d1a753b7fd.jpg",
                "content_url": "http://xyz.com/profile/Sanjeet",
                "author_title": "Sanjeet",
                "user_id": 1,
                "comment_body": "nidldlsl",
                "comment_date": "2017-12-11 10:53:15",
                "params": null,
                "delete": {
                    "name": "delete",
                    "label": "Delete",
                    "url": "comment-delete",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 37
                    }
                },
                "like_count": 0,
                "like": {
                    "name": "like",
                    "label": "Like",
                    "url": "like",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 37
                    },
                    "isLike": 0
                }
            }
        ],
        "38": [
            {
                "subject_id": 2,
                "comment_id": 39,
                "author_image": "http://xyz.com/public/user/ef/06/bd1f631334c50761d32ec5e5674fd64d.jpg",
                "author_image_normal": "http://xyz.com/public/user/f1/06/92ba58ecde03ec08af50ed7054449e20.jpg",
                "author_image_profile": "http://xyz.com/public/user/f0/06/b45e7412a1301cdde2c8704e6cf9ac7b.jpg",
                "author_image_icon": "http://xyz.com/public/user/f2/06/6269cccb48ba764d534ab1db8c504f66.jpg",
                "content_url": "http://xyz.com/profile/test10",
                "author_title": "xyz singh",
                "user_id": 5,
                "comment_body": "reply 2",
                "comment_date": "2017-12-11 10:59:09",
                "params": null,
                "delete": {
                    "name": "delete",
                    "label": "Delete",
                    "url": "comment-delete",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 39
                    }
                },
                "like_count": 0,
                "like": {
                    "name": "like",
                    "label": "Like",
                    "url": "like",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 39
                    },
                    "isLike": 0
                }
            }
        ],
        "39": [
            {
                "subject_id": 2,
                "comment_id": 40,
                "author_image": "http://xyz.com/public/user/dc/06/85e52612611096313d6d0e8dbf253e01.jpg",
                "author_image_normal": "http://xyz.com/public/user/de/06/4eabde367e5fc8e74d8debf6d6c1c178.jpg",
                "author_image_profile": "http://xyz.com/public/user/dd/06/7ecbb0669fdc23d5b282b9ad82e11e39.jpg",
                "author_image_icon": "http://xyz.com/public/user/df/06/e3d4104559ee5ed67f7a51d1a753b7fd.jpg",
                "content_url": "http://xyz.com/profile/Sanjeet",
                "author_title": "Sanjeet",
                "user_id": 1,
                "comment_body": "reply 3",
                "comment_date": "2017-12-11 11:01:45",
                "params": null,
                "delete": {
                    "name": "delete",
                    "label": "Delete",
                    "url": "comment-delete",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 40
                    }
                },
                "like_count": 0,
                "like": {
                    "name": "like",
                    "label": "Like",
                    "url": "like",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 40
                    },
                    "isLike": 0
                }
            },
            {
                "subject_id": 2,
                "comment_id": 42,
                "author_image": "http://xyz.com/public/user/ef/06/bd1f631334c50761d32ec5e5674fd64d.jpg",
                "author_image_normal": "http://xyz.com/public/user/f1/06/92ba58ecde03ec08af50ed7054449e20.jpg",
                "author_image_profile": "http://xyz.com/public/user/f0/06/b45e7412a1301cdde2c8704e6cf9ac7b.jpg",
                "author_image_icon": "http://xyz.com/public/user/f2/06/6269cccb48ba764d534ab1db8c504f66.jpg",
                "content_url": "http://xyz.com/profile/test10",
                "author_title": "xyz singh",
                "user_id": 5,
                "comment_body": "reply 5",
                "comment_date": "2017-12-11 11:05:16",
                "params": null,
                "delete": {
                    "name": "delete",
                    "label": "Delete",
                    "url": "comment-delete",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 42
                    }
                },
                "like_count": 0,
                "like": {
                    "name": "like",
                    "label": "Like",
                    "url": "like",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 42
                    },
                    "isLike": 0
                }
            }
        ],
        "40": [
            {
                "subject_id": 2,
                "comment_id": 41,
                "author_image": "http://xyz.com/public/user/ef/06/bd1f631334c50761d32ec5e5674fd64d.jpg",
                "author_image_normal": "http://xyz.com/public/user/f1/06/92ba58ecde03ec08af50ed7054449e20.jpg",
                "author_image_profile": "http://xyz.com/public/user/f0/06/b45e7412a1301cdde2c8704e6cf9ac7b.jpg",
                "author_image_icon": "http://xyz.com/public/user/f2/06/6269cccb48ba764d534ab1db8c504f66.jpg",
                "content_url": "http://xyz.com/profile/test10",
                "author_title": "xyz singh",
                "user_id": 5,
                "comment_body": "repy4",
                "comment_date": "2017-12-11 11:04:39",
                "params": null,
                "delete": {
                    "name": "delete",
                    "label": "Delete",
                    "url": "comment-delete",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 41
                    }
                },
                "like_count": 0,
                "like": {
                    "name": "like",
                    "label": "Like",
                    "url": "like",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 41
                    },
                    "isLike": 0
                }
            }
        ]
        ],
        "viewAllComments": [
            {
                "subject_id": 2,
                "comment_id": 8,
                "author_image": "http://xyz.com/public/user/dc/06/85e52612611096313d6d0e8dbf253e01.jpg",
                "author_image_normal": "http://xyz.com/public/user/de/06/4eabde367e5fc8e74d8debf6d6c1c178.jpg",
                "author_image_profile": "http://xyz.com/public/user/dd/06/7ecbb0669fdc23d5b282b9ad82e11e39.jpg",
                "author_image_icon": "http://xyz.com/public/user/df/06/e3d4104559ee5ed67f7a51d1a753b7fd.jpg",
                "content_url": "http://xyz.com/profile/Sanjeet",
                "author_title": "Sanjeet",
                "user_id": 1,
                "comment_body": "nice event",
                "comment_date": "2017-12-04 07:42:33",
                "params": null,
                "delete": {
                    "name": "delete",
                    "label": "Delete",
                    "url": "comment-delete",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 8
                    }
                },
                "like_count": 0,
                "like": {
                    "name": "like",
                    "label": "Like",
                    "url": "like",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 8
                    },
                    "isLike": 0
                }
            },
            {
                "subject_id": 2,
                "comment_id": 18,
                "author_image": "http://xyz.com/public/user/eb/06/77e10e0217f2b11224aac5b822233e99.jpg",
                "author_image_normal": "http://xyz.com/public/user/ed/06/fb213e3006956ba55a74fa77a976e394.jpg",
                "author_image_profile": "http://xyz.com/public/user/ec/06/6e8cc0dcf56d8049f135189f941e6ea5.jpg",
                "author_image_icon": "http://xyz.com/public/user/ee/06/6e7c75463334313402b9daed2a4ff67a.jpg",
                "content_url": "http://xyz.com/profile/test08",
                "author_title": "Shyam Kumar",
                "user_id": 4,
                "comment_body": "nice event",
                "comment_date": "2017-12-07 11:26:51",
                "params": null,
                "delete": {
                    "name": "delete",
                    "label": "Delete",
                    "url": "comment-delete",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 18
                    }
                },
                "like_count": 0,
                "like": {
                    "name": "like",
                    "label": "Like",
                    "url": "like",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 18
                    },
                    "isLike": 0
                }
            },
            {
                "subject_id": 2,
                "comment_id": 20,
                "author_image": "http://xyz.com/public/user/eb/06/77e10e0217f2b11224aac5b822233e99.jpg",
                "author_image_normal": "http://xyz.com/public/user/ed/06/fb213e3006956ba55a74fa77a976e394.jpg",
                "author_image_profile": "http://xyz.com/public/user/ec/06/6e8cc0dcf56d8049f135189f941e6ea5.jpg",
                "author_image_icon": "http://xyz.com/public/user/ee/06/6e7c75463334313402b9daed2a4ff67a.jpg",
                "content_url": "http://xyz.com/profile/test08",
                "author_title": "Shyam Kumar",
                "user_id": 4,
                "comment_body": "nice",
                "comment_date": "2017-12-07 11:30:17",
                "params": null,
                "delete": {
                    "name": "delete",
                    "label": "Delete",
                    "url": "comment-delete",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 20
                    }
                },
                "like_count": 0,
                "like": {
                    "name": "like",
                    "label": "Like",
                    "url": "like",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 20
                    },
                    "isLike": 0
                }
            },
            {
                "subject_id": 2,
                "comment_id": 21,
                "author_image": "http://xyz.com/public/user/eb/06/77e10e0217f2b11224aac5b822233e99.jpg",
                "author_image_normal": "http://xyz.com/public/user/ed/06/fb213e3006956ba55a74fa77a976e394.jpg",
                "author_image_profile": "http://xyz.com/public/user/ec/06/6e8cc0dcf56d8049f135189f941e6ea5.jpg",
                "author_image_icon": "http://xyz.com/public/user/ee/06/6e7c75463334313402b9daed2a4ff67a.jpg",
                "content_url": "http://xyz.com/profile/test08",
                "author_title": "Shyam Kumar",
                "user_id": 4,
                "comment_body": "nice",
                "comment_date": "2017-12-07 11:30:18",
                "params": null,
                "delete": {
                    "name": "delete",
                    "label": "Delete",
                    "url": "comment-delete",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 21
                    }
                },
                "like_count": 0,
                "like": {
                    "name": "like",
                    "label": "Like",
                    "url": "like",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 21
                    },
                    "isLike": 0
                }
            },
            {
                "subject_id": 2,
                "comment_id": 22,
                "author_image": "http://xyz.com/public/user/eb/06/77e10e0217f2b11224aac5b822233e99.jpg",
                "author_image_normal": "http://xyz.com/public/user/ed/06/fb213e3006956ba55a74fa77a976e394.jpg",
                "author_image_profile": "http://xyz.com/public/user/ec/06/6e8cc0dcf56d8049f135189f941e6ea5.jpg",
                "author_image_icon": "http://xyz.com/public/user/ee/06/6e7c75463334313402b9daed2a4ff67a.jpg",
                "content_url": "http://xyz.com/profile/test08",
                "author_title": "Shyam Kumar",
                "user_id": 4,
                "comment_body": "nice",
                "comment_date": "2017-12-07 11:32:57",
                "params": null,
                "delete": {
                    "name": "delete",
                    "label": "Delete",
                    "url": "comment-delete",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 22
                    }
                },
                "like_count": 0,
                "like": {
                    "name": "like",
                    "label": "Like",
                    "url": "like",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 22
                    },
                    "isLike": 0
                }
            },
            {
                "subject_id": 2,
                "comment_id": 43,
                "author_image": "http://xyz.com/public/user/dc/06/85e52612611096313d6d0e8dbf253e01.jpg",
                "author_image_normal": "http://xyz.com/public/user/de/06/4eabde367e5fc8e74d8debf6d6c1c178.jpg",
                "author_image_profile": "http://xyz.com/public/user/dd/06/7ecbb0669fdc23d5b282b9ad82e11e39.jpg",
                "author_image_icon": "http://xyz.com/public/user/df/06/e3d4104559ee5ed67f7a51d1a753b7fd.jpg",
                "content_url": "http://xyz.com/profile/Sanjeet",
                "author_title": "Sanjeet",
                "user_id": 1,
                "comment_body": "recustsive comment",
                "comment_date": "2017-12-12 09:35:49",
                "params": null,
                "delete": {
                    "name": "delete",
                    "label": "Delete",
                    "url": "comment-delete",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 43
                    }
                },
                "like_count": 0,
                "like": {
                    "name": "like",
                    "label": "Like",
                    "url": "like",
                    "urlParams": {
                        "action_id": 2,
                        "subject_type": "event",
                        "subject_id": 2,
                        "comment_id": 43
                    },
                    "isLike": 0
                }
            }
        ],
        "viewAllLikesBy": [],
        "isLike": 0,
        "canComment": 1,
        "canDelete": 1,
        "getTotalComments": 22,
        "getTotalLikes": 0
    }

+ Response 401
    + Body

            {
                "status_code": 401,
                "error": true,
                "error_code": "unauthorized",
                "message": "User does not have access to this resource."
            }



### Comment Post [/advancedcomments/comment]
#### Post Comment [POST]
Post comment on content.


| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `body`     | yes      | string | Nice | status update post's content
| `subject_type`     | yes      | string | video| content type
| `subject_id`     | yes      | int | video| content id
|`attachment_id`     | no       |int  |256   | attchment id
|`attachment_type`   |no        |string| photo| attachent type like photo,video


+ Request valid
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb

    + Body

        {
            "body": "Happpy Birthday",
            "subject_type":event,
            "subject_id": 2015,                
            
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



### Reply On Content Comment [/advancedcomments/comment]
#### Reply On Content Comment [POST]
Reply On Content Comment.


| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `body`     | yes      | string | Thank you | status update post's content
| `subject_type`     | yes      | string | video| content type
| `subject_id`     | yes      | int | video| content id
|`comment_id`       |yes        |int|121    | comment id where you want to reply on
|`attachment_id`     | no       |int  |256   | attchment id
|`attachment_type`   |no        |string| photo| attachent type like photo,video


+ Request valid
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb

    + Body

        {
            "body": "Thank you",
            "comment_id":121,
            "subject_type":event,
            "subject_id": 2015,                
            
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


### Reply On Activity Feed Comment [/advancedcomments/reply]
#### Reply On Activity Feed Comment [POST]
Reply On Activity Feed Comment


| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `body`     | yes      | string | Thank you | status update post's content
| `action_id`     | yes      | int | 91| action id of feed
|`comment_id`       |yes        |int|121    | comment id where you want to reply on
|`attachment_id`     | no       |int  |256   | attchment id
|`attachment_type`   |no        |string| photo| attachent type like photo,video



+ Request valid
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb

    + Body

        {
            "body": "Thank you",
            "comment_id":121,
            "action_id":91,
                         
            
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

   

### Edit Activity Feed Comment [/advancedcomments/reply-edit]
#### Edit Activity Feed Comment [POST]
Edit Activity Feed Comment


| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `body`     | yes      | string | Nice | status update post's content
| `action_id`     | yes      | int | 91| action id of feed
|`comment_id`       |yes        |int|121    | comment id 




+ Request valid
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb

    + Body

        {
            "body": "Nice",
            "comment_id":121,
            "action_id":91,
                         
            
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
    


### Delete comment [/comment-delete]
#### Delete comment [DELETE]
Delete comment


| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `subject_type`     | yes      | string | group | status update post's content
|`subject_id`        |yes       |int    | 235      | gorup id
| `action_id`     | yes      | int | 91| action id of feed
|`comment_id`       |yes        |int|121    | delete comment 




+ Request valid
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb

    + Body

        {
           
            "comment_id":121,
            "subject_type":event,
            "subject_id": 2015,
            "action_id ":1995
                         
            
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
    
### Like Comment [/like]
#### Like Comment [POST]
Like comment


| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `subject_type`     | yes      | string | group | status update post's content
|`subject_id`        |yes       |int    | 235      | gorup id
| `action_id`     | yes      | int | 91| action id of feed
|`comment_id`       |yes        |int|121    | delete comment 




+ Request valid
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb

    + Body

        {
           
            "comment_id":121,
            "subject_type":event,
            "subject_id": 2015,
            "action_id ":1995
                         
            
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
    


### Send Notification On Comment [/advancedcomments/add-comment-notifications]
#### Send Notification On Comment [POST]
Send Notification On Comment


| Parameter   | Required | Type   | Example          | Description                                                | 
| ----------- | :------: | ------ | ---------------- | ---------------------------------------------------------- |
| `subject_type`     | yes      | string | group | subject type like group,event video
|`subject_id`        |yes       |int    | 235      | gorup id
|`comment_id`       |yes        |int|121    | delete comment 




+ Request valid
    + Headers

            Accept: application/json
            oauth_consumer_key: e6a5845684bf49df63d9eef489acfee1
            oauth_consumer_secret: 42b9eef48e96c65f9ca29d07712d39fb

    + Body

        {
           
            "comment_id":121,
            "subject_type":event,
            "subject_id": 2015,
            "action_id ":1995
                         
            
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
    


