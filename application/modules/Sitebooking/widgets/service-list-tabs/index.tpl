<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>

<?php $widgetIdentity = $this->identity; ?>

<?php if(!$this->isAjax): ?>
<?php if($this->flag == 0): ?>
<div class = "tabs_alt">
  
  <ul> 

    <li style = "cursor: pointer;" id = "li_1_<?php echo $widgetIdentity ?>" class = " service_list active dnone" data-target = "featured" >    
      <a>Featured</a>
    </li>
    
    <li style = "cursor: pointer;" id = "li_2_<?php echo $widgetIdentity ?>" class = " service_list dnone" data-target = "sponsored">    
      <a>Sponsored</a>
    </li>
    
    <li style = "cursor: pointer;" id = "li_3_<?php echo $widgetIdentity ?>" class = " service_list dnone" data-target = "hot">
      <a>Trending</a>
    </li>
    
    <li style = "cursor: pointer;" id = "li_4_<?php echo $widgetIdentity ?>" class = " service_list dnone" data-target = "newlabel">    
      <a>New</a>
    </li>
    
    <li style = "cursor: pointer;" id = "li_6_<?php echo $widgetIdentity ?>" class = " service_list dnone" data-target = "like_count" >    
      <a>Most Liked</a>
    </li>
    
    <li style = "cursor: pointer;" id = "li_7_<?php echo $widgetIdentity ?>" class = " service_list dnone" data-target = "comment_count" >        
      <a>Most Commented</a>
    </li>
    
    <li style = "cursor: pointer;" id = "li_8_<?php echo $widgetIdentity ?>" class = " service_list dnone" data-target = "review_count" >
      <a>Most Reviewed</a>
    </li>
    
    <li style = "cursor: pointer;" id = "li_9_<?php echo $widgetIdentity ?>" class = " service_list dnone" data-target = "rating" >
      <a>Most Rated</a>
    </li>

    <li style = "cursor: pointer;" id = "li_10_<?php echo $widgetIdentity ?>" class = " service_list dnone" data-target = "creation_date" >
      <a>Recent</a>
    </li>
    
    <li class="fright">
      <span class="seaocore_tab_select_wrapper">
        <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("List View"); ?></div>
      <span id = "listicon-<?php echo $widgetIdentity ?>" class="seaocore_tab_icon tab_icon_list_view dnone" onclick="switchview_<?php echo $widgetIdentity ?>(0)"></span>
      </span>
    </li>

    <li class="fright">
      <span class="seaocore_tab_select_wrapper">
        <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Grid View"); ?></div>
      <span id = "gridicon-<?php echo $widgetIdentity ?>" class="seaocore_tab_icon tab_icon_grid_view dnone" onclick="switchview_<?php echo $widgetIdentity ?>(1)"></span>
      </span>
    </li>
  
  </ul>

</div>
<?php endif; ?>

<input type="hidden" id="hidden_filter_type_<?php echo $widgetIdentity ?>" name="hidden_filter_type_<?php echo $widgetIdentity ?>" value="">

<!-- this just below script will show only selected tabs and also make the first tab as 'active' and it will run only at start on load time-->
<?php if($this->flag == 0): ?>
  <script type="text/javascript">

    var listParams = <?php echo $this->listParams;?>;

    $$('.service_list').each(function(el){
      el.removeClass('active');
    });

    listParams.forEach(function(item){
      if(item == "featured") {
        if(item == listParams[0] && item == "featured") { 
          document.getElementById('hidden_filter_type_<?php echo $widgetIdentity ?>').value = document.getElementById("li_1_<?php echo $widgetIdentity ?>").getAttribute("data-target");
          document.getElementById("li_1_<?php echo $widgetIdentity ?>").addClass('active'); 
        }
        document.getElementById("li_1_<?php echo $widgetIdentity ?>").removeClass('dnone');
      }
      else if(item == "sponsored") {
        if(item == listParams[0] && item == "sponsored") { 
          document.getElementById('hidden_filter_type_<?php echo $widgetIdentity ?>').value = document.getElementById("li_2_<?php echo $widgetIdentity ?>").getAttribute("data-target");
          document.getElementById("li_2_<?php echo $widgetIdentity ?>").addClass('active'); 
        }
        document.getElementById("li_2_<?php echo $widgetIdentity ?>").removeClass('dnone');
      }
      else if(item == "hot") {
        if(item == listParams[0] && item == "hot") {
          document.getElementById('hidden_filter_type_<?php echo $widgetIdentity ?>').value = document.getElementById("li_3_<?php echo $widgetIdentity ?>").getAttribute("data-target"); 
          document.getElementById("li_3_<?php echo $widgetIdentity ?>").addClass('active'); 
        }
        document.getElementById("li_3_<?php echo $widgetIdentity ?>").removeClass('dnone');
      }
      else if(item == "newlabel") {
        if(item == listParams[0] && item == "newlabel") {
          document.getElementById('hidden_filter_type_<?php echo $widgetIdentity ?>').value = document.getElementById("li_4_<?php echo $widgetIdentity ?>").getAttribute("data-target"); 
          document.getElementById("li_4_<?php echo $widgetIdentity ?>").addClass('active'); 
        }
        document.getElementById("li_4_<?php echo $widgetIdentity ?>").removeClass('dnone');
      }

      else if(item == "like_count") {
        if(item == listParams[0] && item == "like_count") {
          document.getElementById('hidden_filter_type_<?php echo $widgetIdentity ?>').value = document.getElementById("li_6_<?php echo $widgetIdentity ?>").getAttribute("data-target"); 
          document.getElementById("li_6_<?php echo $widgetIdentity ?>").addClass('active'); 
        }
        document.getElementById("li_6_<?php echo $widgetIdentity ?>").removeClass('dnone');
      }
      else if(item == "comment_count") {
        if(item == listParams[0] && item == "comment_count") {
          document.getElementById('hidden_filter_type_<?php echo $widgetIdentity ?>').value = document.getElementById("li_7_<?php echo $widgetIdentity ?>").getAttribute("data-target"); 
          document.getElementById("li_7_<?php echo $widgetIdentity ?>").addClass('active'); 
        }
        document.getElementById("li_7_<?php echo $widgetIdentity ?>").removeClass('dnone');
      }
      else if(item == "review_count") {
        if(item == listParams[0] && item == "review_count") { 
          document.getElementById('hidden_filter_type_<?php echo $widgetIdentity ?>').value = document.getElementById("li_8_<?php echo $widgetIdentity ?>").getAttribute("data-target");
          document.getElementById("li_8_<?php echo $widgetIdentity ?>").addClass('active'); 
        }
        document.getElementById("li_8_<?php echo $widgetIdentity ?>").removeClass('dnone');
      }
      else if(item == "rating") {
        if(item == listParams[0] && item == "rating") {
          document.getElementById('hidden_filter_type_<?php echo $widgetIdentity ?>').value = document.getElementById("li_9_<?php echo $widgetIdentity ?>").getAttribute("data-target"); 
          document.getElementById("li_9_<?php echo $widgetIdentity ?>").addClass('active'); 
        }
        document.getElementById("li_9_<?php echo $widgetIdentity ?>").removeClass('dnone');
      }
      else if(item == "creation_date") {
        if(item == listParams[0] && item == "creation_date") {
          document.getElementById('hidden_filter_type_<?php echo $widgetIdentity ?>').value = document.getElementById("li_10_<?php echo $widgetIdentity ?>").getAttribute("data-target"); 
          document.getElementById("li_10_<?php echo $widgetIdentity ?>").addClass('active'); 
        }
        document.getElementById("li_10_<?php echo $widgetIdentity ?>").removeClass('dnone');
      }
    });

  
    // VIEW [LIST, GRID VIEW]
    // Icons will show according to this widget setting
    var viewParams = <?php echo $this->viewParams; ?>;
    var viewCount = viewParams.length;
    viewParams.forEach(function(item){

      if(item+"icon-<?php echo $widgetIdentity ?>" == "listicon-<?php echo $widgetIdentity ?>") {
        en4.core.runonce.add(function() {
          switchview_<?php echo $widgetIdentity ?>(0);
        });
        if(viewCount > 1)
          document.getElementById("listicon-<?php echo $widgetIdentity ?>").removeClass('dnone');
      }
      else if(item+"icon-<?php echo $widgetIdentity ?>" == "gridicon-<?php echo $widgetIdentity ?>") {
        en4.core.runonce.add(function() {
          switchview_<?php echo $widgetIdentity ?>(1);
        })
        if(viewCount > 1)
          document.getElementById("gridicon-<?php echo $widgetIdentity ?>").removeClass('dnone');
      } else {
        en4.core.runonce.add(function() {
          switchview_<?php echo $widgetIdentity ?>(1);
        })
      }
    });

  </script>
<?php endif; ?>

<?php if($this->flag == 1): ?>
  <h3>Browse Services</h3>

  <script type="text/javascript">
    // VIEW [LIST, GRID VIEW]
    var viewParams = <?php echo $this->viewParams; ?>;

    if(viewParams != "") {
      viewParams.forEach(function(item){

        if(item+"icon-<?php echo $widgetIdentity ?>" == "listicon-<?php echo $widgetIdentity ?>") {
          en4.core.runonce.add(function() {
            switchview_<?php echo $widgetIdentity ?>(0);
          });
        }
        else if(item+"icon-<?php echo $widgetIdentity ?>" == "gridicon-<?php echo $widgetIdentity ?>") {
          en4.core.runonce.add(function() {
            switchview_<?php echo $widgetIdentity ?>(1);
          })
        } else {
          en4.core.runonce.add(function() {
            switchview_<?php echo $widgetIdentity ?>(1);
          })
        }
      });
    } else {
      en4.core.runonce.add(function() {
        switchview_<?php echo $widgetIdentity ?>(1);
      }) 
    }
  </script>
<?php endif; ?>    
 
<img src="application/modules/Sitebooking/externals/images/listloader.gif" height=30 width=30 style="display: none; margin: 5px auto;" id="listloader-<?php echo $widgetIdentity ?>">

<script type="text/javascript">                              

  $$('.service_list').addEvent('click', function(){

    $$('.service_list').each(function(el){
      el.removeClass('active');
    });

    var filter_type = this.getAttribute("data-target");

    $(this).addClass('active');

    if(filter_type != '')
      document.getElementById('hidden_filter_type_<?php echo $widgetIdentity ?>').value = filter_type;  

    en4.core.request.send(new Request.HTML({
      url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
      data : {
        format : 'html',
        filter_type : filter_type,
        isAjax : 1
      },

      onRequest: function () {
         $('listloader-<?php echo $widgetIdentity ?>').show();
      },    

      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {

        $('listloader-<?php echo $widgetIdentity ?>').hide();

        $('list-<?php echo $widgetIdentity ?>').innerHTML = " ";

        $('grid-<?php echo $widgetIdentity ?>').innerHTML = " ";


        // LIST VIEW

        var element = new Element('div', {
          'html': responseHTML
        });

        Elements.from(element.getElement('.sitebooking_list').innerHTML).inject($('list-<?php echo $widgetIdentity ?>'));

        if($('list-<?php echo $widgetIdentity ?>').innerHTML.trim() == '') {

          $('list-<?php echo $widgetIdentity ?>').innerHTML = $('list-<?php echo $widgetIdentity ?>').innerHTML + en4.core.language.translate("No Services Found Related to this");
        }


        // GRID VIEW

        var element = new Element('div', {
          'html': responseHTML
        });

        Elements.from(element.getElement('.sitebooking_grid').innerHTML).inject($('grid-<?php echo $widgetIdentity ?>'));

        if($('grid-<?php echo $widgetIdentity ?>').innerHTML.trim() == '') {

          $('grid-<?php echo $widgetIdentity ?>').innerHTML = $('grid-<?php echo $widgetIdentity ?>').innerHTML + en4.core.language.translate("No Services Found Related to this grid view");
        }

        Smoothbox.bind($('list-<?php echo $widgetIdentity ?>'));
        Smoothbox.bind($('grid-<?php echo $widgetIdentity ?>'));
        en4.core.runonce.trigger();

      }     
    }))

  });      

</script>


<script type="text/javascript" >
  function switchview_<?php echo $widgetIdentity ?>(flage){
    if(flage==1){

      $('grid-<?php echo $widgetIdentity ?>').removeClass('dnone');
      $('list-<?php echo $widgetIdentity ?>').addClass('dnone');
      $('listicon-<?php echo $widgetIdentity ?>').removeClass('selected');
      $('gridicon-<?php echo $widgetIdentity ?>').addClass('selected');
    }

    if(flage==0){

      $('list-<?php echo $widgetIdentity ?>').removeClass('dnone');
      $('grid-<?php echo $widgetIdentity ?>').addClass('dnone');
      $('gridicon-<?php echo $widgetIdentity ?>').removeClass('selected');
      $('listicon-<?php echo $widgetIdentity ?>').addClass('selected');
    }

  }

</script>


<!-- USER REVIEW -->
<?php endif; ?>
<?php
  echo $this->partial('service_grid_view.tpl', 'sitebooking', array(
     'paginator' => $this->paginator,
     'widgetIdentity' => $this->identity,
  ));
?>
<?php
  echo $this->partial('service_list_view.tpl', 'sitebooking', array(
     'paginator' => $this->paginator,
     'widgetIdentity' => $this->identity,
  ));
?>
        
 <img src="application/modules/Sitebooking/externals/images/loader.gif" height=30 width=30 style="display: none;" id="loader-<?php echo $widgetIdentity ?>">
<?php if(!$this->isAjax): ?>
  <div class="sitebooking_more" id="view_more-<?php echo $widgetIdentity ?>" onclick = " view_<?php echo $widgetIdentity ?>() ">View more</div>
<?php endif; ?>



<!-- VIEW MORE -->
<script type="text/javascript">

  function view_<?php echo $widgetIdentity ?>(){

    var filter_type; 

    if(filter_type != '')
      filter_type = document.getElementById('hidden_filter_type_<?php echo $widgetIdentity ?>').value;


    en4.core.request.send(new Request.HTML({
      url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
      data : {
      format : 'html',
      filter_type: filter_type,
      page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>,
      isAjax : 1
      },

      onRequest: function () {
        $('loader-<?php echo $widgetIdentity ?>').show();
      },  

      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
   
        $('loader-<?php echo $widgetIdentity ?>').hide();
  
        var element = new Element('div', {
          'html': responseHTML
        });
        Elements.from(element.getElement('.sitebooking_list').innerHTML).inject($('list-<?php echo $widgetIdentity ?>'));

        if($('list-<?php echo $widgetIdentity ?>').innerHTML.trim() == '') {

           $('list-<?php echo $widgetIdentity ?>').innerHTML = $('list-<?php echo $widgetIdentity ?>').innerHTML + "<p>No Services Found Related to this</p>";
        }

        var element = new Element('div', {
          'html': responseHTML
        });
        Elements.from(element.getElement('.sitebooking_grid').innerHTML).inject($('grid-<?php echo $widgetIdentity ?>'));


        if($('grid-<?php echo $widgetIdentity ?>').innerHTML.trim() == '') {

           $('grid-<?php echo $widgetIdentity ?>').innerHTML = $('grid-<?php echo $widgetIdentity ?>').innerHTML + "<p>No Services Found Related to this grid view</p>";
        }

        Smoothbox.bind($('list-<?php echo $widgetIdentity ?>'));
        Smoothbox.bind($('grid-<?php echo $widgetIdentity ?>'));
        en4.core.runonce.trigger();
      }     
    }));
  };

  var cpage_<?php echo $widgetIdentity ?> = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
  
  var pages_<?php echo $widgetIdentity ?> = <?php echo $this->paginator->count() ?>;

  if(cpage_<?php echo $widgetIdentity ?> >= pages_<?php echo $widgetIdentity ?>) {
    document.getElementById("view_more-<?php echo $widgetIdentity ?>").style.display = "none";
  } else {
    document.getElementById("view_more-<?php echo $widgetIdentity ?>").style.display = "block";
  } 

</script>