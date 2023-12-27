var SEAOMooHorizontalScrollBar = function(main, content, options){
 

 
  this.options = {
    maxThumbSize: 15,
         
    arrows: true,
    horizontalScroll: true, // horizontal scrollbars
    horizontalScrollBefore :true
  };
 
  this.initialize = function(main, content, options){
    // this.setOptions(options);
    this.options = scriptJquery.extend(this.options, options);
 
    this.main = scriptJquery("#" + main);
    this.content = scriptJquery("#" + content);
  
    if (this.options.arrows == true){
      this.arrowOffset = 30;
    } else {
      this.arrowOffset = 0;
    }
 
    if (this.options.horizontalScroll == true){
      this.horizontalScrollOffset = 15;
    } else {
      this.horizontalScrollOffset = 0;
    }
    if(this.options.horizontalScrollElement.length){
      this.horizontalScrollbar = scriptJquery.crtEle('div', {
        'class': 'horizontalScrollbar'
      }).injectSeaoCustom(scriptJquery("#" + this.options.horizontalScrollElement));
    }else{
      this.horizontalScrollbar = scriptJquery.crtEle('div', {
        'class': 'horizontalScrollbar'
      }).injectSeaoCustom(this.content, 'after');
    }
    if (this.options.arrows == true){
      this.arrowLeft = scriptJquery.crtEle('div', {
        'class': 'arrowLeft'
      }).injectSeaoCustom(this.horizontalScrollbar);
    }
 
    this.horizontalTrack = scriptJquery.crtEle('div', {
      'class': 'horizontalTrack'
    }).injectSeaoCustom(this.horizontalScrollbar);
 
    this.horizontalThumb = scriptJquery.crtEle('div', {
      'class': 'horizontalThumb'
    }).injectSeaoCustom(this.horizontalTrack);
 
    if (this.options.arrows == true){
      this.arrowRight = scriptJquery.crtEle('div', {
        'class': 'arrowRight'
      }).injectSeaoCustom(this.horizontalScrollbar);
    }
 
 
    if(this.options.horizontalScrollBefore==true){
      this.horizontalScrollbarBefore = scriptJquery.crtEle('div', {
        'class': 'horizontalScrollbar'
      }).injectSeaoCustom(scriptJquery('#' + this.options.horizontalScrollBeforeElement));
 
      if (this.options.arrows == true){
        this.arrowLeftBefore = scriptJquery.crtEle('div', {
          'class': 'arrowLeft'
        }).injectSeaoCustom(this.horizontalScrollbarBefore);
      }
 
      this.horizontalTrackBefore = scriptJquery.crtEle('div', {
        'class': 'horizontalTrack'
      }).injectSeaoCustom(this.horizontalScrollbarBefore);
 
      this.horizontalThumbBefore = scriptJquery.crtEle('div', {
        'class': 'horizontalThumb'
      }).injectSeaoCustom(this.horizontalTrackBefore);
 
      if (this.options.arrows == true){
        this.arrowRightBefore = scriptJquery.crtEle('div', {
          'class': 'arrowRight'
        }).injectSeaoCustom(this.horizontalScrollbarBefore);
      }
    }
           
 
    this.bound = {
              
      'horizontalStart': this.horizontalStart.bind(this),
      'end': this.end.bind(this),
              
      'horizontalDrag': this.horizontalDrag.bind(this),
            
               
      'horizontalPage': this.horizontalPage.bind(this)
    };
 
   
    this.horizontalPosition = {};
         
    this.horizontalMouse = {};
    this.update();
    this.attach();
  };
 
  this.update = function(){

        
 
    this.main.css('width', this.content.get(0).offsetWidth + 15);
    this.horizontalTrack.css('width', this.content.get(0).offsetWidth - this.arrowOffset);
    if (this.options.horizontalScrollBefore == true){
      this.horizontalTrackBefore.css('width', this.content.get(0).offsetWidth - this.arrowOffset);
    }
          

 
    // Remove and replace horizontal scrollbar
    if (this.content.get(0).scrollWidth <= this.main.get(0).offsetWidth) {
      this.horizontalScrollbar.css('display', 'none');
      if (this.options.horizontalScrollBefore == true){
        this.horizontalScrollbarBefore.css('display', 'none');
      }  
      if(!this.options.horizontalScrollElement){
      this.content.css('height', this.content.get(0).offsetHeight() + this.horizontalScrollOffset);
      }
    } else {
      this.horizontalScrollbar.css('display', 'block');
      if (this.options.horizontalScrollBefore == true){
        this.horizontalScrollbarBefore.css('display', 'block');
      }         
    }
 
              
    // Horizontal
 
    this.horizontalContentSize = this.content.get(0).offsetWidth;
    this.horizontalContentScrollSize = this.content.get(0).scrollWidth;
    this.horizontalTrackSize = this.horizontalTrack.get(0).offsetWidth;
 
    this.horizontalContentRatio = this.horizontalContentSize / this.horizontalContentScrollSize;
 
    this.horizontalThumbSize = Number(this.horizontalTrackSize * this.horizontalContentRatio).seaolimit(this.options.maxThumbSize, this.horizontalTrackSize);
 
    this.horizontalScrollRatio = this.horizontalContentScrollSize / this.horizontalTrackSize;
 
    this.horizontalThumb.css('width', this.horizontalThumbSize);
    if (this.options.horizontalScrollBefore == true){        
      this.horizontalThumbBefore.css('width', this.horizontalThumbSize);
    }         
    this.horizontalUpdateThumbFromContentScroll();
    this.horizontalUpdateContentFromThumbPosition();     
  };
 
      
  this.horizontalUpdateContentFromThumbPosition = function(){
    this.content.get(0).scrollLeft = this.horizontalPosition.now * this.horizontalScrollRatio + 1;
  };
 
    
 
  this.horizontalUpdateThumbFromContentScroll = function(){
    this.horizontalPosition.now = (this.content.get(0).scrollLeft / this.horizontalScrollRatio).seaolimit(0, (this.horizontalTrackSize - this.horizontalThumbSize));
    this.horizontalThumb.css('left', this.horizontalPosition.now);
    if (this.options.horizontalScrollBefore == true){   
      this.horizontalThumbBefore.css('left', this.horizontalPosition.now);
    }
  };
 
  this.attach = function(){
          
    this.horizontalThumb.on('mousedown', this.bound.horizontalStart);
    this.horizontalTrack.on('mouseup', this.bound.horizontalPage);
 
    if (this.options.arrows == true){
              
              
      this.arrowLeft.on('mousedown', function(event){
        this.interval = setInterval(function(event){
          this.content.get(0).scrollLeft = this.content.get(0).scrollLeft  - this.options.wheel;
          this.horizontalUpdateThumbFromContentScroll();
        }.bind(this), 40);
      }.bind(this));
 
      this.arrowLeft.on('mouseup', function(event){
        clearInterval(this.interval);
      }.bind(this));
 
      this.arrowLeft.on('mouseout', function(event){
        clearInterval(this.interval);
      }.bind(this));
 
      this.arrowRight.on('mousedown', function(event){
        this.interval = setInterval(function(event){
          this.content.get(0).scrollLeft = this.content.get(0).scrollLeft + this.options.wheel;
          this.horizontalUpdateThumbFromContentScroll();
        }.bind(this), 40);
      }.bind(this));
 
      this.arrowRight.on('mouseup', function(event){
        clearInterval(this.interval);
      }.bind(this));
 
      this.arrowRight.on('mouseout', function(event){
        clearInterval(this.interval);
      }.bind(this));
    }
    if (this.options.horizontalScrollBefore == true){ 
      this.horizontalThumbBefore.on('mousedown', this.bound.horizontalStart);
      this.horizontalTrackBefore.on('mouseup', this.bound.horizontalPage);
 
      if (this.options.arrows == true){
              
              
        this.arrowLeftBefore.on('mousedown', function(event){
          this.interval = setInterval(function(event){
            this.content.get(0).scrollLeft  = this.content.get(0).scrollLeft - this.options.wheel;
            this.horizontalUpdateThumbFromContentScroll();
          }.bind(this), 40);
        }.bind(this));
 
        this.arrowLeftBefore.on('mouseup', function(event){
          clearInterval(this.interval);
        }.bind(this));
 
        this.arrowLeftBefore.on('mouseout', function(event){
          clearInterval(this.interval);
        }.bind(this));
 
        this.arrowRightBefore.on('mousedown', function(event){
          this.interval = setInterval(function(event){
            this.content.get(0).scrollLeft = this.content.get(0).scrollLeft + this.options.wheel;
            this.horizontalUpdateThumbFromContentScroll();
          }.bind(this), 40);
        }.bind(this));
 
        this.arrowRightBefore.on('mouseup', function(event){
          clearInterval(this.interval);
        }.bind(this));
 
        this.arrowRightBefore.on('mouseout', function(event){
          clearInterval(this.interval);
        }.bind(this));
      } 
    }
  };
 
      
      
       
 
  this.horizontalPage = function(event){
    if (event.clientX > this.horizontalThumb.offset().left) this.content.get(0).scrollLeft = this.content.get(0).scrollLeft + this.content.get(0).offsetWidth;
    else this.content.scrollLeft(this.content.get(0).scrollLeft - this.content.get(0).offsetWidth);
    this.horizontalUpdateThumbFromContentScroll();
    event.stopPropagation();
  };
 
     
 
  this.horizontalStart = function(event){
    this.horizontalMouse.start = event.page ? event.page.x : event.clientX;
    this.horizontalPosition.start = Number(this.horizontalThumb.get(0).offsetLeft);
    document.addEventListener('mousemove', this.bound.horizontalDrag);
    document.addEventListener('mouseup', this.bound.end);
    this.horizontalThumb.on('mouseup', this.bound.end);
    if (this.options.horizontalScrollBefore == true){ 
      this.horizontalThumbBefore.on('mouseup', this.bound.end);
    }
    event.stopPropagation();
  };
 
  this.end = function(event){
    scriptJquery(document).off('mousemove');
    scriptJquery(document).off('mouseup');
          
    this.horizontalThumb.off('mouseup');
    if (this.options.horizontalScrollBefore == true){ 
      this.horizontalThumbBefore.off('mouseup');
    }
    event.stopPropagation();
  };
 
       
  this.horizontalDrag = function(event){
    this.horizontalMouse.now = event.page ? event.page.x : event.clientX;
    this.horizontalPosition.now = Number(this.horizontalPosition.start + (this.horizontalMouse.now - this.horizontalMouse.start)).seaolimit(0, (this.horizontalTrackSize - this.horizontalThumbSize));
    this.horizontalUpdateContentFromThumbPosition();
    this.horizontalUpdateThumbFromContentScroll();
    event.stopPropagation();
  };


  this.initialize(main, content, options);
 
};
