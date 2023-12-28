
/* $Id: core.js 10182 2014-04-29 23:52:40Z andres $ */



(function() { // START NAMESPACE

    var audio = scriptJquery.crtEle("audio",{
                id: '_chat_ding',
                autoLoad: true,
                volume: 0.5,
              }).html(scriptJquery.crtEle('source',{
                          src :en4.core.staticBaseUrl + 'application/modules/Chat/externals/ding.mp3',
              }));
        audio.volume = 0.5;
    /**
     * Undocumented Classs
     *
     **/
    ChatHandler = class {

        options = {
            debug : false,
            baseUrl : '/',
            identity : false,
            delay : 3000,
            minDelay : 3000,
            maxDelay : 60000,
            delayFactor : 1.25,
            requestTimeout: 10000,
            admin : false,
            idleTimeout : 60000,

            // Chat specific
            enableChat : true,
            chatOptions : {
                identity : 1,
                operator : false
            },

            // IM specific
            enableIM : true,
            imOptions : {
                container : false,
                memberIm : false
            }
        }

        state = false;

        chatstate = 0;

        imstate = 0;

        activestate = 1;

        fresh = true;

        rooms = {};

        lastEventTime = false;

        /**
         * undocumented function
         * @params object options
         * @return void
         **/
        constructor(options) {
            this.options = scriptJquery.extend(true,{},this.options,options);
            this.options.requestTimeout = Math.round(this.options.delay * 0.5);
            // Let's set minimum values just in case
            if( this.options.delay < 1000 ) this.options.delay = 1000;
            if( this.options.requestTimeout < 1000 ) this.options.requestTimeout = 1000;
            this.options.minDelay = this.options.delay;
            this.rooms = new Hash();
        }

        /**
         * undocumented function
         *
         * @return void
         **/
        start() {
          this.state = true;
          if( this.options.enableIM ) {
            this.startIm(this.options.imOptions);
          }
          if( this.options.enableChat ) {
            this.startChat(this.options.chatOptions);
          }
          scriptJquery(this).on('onEvent_reconfigure', this.onReconfigure.bind(this));
          // // Do idle checking
          scriptJquery(document).idle({
            onIdle: function(){
              this.activestate = 0;
              this.status(2);
            }.bind(this),
            onActive: function(){
              this.activestate = 1;
              this.status(1);
            }.bind(this),
            idle: this.options.idleTimeout,
            keepTracking: true
          });
          window.addEventListener("beforeunload", function (e) {
            this.activestate = 0;
            this.status(3); //Webkit, Safari, Chrome
          }.bind(this));

          this.loop();
        }

        /**
         * undocumented function
         * @params object options
         * @return void
         **/
        startIm(options) {
          if( $type(this.im) ) {
              return; // Maybe destroy it later
          }

          this.options.imOptions = scriptJquery.extend(true,{},this.options.imOptions, options);
          this.im = new ChatHandler_Whispers(this, this.options.imOptions);
          this.imstate = 1;
          var savedState = Cookie.read('en4_chat_imstate', {path:en4.core.basePath});
          if( savedState == 0 ) {
              this.im.items.settings.toggleOnline();
          }
        }

        /**
         * undocumented function
         * @params object options
         * @return void
         **/
        startChat(options) {
          // If no id or already in, ignore
          var identity = options.identity || Cookie.read('en4_chat_room_last', {path:en4.core.basePath}) || 1;
          options.identity = identity;

          if(!identity || $type(this.rooms.get(identity)) ) return;

          // Close any open rooms (if any)
          if( this.rooms.getLength() > 0 ) {
              var count = 0;
              Object.entries(this.rooms).forEach(function([key,room]) {
                  count++;
                  this.leave(room.options.identity).complete(function() {
                      //room.destroy();
                      this.rooms.erase(room.options.identity);
                      count--;
                      if( count <= 0 ) {
                          this.options.chatOptions = scriptJquery.extend(true,{},this.options.chatOptions, options);
                          this.rooms.set(identity, new ChatHandler_Room(this, this.options.chatOptions));
                      }
                  }.bind(this));
              }.bind(this));
              return;
          }

          // Just open
          this.options.chatOptions = scriptJquery.extend(true,{},this.options.chatOptions, options);
          this.rooms.set(identity, new ChatHandler_Room(this, this.options.chatOptions));
          this.chatstate = 1;
        }

        /**
         * undocumented function
         *
         * @return void
         **/
        stop() {
            this.state = false;
        }

        /**
         * undocumented function
         *
         * @return void
         **/
        loop() {
            if( !this.state || (!this.imstate && !this.chatstate) ) {
                setTimeout(this.loop.bind(this),this.options.delay);
                return;
            }
            try {
              // send request
              var request = this.ping();
              var completed = false;
              var cancelled = false;
              request.complete(function() {
                if(!cancelled) {
                  setTimeout(this.loop.bind(this),this.options.delay);
                }
              }.bind(this))
              .error(function() {
                cancelled = true; 
                setTimeout(this.loop.bind(this),this.options.delay);
              }.bind(this));
            } catch(e) {
              setTimeout(this.loop.bind(this),this.options.delay);
              this._log(e);
            }
        }
        // Start requests

        /**
         * undocumented function
         *
         * @return void
         **/
        ping() {
            this._log({'type' : 'cmd.ping'});

            var fresh = this.fresh;
            this.fresh = false;

            // Gather extra data
            var extraData = {};
            scriptJquery(this).trigger('onPingBefore', extraData);

            var request = scriptJquery.ajax({
                url : this.options.baseUrl + '?m=lite&module=chat&action=ping',
                timeout : this.options.requestTimeout,
                noCache : true,
                dataType : 'json',
                method : 'post',
                //url : this.options.baseUrl + 'chat/ajax/ping',
                data : scriptJquery.extend(true,{},{
                    'format' : 'json',
                    'fresh' : fresh,
                    'lastEventTime' : this.lastEventTime || null
                }, extraData),
                success : this.onPingResponse.bind(this)
            });
            return request;
        }

        join(room_id) {
            this._log({'type' : 'cmd.join', 'room_id' : room_id});

            var request = scriptJquery.ajax({
                url : this.options.baseUrl + '?m=lite&module=chat&action=join',
                timeout : this.options.requestTimeout,
                noCache : true,
                dataType : 'json',
                method : 'post',
                //url : this.options.baseUrl + 'chat/ajax/join',
                data : {
                    'format' : 'json',
                    'room_id' : room_id
                },
                success : this.onJoinResponse.bind(this, room_id)
            });
            return request;
        }

        leave(room_id) {
            this._log({'type' : 'cmd.leave', 'room_id' : room_id});

            var request = scriptJquery.ajax({
                url : this.options.baseUrl + '?m=lite&module=chat&action=leave',
                timeout : this.options.requestTimeout,
                noCache : true,
                dataType : 'json',
                method : 'post',
                //url : this.options.baseUrl + 'chat/ajax/leave',
                data : {
                    'format' : 'json',
                    'room_id' : room_id
                },
                success : this.onLeaveResponse.bind(this, room_id)
            });
            return request;
        }

        send(room_id, message, callback) {
            this._log({'type' : 'cmd.send', 'room_id' : room_id, 'message' : message});

            return (scriptJquery.ajax({
                url : this.options.baseUrl + '?m=lite&module=chat&action=send',
                timeout : this.options.requestTimeout,
                noCache : true,
                dataType : 'json',
                method : 'post',
                //url : this.options.baseUrl + 'chat/ajax/send',
                data : {
                    'format' : 'json',
                    'room_id' : room_id,
                    'message' : message
                },
                success : this.onSendResponse.bind(this, [room_id, callback])
            }));
        }

        whisper(user_id, message, callback) {
            this._log({'type' : 'cmd.whisper', 'user_id' : user_id, 'message' : message});

            return (scriptJquery.ajax({
                url : this.options.baseUrl + '?m=lite&module=chat&action=whisper',
                timeout : this.options.requestTimeout,
                noCache : true,
                dataType : 'json',
                method : 'post',
                //url : this.options.baseUrl + 'chat/ajax/whisper',
                data : {
                    'format' : 'json',
                    'user_id' : user_id,
                    'message' : message
                },
                success : this.onWhisperResponse.bind(this, user_id,callback)
            }));
        }

        whisperClose(user_id) {
            this._log({'type' : 'cmd.whisperClose', 'user_id' : user_id});

            return (scriptJquery.ajax({
                url : this.options.baseUrl + '?m=lite&module=chat&action=whisper-close',
                timeout : this.options.requestTimeout,
                noCache : true,
                dataType : 'json',
                method : 'post',
                //url : this.options.baseUrl + 'chat/ajax/whisper-close',
                data : {
                    'format' : 'json',
                    'user_id' : user_id
                },
                success : this.onWhisperCloseResponse.bind(this)
            }));
        }

        status(state, type) {
          this._log({'type' : 'cmd.status', 'state' : state});
          return (scriptJquery.ajax({
            url : this.options.baseUrl + '?m=lite&module=chat&action=status',
            timeout : this.options.requestTimeout,
            noCache : true,
            dataType : 'json',
            method : 'post',
            //url : this.options.baseUrl + 'chat/ajax/status',
            data : {
              'format' : 'json',
              'status' : state,
              'type' : type
            },
            success : this.onStatusResponse.bind(this)
          }));
        }

        list() {
          this._log({'type' : 'cmd.list'});
          return (scriptJquery.ajax({
              url : this.options.baseUrl + '?m=lite&module=chat&action=list',
              timeout : this.options.requestTimeout,
              noCache : true,
              dataType : 'json',
              method : 'post',
              //url : this.options.baseUrl + 'chat/ajax/list',
              data : {
                  'format' : 'json'
              },
              success : this.onListResponse.bind(this)
          }));
        }
        // Handle requests
        onPingResponse(responseJSON) {
          this._log({'type' : 'resp.ping', 'data' : responseJSON});
          try {
              if( $type(responseJSON) == 'object' ) {
                  var currentTimeStamp = (Date.parse(new Date()) / 1000);
                  if( $type(responseJSON.lastEventTime) && responseJSON.lastEventTime ) {

                      if ( this.lastEventTime == responseJSON.lastEventTime ) {
                          this.options.delay = Math.min(this.options.maxDelay, this.options.delayFactor * this.options.delay);
                      } else {
                          this.options.delay = this.options.minDelay;
                      }
                      this.lastEventTime = responseJSON.lastEventTime;
                  }

                  // Online friends
                  if( $type(responseJSON.users) == 'object' ) {
                      for( var x in responseJSON.users ) {
                        var data = responseJSON.users[x];
                        scriptJquery(this).trigger('onEvent_presence', data);
                      }
                  }
                  // Whispers
                  if( $type(responseJSON.whispers) == 'object' ) {
                      for( var x in responseJSON.whispers ) {
                        scriptJquery(this).trigger('onEvent_chat', responseJSON.whispers[x]);
                      }
                  }

                  // Events
                  if( $type(responseJSON.events) == 'object' ) {
                    for( var x in responseJSON.events ) {
                      var type      = responseJSON.events[x].type;
                      var eventName = 'onEvent_' + type;
                      if (type == 'chat' && responseJSON.events[x].sender_id != en4.user.viewer.id && audio) {
                        //if ( (type == 'chat' || type == 'groupchat') && responseJSON.events[x].sender_id != en4.user.viewer.id && 'object' == $type(soundManager)) {
                        // ding!
                        try {
                          
                          if (responseJSON.newmessage) {
                            audio[0].currentTime = 0;
                            audio[0].play();
                          }
                        } catch (e) { console.log(e,"hello "); }
                      }
                      scriptJquery(this).trigger(eventName, responseJSON.events[x]);
                    }
                  }

              }
          }
          catch( e )
          {
              this._log({type:'error', data: e});
          }
            if( $type(responseJSON.status) && responseJSON.status ) {
              scriptJquery(this).trigger('onPingSuccess', responseJSON);
            } else {
              scriptJquery(this).trigger('onPingFailure', responseJSON);
            }
        }

        onJoinResponse(room_id,responseJSON) {
          this._log({'type' : 'resp.join', 'data' : responseJSON, 'room_id' : room_id});
          // We were sent back the data anyway (we probably already are in it)
          /*
          if( $type(responseJSON.room.room_id) ) {
            var room_id = responseJSON.room.room_id;
            if( !$type(this.rooms[room_id]) ) {
              this.rooms[room_id] = new ChatHandler_Room(this, $merge(this.options.chatOptions, {
                identity : room_id
              }), responseJSON);
            }
          }
          */

          responseJSON.room_id = room_id;
          scriptJquery(this).trigger('onJoin', responseJSON);
          /*
          if( $type(responseJSON.status) && responseJSON.status ) {
            this.fireEvent('onJoinSuccess', responseJSON);
          } else {
            this.fireEvent('onJoinFailure', responseJSON);
          }
          */
        }

        onLeaveResponse(room_id,responseJSON) {
          this._log({'type' : 'resp.leave', 'data' : responseJSON, 'room_id' : room_id});

          responseJSON.room_id = room_id;
          //this.fireEvent('onLeave', responseJSON);
          /*
          if( $type(responseJSON.status) && responseJSON.status ) {
            this.fireEvent('onLeaveSuccess', responseJSON);
          } else {
            this.fireEvent('onLeaveFailure', responseJSON);
          }
          */
        }

        onSendResponse(data,responseJSON) {
          let room_id = data[0];
          let callback = data[1];
          this._log({'type' : 'resp.send', 'data' : responseJSON});

          if( $type(responseJSON.status) && responseJSON.status ) {
              //this.fireEvent('onSendSuccess', responseJSON);
          } else {
              //this.fireEvent('onSendFailure', responseJSON);
          }
          if($type(callback) == 'function' ) {
            callback(responseJSON, room_id);
          }
        }

        onWhisperResponse(user_id, callback,responseJSON) {
          this._log({'type' : 'resp.whisper', 'data' : responseJSON});

          if( $type(responseJSON.status) && responseJSON.status ) {
            scriptJquery(this).trigger('onWhisperSuccess', responseJSON);
          } else {
            scriptJquery(this).trigger('onWhisperFailure', responseJSON);
          }
          if( $type(callback) == 'function' ) {
              callback(responseJSON, user_id);
          }
        }

        onWhisperCloseResponse(responseJSON) {
          this._log({'type' : 'resp.whisperClose', 'data' : responseJSON});
          scriptJquery(this).trigger('onWhisperClose', responseJSON);
        }

        onStatusResponse(responseJSON) {

        }

        onListResponse(responseJSON) {
          scriptJquery(this).trigger('onListRooms', responseJSON);
        }
        // Other events
        onReconfigure(data) {
          if( $type(data.delay) ) {
            this.options.delay = data.delay;
          }
          if( $type(data.chat_enabled) ) {
            // Disabling chat
            if( parseInt(data.chat_enabled) == 0 ) {
                this.rooms.each(function(room) {
                    room.destroy();
                });
                (scriptJquery.crtEle('div', {
                }).html(en4.core.language.translate('The chat room has been disabled by the site admin.')).appendTo(this.container || (scriptJquery('#global_content').length ? scriptJquery('#global_content') : document.body)));
            }
            // Enabling chat
            else
            {
                // dont do anything
            }
          }
          if( $type(data.im_enabled) ) {
            if( parseInt(data.im_enabled) == 0 ) {
                if( $type(this.im) ) {
                    this.im.destroy();
                }
            }
          }
        }
        // Utility

        _log(object) {
            if( !this.options.debug ) {
                return;
            }
            // Firefox is dumb and causes problems sometimes with console
            try {
                if( typeof(console) && $type(console) ) {
                    console.log(object);
                }
            } catch( e ) {
                // Silence
            }
        }

        _supportsContentEditable() {
            // contenteditable support broken in mootools 1.3
            return false;
            if( $type(window.DetectMobileQuick) && $type(window.DetectIpad) ) {
                if( DetectMobileQuick() || DetectIpad() ) {
                    return false;
                }
            }
            return ( true == ('contentEditable' in document.body) );
            /*
            if( Browser.Engine.trident && Browser.Engine.version >= 4 ) { // Might support it even before 4, but mootools doesn't detect before that
              return true;
            }else if( Browser.Engine.gecko && Browser.Engine.version >= 19 ) {
              return true;
            }else if( DetectWebkit() && Browser.Engine.version >= 419 ) { // Not enough information to confirm
              return true;
            }else if( Browser.Engine.presto && Browser.Engine.version >= 925 ) { // Not enough information to confirm
              return true;
            }
            return false;
            */
        }
    };



    ChatHandler_Room = class {

        options = {
            identity : false,
            rateMessages : 10,
            rateTimeout : 10000,
            maxLength : 1023
        }

        handler = false;

        data = {};

        elements = {};

        rate = [];

        constructor(handler, options) {
            this.handler = handler;
            this.room = new Hash();
            this.users = new Hash();
            this.elements = new Hash();
            this.options = scriptJquery.extend(true,{},this.options,options);

            this.render();
            this.attach();

            this.handler.join(this.options.identity);
            Cookie.write('en4_chat_room_last', this.options.identity, {path:en4.core.basePath});
        }

        destroy() {
            this.detach();
            this.handler.chatstate = 0;
            this.elements.container.remove();
        }

        attach() {
            scriptJquery(this.handler).on('onPingBefore', this.onPingBefore.bind(this));
            scriptJquery(this.handler).on('onJoin', this.onJoin.bind(this));
            scriptJquery(this.handler).on('onLeave', this.onLeave.bind(this));
            scriptJquery(this.handler).on('onEvent_grouppresence', this.onPresence.bind(this));
            scriptJquery(this.handler).on('onEvent_groupchat', this.onGroupChat.bind(this));
            scriptJquery(this.handler).on('onListRooms', this.onListRooms.bind(this));
        }

        detach() {
            scriptJquery(this.handler).off('onPingBefore', this.onPingBefore.bind(this));
            scriptJquery(this.handler).off('onJoin', this.onJoin.bind(this));
            scriptJquery(this.handler).off('onLeave', this.onLeave.bind(this));
            scriptJquery(this.handler).off('onEvent_grouppresence', this.onPresence.bind(this));
            scriptJquery(this.handler).off('onEvent_groupchat', this.onGroupChat.bind(this));
            scriptJquery(this.handler).off('onListRooms', this.onListRooms.bind(this));
        }

        render() {

            var identity = this.options.identity;

            var self = this;
            if(scriptJquery('.chat_container').length) {
                scriptJquery('.chat_container').remove();
            }

            // Container
            this.elements.container = scriptJquery.crtEle('div', {
                'id' : 'chat_container_' + identity,
                'class' : 'chat_container'
            }).appendTo(scriptJquery(this.container).length ? scriptJquery(this.container) : (scriptJquery(this.options.container).length ? scriptJquery(this.options.container) :  (scriptJquery('#global_content').length ? scriptJquery('#global_content') : document.body)));

            // Header
            this.elements.header = scriptJquery.crtEle('div', {
                'class' : 'chat_header'
            }).appendTo(this.elements.container);

            // Title
            this.elements.headerTitle = scriptJquery.crtEle('div', {
                'class' : 'chat_header_title'
            }).appendTo(this.elements.header);

            // Rooms
            var roomList = new Hash(this.options.roomList);
            if(Object.keys(roomList).length > 0) {
                this.elements.roomsButton = scriptJquery.crtEle('span', {
                    'class' : 'pulldown',
                }).click(function() {
                  if(this.elements.roomsButton.hasClass('pulldown_active') ) {
                      this.elements.roomsButton.removeClass('pulldown_active').addClass('pulldown');
                      // Get rooms
                      this.handler.list();
                  } else {
                      this.elements.roomsButton.removeClass('pulldown').addClass('pulldown_active');
                  }
                }.bind(this)).appendTo(this.elements.header);
                //(scriptJquery.crtEle('span', 'Browse Chatrooms')).appendTo((scriptJquery.crtEle('div')).appendTo(this.elements.roomsButton));

                var pulldownWrapper = scriptJquery.crtEle('div', {'class' : 'pulldown_contents_wrapper'}).appendTo(this.elements.roomsButton);
                var pulldownContainer = scriptJquery.crtEle('div', {'class' : 'pulldown_contents'}).appendTo(pulldownWrapper);

                this.elements.roomsList = scriptJquery.crtEle('ul', {})
                .appendTo(pulldownContainer);

                scriptJquery.crtEle('a', {
                    'href' : 'javascript:void(0);',
                }).html(en4.core.language.translate('Browse Chatrooms')).appendTo(this.elements.roomsButton);

                this.onListRooms(roomList);
            }

            // Users
            this.elements.usersWrapper = scriptJquery.crtEle('div', {
                'id' : 'chat_users_wrapper_' + identity,
                'class' : 'chat_users_wrapper'
            }).appendTo(this.elements.container);

            this.elements.usersList = scriptJquery.crtEle('ul', {
                'id' : 'chat_users_' + identity,
                'class' : 'chat_users chat_users_list'
            }).appendTo(this.elements.usersWrapper);

            // Body
            this.elements.body = scriptJquery.crtEle('div', {
                'id' : 'chat_main_' + identity,
                'class' : 'chat_main chat_body'
            }).appendTo(this.elements.container);

            // Messages
            this.elements.messagesWrapper = scriptJquery.crtEle('div', {
                'id' : 'chat_messages_wrapper_' + identity,
                'class' : 'chat_messages_wrapper'
            }).appendTo(this.elements.body);

            this.elements.messagesList = scriptJquery.crtEle('ul', {
                'id' : 'chat_messages_' + identity,
                'class' : 'chat_messages chat_messages_list'
            }).appendTo(this.elements.messagesWrapper);

            // Input
            this.elements.inputWrapper = scriptJquery.crtEle('div', {
                'id' : 'chat_input_wrapper_' + identity,
                'class' : 'chat_input_wrapper'
            }).appendTo(this.elements.body);

            if( this.handler._supportsContentEditable() ) {
                // Div+ContentEditable
                this.elements.input = scriptJquery.crtEle('div', {
                    'class' : 'chat_input',
                }).html('').keypress(function(event) {
                    // if( event.key == 'a' && event.control ) {
                    //     // FF only
                    //     if( Browser.Engine.gecko ) {
                    //         fix_gecko_select_all_contenteditable_bug(this, event);
                    //     }
                    // }
                }).appendTo(this.elements.inputWrapper);
                this.elements.input.contentEditable = true;
            } else {
                // Input
                this.elements.input = scriptJquery.crtEle('textarea', {
                    'id' : 'chat_input_' + identity,
                    'class' : 'chat_input'
                }).appendTo(this.elements.inputWrapper);
            }

            this.elements.input.keypress(function(event) {
                if(event.key == 'Enter' ) {
                    event.preventDefault();
                    this.send();
                }
            }.bind(this));
        }
        // Actions
        send() {
            var message;
            if( this.handler._supportsContentEditable() ) {
                message = this.elements.input.html();
                // Webkit, you're killing me!
                if( DetectWebkit() ) {
                    this.elements.input.remove();
                    delete this.elements.input;
                    this.elements.input = scriptJquery.crtEle('div', {
                        'id' : 'chat_input',
                        'class' : 'chat_input',
                    }).appendTo(this.elements.inputWrapper);
                    this.elements.input.contentEditable = true;
                    this.elements.input.keypress(function(event) {
                        if( event.key == 'Enter' ) {
                            event.preventDefault();
                            this.send();
                        }
                    }.bind(this));
                    // Everything else works fine
                } else {
                    this.elements.input.empty();
                    this.elements.input.html('<p><br /></p>');
                }

                this.elements.input.focus();
                message = scriptJquery("<div>"+message+"</div>").text();
            } else {
                message = this.elements.input.val();
                message = scriptJquery("<div>"+message+"</div>").text();
                this.elements.input.val('');
            }

            message = message.trim();
            if( message == '' ) {
                return;
            }

            if( message.length > this.options.maxLength ) message = message.substring(0, this.options.maxLength);

            // Check rate
            this.rate = this.rate.filter(function(item) {
                return $time() < item + this.options.rateTimeout;
            }.bind(this));

            if( this.rate.length >= this.options.rateMessages ) {
                this.onGroupChat({
                    'room_id' : this.options.identity,
                    'system' : 1,
                    'body' : en4.core.language.translate('You are sending messages too quickly - please wait a few seconds and try again.')
                });
                return;
            }

            this.rate.push($time());

            // Create el
            var ref = {};

            // For commands, don't display here
            if( message.indexOf('/') !== 0 ) {
                this.onGroupChat({
                    room_id : this.options.identity,
                    user_id : this.getSelf().identity,
                    body : message
                }, ref);
            }

            this.handler.send(this.options.identity, message, function(responseJSON) {
                if( $type(ref.element) ) {
                  ref.element.attr('id', 'chat_message_'+responseJSON.message_id);
                }
            });
        }
        getSelf() {
            var self;
            Object.entries(this.users).forEach(function([key,data]) {
              if(data.self) {
                self = data;
              }
            });
            if(!$type(self) ) {
                self = {
                  'identity' : 0,
                  'title' : en4.core.language.translate('You')
                };
            }
            return self;
        }
        // Events
        onPingBefore(event,data) {
            data.rooms = [this.options.identity];
        }

        onJoin(event,data) {
         if(!$type(event.isTrigger)){
            data = event;
          }
          this.handler._log({type:'chat.join', data:data});
          // Remove existing messages and users (we are going to re-populate)
          this.elements.usersList.empty();
          this.elements.messagesList.empty();

          if( $type(data.room) == 'object' ) {
              this.room = new Hash(data.room);
          }

          if( $type(data.users) == 'object' ) {
              for( var x in data.users ) {
                  this.onPresence(data.users[x]);
              }
          }
          if(data.room)
          this.elements.headerTitle.html('<h3>' + en4.core.language.translate(data.room.title) + '</h3>');
        }

        onLeave(data) {
          if( data.room_id != this.options.identity ) return;
            this.destroy();
        }

        onPresence(event,data) {
          if(!$type(event.isTrigger)){
            data = event;
          }
          this.handler._log({type:'chat.presence', data:data, self:this.options.identity});
          if( data.room_id != this.options.identity ) return;

          // Update user info
          if( parseInt(data.state) > 0 || !this.users.has(data.identity) ) {
              this.users.set(data.identity, data);
          }

          // Get el
          var userElId = 'chat_room_' + this.options.identity + '_user_' + data.identity;
          var userEl = scriptJquery("#"+userElId);
          if( parseInt(data.state) >= 1 ) {
              if( !userEl.length ) {
                if(data.photo) {
                  userEl = scriptJquery.crtEle('li', {
                    'id' : userElId,
                  }).html('<span class="chat_user_photo"><span class="bg_item_photo bg_thumb_icon bg_item_photo_user" style="background-image:url(' + (data.photo) + ')"></span></span>' + '<span class="chat_user_name"><a href="' + data.href + '" target="_blank">' + data.title + '</a></span>')
                  .appendTo(this.elements.usersList);
                } else {
                  userEl = scriptJquery.crtEle('li', {
                    'id' : userElId,
                  }).html('<span class="chat_user_photo"><span class="bg_item_photo bg_thumb_icon bg_item_photo_user bg_item_nophoto"></span></span>' + '<span class="chat_user_name"><a href="' + data.href + '" target="_blank">' + data.title + '</a></span>')
                  .appendTo(this.elements.usersList);
                }

                // Add system notice
                if( !$type(data.stale) || !data.stale) {
                  this.onGroupChat({
                      'system' : 1,
                      'body' : en4.core.language.translate('%1$s has joined the room.', '<a href="' + data.href + '" target="_blank">' + data.title + '</a>'),
                      'room_id' : this.options.identity
                  });
                }

                // Do admin stuff
                /*
                if( this.options.operator || this.handler.options.admin ) {
                  scriptJquery.crtEle('a', {
                    'href' : this.handler.options.baseUrl + 'chat/index/ban/format/smoothbox',
                    'class' : 'smoothbox',
                    'html' : 'x'
                  }).appendTo(userEl);
                }
                */
              }
              ChatHandler_Utility.applyStateClass(userEl.find('.chat_user_name'), parseInt(data.state));
          } else if( parseInt(data.state) < 1 ) {
              if( userEl.length ) {
                  userEl.remove();
                  // Add system notice
                  if( !$type(data.stale) || parseInt(data.stale) != 1 ) {
                      this.onGroupChat({
                          'system' : 1,
                          'body' : en4.core.language.translate('%1$s has left the room.', '<a href="' + data.href + '" target="_blank">' + data.title + '</a>'),
                          'room_id' : this.options.identity
                      });
                  }
              }
          }
        }
        onGroupChat(event,data, ref) {
          if(!$type(event.isTrigger)){
            ref = data;
            data = event;
          }
          this.handler._log({type:'chat.message', data:data});
          if(data.room_id != this.options.identity ) return;

          // Check to see if we already have recv this message
          if( $type(data.message_id) && scriptJquery('#chat_message_'+data.message_id).length ) {
              return;
          }

          var body = data.body;
          if( body.length > this.options.maxLength ) body = body.substring(0, this.options.maxLength);
            body = ChatHandler_Utility.replaceSmilies(body);
          //body = body.replaceLinks();

          body = ChatHandler_Utility.unicodeToChar(body);
          var msgWrpr, tmpDivEl;

          // System message
          if($type(data.system) && parseInt(data.system) ==1) {
              msgWrpr = scriptJquery.crtEle('li').appendTo(this.elements.messagesList);

              tmpDivEl = scriptJquery.crtEle('div', {
                  'class' : 'chat_message_info'
              }).appendTo(msgWrpr);

              var tmpMsgEl = (scriptJquery.crtEle('span', {'class' : 'chat_message_info_body chat_message_info_body_system'}).html(body));
              tmpMsgEl.appendTo(tmpDivEl);
          }
          // Normal message
          else
          {
              var user = this.users.get(data.user_id);

              // Add message
              msgWrpr = scriptJquery.crtEle('li').appendTo(this.elements.messagesList);

              /*
              tmpDivEl = (scriptJquery.crtEle('div', {
                'class' : 'chat_message_info'
              })).appendTo(msgWrpr);
              */

              if(user.photo) {
                (scriptJquery.crtEle('div', {
                    'class' : 'chat_message_photo',
                })).html('<a href="' + user.href + '" target="_blank"><span class="bg_item_photo bg_thumb_icon bg_item_photo_user" style="background-image:url('+ user.photo  +')"></span></a>').appendTo(msgWrpr);
              } else {
                (scriptJquery.crtEle('div', {
                  'class' : 'chat_message_photo',
                })).html('<a href="' + user.href + '" target="_blank"><span class="bg_item_photo bg_thumb_icon bg_item_photo_user bg_item_nophoto"></span></a>').appendTo(msgWrpr);
              }

              var tmpMsgEl = (scriptJquery.crtEle('div', {
                  'class' : 'chat_message_info',
              })).html('\n\
                <span class="chat_message_info_author"><a href="' + user.href + '" target="_blank">' + user.title + '</a></span>\n\
                <span class="chat_message_info_body">' + body + '</span>\n\
              ');
              tmpMsgEl.appendTo(msgWrpr);
          }

          if( $type(msgWrpr) && $type(data.message_id) ) {
              msgWrpr.attr('id', 'chat_message_'+data.message_id);
          }

          if( $type(ref) == 'object' ) {
              ref.element = msgWrpr;
          }
          this.elements.messagesWrapper.scrollTop(this.elements.messagesWrapper.get(0).scrollHeight);
        }
        onListRooms(event,data) {
          if(!$type(event.isTrigger)){
            data = event;
          }
          if( $type(data.rooms) ) {
              data = data.rooms;
          }

          // Clear
          this.elements.roomsList.empty();
          // Rebuild
          var self = this;
          Object.entries(data).forEach(function([key,room]) {
            scriptJquery.crtEle('li', {}).click(function(event) {
              self.handler.startChat({identity:room.identity});
            }).html(en4.core.language.translate(room.title) + ' (' + en4.core.language.translate(['%1$s person', '%1$s people', room.people], room.people) + ')').appendTo(this.elements.roomsList);
          }.bind(this));
        }
        onReconfigure(data) {
            if( $type(data.chat_enabled) && parseInt(data.chat_enabled) == 0 ) {
                var container = this.container;
                //this.destroy();
                (scriptJquery.crtEle('div', {
                }).html(en4.core.language.translate('The chat room has been disabled by the site admin.')).appendTo(this.container || (scriptJquery('#global_content').length ? scriptJquery('#global_content') : document.body)));
            }
        }
    };

    ChatHandler_Whispers = class {
        options = {
            identity : false,
            rateMessages : 10,
            rateTimeout : 10000,
            maxLength : 1023
        }
        handler = false;

        elements = {};

        itemOrder = [];

        rate = [];

        constructor(handler, options) {
            this.handler = handler;
            this.items = new Hash();
            this.users = new Hash();
            this.elements = new Hash();
            this.options = scriptJquery.extend(true,{},this.options,options);
            this.render();
            this.attach();
        }

        destroy() {
            this.detach();
            this.elements.container.remove();
            this.handler.imstate = 0;
        }

        attach() {
            scriptJquery(this.handler).on('onPingBefore', this.onPingBefore.bind(this));
            scriptJquery(this.handler).on('onEvent_presence', this.onPresence.bind(this));
            scriptJquery(this.handler).on('onEvent_chat', this.onChat.bind(this));
            scriptJquery(window).on('keypress', this.onCommand.bind(this));
        }

        detach() {
            scriptJquery(this.handler).off('onPingBefore', this.onPingBefore.bind(this));
            scriptJquery(this.handler).off('onEvent_presence', this.onPresence.bind(this));
            scriptJquery(this.handler).off('onEvent_chat', this.onChat.bind(this));
            scriptJquery(window).off('keypress', this.onCommand.bind(this));
        }

        // Informational

        getSelf() {
            var self;
            Object.entries(this.users).forEach(function(data) {
                if( data.self ) {
                    self = data;
                }
            });
            if( !$type(self) ) {
                self = {
                    'identity' : 0,
                    'title' : en4.core.language.translate('You')
                };
            }
            return self;
        }

        // Rendering stuff
        render() {
          this.container = scriptJquery(this.options.container).length ? scriptJquery(this.options.container) : scriptJquery(document.body);

          if(scriptJquery('#im_container').length) {
              scriptJquery('#im_container').remove();
          }

          // General
          this.elements.container = scriptJquery.crtEle('ul', {
              'id' : 'im_container'
          }).appendTo(this.container);

          // Settings
          this.items.settings = new ChatHandler_Whispers_UI_Settings(this, this.elements.container, {
              'name' : 'settings',
              'uid' : 'settings',
              'title' : en4.core.language.translate('Settings'),
              'showClose' : false
          });

          // Friends
          this.items.friends = new ChatHandler_Whispers_UI_Friends(this, this.elements.container, {
              'name' : 'friends',
              'uid' : 'friends',
              'title' : en4.core.language.translate('Friends Online'),
              'showClose' : false
          });
          this.itemOrder.push('friends');
        }


        open(identity, focus) {
          var uid = 'convo' + identity;
          var user = this.users.get(identity);
          if (!user) {
              return;
          };

          if( !$type(this.items[uid]) ) {
              var name = 'convo';
              this.items[uid] = new ChatHandler_Whispers_UI_Conversation(this, this.elements.container, {
                  'name' : name,
                  'uid' : uid,
                  'title' : user.title,
                  'identity' : identity
              });
              this.itemOrder.push(uid);
          }

          var item = this.items[uid];
          item.state(user.state);
          if( focus ) {
              item.focus();
          }

          // Handle wrapping
          this.resize();
        }

        resize() {
          if(this.elements.container.offset().left < 250 ) {
             this.elements.container.addClass('im_container_crunched');
          } else {
              //this.elements.container.removeClass('im_container_crunched');
          }
        }
        // Event handlers
        onPingBefore(data) {
          data.im = ( this.handler.imstate == 1);
        }

        onPresence(event,data) {
          if(!$type(event.isTrigger)){
            data = event;
          }
         
          this.handler._log({type:'im.presence', data : data});
          var user_id = data.identity;
          var state = parseInt(data.state);

          if( parseInt(data.state) > 0 || !this.users.has(user_id)) {
              this.users.set(user_id, data);
          }
          // Notify any open convos
          var uid = 'convo' + user_id;
          if( $type(this.items[uid]) ) {
              this.items[uid].onPresence(data);
          }
        }

        onChat(event,data) {
          if(!$type(event.isTrigger)){
            data = event;
          }
          this.handler._log({'type' : 'im.chat', 'data' : data});
          var uid = 'convo' + data.user_id;
          var name = 'convo';
          var user = this.users.get(data.sender_id);
          if (!user) {
              return;
          };

          if( !$type(this.items[uid]) ) {
              // Only focus if not stale
              this.open(data.user_id);
          }

          var item = this.items[uid];

          item.onChat(data, user);
        }
        onCommand(event) {
          if( event.control && event.alt && event.key == 'right' ) {
              this.seekItem(-1);
          }

          if( event.control && event.alt && event.key == 'left' ) {
              this.seekItem(1);
          }
        }
        seekItem(count) {
          // Get current index
          var activeIndex = 0;
          this.itemOrder.each(function(uid, index) {
              var item = this.items.get(uid);
              if( item.isVisible() ) {
                  activeIndex = index;
              }
          }.bind(this));
          activeIndex += count;
          if( activeIndex >= this.itemOrder.length ) activeIndex -= this.itemOrder.length
          if( activeIndex < 0 ) activeIndex += this.itemOrder.length

          var item = this.items.get(this.itemOrder[activeIndex]);
          item.focus();
      }
    };

    // GUI Classes
    ChatHandler_Whispers_UI_Abstract = class {
        options = {
            name : 'generic',
            uid : false,
            title : 'Untitled',
            showClose : true,
            showHide : true,
            hiddenByDefault : true
        };

        handler = false;

        container = false;

        elements = {};

        constructor(handler, container, options) {
            this.handler = handler;
            this.container = container;
            this.options = scriptJquery.extend(true,{},this.options,options);

            this.render();

            this.attach();
            this.reposition();
        }

        destroy() {
            this.handler.itemOrder.forEach((val,i)=>{
              if(this.options.uid == val){
                this.handler.itemOrder.slice(i,1);
              }
            });
            this.detach();
            this.elements.main.remove();
        }

        attach() {
            scriptJquery(this.handler).on('onItemShow', this.onOtherItemShow.bind(this));
        }

        detach() {
            scriptJquery(this.handler).off('onItemShow', this.onOtherItemShow.bind(this));
        }

        render() {

            var name = this.options.name;
            var uid = this.options.uid;

            // Main
            this.elements.main = scriptJquery.crtEle('li', {
                'class' : 'im_main im_main_' + name + ' im_main_inactive'
            }).appendTo(this.container);

            if( uid ) this.elements.main.attr('id', 'im_main_' + uid);


            // Menu
            this.elements.menu = scriptJquery.crtEle('div', {
                'class' : 'im_menu_wrapper im_menu_'  + name + '_wrapper',
            }).appendTo(this.elements.main);

            if( uid ) this.elements.menu.attr('id', 'im_menu_' + uid + '_wrapper');

            // Menu head
            this.elements.menuHead = scriptJquery.crtEle('div', {
                'class' : 'im_menu_head im_menu_'  + name + '_head'
            }).appendTo(this.elements.menu);

            if( uid ) this.elements.menuHead.attr('id', 'im_menu_' + uid + '_head');

            // Menu title
            this.elements.menuTitle = scriptJquery.crtEle('div', {
                'class' : 'im_menu_title im_menu_'  + name + '_title',
            }).html(this.options.title).appendTo(this.elements.menuHead);

            if( uid ) this.elements.menuTitle.attr('id', 'im_menu_' + uid + '_title');

            // Menu hide
            if( this.options.showHide ) {
                this.elements.menuHide = scriptJquery.crtEle('div', {
                    'class' : 'im_menu_hide im_menu_'  + name + '_hide'
                }).appendTo(this.elements.menuHead);

                if( uid ) this.elements.menuHide.attr('id', 'im_item_' + uid + '_hide');

                this.elements.menuHideLink = scriptJquery.crtEle('a', {
                    'href' : 'javascript:void(0);',
                    'class' : 'im_menu_hidelink im_menu_'  + name + '_hidelink',
                    /* @todo change to bgimage */
                }).html('<i class="fa-minus fa"></i>').click(this.hide.bind(this)).appendTo(this.elements.menuHide);

                if( uid ) this.elements.menuHideLink.attr('id', 'im_menu_' + uid + '_hidelink');

                //Close Chat
                if( uid  != 'friends') {
                  this.elements.menuRemoveLink = scriptJquery.crtEle('a', {
                    'href' : 'javascript:void(0);',
                    'class' : 'im_menu_removelink im_menu_'  + name + '_removelink',
                  }).html('<i class="fas fa-times"></i>')
                  .click(this.close.bind(this))
                  .appendTo(this.elements.menuHide);
                  if( uid ) this.elements.menuRemoveLink.attr('id', 'im_menu_' + uid + '_removelink');
                }

            }
            this.elements.menuHide = scriptJquery.crtEle('div', {});

            // Body
            this.elements.menuBody = scriptJquery.crtEle('ul', {
                'class' : 'im_menu_body im_menu_'  + name + '_body'
            }).appendTo(this.elements.menu);

            if( uid ) this.elements.menuBody.attr('id', 'im_menu_' + uid + '_body');


            // Item
            this.elements.item = scriptJquery.crtEle('div', {
                'class' : 'im_item im_item_'  + name,
            }).click(this.toggle.bind(this)).appendTo(this.elements.main);

            if( uid ) this.elements.item.attr('id', 'im_item_' + uid);

            // Item wrapper
            this.elements.itemTitle = scriptJquery.crtEle('span', {
                'class' : 'im_item_title im_item_'  + name + '_title',
            }).html(this.options.title).appendTo(this.elements.item);

            if( uid ) this.elements.itemTitle.attr('id', 'im_item_' + uid + '_title');

            // Item close
            if( this.options.showClose ) {
                this.elements.itemClose = scriptJquery.crtEle('span', {
                    'class' : 'im_item_close im_item_'  + name + '_close'
                }).appendTo(this.elements.item);

                if( uid ) this.elements.itemClose.attr('id', 'im_item_' + uid + '_close');

                this.elements.itemCloseLink = scriptJquery.crtEle('a', {
                    'href' : 'javascript:void(0);',
                    'class' : 'im_item_closelink im_item_'  + name + '_closelink',
                    /* @todo change to bgimage */
                }).html('<i class="fa fa-close"></i>').click(this.close.bind(this)).appendTo(this.elements.itemClose);

                if( uid ) this.elements.itemCloseLink.attr('id', 'im_item_' + uid + '_closelink');
            }
            if(Cookie.read('en4_chat_whispers_active', {path:en4.core.basePath}) == this.options.uid ) {
                this.show();
            } else if( this.options.hiddenByDefault ) {
                this.hide();
            }

            this.handler.resize();
        }
        reposition() {
            if(this.elements.menu && this.elements.menu.length)
                this.elements.menu.css('margin-top', 0 - this.elements.menu.height());
        }

        getBody() {
            return this.elements.menuBody;
        }
        // Actions
        isVisible() {
          return !this.elements.main.hasClass('im_main_inactive');
          //return this.elements.menu.getStyle('display') != 'none';
        }

        show(e) {
            if( $type(e) ) {e.preventDefault();}
            if( !this.isVisible() ) {
                scriptJquery(this.handler).trigger('onItemShow',this);
                Cookie.write('en4_chat_whispers_active', this.options.uid, {path:en4.core.basePath});
                this.elements.main.addClass('im_main_active').removeClass('im_main_inactive');
                this.reposition();
            }
        }

        focus(e) {
            if( $type(e) && e.preventDefault) { e.preventDefault(); }
            this.show();
        }

        hide(e) {
            if( $type(e) && e.preventDefault) { e.preventDefault(); }
            if( this.isVisible() ) {
                if( Cookie.read('en4_chat_whispers_active', {path:en4.core.basePath}) == this.options.uid ) {
                    Cookie.dispose('en4_chat_whispers_active', {path:en4.core.basePath});
                }
                this.elements.main.addClass('im_main_inactive').removeClass('im_main_active');
                //this.elements.menu.css('display', 'none');
                scriptJquery(this.handler).trigger('onItemHide');
            }
        }

        toggle(e) {
          if( $type(e) && e.preventDefault) { e.preventDefault(); }
          if( !this.isVisible() ) {
              this.show();
          } else {
              this.hide();
          }
          this.handler.resize();
        }
        close(e) {
            if( $type(e) && e.preventDefault) { e.preventDefault(); }
            if(this.isVisible()) {
              if(Cookie.read('en4_chat_whispers_active', {path:en4.core.basePath}) == this.options.uid) {
                console.log(Cookie.read('en4_chat_whispers_active', {path:en4.core.basePath}),"Cookie.read('en4_chat_whispers_active', {path:en4.core.basePath})");
                Cookie.dispose('en4_chat_whispers_active', {path:en4.core.basePath});
              }
            }
            this.destroy();
            this.handler.resize();
            delete this.handler.items[this.options.uid];
        }
        // Events
        onOtherItemShow(event,item) {
          if(!$type(event.isTrigger)){
            item = event;
          }
          if( item.options.uid != this.options.uid ) {
              this.hide();
          }
        }
    };



    class ChatHandler_Whispers_UI_Settings extends ChatHandler_Whispers_UI_Abstract {
        render() {
            var name = this.options.name;
            var uid = this.options.uid;

            // Main
            this.elements.main = scriptJquery.crtEle('li', {
                'class' : 'im_main im_main_' + name + ' im_main_settings_online'
            }).appendTo(this.container);

            if( uid ) this.elements.main.attr('id', 'im_main_' + uid);

            // Tooltip
            this.elements.tooltip = scriptJquery.crtEle('span', {
                'class' : 'im_item_tooltip_settings',
                'id' : 'im_item_tooltip_settings',
            }).html(en4.core.language.translate('Go Offline')).appendTo(this.elements.main);

            // Item
            this.elements.item = scriptJquery.crtEle('span', {
                'class' : 'im_item im_item_'  + name,
            }).click(this.toggleOnline.bind(this)).appendTo(this.elements.main);

            if( uid ) this.elements.item.attr('id', 'im_item_' + uid);

            // Item wrapper
            this.elements.itemTitle = scriptJquery.crtEle('span', {
                'class' : 'im_item_title im_item_'  + name + '_title',
            }).html('&nbsp;').appendTo(this.elements.item);

            if( uid ) this.elements.itemTitle.attr('id', 'im_item_' + uid + '_title');


            // Desktop Notifications
            if (notify.isSupported) {
              notify.config({ pageVisibility: false,autoClose: 10000});
              var notificationEnabled = Cookie.read('en4_chat_notifications', {path:en4.core.basePath}) || 0;

              this.elements.main = scriptJquery.crtEle('li', {
                  'class' : 'im_main im_main_' + name + ' im_main_settings_notifications_' + (notificationEnabled != 0 ? 'on' : 'off')
              }).appendTo(this.container);

              this.elements.main.attr('id', 'im_main_notification');

              // Tooltip
              this.elements.tooltip = scriptJquery.crtEle('span', {
                  'class' : 'im_item_tooltip_settings',
              }).html(en4.core.language.translate('Toggle Notifications')).appendTo(this.elements.main);

              // Item
              this.elements.item = scriptJquery.crtEle('span', {
                  'class' : 'im_item im_item_'  + name,
              }).click(this.toggleNotification.bind(this)).appendTo(this.elements.main);

              // Item wrapper
              this.elements.itemTitle = scriptJquery.crtEle('span', {
                  'class' : 'im_item_title im_item_'  + name + '_title',
              }).html('&nbsp;').appendTo(this.elements.item);

              if( uid ) this.elements.itemTitle.attr('id', 'im_item_' + uid + '_title');
            }
        }
        toggleNotification() {
          if (this.elements.main.hasClass('im_main_settings_notifications_off')) {
              Cookie.write('en4_chat_notifications', 1, {path:en4.core.basePath});
              notify.requestPermission(function() {});
              this.elements.main.addClass('im_main_settings_notifications_on').removeClass('im_main_settings_notifications_off');
          } else {
              Cookie.dispose('en4_chat_notifications',{path:en4.core.basePath});
              this.elements.main.addClass('im_main_settings_notifications_off').removeClass('im_main_settings_notifications_on');
          }
        }
        toggleOnline(state) {
          if( typeof(state) == 'object' ) state = null; // For events
          state = state || ( 1 - this.handler.handler.imstate );
          
          if( state == 0 ) {
              scriptJquery('#im_container').find('#im_main_settings').addClass('im_main_settingsoff').removeClass('im_main_settingsonline');
              scriptJquery('#im_item_tooltip_settings').html(en4.core.language.translate('Go Online'));
              this.elements.main.addClass('im_main_settings_offline').removeClass('im_main_settings_online');
              this.handler.handler.imstate = 0;
              this.handler.handler.status(0, 'im');

              // Show hide the rest of the stuff
              Object.entries(this.handler.items).forEach(function([key,item]) {
                if( !item.elements.main.hasClass('im_main_' + this.options.name) ) {
                    item.elements.main.css('display', 'none');
                }
              }.bind(this));
          } else {
              scriptJquery('#im_container').find('#im_main_settings').addClass('im_main_settingsonline').removeClass('im_main_settingsoff');
              scriptJquery('#im_item_tooltip_settings').html(en4.core.language.translate('Go Offline'));
              this.elements.main.addClass('im_main_settings_online').removeClass('im_main_settings_offline');
              this.handler.handler.imstate = 1;
              this.handler.handler.status(1, 'im');

              // Show hide the rest of the stuff
              Object.entries(this.handler.items).forEach(function([key,item]) {
                if( !item.elements.main.hasClass('im_main_' + this.options.name) ) {
                    item.elements.main.css('display', '');
                    item.reposition();
                }
              }.bind(this));
          }
          Cookie.write('en4_chat_imstate', this.handler.handler.imstate, {path:en4.core.basePath});
        }
        reposition = function(){};
        show = function(){};
        hide = function(){};
        toggle = function(){};
        close = function(){};
        isVisible = function(){};
    };

    class ChatHandler_Whispers_UI_Friends extends ChatHandler_Whispers_UI_Abstract{

        attach() {
            super.attach();
            scriptJquery(this.handler.handler).on('onEvent_presence', this.onPresence.bind(this));
        }

        detach() {
            super.detach();
            scriptJquery(this.handler.handler).off('onEvent_presence', this.onPresence.bind(this));
        }
        render() {
            super.render();

            // Show friend counts
            this.elements.menuCount = scriptJquery.crtEle('span', {
            }).html('(0)').appendTo(this.elements.menuTitle);

            this.elements.itemCount = scriptJquery.crtEle('span', {
            }).html('(0)').appendTo(this.elements.itemTitle);

            // Show no friends online notice
            this.elements.menuBody.css('display', 'none');
            scriptJquery.crtEle('div', {
                'class' : 'im_menu_' + this.options.name + '_none',
            }).html(this.handler.options.memberIm ? en4.core.language.translate('No members are online.') : en4.core.language.translate('None of your friends are online.')).appendTo(this.elements.menu);
        }
        // Events
        onPresence(event,data) {
          if(!$type(event.isTrigger)){
            data = event;
          }
          var user_id = data.identity;
          var bodyEl = this.getBody();
          var userElId = 'im_user_' + user_id;
          var userEl = scriptJquery("#"+userElId);

          if( data.self == 1 ) {
            return;
          }
          if( parseInt(data.state) >= 1 ) {
            if(!userEl.length) {
              if(data.photo) {
                userEl = scriptJquery.crtEle('li', {
                    'id' : userElId,
                }).html('\n\
                <span class="im_menu_friends_photo">\n\
                  <span class="bg_item_photo bg_thumb_icon bg_item_photo_user" style="background-image:url('+ data.photo  +')"></span>\n\
                </span>\n\
                <span class="im_menu_friends_name">\n\
                  ' + data.title + '\n\
                </span>\n\
                ').click(function() {
                    this.handler.open(user_id, true);
                }.bind(this)).appendTo(bodyEl);
              } else {
                userEl = scriptJquery.crtEle('li', {
                  'id' : userElId,
                }).html('\n\
                <span class="im_menu_friends_photo">\n\
                  <span class="bg_item_photo bg_thumb_icon bg_item_photo_user bg_item_nophoto"></span>\n\
                </span>\n\
                <span class="im_menu_friends_name">\n\
                  ' + data.title + '\n\
                </span>\n\
                ').click(function() {
                    this.handler.open(user_id, true);
                }.bind(this)).appendTo(bodyEl);
              }
            }
            // Update online state
            var nameEl = userEl.find('.im_menu_friends_name');
            ChatHandler_Utility.applyStateClass(nameEl, parseInt(data.state));
          } else {
            if( userEl.length ) {
                userEl.remove();
            }
          }

          // Update total
          this.elements.menuCount.html('(' + bodyEl.children().length + ')');
          this.elements.itemCount.html('(' + bodyEl.children().length + ')');

          // Show/hide no friends notice
          var childrenLength = bodyEl.children().length;
          var noFriendsEl = this.elements.menu.find('.im_menu_' + this.options.name + '_none');
          if( childrenLength < 1 && !noFriendsEl.length) {
            this.elements.menuBody.css('display', 'none');
            scriptJquery.crtEle('div', {
                'class' : 'im_menu_' + this.options.name + '_none',
            }).html(this.handler.options.memberIm ? en4.core.language.translate('No members are online.') : en4.core.language.translate('None of your friends are online.')).appendTo(this.elements.menu);
          } else if( childrenLength >= 1 && noFriendsEl.length) {
            this.elements.menuBody.css('display', '');
            noFriendsEl.remove();
          }
          this.reposition();
      }
    };
    class ChatHandler_Whispers_UI_Conversation extends ChatHandler_Whispers_UI_Abstract{
        flasher = false;
        unread = 0;
        constructor(handler, container, options) {
          super(handler, container, options);
          this.unread = Cookie.read('en4_whispers_unread_'+this.options.uid, {path:en4.core.basePath}) || 0;
          this.checkFlasher();
        }
        attach() {
          super.attach();
        }
        detach() {
          super.detach();
          if( this.flasher ) clearInterval(this.flasher);
        }
        // Rendering
        render() {
          super.render();
          // Footer
          this.elements.menuFooter = scriptJquery.crtEle('div', {
              'class' : 'im_menu_footer im_menu_'  + this.options.name + '_footer'
          }).appendTo(this.elements.menu);

          if( this.options.uid ) this.elements.menuFooter.attr('id', 'im_menu_' + this.options.uid + '_footer');

          // Input
          if( this.handler.handler._supportsContentEditable() ) {
              this.elements.menuInput = scriptJquery.crtEle('div', {
                  'class' : 'im_menu_input im_menu_'  + this.options.name + '_input',
                  'contentEditable' : true,
              }).html('<p><br /></p>').keypress(function(event) {
                          if( event.key == 'a' && event.control ) {
                              // FF only
                              // if( Browser.Engine.gecko ) {
                              //     fix_gecko_select_all_contenteditable_bug(this, event);
                              // }
                          }
              }).appendTo(this.elements.menuFooter);
              //this.elements.menuInput.focus();
          } else {
              this.elements.menuInput = scriptJquery.crtEle('textarea', {
                  'class' : 'im_menu_input im_menu_'  + this.options.name + '_input'
              }).appendTo(this.elements.menuFooter);

              if( this.options.uid ) this.elements.menuInput.attr('id', 'im_menu_' + this.options.uid + '_input');

              this.elements.menuDiv = scriptJquery.crtEle('div', {
                  'class' : 'im_menu_emotions_wrapper',
              }).appendTo(this.elements.menuFooter);
              if( this.options.uid ) this.elements.menuDiv.attr('id', 'im_menu_' + this.options.uid + '_div');

              this.elements.menuSpan = scriptJquery.crtEle('span', {
                  'class' : 'im_menu_emotions im_menu_'  + this.options.name + '_emotions',
                  'onmousedown' : 'setChatEmoticonsBoard("'+this.options.uid+'")',
              }).appendTo(this.elements.menuDiv);
              if( this.options.uid ) this.elements.menuSpan.attr('id', 'im_menu_' + this.options.uid + '_span');
          }
          this.elements.menuInput.keypress(function(event) {
            if(event.key == 'Enter' ) {
                event.preventDefault();
                this.send();
            }
          }.bind(this));
        }
        // Actions
        focus() {
          super.focus();
          this.elements.menuInput.focus();
          this.elements.menuBody.scrollTop(this.elements.menuBody.get(0).scrollHeight);
        }
        show(e) {
          super.show(e);
          // Reset unread count
          this.unread = 0;
          Cookie.dispose('en4_whispers_unread_'+this.options.uid, {path:en4.core.basePath});

          if( this.flasher ) {
            clearInterval(this.flasher);
            this.flasher = false;
            this.unread = 0;
            Cookie.write('en4_whispers_unread_'+this.options.uid, this.unread, {path:en4.core.basePath});
            this.elements.main.removeClass('im_main_unread');
          }

          if( $type(this.elements.menuInput) ) {
            this.elements.menuInput.focus();
          }
          if( $type(this.elements.menuBody) ) {
            this.elements.menuBody.scrollTop(this.elements.menuBody.get(0).scrollHeight);
          }
        }
        send() {
          var message;
          var data = this.handler.getSelf();
          // Get message
          if( this.handler.handler._supportsContentEditable() ) {
            message = this.elements.menuInput.html();
            // Webkit you're killing me!
            if( DetectWebkit() ) {
              this.elements.menuInput.remove();
              delete this.elements.menuInput;

              this.elements.menuInput = scriptJquery.crtEle('div', {
                  'class' : 'im_menu_input im_menu_'  + this.options.name + '_input',
                  'contentEditable' : true
              }).html('<p><br /></p>').appendTo(this.elements.menuFooter);

              this.elements.menuInput.keypress(function(event) {
                if( event.key == 'Enter' ) {
                    event.preventDefault();
                    this.send();
                }
              }.bind(this));
                // Everything else works great
            } else {
                this.elements.menuInput.empty();
                this.elements.menuInput.html('<p><br /></p>');
            }
            this.elements.menuInput.focus();
            message = scriptJquery("<div>"+message+"</div>").text();
          } else {
            message = this.elements.menuInput.val();
            message = scriptJquery("<div>"+message+"</div>").text();
            this.elements.menuInput.val('');
          }
          message = message.trim();
          if( message == '' ) {
            return;
          }
          if(message.length > this.handler.options.maxLength) message = message.substring(0, this.handler.options.maxLength);

          // Check rate
          this.handler.rate = this.handler.rate.filter(function(item) {
              return $time() < item + this.handler.options.rateTimeout;
          }.bind(this));

          if( this.handler.rate.length >= this.handler.options.rateMessages ) {
            this.onChat({
                'system' : true,
                'body' : en4.core.language.translate('You are sending messages too quickly - please wait a few seconds and try again.')
            });
            return;
          }
          this.handler.rate.push($time());
          // Send
          var ref = {};
          this.onChat({
              'body' : message
          }, data, ref);

          this.handler.handler.whisper(this.options.identity, message, function(responseJSON) {
            if( $type(ref.element) ) {
                ref.element.attr('id', 'chat_whisper_'+responseJSON.whisper_id);
            }
          });
        }
        state(state) {
          ChatHandler_Utility.applyStateClass(this.elements.itemTitle, parseInt(state));
        }

        close(e, force) {
          if( $type(e) ) { /*e.stop();*/ }
          if( force ) {
              super.close();
          } else {
              this.handler.handler.whisperClose(this.options.identity).complete(function() {
                  this.close(null, true);
              }.bind(this));
          }
        }
        // Events
        onChat(data, user, ref) {
          this.handler.handler._log({'type' : 'ui.conov.chat', 'data' : data, 'user' : user});
          // Ignore if mesage already exists
          if( $type(data.whisper_id) && scriptJquery('#chat_whisper_'+data.whisper_id).length) {
              return;
          }
          var messageEl;
          // Process body
          var body = data.body;
          body = ChatHandler_Utility.replaceSmilies(body);
          body = ChatHandler_Utility.unicodeToChar(body);
          //body = body.replaceLinks();
          if( body.length > this.handler.options.maxLength ) body = body.substring(0, this.handler.options.maxLength);

          // System message
          if( $type(data.system) && data.system ) {
              messageEl = (scriptJquery.crtEle('li')).appendTo(this.elements.menuBody);
              var tmpMsgEl = (scriptJquery.crtEle('span', {'class' : 'im_convo_messages_body'}).html(body).appendTo(messageEl));
              tmpMsgEl.enableLinks();
          }
          // Normal
          else
          {
            // If not visible, increment unread
            if(!this.isVisible() && !data.stale) {
              this.unread++;
              Cookie.write('en4_whispers_unread_'+this.options.uid, this.unread, {path:en4.core.basePath});
              this.checkFlasher();
            }
            messageEl = (scriptJquery.crtEle('li')).appendTo(this.elements.menuBody);
            if( $type(data.whisper_id) ) {
                messageEl.attr('id', 'chat_whisper_'+data.whisper_id);
            }
            (scriptJquery.crtEle('span', {'class' : 'im_convo_messages_author'}).html(user.title).appendTo(messageEl));
            var tmpMsgEl = (scriptJquery.crtEle('span', {'class' : 'im_convo_messages_body'}).html(body).appendTo(messageEl));
            tmpMsgEl.enableLinks();

            if (notify.isSupported ) {
              var notificationsEnable = scriptJquery('#im_main_notification').hasClass('im_main_settings_notifications_on');
              if (notificationsEnable && !data.stale && !user.self ) {
                if (notify.permissionLevel() == notify.PERMISSION_GRANTED) {
                  var icon = {x16: en4.core.staticBaseUrl + "externals/desktop-notify/notifications16x16.ico",
                      x32: en4.core.staticBaseUrl + "externals/desktop-notify/notifications32x32.png"};
                  var msg = notify.createNotification(user.title, {body:body, icon: icon,tag: data.whisper_id});
                  setTimeout(msg.close, 10000);
                }
              }
            }
          }
          if( $type(messageEl) && $type(ref) == 'object' ) {
            ref.element = messageEl;
          }
          if( this.isVisible() ) {
             this.elements.menuBody.scrollTop(this.elements.menuBody.get(0).scrollHeight);
          }
        }
        onPresence(data) {
          this.state(data.state);
        }
        checkFlasher() {
          if(!this.flasher && this.unread > 0 ) {
            this.flasher = setInterval(function() {
              if( this.elements.main.hasClass('im_main_unread') ) {
                this.elements.main.removeClass('im_main_unread');
              } else {
                  this.elements.main.addClass('im_main_unread');
              }
            }.bind(this),500);
          }
        }
    };

    ChatHandler_Utility = {
        // States
        states : new Hash({
            0 : 'offline',
            1 : 'online',
            2 : 'idle',
            3 : 'away'
        }),
        classPrefix : 'im_state_',
        getStateClass : function(state) {
          return this.classPrefix + this.states[state];
        },
        applyStateClass : function(element, state) {
            // Remove old states
            Object.entries(this.states).forEach(function([key,stateName]) {
                element.removeClass(this.classPrefix + stateName);
            }.bind(this));

            // Add new state
            element.addClass(this.getStateClass(state));
        },
        // Smilies
        // Symbols from http://www.astro.umd.edu/~marshall/smileys.html
        imageSpec : en4.core.staticBaseUrl + 'application/modules/Activity/externals/emoticons/images/:name:',
        smilies : new Hash(chatEmotions),
        replaceSmilies : function(text) {          
          Object.entries(this.smilies).forEach(function([val,name]) {
            if(text.indexOf(val) < 0) return;
            var parts = text.split(val);
            var image = '<img class="emoticon_img" src="' + unescape(this.imageSpec.replace(':name:', name)) + '" alt="' + unescape(val) + '" />';
            text = parts.join(image);
          }.bind(this));
          return text;
        },
        unicodeToChar : function(text) {
          return text.replace(/\\u[\dA-F]{4}/gi, function(match) {
              return String.fromCharCode(parseInt(match.replace(/\\u/g, ''), 16));
          });
        }
    };
})(); // END NAMESPACE


function setChatEmoticonsBoard(id) {
    var chatString = emotionsString = '';
    if(scriptJquery('#chat-emoticons-box').length)
        scriptJquery('#chat-emoticons-box').remove();

    chatString += '<span id="chat-emoticons-box"  class="chat_emoticons_box chat_emoticons_box_closed"><span id="chat-emoticons-board"  class="chat_emoticons_box_inner"><span class="chat_emoticons_box_arrow"></span>';

    for (var key in chatEmotions) {
        if (chatEmotions.hasOwnProperty(key)) {
            emotionsString += '<span class="chat_emoticons_box_icon" data-code="'+key+'" data-id="'+id+'" onmousedown="addChatEmoticonIcon(this)"><img src="application/modules/Activity/externals/emoticons/images/'+chatEmotions[key]+'" border="0"></span>';
        }
    }
    scriptJquery('#im_menu_' + id + '_div').html(scriptJquery('#im_menu_' + id + '_div').html() + chatString + emotionsString + '</span>');
    scriptJquery('#chat-emoticons-box').toggleClass('emoticons_active');
    scriptJquery('#chat-emoticons-box').toggleClass('');
    scriptJquery('#chat-emoticons-box').toggleClass('chat_emoticons_box_opened');
    scriptJquery('#chat-emoticons-box').toggleClass('chat_emoticons_box_closed');

}


function addChatEmoticonIcon(obj) {
    var chat_content = unescape(scriptJquery('#im_menu_'+scriptJquery(obj).attr("data-id")+'_input').val());
    chat_content = chat_content.replace(/&nbsp;/g, ' ');
    scriptJquery('#im_menu_'+scriptJquery(obj).attr("data-id")+'_input').val(chat_content + ' ' + scriptJquery(obj).attr("data-code") + ' ');
}

window.addEventListener('DOMContentLoaded', function() {
    scriptJquery(document.body).on('click', function(event){
        hideChatEmoticonsBoxEvent();
    });
});

function hideChatEmoticonsBoxEvent() {
    if (scriptJquery('#chat-emoticons-box').length) {
        scriptJquery('#chat-emoticons-box').removeClass('chat_emoticons_box_opened').addClass('chat_emoticons_box_closed');
    }
}
