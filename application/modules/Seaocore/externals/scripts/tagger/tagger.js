var SEAOLasso = {};
SEAOLasso.Crop = function( img, options ){

  //Extends = SEAOLasso,

  this.active = false,
  this.binds = {},

  this.options = {
    autoHide: true,
    cropMode : false,
    globalTrigger : false,
    min : false,
    max : false,
    ratio : false,
    contain : false,
    trigger : null,
    border : '#999',
    color : '#7389AE',
    opacity : .3,
    zindex : 10000,
    bgimage : './blank.gif',
    autoHide : false,
    cropMode : true,
    contain : true,
    handleSize : 8,
    preset : false,
    handleStyle : {
      'border' : '1px solid #000',
      'background-color' : '#ccc' ,
      opacity : .75
    },
    handlePreventsDefault : true
  },
	
  this.initialize = function( img,options ) {

    this.img = scriptJquery(img);
    if( this.img.prop("tagName").toLowerCase() != 'img' ) return false;
		
    var coords = this.img.seaoGetCoordinates();

    // the getCoordinates adds 2 extra pixels
    var widthFix = coords.width - 2;
    var heightFix = coords.height - 2;
                
    this.container = scriptJquery.crtEle('div', {
      'id' :'lassoMask',
    }).css({
        'position' : 'relative',
        'width' : widthFix,
        'height' : heightFix       
    }).injectSeaoCustom(this.img, 'after');
		
    this.img.css('display', 'none');
    
    options.p = this.container;

    
    this.crop = scriptJquery.crtEle('img', {
      'src' : this.img.attr('src'),
      styles : {
        'position' : 'absolute',
        'top' : 0,
        'left' : 0,
        'width' : widthFix +1,
        'height' : heightFix +1,
        'padding' : 0,
        'margin' : 0,
        'z-index' : this.options.zindex-1
      }
    }).appendTo(this.container);
    
	






      this.options = scriptJquery.extend( this.options, options );
      //this.setOptions(options);
      this.box = scriptJquery.crtEle('div', {  })
      .css({ 'display' : 'none',  'position' : 'absolute',   'z-index' : this.options.zindex })
      .appendTo((this.container) ? this.container : document.body);
      
      this.overlay = scriptJquery.crtEle('div',{
        'class' : 'lasso-overlay',
      }).css({
          'position' : 'relative',
          'height' : '100%', 
          'width' : '100%',   
          'z-index' : this.options.zindex+1
      }).appendTo(this.box);

      this.mask = scriptJquery.crtEle('div',{
      }).css({
          'position' : 'absolute', 
          'background-color' : this.options.color, 
          'opacity' : this.options.opacity, 
          'height' : '100%', 
          'width' : '100%', 
          'z-index' : this.options.zindex-1
      });

      if(this.options.cropMode){
        this.mask.css('z-index',this.options.zindex-2).appendTo(this.container);
        this.options.trigger = this.mask; // override trigger since we are a crop
      } else {
        this.mask.appendTo(this.overlay);
      }
      this.trigger = scriptJquery(this.options.trigger);
      
      // Marching Ants
      var antStyles = { 'position' : 'absolute',  'width' : 1,  'height' : 1,  'overflow' : 'hidden',  'z-index' : this.options.zindex+1 };

      if( this.options.border.seaotest(/\.(jpe?g|gif|png)/) ) {
        antStyles.backgroundImage = 'url('+this.options.border+')';
      } else {
        var antBorder = '1px dashed '+this.options.border;
      }

      this.marchingAnts = {};
      ['left','right','top','bottom'].forEach(function(side,idx){
        switch(side){
          case 'left' :
            style = scriptJquery.extend(antStyles,{ top : 0, left : -1,  height : '100%' });
          break;
          case 'right' :
            style = scriptJquery.extend(antStyles,{ top : 0, right : -1,  height : '100%' });
          break;
          case 'top' :
            style = scriptJquery.extend(antStyles,{ top : -1, left : 0,  width : '100%' });
          break;
          case 'bottom' :
            style = scriptJquery.extend(antStyles,{ bottom : -1,  left : 0,  width : '100%' });
          break;
        }
        if(antBorder) style['border-'+side] = antBorder;
        this.marchingAnts[side] = scriptJquery.crtEle('div',{
        }).css(style).appendTo(this.overlay);
      },this);

      this.binds.start = this.start.bind(this);
      this.binds.move = this.move.bind(this);
      this.binds.end = this.end.bind(this);

      this.attach();

      document.body.onselectstart = function(e){
        e = new Event(e).stop();
        return false;
      };

      // better alternative?
      this.removeDOMSelection = (document.selection && document.selection.empty) ? function(){
        document.selection.empty();
      } : 
      (window.getSelection) ? function(){
        var s=window.getSelection();
        if(s && s.removeAllRanges) s.removeAllRanges();
      } : $lambda(false);

      this.resetCoords();   


				



    this.binds.handleMove = this.handleMove.bind(this);
    this.binds.handleEnd = this.handleEnd.bind(this);
    this.binds.handles = {};
		
    this.handles = {}; // stores resize handler elements
    // important! this setup a matrix for each handler, patterns emerge when broken into 3x3 grid. Faster/easier processing.
    this.handlesGrid = {
      'NW':[0,0],
      'N':[0,1],
      'NE':[0,2],
      'W':[1,0],
      'E':[1,2],
      'SW':[2,0],
      'S':[2,1],
      'SE':[2,2]
    };
    // this could be more elegant!
    ['NW','N','NE','W','E','SW','S','SE'].forEach(function(handle){
      var grid = this.handlesGrid[handle]; // grab location in matrix
      this.binds.handles[handle] = this.handleStart.start(this,[handle,grid[0],grid[1]]); // bind
      this.handles[handle] = scriptJquery.crtEle("div", {
      })
      .on( 'mousedown', this.binds.handles[handle] )
      .css(
        scriptJquery.extend({
          'position' : 'absolute',
          'display' : 'block',
          'visibility' : 'hidden',
          'width' : this.options.handleSize,
          'height' : this.options.handleSize,
          'overflow' : 'hidden',
          'cursor' : (handle.toLowerCase()+'-resize'),
          'z-index' : this.options.zindex+2
        },this.options.handleStyle)
      ).injectSeaoCustom(this.box,'bottom');
      // start - Webligo Developments
      // Seems to not let them be hidden for some reason
      this.handles[handle].css('visibility', 'hidden');
    // end - Webligo Developments
    },this);
		
    this.binds.drag = this.handleStart.start(this,['DRAG',1,1]);
    this.overlay.addEventListener('mousedown', this.binds.drag);
    
    this.setDefault();



  },


  this.attach = function(){
    this.trigger.addEventListener('mousedown', this.binds.start);
  },

  this.detach = function(){
    if(this.active) this.end();
    this.trigger.off('mousedown', this.binds.start);
  },

  this.start = function(event){
    if((!this.options.autoHide && event.target == this.box) || (!this.options.globalTrigger && (this.trigger != event.target))) return false;
    this.active = true;
    document.addEventListener({
      'mousemove' : this.binds.move, 
      'mouseup' : this.binds.end
    });
    this.resetCoords();
    if(this.options.contain) this.getContainCoords();
    if(this.container) this.getRelativeOffset();
    this.setStartCoords(event.page);
    this.fireEvent('start');
    return true;
  },

  this.move = function(event){
    if(!this.active) return false;
    
    this.removeDOMSelection(); // clear as fast as possible!
    
    // saving bytes s = start, m = move, c = container
    var s = this.coords.start, m = event.page, box = this.coords.box = {}, c = this.coords.container;

    if(this.container){
      m.y -= this.offset.top;
      m.x -= this.offset.left;
    } 

    var f = this.flip = {
      y : (s.y > m.y), 
      x : (s.x > m.x)
    }; // flipping orgin? compare start to move
    box.y = (f.y) ? [m.y,s.y] : [s.y, m.y]; // order y
    box.x = (f.x) ? [m.x,s.x] : [s.x, m.x]; // order x

    if(this.options.contain){
      if(box.y[0] < c.y[0] ) box.y[0] = c.y[0]; // constrain top
      if(box.y[1] > c.y[1] ) box.y[1] = c.y[1]; // constrain bottom
      if(box.x[0] < c.x[0] ) box.x[0] = c.x[0]; // constrain left
      if(box.x[1] > c.x[1] ) box.x[1] = c.x[1]; // constrain right
    }
    
    if(this.options.max){ // max width & height
      if( box.x[1] - box.x[0] > this.options.max[0]){ // width is larger then max, fix
        if(f.x) box.x[0] = box.x[1] - this.options.max[0]; // if flipped
        else box.x[1] = box.x[0] + this.options.max[0]; // if normal
      }
      if( box.y[1] - box.y[0] > this.options.max[1]){ // height is larger then max, fix
        if(f.y) box.y[0] = box.y[1] - this.options.max[1]; // if flipped
        else box.y[1] = box.y[0] + this.options.max[1];  // if normal
      }
    }
  
    // ratio constraints
    if(this.options.ratio){ 
      var ratio = this.options.ratio;
      // get width/height divide by ratio
      var r = {
        x  : (box.x[1] - box.x[0]) / ratio[0],  
        y  : (box.y[1] - box.y[0]) / ratio[1]
      };
      if(r.x > r.y){ // if width ratio is bigger fix width
        if(f.x) box.x[0] =  box.x[1] - (r.y * ratio[0]); // if flipped width fix
        else    box.x[1] =  box.x[0] + (r.y * ratio[0]); // normal width fix
      } else if( r.x < r.y){ // if height ratio is bigger fix height
        if(f.y) box.y[0] =  box.y[1] - (r.x * ratio[1]); // if flipped height fix
        else  box.y[1] =  box.y[0] + (r.x * ratio[1]); // normal height fix
      }
    }

    this.refresh();
    return true;
  },
  
  this.refresh = function(){
    var c = this.coords, box = this.coords.box, cc = this.coords.container;
    c.w = box.x[1] - box.x[0];
    c.h = box.y[1] - box.y[0];
    c.top = box.y[0];
    c.left = box.x[0];
    this.box.css({
      'display' : 'block',  
      'top' : c.top, 
      'left' : c.left, 
      'width' : c.w, 
      'height' : c.h
    });
    this.fireEvent('resize',this.getRelativeCoords());      
  },

  this.end = function(event){
    if(!this.active) return false;
    this.active = false;
    scriptJquery(document).off({
      'mousemove' : this.binds.move, 
      'mouseup' : this.binds.end
    });
    if(this.options.autoHide) this.resetCoords();
    else if(this.options.min){
      if(this.coords.w < this.options.min[0] || this.coords.h < this.options.min[1]) this.resetCoords();
    }
    var ret = (this.options.autoHide) ? null : this.getRelativeCoords();
    this.fireEvent('complete',ret);
    return true;
  },

  this.setStartCoords = function(coords){
    if(this.container){
      coords.y -= this.offset.top;
      coords.x -= this.offset.left;
    } 
    this.coords.start = coords;
    this.coords.w = 0;
    this.coords.h = 0;
    this.box.css({
      'display' : 'block', 
      'top' : this.coords.start.y, 
      'left' : this.coords.start.x
    });
  },

  this.resetCoords = function(){
    this.coords = { start : { x : 0, y : 0 },  move : { x : 0, y : 0 },  end : { x: 0, y: 0 },  w: 0,  h: 0 };
    this.box.css({  'display' : 'none',   'top' : 0,   'left' : 0,   'width' : 0,   'height' : 0 });  
    this.getContainCoords();
  },
  
  this.getRelativeCoords = function(){
    var box = this.coords.box, cc = scriptJquery.extend(this.coords.container), c = this.coords;
    if(!this.options.contain) cc = {
      x : [0,0], 
      y : [0,0]
      };
    return {
      x : parseInt(box.x[0] - cc.x[0]), 
      y : parseInt(box.y[0] - cc.y[0]), 
      w : parseInt(c.w), 
      h : parseInt(c.h)
    };
  },

  this.getContainCoords = function(){
    var tc = this.trigger.getCoordinates(this.container);
    this.coords.container = {
      y : [tc.top,tc.top+tc.height], 
      x : [tc.left,tc.left+tc.width]
    }; // FIXME
  },
  
  this.getRelativeOffset = function(){
    this.offset = this.container.seaoGetCoordinates();
  },
  
  this.reset = function(){
    this.detach();
  },

  this.destroy = function(){
    this.detach();
    this.mask.remove();
    this.overlay.remove();
    this.box.remove();
  }


	
  this.setDefault = function(){
    if(!this.options.preset) return this.resetCoords();
    this.getContainCoords();
    this.getRelativeOffset();
    var c = this.coords.container, d = this.options.preset;
    this.coords.start = {
      x : d[0],
      y : d[1]
    };
    this.active = true;
    this.move({
      page : {
        x: d[2]+this.offset.left,
        y: d[3]+this.offset.top
      }
    });
    this.active = false;
  },
	
  this.handleStart = function(event,handle,row,col){
    this.currentHandle = {
      'handle' : handle,
      'row' : row,
      'col' : col
    }; // important! used for easy matrix transforms.
    document.addEventListener({
      'mousemove' : this.binds.handleMove,
      'mouseup' : this.binds.handleEnd
    });
    // had to merge because we don't want to effect the class instance of box. we want to record it
    event.page.y -= this.offset.top;
    event.page.x -= this.offset.left;
    this.coords.hs = {
      's' : event.page,
      'b' : scriptJquery.extend(this.coords.box)
    }; // handler start (used for 'DRAG')
    this.active = true;

    if( this.options.handlePreventsDefault ) {
      event.stop();
    }
  },
	
  this.handleMove = function(event){
    var box = this.coords.box, c = this.coords.container, m = event.page, cur = this.currentHandle, s = this.coords.start;
    m.y -= this.offset.top;
    m.x -= this.offset.left;
    if(cur.handle == 'DRAG'){ // messy? could probably be optimized.
      var hs = this.coords.hs, xm = m.x - hs.s.x, ym = m.y - hs.s.y, diff;
      box.y[0] = hs.b.y[0] + ym;
      box.y[1] = hs.b.y[1] + ym;
      box.x[0] = hs.b.x[0] + xm;
      box.x[1] = hs.b.x[1] + xm;
      if((diff = box.y[0] - c.y[0]) < 0) {
        box.y[0] -= diff;
        box.y[1] -= diff;
      } // constrains drag North
      if((diff = box.y[1] - c.y[1]) > 0) {
        box.y[0] -= diff;
        box.y[1] -= diff;
      } // constrains drag South
      if((diff = box.x[0] - c.x[0]) < 0) {
        box.x[0] -= diff;
        box.x[1] -= diff;
      } // constrains drag West
      if((diff = box.x[1] - c.x[1]) > 0) {
        box.x[0] -= diff;
        box.x[1] -= diff;
      } // constrains drag East
      return this.refresh();
    }

    // handles flipping ( nw handle behaves like a se when past the orgin )
    if(cur.row == 0 && box.y[1] < m.y){
      cur.row = 2;
    } 		// fixes North passing South
    if(cur.row == 2 && box.y[0] > m.y){
      cur.row = 0;
    } 		// fixes South passing North
    if(cur.col == 0 && box.x[1] < m.x){
      cur.col = 2;
    } 		// fixes West passing East
    if(cur.col == 2 && box.x[0] > m.x){
      cur.col = 0;
    } 		// fixes East passing West

    if(cur.row == 0 || cur.row == 2){ // if top or bottom row ( center e,w are special case)
      s.y = (cur.row) ? box.y[0] : box.y[1]; 				// set start.y opposite of current direction ( anchor )
      if(cur.col == 0){
        s.x = box.x[1];
      } 				// if West side anchor East
      if(cur.col == 1){
        s.x = box.x[0];
        m.x = box.x[1];
      } // if center lock width
      if(cur.col == 2){
        s.x = box.x[0];
      } 				// if East side anchor West
    }
		
    if(!this.options.ratio){ // these handles only apply when ratios are not in effect. center handles don't makes sense on ratio
      if(cur.row == 1){ // sanity check make sure we are dealing with the right handler
        if(cur.col == 0){
          s.y = box.y[0];
          m.y = box.y[1];
          s.x = box.x[1];
        }		// if West lock height anchor East
        else if(cur.col == 2){
          s.y = box.y[0];
          m.y = box.y[1];
          s.x = box.x[0];
        }// if East lock height anchor West
      }
    }
    m.y += this.offset.top;
    m.x += this.offset.left;
    this.move(event); // now that we manipulated event pass it to move to manage.

    if( this.options.handlePreventsDefault ) {
      event.stop();
    }
  },
	
  this.handleEnd = function(event){
    scriptJquery(document).off({
      'mousemove' : this.binds.handleMove,
      'mouseup' : this.binds.handleEnd
    });
    this.active = false;
    this.currentHandle = false;
    if(this.options.min && (this.coords.w < this.options.min[0] || this.coords.h < this.options.min[1])){
      if(this.options.preset) this.setDefault();
      else this.resetCoords();
    }

    if( this.options.handlePreventsDefault ) {
      event.stop();
    }
  },
	
  this.end = function(event){
    if(!this.parent(event)) return false;
    if(this.options.min && (this.coords.w < this.options.min[0] || this.coords.h < this.options.min[1])){
      this.setDefault();
    }
  },
	
  this.resetCoords = function(){
    this.parent();
    this.coords.box = {
      x : [0,0],
      y : [0,0]
    };
    this.hideHandlers();
  //this.crop.css('clip', 'rect(0px 0px 0px 0px)');
  },	
	
  this.showHandlers = function(){
    var box = this.coords.box;
		
    if(this.options.min && (this.coords.w < this.options.min[0] || this.coords.h < this.options.min[1])) this.hideHandlers();
		
    else {
      var tops = [], lefts = [], pxdiff = (this.options.handleSize / 2)+1; // used to store location of handlers
      for(var cell = 0, cells = 2; cell <= cells; cell++ ){  // using matrix again
        tops[cell] = ( (cell == 0) ? 0 : ((cell == 2) ? box.y[1] - box.y[0] : (box.y[1] - box.y[0])/2  ) ) - pxdiff;
        lefts[cell] = ( (cell == 0) ? 0 : ((cell == 2) ? box.x[1] - box.x[0] : (box.x[1] - box.x[0])/2 ) ) - pxdiff;
      }

      for(var handleID in this.handlesGrid){ // get each handler's matrix location
        var grid = this.handlesGrid[handleID], handle = this.handles[handleID];
        if(!this.options.ratio || (grid[0] != 1 && grid[1] != 1)){ // if no ratio or not N,E,S,W show
          if(this.options.min && this.options.max){
            if((this.options.min[0] == this.options.max[0]) && (grid[1] % 2) == 0) continue; // turns off W&E since width is set
            if(this.options.min[1] == this.options.max[1] && (grid[0] % 2) == 0) continue;  // turns off N&S since height is set
          }
          handle.css({
            'visibility' : 'visible',
            'top' : tops[grid[0]],
            'left' : lefts[grid[1]]
          }); // just grab from grid
        }
      }
    }
  },
	
  this.hideHandlers = function(){
    for(handle in this.handles){
      this.handles[handle].css('visibility','hidden');
    }
  },
	
  this.refresh = function(){
    this.parent();
    var box = this.coords.box, cc = this.coords.container;

    if(Browser.Engine.trident && Browser.Engine.version < 5 && this.currentHandle && this.currentHandle.col === 1)
      this.overlay.css('width' , '100.1%').css('width','100%');

    //  this.crop.css('clip' , 'rect('+(box.y[0])+'px '+(box.x[1])+'px '+(box.y[1])+'px '+(box.x[0])+'px )' );
    this.showHandlers();
  },

  this.destroy = function(){

    this.parent();          
    this.img.injectSeaoCustom(this.container, 'after');
    this.img.css('display', '');
    this.container.remove();
  }

  this.initialize( img, options );

};


SEAOLasso.Crop_cropper = function( element, options ) {
  // tagger is not working
  //return;
  console.error("show Crop_cropper")
  console.error(  !( element instanceof scriptJquery ) || element.length != 1  );
  if ( !( element instanceof scriptJquery ) || element.length != 1 ) {
    return false
  }
  this.options = {}
  this.element = null;
  this.elementCoordinates = { x1:0, y1:0, width:0, height:0 };
  this.initialize = function( element, options ) {
    this.options = scriptJquery.extend( this.options, options );
    this.element = element;
    if( this.element.prop("tagName").toLowerCase() != 'img' ) {
      return false;
    }
    return this;
  }
  this.setCropperCoordinates = function(coords) {
    this.elementCoordinates = { x1 : coords.x1,
                                y1 : coords.y1,
                                width : coords.width,
                                height : coords.height
                              };
  }
  this.getCropperCoordinates = function() {
    return this.elementCoordinates;
  }
  this.cropperStart = function() {
    this.element.cropper({
        done: this.setCropperCoordinates
    });
  }

  this.cropperEnd = function() {
    // It is not working so a trick is being used here
    this.element.next().remove(); // Remove next div element with class cropper-container
    this.element.removeClass('cropper-hidden');
    this.setCropperCoordinates({ x1:0, y1:0, width:0, height:0 }); // crop is removed so set coordinates to zero
  }

  this.initialize( element, options );
  return this;
}


var SEAOTagger = function( element, options ) {

  this.options = {
    // Local options
    'title' : false,
    'description' : false,
    'transImage' : en4.core.baseUrl + 'application/modules/Seaocore/externals/images/trans.gif',
    'existingTags' : [],
    'tagListElement' : false,
    'linkElement' : false,
    'noTextTagHref' : true,
    'guid' : false,
    'enableCreate' : false,
    'enableDelete' : false,
    // Create
    'createRequestOptions' : { 'url' : '', 'data' : { 'format' : 'json' } },
    'deleteRequestOptions' : { 'url' : '', 'data' : { 'format' : 'json' } },
    // Cropper options
    'cropOptions' : {
      'preset' : [10,10,58,58],
      'min' : [48,48],
      'max' : [128,128],
      'handleSize' : 8,
      'opacity' : .6,
      'color' : '#7389AE',
      'border' : 'externals/moolasso/crop.gif'
    },
    // Autosuggest options
    'suggestProto' : 'local',
    'suggestParam' : [ ],
    'suggestOptions' : {
      'minLength': 0,
      'maxChoices' : 100,
      'delay' : 250,
      'selectMode': 'pick',
      //'autocompleteType': 'message',
      'multiple': false,
      'className': 'message-autosuggest',
      'filterSubset' : true,
      'tokenFormat' : 'object',
      'tokenValueKey' : 'label',
      /*'appendToChoice': $empty,
      'onPush' : $empty,*/
      'prefetchOnInit' : true,
      'alwaysOpen' : true,
      'ignoreKeys' : true
    },    
    'enableShowToolTip':false,
    // Show ToolTip
    'showToolTipRequestOptions' : { 'url' : '', 'data' : { 'format' : 'json' } }
  },

  this.initialize = function(el, options) {

    el = scriptJquery( '#'+el );
    if(!el)
      return;
    if( el.prop("tagName").toLowerCase() != 'img' ) {
      this.image = el.find('img');
    } else {
      this.image = el;
    }

    this.element = el;
    this.count = 0;
    scriptJquery.extend( this.options, options );
    this.actualImage = new Image();
    // this.actualImage.src = this.image.attr('src'); will always after onload
    thisObj = this;
    this.actualImage.onload = function() {
      thisObj.options.existingTags.forEach(thisObj.addTag.bind(thisObj));
      thisObj.addToolTip();
    }
    this.actualImage.src = this.image.attr('src');


  },

  this.begin = function() {
    if( !this.options.enableCreate ) return;
    this.getCrop();
    this.getForm();
    this.getSuggest();
    this.onBegin();
    //this.fireEvent('onBegin');
  },

  this.end = function() {

    if( this.crop ) {
      this.crop.cropperEnd();
      //this.crop.remove();
      delete this.crop;
    }
    if( this.form ) {
      this.form.remove();
      delete this.form;
    }
    if( this.suggest ) {
      delete this.suggest;
    }
    this.onEnd();
    //this.fireEvent('onEnd');

  },

  this.getCrop = function() {
    if( !this.crop ) {
      var options = scriptJquery.extend(this.options.cropOptions, { } );
      var cropObj = this;
      jQuery.ajax({
        url: en4.core.baseUrl + 'externals/cropper/cropper.js',
        success: function() {
          jQuery.ajax({
            url: en4.core.baseUrl + 'externals/cropper/cropper.css',
            success: function() {
              cropObj.crop = SEAOLasso.Crop_cropper( cropObj.image, options );
              cropObj.crop.cropperStart();
            }
          });
        }
      });
      //this.crop = SEAOLasso.Crop(this.image, options);

      //this.crop.addEventListener('resize', this.onMove.bind(this));
      //this.crop.refresh();
    }
    return this.crop;
  },

  this.getForm = function() {
    if( !this.form ) {
      this.form = scriptJquery.crtEle('div', {
        'id' : 'tagger_form',
        'class' : 'tagger_form',
      }).css({
          'position' : 'absolute',
          'z-index' : '100000',
          'width' : '150px'
      }).injectSeaoCustom(this.element, 'after');

      // Title
      if( this.options.title ) {
        scriptJquery.crtEle('div', {
          'class' : 'media_photo_tagform_titlebar',
        })
        .html(this.options.title)
        .appendTo(this.form);
      }

      // Container
      this.formContainer = scriptJquery.crtEle('div', {
        'class' : 'media_photo_tagform_container'
      }).appendTo(this.form);

      // Description
      if( this.options.description ) {
        scriptJquery.crtEle('div', {
          'class' : 'media_photo_tagform_text',
        })
        .html(this.options.description)
        .appendTo(this.formContainer);
      }

      // Input
      this.input = scriptJquery.crtEle('input', {
        'id' : 'tagger_input',
        'class' : 'tagger_input',
        'type' : 'text',
      }).appendTo(this.formContainer);

      // Choices
      this.choices = scriptJquery.crtEle('div', {
        'class' : 'tagger_list'
      }).appendTo(this.formContainer);

      // Submit container
      var submitContainer = scriptJquery.crtEle('div', {
        'class' : 'media_photo_tagform_submits'
      }).appendTo(this.formContainer);

      var self = this;
      scriptJquery.crtEle('a', {
        'id' : 'tag_save',
        'href' : 'javascript:void(0);',
      })
      .click( function(){
        var data = {}; //JSON.decode(choice.find('input').value);
        data.label = self.input.value;
        if( $type(data.label) && data.label != '' ) {
          data.extra = self.coords;
          self.createTag(data);
        }
      })
      .html(en4.core.language.translate('Save'))
      .appendTo(submitContainer);

      scriptJquery.crtEle('a', {
        'id' : 'tag_cancel',
        'href' : 'javascript:void(0);',
      })
      .click( function() {
        self.end();
      })
      .html(en4.core.language.translate('Cancel'))
      .appendTo(submitContainer);

      this.input.focus();
    }
    
    return this.form;
  },
  this.cache = {};
  this.getSuggest = function() {

    if( !this.suggest ) {

      this.choices = scriptJquery.crtEle('ul', {
        'class':'tag-autosuggest',
        'tabindex' : "0",
      }).insertAfter(this.element);

      var self = this;
      var options = {
        disabledSuggest: true,
        response: function(event, ui ) {
          if(self.choices)
            self.choices.html("");
          
          if(typeof ui.content === "undefined"){
            return false;
          }
          ui.content.forEach((token)=>{
            var choice = scriptJquery.crtEle('li', {
              'class': 'autocompleter-choices',
              'id': token.guid,
              'tabindex' : "-1"
            }).html(token.photo || '');

            scriptJquery.crtEle('div', {
              'class' : 'autocompleter-choice'
            }).html(token.label).appendTo(choice);

            scriptJquery.crtEle('input', {
              'type' : 'hidden',
              'value' : JSON.stringify(token)
            }).appendTo(choice);
            
            choice.data('autocompleteChoice', token);
            
            choice.click(function(e){
              options.select.call(this,e);
            });
            choice.appendTo(self.choices);

          });
        },
        select: function(event) { 

          var data = JSON.parse(choice.find('input').val());
          data.extra = self.coords;
          self.createTag(data);
          self.choices.remove();
          delete self.choices;
        },
        close: function(event, ui ) {
          if(self.choices)
            self.choices.remove();
          delete self.choices;
        },
      };
      if( this.options.suggestProto == 'local' ) {
        options['source'] = this.options.suggestParam;
        this.suggest = this.getHiddenInput().autocomplete(options);
      } else if( this.options.suggestProto == 'request.json' ) {
        options['source'] = function(request, response) { 
            if(self.cache[request.term]){
               response(self.cache[request.term]);
            } else {
              scriptJquery.ajax({
                url: self.options.suggestOptions.url,
                dataTyp: 'json',
                method: 'post',
                data: scriptJquery.extend({ value: request.term },self.options.suggestOptions.data),
                success: function (transformed) {
                  transformed = scriptJquery.parseJSON(transformed);
                  response(transformed);
                  self.cache[request.term] = transformed;
                },
                error: function () {
                    response([]);
                }
            });
          }
        };
        this.suggest = this.getHiddenInput().autocomplete(options);
      }
    }

    return this.suggest;
  }

  this.getTagList = function() {
    if( !this.tagList ) {
      if( !this.options.tagListElement ) {
        this.tagList = scriptJquery.crtEle('div', {
          'class' : 'tag_list'
        }).injectSeaoCustom(this.element, 'after');
      } else {
        this.tagList = scriptJquery(this.options.tagListElement);
      }
    }

    return this.tagList;
  },

  this.onMove = function(coords) {
    this.coords = coords;
    var coords_y=coords.y;
    var pos = {
      x:0,
      y:0
    }; //this.element.getPosition();
    var form = this.getForm();
    var formParentHeight=this.getForm().parent().seaoGetCoordinates().height - 2;
    if(formParentHeight < (coords_y + 20 +form.seaoGetCoordinates().height - 2  )){
      coords_y = coords_y - (form.seaoGetCoordinates().height - 2);    
    }
    form.css({
      'top' : pos.y + coords_y + 20,
      'left' : pos.x + coords.x + coords.w + 20
    });
  },

  // Tagging stuff

  this.addTag = function(params) {

    thisObj = this;
    if ( 'object' != $type(params)  || !params.extra) {
      return;
    }

    var baseX = 0, baseY = 0, baseW = 0, baseH = 0;
    ["x", "y", "w", "h"].forEach(function(key) {
      params.extra[key] = parseInt(params.extra[key]);
    });
    var actualImageCoords =this.getActualImageCoords();
    var imageCoords =this.getImageCoords();
    // Set Relative X Coords    
    ["x", "w"].forEach(function(key) {
      params.extra[key]= parseInt(params.extra[key]/ actualImageCoords.width * imageCoords.width);    
    });
    
    // Set Relative Y Coords     
    ["y", "h"].forEach(function(key) {
      params.extra[key]= parseInt(params.extra[key] / actualImageCoords.height * imageCoords.height);
    });

    if( this.options.noTextTagHref && params.tag_type == 'core_tag' ) {
      delete params.href;
    }
   
    // Make tag
    if(scriptJquery('#tag_' + params.id).length) {
      scriptJquery('#tag_' + params.id).remove();
    }

    var tag = scriptJquery.crtEle('div', {
      'id' : 'tag_' + params.id,
      'class' : 'tag_div',
    }).injectSeaoCustom(this.element, 'after');
    tag.html('<img src="'+this.options.transImage+'" width="100%" height="100%" />')
    tag.css({ 'position' : 'absolute',
              'width' : params.extra.w,
              'height' : params.extra.h,
              'top' : baseY + params.extra.y,
              'left' : baseX + params.extra.x
      });

    tag.hover( function(){
      thisObj.showTag(params.id);
    }, function() {
        thisObj.hideTag(params.id);
    });

    // Make label
    // Note: we need to use visibility hidden to position correctly in IE
    if(scriptJquery('#tag_label_' + params.id).length) {
      scriptJquery('#tag_label_' + params.id).remove();
    }
    var label = scriptJquery.crtEle("span", {
      'id' : 'tag_label_' + params.id,
      'class' : 'tag_label',
    }).injectSeaoCustom(this.element, 'after');
    label.html(params.text)
    label.css({'position' : 'absolute'})

    var labelPos = {};
    labelPos.top = ( baseY + params.extra.y + tag.seaogetSize().y );
    labelPos.left = Math.round( ( baseX + params.extra.x ) + ( tag.seaogetSize().x / 2 ) - (label.seaogetSize().x / 2) );

    if( this.element.seaogetSize().y < parseInt(labelPos.top) + 20 ){
      labelPos.top = baseY + params.extra.y - label.seaogetSize().y;
    }

    label.css(labelPos);

    this.hideTag(params.id);

    var isFirst = ( !$type(this.count) || this.count == 0 );
    this.getTagList().css('display', '');

    // Make list
    if(scriptJquery('#tag_comma_' + params.id).length)
      scriptJquery('#tag_comma_' + params.id).remove();
    if( !isFirst ) scriptJquery.crtEle('span', {
      'id' : 'tag_comma_' + params.id,
      'class' : 'tag_comma',
    })
    .html(',')
    .appendTo(this.getTagList());

    // Make other thingy
    if(scriptJquery('#tag_info_' + params.id).length)
      scriptJquery('#tag_info_' + params.id).remove();
    var info = scriptJquery.crtEle('span', {
      'id' : 'tag_info_' + params.id,
      'class' : 'tag_info media_tag_listcontainer'
    }).appendTo(this.getTagList());

    if(scriptJquery('#tag_activator_' + params.id).length) {
      scriptJquery('#tag_activator_' + params.id).remove();
    }
    var activator = scriptJquery.crtEle('a', {
      'id' : 'tag_activator_' + params.id,
      'class' : 'tag_activator',
      'href' : params.href || null,
      'rel': params.id,
    }).appendTo(info);
    activator.html(params.text);
    activator.hover( function(){
      thisObj.showTag(params.id);
    }, function() {
        thisObj.hideTag(params.id);
    });
    // Delete
    if(!this.options.enableShowToolTip  && this.checkCanRemove(params.id) ) {
      info.appendText(' (');
      if(scriptJquery('#tag_destroyer_' + params.id).length)
        scriptJquery('#tag_destroyer_' + params.id).remove();
      var destroyer = scriptJquery.crtEle('a', {
        'id' : 'tag_destroyer_' + params.id,
        'class' : 'tag_destroyer albums_tag_delete',
        'href' : 'javascript:void(0);',
      }).appendTo(info);
      destroyer.html(en4.core.language.translate('delete'));
      destroyer.click( function() {
        thisObj.removeTag(params.id)
      })
      info.appendText(')');
    }
    this.count++;

  },

  this.createTag = function(params) {
    if( !this.options.enableCreate ) return;
    
    params.extra = this.getSaveCoords(params.extra);
    // Send request
    var requestOptions = scriptJquery.extend(this.options.createRequestOptions, {
      'data' : scriptJquery.extend(params, {}),
      'onComplete' : function(responseJSON) {
        this.addTag(responseJSON);
        this.addToolTip();
        this.onCreateTag(responseJSON);
        //this.fireEvent('onCreateTag',responseJSON);
      }.bind(this)
    });
    var request = scriptJquery.ajax(requestOptions);
    // End tagging
    this.end();
  },

  this.removeTag = function(id) {

    if( !this.checkCanRemove(id) ) return;

    // Remove from frontend
    var next = scriptJquery('#tag_info_' + id).getNext(); 
    if(scriptJquery('#tag_comma_' + id).length)
      scriptJquery('#tag_comma_' + id).remove();
    else if( next && next.html().trim() == ',' ) next.remove();
    scriptJquery('#tag_' + id).remove();
    scriptJquery('#tag_label_' + id).remove();
    scriptJquery('#tag_info_' + id).remove();
    this.count--;
    this.onRemoveTag(id)
    //this.fireEvent('onRemoveTag',[id]);
    
    // Send request
    var requestOptions = scriptJquery.extend(this.options.deleteRequestOptions, {
      'data' : {
        'tagmap_id' : id
      },
      'onComplete' : function(responseJSON) {
        
      }.bind(this)
    });
    var request = scriptJquery.ajax(requestOptions);

  },

  this.checkCanRemove = function(id) {

    // Check if can remove
    var tagData;
    this.options.existingTags.each(function(datum) {
      if( datum.tagmap_id == id ) {
        tagData = datum;
      }
    });

    if( this.options.enableDelete ) return true;

    if( tagData ) {
      if( tagData.tag_type + '_' + tagData.tag_id == this.options.guid ) return true;
      if( tagData.tagger_type + '_' + tagData.tagger_id == this.options.guid ) return true;
    }
    
    return false;
  },

  this.showTag = function(id) {
    scriptJquery('#tag_' + id).removeClass('tag_div_hidden');
    scriptJquery('#tag_label_' + id).removeClass('tag_label_hidden');
  },

  this.hideTag = function(id) {
    scriptJquery('#tag_' + id).addClass('tag_div_hidden');
    scriptJquery('#tag_label_' + id).addClass('tag_label_hidden');
  },
  
  this.getActualImageCoords = function(){
    return {
      'width' : this.actualImage.width, 
      'height' : this.actualImage.height
    };
  },
  
  
  this.getImageCoords = function(){
    var coords = this.image.seaoGetCoordinates();    
    // the getCoordinates adds 2 extra pixels
    return {
      'width' : (coords.width - 2), 
      'height' : (coords.height - 2)
    };
  },
  this.getCropCoords = function(){

    var coords =this.getCrop().getCropperCoordinates();

    // the getCoordinates adds 2 extra pixels
    return {
      'width' : (coords.width - 2), 
      'height' : (coords.height - 2)
    };
  },
  this.getSaveCoords = function(coods){
    var actualImageCoords =this.getActualImageCoords();
    var imageCoords =this.getCropCoords();
    // Set Relative X Coords    
    ["x", "w"].forEach(function(key) {      
      coods[key]= parseInt((coods[key]/ imageCoords.width) * actualImageCoords.width);     
    });
    
    // Set Relative Y Coords     
    ["y", "h"].forEach(function(key) {
      coods[key]= parseInt((coods[key] / imageCoords.height) * actualImageCoords.height);
    });
   
    return coods;
  },

  this.setToolTip = function(){

    if(this.options.enableShowToolTip == false) {
      return;
    }
    // Add tooltips

    var window_size = window.seaogetSize();
    return  new SEATips( scriptJquery('.tag_activator'), {
      fixed : true,
      title:'',
      className : 'sea_add_tooltip_link_tips',
      hideDelay :200,
      showDelay :200,
      offset : { 'x' : 0, 'y' : 0 },
      windowPadding: { 'x':200, 'y':(window_size.y/2) },
      req_pendding:0
    });

  },
  this.getToolTipDefaultContent = function(){

    var toolTipDefault = scriptJquery.crtEle('div', {});     
    var info_tip = scriptJquery.crtEle('div', {
      'class' : 'uiOverlay info_tip',    
    }).css({ 'width' : 200, 'top' : 0 }).appendTo(toolTipDefault);
    
    var info_tip_content_wrapper = scriptJquery.crtEle('div', { 'class' : 'info_tip_content_wrapper' }).appendTo(info_tip);
   
    var info_tip_content = scriptJquery.crtEle('div', {  'class' : 'info_tip_content' }).appendTo(info_tip_content_wrapper);
    scriptJquery.crtEle('div', {
      'class' : 'info_tip_content_loader',
    })
    .html('<img src="application/modules/Seaocore/externals/images/core/loading.gif" alt="Loading" />')
    .appendTo(info_tip_content);
    return toolTipDefault;

  },
  this.addToolTip = function(){

    var self=this;
    if(this.options.enableShowToolTip){

      /*scriptJquery('.tag_activator').on('mouseover', function(event) {

        var el = scriptJquery(event.target);   
        ItemTooltips.options.offset.y = el.offsetHeight;
        ItemTooltips.options.showDelay = 0; 
        ItemTooltips.options.showToolTip=true;
        if( !el.retrieve('tip-loaded', false) ) {
          ItemTooltips.options.req_pendding++;
          var id='';
          if(el.hasAttribute("rel"))
            id=el.rel;
          if(id =='')
            return;

          el.store('tip-loaded', true);
          el.store('tip:title',self.getToolTipDefaultContent());
          el.store('tip:text', ''); 

          el.addEventListener('mouseleave',function(){
            ItemTooltips.options.showToolTip=false;  
          });

          var requestOptions = scriptJquery.extend(self.options.showToolTipRequestOptions, {
            'data' : {
              format : 'html',
              'tagmap_id' : id
            },
            evalScripts : true,
            success : function(responseHTML) {          
              el.store('tip:title', '');
              var responseHTMLContent = scriptJquery.crtEle('div',
              {
                html :responseHTML    
              });

              if( self.checkCanRemove(id) ) {
                var responseHTMLContentDelete = responseHTMLContent.find('.tagged_info_tip_content_delete');
                responseHTMLContentDelete.appendText(' (');
                if(scriptJquery('#tag_destroyer_' + id).length)
                  scriptJquery('#tag_destroyer_' + id).remove();
                scriptJquery.crtEle('a', {
                  'id' : 'tag_destroyer_' + id,
                  'class' : 'tag_destroyer albums_tag_delete',
                  'href' : 'javascript:void(0);',
                  'html' : en4.core.language.translate('remove tag'),
                  'events' : {
                    'click' : function() {
                      ItemTooltips.options.canHide = true;
                      ItemTooltips.hide(el);
                      self.removeTag(id);                                            
                    }.bind(this)
                  }
                }).appendTo(responseHTMLContentDelete);
                responseHTMLContentDelete.appendText(')');
              }
              
              el.store('tip:text', responseHTMLContent);
              ItemTooltips.options.showDelay=0;
              ItemTooltips.elementEnter(event, el); // Force it to update the text
              ItemTooltips.options.showDelay=200;
              ItemTooltips.options.req_pendding--;
              if(!ItemTooltips.options.showToolTip || ItemTooltips.options.req_pendding > 0){
                ItemTooltips.elementLeave(event,el);
              }           
              var tipEl=ItemTooltips.toElement();
              tipEl.addEventListeners({
                'mouseenter': function() {
                  ItemTooltips.options.canHide = false;
                  ItemTooltips.show(el);
                },
                'mouseleave': function() {                
                  ItemTooltips.options.canHide = true;
                  ItemTooltips.hide(el);                    
                }
              });
              Smoothbox.bind(scriptJquery(".tag_activator"));
            }.bind(this)
          });
          
          scriptJquery.ajax(requestOptions);
        }

      });*/

      var ItemTooltips = self.setToolTip();
    }
  }

  this.initialize( element, options );
  return this;

};