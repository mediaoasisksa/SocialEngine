
var SEAOMooVerticalScroll = function( main, content, options){

  this.options = {
    maxThumbSize: 15,
    wheel: 40  
  };

  this.initialize = function(main, content, options){
    this.options = scriptJquery.extend( this.options, options );
    this.main = scriptJquery( '#' + main);
    this.content = scriptJquery( '#' + content);		
    // CUSTOM WORK TO SHOW THE SCROLL BAR ON RIGHT
    this.main.css('display', 'flex');
    // CUSTOM WORK ENDS HERE
    this.scrollbar = scriptJquery.crtEle('div', { 'class': 'verticalScroll' })
                                 .injectSeaoCustom( this.content, 'after' );
    this.track = scriptJquery.crtEle('div', { 'class': 'verticalTrack' })
                                 .injectSeaoCustom(this.scrollbar);
    this.thumb = scriptJquery.crtEle('div', { 'class': 'verticalThumb' })
                                 .injectSeaoCustom(this.track);
    this.thumbTop = scriptJquery.crtEle('div', { /* +kh */ 'class': 'verticalThumbTop' })
                                 .injectSeaoCustom(this.thumb); /* kh (this.track); */

    this.thumbBot = scriptJquery.crtEle('div', { /* +kh */ 'class': 'verticalThumbBottom' })
                                 .injectSeaoCustom(this.thumb); /* kh (this.track); */
					
    this.bound = {
      'update': this.update.bind(this),
      'start': this.start.bind(this),			
      'end': this.end.bind(this),
      'drag': this.drag.bind(this),			
      'wheel': this.wheel.bind(this),
      'page': this.page.bind(this)			
    };
    this.position = {};	
    this.mouse = {};		
    this.update();
    this.attach();
  };

  this.update = function(){
			
    this.main.css('height', this.content.get(0).offsetHeight);
    this.track.css('height', this.content.get(0).offsetHeight);		
		      
    // Remove and replace vertical scrollbar			
    if (this.content.get(0).scrollHeight <= this.main.get(0).offsetHeight) {
      this.scrollbar.css('display', 'none');								
    } else {
      this.scrollbar.css('display', 'block');			
    }
			
    // Vertical
			
    this.contentHeight = this.content.get(0).offsetHeight;
    this.contentScrollHeight = this.content.get(0).scrollHeight;
    this.trackHeight = this.track.get(0).offsetHeight;
    if(this.contentScrollHeight <=0) {
      this.contentScrollHeight=1;
    }
    this.ContentHeightRatio = this.contentHeight / this.contentScrollHeight;
    this.thumbSize = Number(this.trackHeight * this.ContentHeightRatio).seaolimit(this.options.maxThumbSize, this.trackHeight);
    this.scrollHeightRatio = this.contentScrollHeight / this.trackHeight;
    this.thumb.css('height', this.thumbSize);
    this.updateThumbFromContentScroll();
    this.updateContentFromThumbPosition();

  };

  this.updateContentFromThumbPosition = function(){
    this.content.get(0).scrollTop = this.position.now * this.scrollHeightRatio;
  };
	

  this.updateThumbFromContentScroll = function(){
    this.position.now = Number(this.content.get(0).scrollTop / this.scrollHeightRatio).seaolimit(0, (this.trackHeight - this.thumbSize));
    this.thumb.css('top', this.position.now);
    if ( this.seaoMooVerticalScrool) {
      this.seaoMooVerticalScrool(this);
      //this.fireEvent('seaoMooVerticalScrool',this);
    }
  };		
          
  this.attach = function(){
    this.content.on('mouseenter', this.bound.update );
    this.content.on('mouseleave', this.bound.update );
    this.thumb.on('mousedown', this.bound.start);
    if (this.options.wheel) {
      this.content.on('mousewheel', this.bound.wheel);
    }
    this.track.on('mouseup', this.bound.page);		
					
  };
		
  this.wheel = function(event){
    this.content.get(0).scrollTop -= event.wheel * this.options.wheel;
    this.updateThumbFromContentScroll();
    event.stopPropagation();
  };

  this.page = function(event){
    if (event.clientY > this.thumb.offset().top) this.content.get(0).scrollTop += this.content.get(0).offsetHeight;
    else this.content.get(0).scrollTop -= this.content.get(0).offsetHeight;
    this.updateThumbFromContentScroll();
    event.stopPropagation();
  };

  this.scrollTop = function(){
    this.content.get(0).scrollTop = 0;
    this.updateThumbFromContentScroll();
  };		

  this.start = function(event){
    this.mouse.start = event.page ? event.page.y : event.clientY;
    this.position.start = Number(this.thumb.get(0).offsetTop);
    document.addEventListener('mousemove', this.bound.drag);
    document.addEventListener('mouseup', this.bound.end);
    this.thumb.on('mouseup', this.bound.end);
    event.stopPropagation();
  };	
	
  this.end = function(event){
    document.removeEventListener("mousemove", this.bound.drag);
    document.removeEventListener("mouseup", this.bound.end);

    // scriptJquery(document).off('mousemove');					
    // scriptJquery(document).off('mouseup');
    this.thumb.off('mouseup');		
    event.stopPropagation();
  };

  this.drag = function(event){
    this.mouse.now = event.page ? event.page.y : event.clientY;
    this.position.now = Number(this.position.start + (this.mouse.now - this.mouse.start)).seaolimit(0, (this.trackHeight - this.thumbSize));
    this.updateContentFromThumbPosition();
    this.updateThumbFromContentScroll();
    event.stopPropagation();
  };

  this.initialize( main, content, options )

};
