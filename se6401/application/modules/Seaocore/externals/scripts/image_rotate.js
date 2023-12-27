  var durationOfRotateImage = 5000;
  function image_rotate() {
    var slideshowDivObj = scriptJquery('#slide-images');
    var imagesObj = slideshowDivObj.find('img');
    var indexOfRotation = 0;

    imagesObj.each(function(img, i){
      if(i > 0) {
        scriptJquery(this).attr('opacity',0);
      }
    });    

    var show = function() {
      imagesObj[indexOfRotation].fadeOut();
      indexOfRotation = indexOfRotation < imagesObj.length - 1 ? indexOfRotation+1 : 0;
      imagesObj[indexOfRotation].fadeIn();
    };
    
    window.addEventListener('load',function(){
      show.periodical(durationOfRotateImage);
    });
  }