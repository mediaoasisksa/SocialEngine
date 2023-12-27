
/* $Id: slideitmoo-1.1_full_source.js  $ */
/**
	SlideItMoo v1.1 - Image slider
	(c) 2007-2010 Constantin Boiangiu <http://www.php-help.ro>
	MIT-style license.
	
	Changes from version 1.0
	- added continuous navigation
	- changed the navigation from Fx.Scroll to Fx.Morph
	- added new parameters: itemsSelector: pass the CSS class for divs
	- itemWidth: for elements with margin/padding pass their width including margin/padding
	
	Updates ( August 4th 2009 )
	- added new parameter 'elemsSlide'. When this is set to a value lower that the actual number of elements in HTML, it will slide at once that number of elements when navigation clicked. Default: null
	- added onChange event that returns the index of the current element
	
	Updates ( January 12th 2010 )
	- vertical sliding available. First, set your HTML to display vertically and set itemHeight:height of individual items ( including padding, border and so on ) and slideVertical:true
	- navigators ( forward/back ) no longer added by script. Instead, add them into overallContainer making their display from CSS and after add the CSS selector class to navs parameter
		IE: navs:{ 
				fwd:'.SlideItMoo_forward', 
				bk:'.SlideItMoo_back' 
			}
	- new method available resetAll(). When called, this will reset the previous settings and restart the script. Useful if you change slider content on-the-fly
	- new method available to stop autoSlide ( stopAutoSlide() ). To start autoslide back, use startAutoSlide()
**/
var SeaocoreSlideItMoo = function(options){
  this.options= {
    overallContainer: null,/* outer container, contains fwd/back buttons and container for thumbnails */
    elementScrolled: null, /* has a set width/height with overflow hidden to allow sliding of elements */
    thumbsContainer: null,  /* actual thumbnails container */
    thumbsContainerOuter:null,
    itemsSelector: null, /* css class for inner elements ( ie: .SlideItMoo_element ) */
    itemsVisible:4, /* number of elements visible at once */
    elemsSlide: null, /* number of elements that slide at once */
    itemWidth: null, /* single element width */
    itemHeight: null, /* single element height */
    itemsSelectorLoading:null,
    navs:{ /* starting this version, you'll need to put your back/forward navigators in your HTML */
      fwd:'SlideItMoo_forward', /* forward button CSS selector */
      bk:'SlideItMoo_back' /* back button CSS selector */
    },
    slideVertical: true, /* vertical sliding enabled */
    showControls:1, /* show forward/back controls */
    transition: 'linear', /* transition */
    duration: 8000, /* transition duration */
    direction: 1, /* sliding direction ( 1: enter from left/top; -1:enter from right/bottom ) */
    autoSlide: false, /* auto slide - as milliseconds ( ie: 10000 = 10 seconds ) */
    mouseWheelNav: false, /* enable mouse wheel nav */
    startIndex: null,
    contentstartIndex:null,
    forwardLink:null,
    isActive:false,
    params : {
  }
  /*onChange: $empty*/
  }
  
  this.initialize= function(options) {
    this.options = scriptJquery.extend(this.options, options);
    var self=this;
    scriptJquery('#'+this.options.navs.bk).on('click', function () {
      self.sendajax(-1);
    });
    scriptJquery('#'+this.options.navs.fwd).on('click', function () { 
      self.sendajax(1);
    });
    if((this.options.totalCount -this.options.itemsVisible)<=0){
      // hidding forward button
      scriptJquery('#'+this.options.navs.fwd).css('display', 'none');
      scriptJquery('#'+this.options.navs.bk+'_dis').css('display', 'none');
    }

    /* all elements are identified on CSS selector (itemsSelector) */
    this.elements = scriptJquery('#'+this.options.thumbsContainer).find(this.options.itemsSelector);
    this.totalElements = this.elements.length;
    if( this.totalElements <= this.options.itemsVisible ) return;
    // width of thumbsContainer children
    console.log('this element is:- ', this.elements[0]);  
    var defaultSize = scriptJquery(this.elements[0]).getSize();
    this.elementWidth = this.options.itemWidth || defaultSize.x;
    this.elementHeight = this.options.itemHeight || defaultSize.y;
    this.currentElement = 0;
    this.direction = this.options.direction;
    this.autoSlideTotal = this.options.autoSlide + this.options.duration;
    if( this.options.elemsSlide == 1 ) this.options.elemsSlide = null;
    this.begin();
  }
    
  this.begin = function(){
    /* if navigation is needed and enabled, add it */
    this.addControls();
        
    // resizes the container div's according to the number of itemsVisible thumbnails
    this.setContainersSize();
    
    // assign the container which will be responsible for fx animation
    this.myFx = scriptJquery('#' + this.options.thumbsContainer);

    /* if autoSlide is not set, scoll on mouse wheel */
    if( this.options.mouseWheelNav && !this.options.autoSlide ){
      scriptJquery('#'+this.options.thumbsContainer).on('mousewheel', function(ev){
        ev.stopPropagation();
        this.slide(-ev.wheel);
      }.bind(this));
    }
    
    /* start index element */
    if( this.options.startIndex && this.options.startIndex > 0 && this.options.startIndex < this.elements.length ){
      for( var t = 1; t < this.options.startIndex; t++ )
        this.rearange();
    }
    
    if( this.options.autoSlide && this.elements.length > this.options.itemsVisible )

    this.startAutoSlide();
  }

  /* resets the whole slider in case content changes */
  this.resetAll=  function(){
    scriptJquery('#'+this.options.overallContainer).removeAttr('style');
    scriptJquery('#'+this.options.elementScrolled).removeAttr('style');
    scriptJquery('#'+this.options.thumbsContainer).removeAttr('style');
    this.stopAutoSlide();
    this.initialize(options);
  }

  /* sets the containers width to leave visible only the specified number of elements */
  this.setContainersSize = function(){
    var overallSize = {};
    var scrollSize = {};
    var thumbsSize = {};
    
    if( this.options.slideVertical ){
      //overallSize.height = this.options.itemsVisible * this.elementHeight + 50 * this.options.showControls;
      scrollSize.height = this.options.itemsVisible * this.elementHeight;
      thumbsSize.height = this.totalElements * (this.elementHeight + 10);
    }else{
      /* if navigation is enabled, add the width to the overall size */
      var navsSize = 0;
      if( this.options.showControls ){
        var s1={
          x:0,
          y:0
        };
        var s2 ={
          x:0,
          y:0
        };
        if(scriptJquery('#'+this.options.navs.fwd).css('display') !='none')
          s1 = scriptJquery('#'+this.options.navs.fwd).getSize();
        else if(scriptJquery('#'+this.options.navs.fwd+"_dis").css('display') !='none')
          s1 = scriptJquery('#'+this.options.navs.fwd+"_dis").getSize();
         
        if(scriptJquery('#'+this.options.navs.bk).css('display') !='none')
          s2 = scriptJquery('#'+this.options.navs.bk).getSize();
        else if(scriptJquery('#'+this.options.navs.bk+"_dis").css('display') !='none')
          s2 = scriptJquery('#'+this.options.navs.bk+"_dis").getSize(); 
         
        navsSize = s1.x+s2.x;
      }
      overallSize.width = this.options.itemsVisible * this.elementWidth + navsSize;
      scrollSize.width = this.options.itemsVisible * this.elementWidth;
      thumbsSize.width = this.totalElements * (this.elementWidth + 10);
    }
    scriptJquery('#'+this.options.overallContainer).css(overallSize);
    scriptJquery('#'+this.options.elementScrolled).css(scrollSize);
    scriptJquery('#'+this.options.thumbsContainer).css(thumbsSize);
  }

  /* adds forward/back buttons */
  this.addControls = function(){
    if( !this.options.showControls || this.elements.length <= this.options.itemsVisible ) return;
    
    this.fwd = scriptJquery('#'+this.options.overallContainer).find(this.options.navs.fwd);
    this.bkwd = scriptJquery('#'+this.options.overallContainer).find(this.options.navs.bk);
    
  }

  /* slides elements */
  this.slide = function( direction ){
    
    if(this.started) return;
    this.direction = direction ? direction : this.direction;
    var currentIndex = this.currentIndex();
    /* if multiple elements are to be skipped (elemsSlide > 1), calculate the ending element */
    if( this.options.elemsSlide && this.options.elemsSlide>1 && this.endingElem==null ){
      this.endingElem = this.currentElement;
      for(var i = 0; i < this.options.elemsSlide; i++ ){
        this.endingElem += direction;
        if( this.endingElem >= this.totalElements ) this.endingElem = 0;
        if( this.endingElem < 0 ) this.endingElem = this.totalElements-1;
      }
    }
    
    var s = new Hash();
    var fxDist = 0;
    if( this.options.slideVertical ){
      s.set('margin-top', -this.elementHeight);
      fxDist = this.direction == 1 ? -this.elementHeight : 0;
    }else{
      s.set('margin-left', -this.elementWidth);
      fxDist = this.direction == 1 ? -this.elementWidth : 0;
    }
    
    if( this.direction == -1 ){
      this.rearange();
      if( this.options.slideVertical ) scriptJquery('#'+this.options.thumbsContainer).css({
        'margin-top': -this.elementHeight
      });
      else scriptJquery('#'+this.options.thumbsContainer).css({
        'margin-left': -this.elementWidth
      });
    }
    this.started = true;
    
    // Fx.tween equivalent in jquery 
    this.myFx = this.myFx.animate({
      [this.options.slideVertical ? 'margin-top' : 'margin-left']: fxDist
    }, {
      duration: this.options.duration,
      queue: true,
      easing: this.options.transition
    });

    this.myFx.promise().done(function() {
      this.rearange(true);
      if (this.options.elemsSlide) {
        if (this.endingElem != this.currentElement) {
          if (this.options.autoSlide) this.stopAutoSlide();
          this.slide(this.direction);
        } else {
          if (this.options.autoSlide) this.startAutoSlide();
          this.endingElem = null;
        }
      }
    }.bind(this));
    
    // trigger the custom event
    if(('onChange' in this.options) && (this.options.onChange instanceof Function))
      this.options.onChange(currentIndex);
    
  }

  /* rearanges elements for continuous navigation */
  this.rearange= function( rerun ){
    
    if(rerun) this.started = false;
    if( rerun && this.direction == -1 ) return;
    
    this.currentElement = this.currentIndex( this.direction );
    
    
    if( this.options.slideVertical ) scriptJquery('#'+this.options.thumbsContainer).css({
      'margin-top': 0
    });
    else scriptJquery('#'+this.options.thumbsContainer).css({
      'margin-left': 0
    });
    
    if( this.currentElement == 1 && this.direction == 1 ){
      scriptJquery(this.elements[0]).injectSeaoCustom(this.elements[this.totalElements-1], 'after');
      return;
    }
    if( (this.currentElement == 0 && this.direction ==1) || (this.direction==-1 && this.currentElement == this.totalElements-1) ){
      this.rearrangeElement( this.elements.last(), this.direction == 1 ? this.elements[this.totalElements-2] : this.elements[0]);
      return;
    }
    
    if( this.direction == 1 ) this.rearrangeElement( this.elements[this.currentElement-1], this.elements[this.currentElement-2]);
    else this.rearrangeElement( this.elements[this.currentElement], this.elements[this.currentElement+1]);
  }

  /* rearanges a single element for continuous navigation */
  this.rearrangeElement= function( element , indicator ){
    element = scriptJquery(element);
    this.direction == 1 ? element.injectSeaoCustom(indicator, 'after') : element.injectSeaoCustom(indicator, 'before');
  }

  /* determines the current index in element siteevent */
  this.currentIndex= function(){
    var elemIndex = null;
    switch( this.direction ){
      /* forward */
      case 1:
        elemIndex = this.currentElement >= this.totalElements-1 ? 0 : this.currentElement + this.direction;
        break;
      /* backwards */
      case -1:
        elemIndex = this.currentElement == 0 ? this.totalElements - 1 : this.currentElement + this.direction;
        break;
    }
    return elemIndex;
  }

  /* starts auto sliding */
  this.startAutoSlide= function(){
    this.startIt = scriptJquery.proxy(this.slide, this, this.direction|1);
    this.autoSlide = setInterval(this.startIt, this.autoSlideTotal);
    this.isRunning = true;
    this.elements.on({
      'mouseenter':function(){
        clearInterval(this.autoSlide);
        this.isRunning = false;
      }.bind(this),
      'mouseleave':function(){
        this.autoSlide = setInterval(this.startIt, this.autoSlideTotal);
        this.isRunning = true;
      }.bind(this)
    })
  }

  /* stops auto sliding */
  this.stopAutoSlide = function(){
    clearInterval(this.autoSlide);
    this.isRunning = false;
  }

  this.sendajax = function (direction) {
    if (this.options.isActive == false ) {
      this.options.isActive = true; 
    
      var startindex = this.options.contentstartIndex;
      if (direction == 1) {
        
        
        if (startindex <= -1) {
          startindex = 0;
        }
        if (this.options.forwardLink == 1) {
            
          startindex = parseInt(startindex) + parseInt(this.options.itemsVisible);
          if (startindex > this.options.totalCount) { 
            startindex = parseInt(startindex) - parseInt(this.options.itemsVisible);
          }
        }
        else {
          this.options.forwardLink = 1;
        }
      }
      else {
        
        if (startindex <= -1 ) {
          startindex = 0;
          
        }
        else {
          if (this.options.forwardLink == 2) {
            startindex = parseInt(startindex) - parseInt(this.options.itemsVisible);
            if (startindex <= -1) {
              startindex = 0;
            }
        
          }
          else {
            this.options.forwardLink = 2;
          }
        
        }
      }

      if(this.options.itemsSelectorLoading){
        scriptJquery('#'+this.options.thumbsContainerOuter).addClass(this.options.itemsSelectorLoading);
      }
     
      this.options.contentstartIndex = startindex;
      var url = this.options.url; 
      var obj = this;
      var limit= parseInt(this.options.itemsVisible)*2;
      var request = scriptJquery.ajax({
        url : url,
        method: 'GET',
        data : scriptJquery.extend(this.options.params,{
          format : 'html',
          'task': 'ajax',
          'curnt_limit':this.options.itemsVisible,
          'startindex' : startindex,
          'total':this.options.totalCount,
          'direction' : direction
        }),
        success : function(responseHTML)
        {
          if(obj.options.itemsSelectorLoading){
            scriptJquery('#'+obj.options.thumbsContainerOuter).removeClass(obj.options.itemsSelectorLoading);
          }
          scriptJquery('#'+obj.options.thumbsContainer).html(responseHTML); 
          en4.core.runonce.trigger();
          Smoothbox.bind(scriptJquery('#'+obj.options.thumbsContainer));
          new SeaocoreSlideItMoo(obj.options).slide(direction);
          obj.options.isActive = false; 
          if(startindex<=0 && direction== -1){
            // hidding back button
            scriptJquery('#'+obj.options.navs.bk).css('display', 'none');
            scriptJquery('#'+obj.options.navs.bk+'_dis').css('display', 'block');
          }else{
            // vissible back button
            scriptJquery('#'+obj.options.navs.bk).css('display', 'block');
            scriptJquery('#'+obj.options.navs.bk+'_dis').css('display', 'none');
          }
         
          if(((startindex>(obj.options.totalCount-limit)|| (startindex>=(obj.options.totalCount-limit))) && direction== 1) || ((startindex>=(obj.options.totalCount-obj.options.itemsVisible)) && direction==  -1)){
            // hidding forward button
            scriptJquery('#'+obj.options.navs.fwd).css('display', 'none');
            scriptJquery('#'+obj.options.navs.fwd+'_dis').css('display', 'block');
          }else{
            // vissible forward button
            scriptJquery('#'+obj.options.navs.fwd).css('display', 'block');
            scriptJquery('#'+obj.options.navs.fwd+'_dis').css('display', 'none');
          }
        }
      });
    
    }
  }
  this.initialize(options);
};
