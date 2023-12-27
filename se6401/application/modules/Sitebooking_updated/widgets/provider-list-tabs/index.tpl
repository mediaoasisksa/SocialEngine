<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>

<?php $widgetIdentity = $this->identity; ?>

<?php if(!$this->isAjax): ?>
<?php if($this->flag != 1): ?>
<div id="hideResponse"> </div>
<div class = "tabs_alt">
  
  <ul> 

    <li style = "cursor: pointer;" id = "li_1_<?php echo $widgetIdentity ?>" class = " provider_list dnone" data-target = "featured" >    
      <a>Featured</a>
    </li>
    
    <li style = "cursor: pointer;" id = "li_2_<?php echo $widgetIdentity ?>" class = " provider_list dnone" data-target = "sponsored">    
      <a>Sponsored</a>
    </li>
    
    <li style = "cursor: pointer;" id = "li_3_<?php echo $widgetIdentity ?>" class = " provider_list dnone" data-target = "hot">
      <a>Trending</a>
    </li>
    
    <li style = "cursor: pointer;" id = "li_4_<?php echo $widgetIdentity ?>" class = " provider_list dnone" data-target = "newlabel">    
      <a>New</a>
    </li>
    
    <li style = "cursor: pointer;" id = "li_5_<?php echo $widgetIdentity ?>" class = " provider_list dnone" data-target = "verified" >    
      <a>Verified</a>
    </li>
    
    <li style = "cursor: pointer;" id = "li_6_<?php echo $widgetIdentity ?>" class = " provider_list dnone" data-target = "like_count" >    
      <a>Most Liked</a>
    </li>
    
    <li style = "cursor: pointer;" id = "li_7_<?php echo $widgetIdentity ?>" class = " provider_list dnone" data-target = "comment_count" >        
      <a>Most Commented</a>
    </li>
    
    <li style = "cursor: pointer;" id = "li_8_<?php echo $widgetIdentity ?>" class = " provider_list dnone" data-target = "review_count" >
      <a>Most Reviewed</a>
    </li>
    
    <li style = "cursor: pointer;" id = "li_9_<?php echo $widgetIdentity ?>" class = " provider_list dnone" data-target = "rating" >
      <a>Most Rated</a>
    </li>

    <li style = "cursor: pointer;" id = "li_10_<?php echo $widgetIdentity ?>" class = " provider_list dnone" data-target = "creation_date" >
      <a>Recent</a>
    </li>
    
    <li class="fright">
      <span class="seaocore_tab_select_wrapper">
        <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("List View"); ?></div>
      <spanid id = "listicon-<?php echo $widgetIdentity ?>" class="seaocore_tab_icon tab_icon_list_view dnone" onclick="switchview_<?php echo $widgetIdentity ?>(0)"></span>
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

<?php if($this->flag != 1): ?>
<script type="text/javascript">

  var listParams = <?php echo $this->listParams;?>;

  scriptJquery('.provider_list').each(function(el){

    scriptJquery(this).removeClass('active');
  });

  listParams.forEach(function(item){
    if(item == "featured") {
      if(item == listParams[0] && item == "featured") { 
        scriptJquery('#hidden_filter_type_<?php echo $widgetIdentity ?>').value = scriptJquery("#li_1_<?php echo $widgetIdentity ?>").attr("data-target");
        scriptJquery("#li_1_<?php echo $widgetIdentity ?>").addClass('active'); 
      }
      scriptJquery("#li_1_<?php echo $widgetIdentity ?>").removeClass('dnone');
    }
    else if(item == "sponsored") {
      if(item == listParams[0] && item == "sponsored") { 
        scriptJquery('#hidden_filter_type_<?php echo $widgetIdentity ?>').value = scriptJquery("#li_2_<?php echo $widgetIdentity ?>").attr("data-target");
        scriptJquery("#li_2_<?php echo $widgetIdentity ?>").addClass('active'); 
      }
      scriptJquery("#li_2_<?php echo $widgetIdentity ?>").removeClass('dnone');
    }
    else if(item == "hot") {
      if(item == listParams[0] && item == "hot") {
        scriptJquery('#hidden_filter_type_<?php echo $widgetIdentity ?>').value = scriptJquery("#li_3_<?php echo $widgetIdentity ?>").attr("data-target"); 
        scriptJquery("#li_3_<?php echo $widgetIdentity ?>").addClass('active'); 
      }
      scriptJquery("#li_3_<?php echo $widgetIdentity ?>").removeClass('dnone');
    }
    else if(item == "newlabel") {
      if(item == listParams[0] && item == "newlabel") {
        scriptJquery('#hidden_filter_type_<?php echo $widgetIdentity ?>').value = scriptJquery("#li_4_<?php echo $widgetIdentity ?>").attr("data-target"); 
        scriptJquery("#li_4_<?php echo $widgetIdentity ?>").addClass('active'); 
      }
      scriptJquery("#li_4_<?php echo $widgetIdentity ?>").removeClass('dnone');
    }
    else if(item == "verified") {
      if(item == listParams[0] && item == "verified") {
        scriptJquery('#hidden_filter_type_<?php echo $widgetIdentity ?>').value = scriptJquery("#li_5_<?php echo $widgetIdentity ?>").attr("data-target"); 
        scriptJquery("#li_5_<?php echo $widgetIdentity ?>").addClass('active'); 
      }
      scriptJquery("#li_5_<?php echo $widgetIdentity ?>").removeClass('dnone');
    }
    else if(item == "like_count") {
      if(item == listParams[0] && item == "like_count") {
        scriptJquery('#hidden_filter_type_<?php echo $widgetIdentity ?>').value = scriptJquery("#li_6_<?php echo $widgetIdentity ?>").attr("data-target"); 
        scriptJquery("#li_6_<?php echo $widgetIdentity ?>").addClass('active'); 
      }
      scriptJquery("#li_6_<?php echo $widgetIdentity ?>").removeClass('dnone');
    }
    else if(item == "comment_count") {
      if(item == listParams[0] && item == "comment_count") {
        scriptJquery('#hidden_filter_type_<?php echo $widgetIdentity ?>').value = scriptJquery("#li_7_<?php echo $widgetIdentity ?>").attr("data-target"); 
        scriptJquery("#li_7_<?php echo $widgetIdentity ?>").addClass('active'); 
      }
      scriptJquery("#li_7_<?php echo $widgetIdentity ?>").removeClass('dnone');
    }
    else if(item == "review_count") {
      if(item == listParams[0] && item == "review_count") { 
        scriptJquery('#hidden_filter_type_<?php echo $widgetIdentity ?>').value = scriptJquery("#li_8_<?php echo $widgetIdentity ?>").attr("data-target");
        scriptJquery("#li_8_<?php echo $widgetIdentity ?>").addClass('active'); 
      }
      scriptJquery("#li_8_<?php echo $widgetIdentity ?>").removeClass('dnone');
    }
    else if(item == "rating") {
      if(item == listParams[0] && item == "rating") {
        scriptJquery('#hidden_filter_type_<?php echo $widgetIdentity ?>').value = scriptJquery("#li_9_<?php echo $widgetIdentity ?>").attr("data-target"); 
        scriptJquery("#li_9_<?php echo $widgetIdentity ?>").addClass('active'); 
      }
      scriptJquery("#li_9_<?php echo $widgetIdentity ?>").removeClass('dnone');
    }
    else if(item == "creation_date") {
      if(item == listParams[0] && item == "creation_date") {
        scriptJquery('#hidden_filter_type_<?php echo $widgetIdentity ?>').value = scriptJquery("#li_10_<?php echo $widgetIdentity ?>").attr("data-target"); 
        scriptJquery("#li_10_<?php echo $widgetIdentity ?>").addClass('active'); 
      }
      scriptJquery("#li_10_<?php echo $widgetIdentity ?>").removeClass('dnone');
    }
  });
  
  // VIEW [LIST, GRID VIEW]
  var viewParams = <?php echo $this->viewParams; ?>;
  var viewCount = viewParams.length;
  viewParams.forEach(function(item){

    if(item+"icon-<?php echo $widgetIdentity ?>" == "listicon-<?php echo $widgetIdentity ?>") {
      en4.core.runonce.add(function() {
        switchview_<?php echo $widgetIdentity ?>(0);
      });
      if(viewCount > 1)
        scriptJquery("#listicon-<?php echo $widgetIdentity ?>").removeClass('dnone');
    }
    else if(item+"icon-<?php echo $widgetIdentity ?>" == "gridicon-<?php echo $widgetIdentity ?>") {
      en4.core.runonce.add(function() {
        switchview_<?php echo $widgetIdentity ?>(1);
      })
      if(viewCount > 1)
        scriptJquery("#gridicon-<?php echo $widgetIdentity ?>").removeClass('dnone');
    } else {
      en4.core.runonce.add(function() {
        switchview_<?php echo $widgetIdentity ?>(1);
      })
    }
  });

</script>
<?php endif; ?>

<?php if($this->flag == 1): ?>
  <h3>Browse Providers</h3>
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

<img src="application/modules/Sitebooking/externals/images/loader.gif" height=30 width=30 style="display: none;" id="loader-<?php echo $widgetIdentity ?>">              
<script type="text/javascript">                              

  scriptJquery('.provider_list').on('click', function(){

    scriptJquery('.provider_list').each(function(el){

      scriptJquery(this).removeClass('active');
    });

    
    var filter_type = this.getAttribute("data-target");

    scriptJquery(this).addClass('active');

    if(filter_type != '')
      scriptJquery('#hidden_filter_type_<?php echo $widgetIdentity ?>').value = filter_type;  


    en4.core.request.send(scriptJquery.ajax({
      dataType: 'html',
      url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
      data : {
      format : 'html',
      filter_type : filter_type,
      isAjax : 1
      },

    beforeSend: function () {
      scriptJquery('#listloader-<?php echo $widgetIdentity ?>').show();
    },    
  

      success: function(responseHTML) {

        scriptJquery('#hideResponse').html(responseHTML);
        scriptJquery('#listloader-<?php echo $widgetIdentity ?>').hide();


        // LIST VIEW

        scriptJquery('#views-<?php echo $widgetIdentity ?>').find('.sitebooking_list').html(scriptJquery('#hideResponse').find('.sitebooking_list').html());


        if(scriptJquery('#views-<?php echo $widgetIdentity ?>').html() == '') {

          scriptJquery('#views-<?php echo $widgetIdentity ?>').html(scriptJquery('#list-<?php echo $widgetIdentity ?>').html() + en4.core.language.translate("No Services Found Related to this"));
        }


        // GRID VIEW

        scriptJquery('#views-<?php echo $widgetIdentity ?>').find('.sitebooking_grid').html(scriptJquery('#hideResponse').find('.sitebooking_grid').html());


        if(scriptJquery('#views-<?php echo $widgetIdentity ?>').html() == '') {

          scriptJquery('#views-<?php echo $widgetIdentity ?>').html(scriptJquery('#views-<?php echo $widgetIdentity ?>').html() + en4.core.language.translate("No Services Found Related to this grid view"));
        }
        scriptJquery('#hideResponse').html('');
      }     
    }))

  });      

</script>

<script type="text/javascript" >
  function switchview_<?php echo $widgetIdentity ?>(flage){
    if(flage==1){

      scriptJquery('#grid-<?php echo $widgetIdentity ?>').removeClass('dnone');
      scriptJquery('#list-<?php echo $widgetIdentity ?>').addClass('dnone');
      scriptJquery('#listicon-<?php echo $widgetIdentity ?>').removeClass('selected');
      scriptJquery('#gridicon-<?php echo $widgetIdentity ?>').addClass('selected');
    }

    if(flage==0){

      scriptJquery('#list-<?php echo $widgetIdentity ?>').removeClass('dnone');
      scriptJquery('#grid-<?php echo $widgetIdentity ?>').addClass('dnone');
      scriptJquery('#gridicon-<?php echo $widgetIdentity ?>').removeClass('selected');
      scriptJquery('#listicon-<?php echo $widgetIdentity ?>').addClass('selected');
    }

  }

</script>


<!-- USER REVIEW -->
<div id="views-<?php echo $widgetIdentity ?>">
<?php endif; ?>
<?php
  echo $this->partial('provider_grid_view.tpl', 'sitebooking', array(
     'paginator' => $this->paginator,
     'widgetIdentity' => $this->identity,

  ));
?>
<?php
  echo $this->partial('provider_list_view.tpl', 'sitebooking', array(
     'paginator' => $this->paginator,
     'widgetIdentity' => $this->identity,
  ));
?>

<?php if(!$this->isAjax): ?>
</div>  
  <div class="sitebooking_more" id="view_more-<?php echo $widgetIdentity ?>" onclick = " view_<?php echo $widgetIdentity ?>() ">View more</div>
<?php endif; ?>


<!-- VIEW MORE -->
<script type="text/javascript">

  function view_<?php echo $widgetIdentity ?>(){

    var filter_type; 

    if(filter_type != '')
      filter_type = scriptJquery('#hidden_filter_type_<?php echo $widgetIdentity ?>').value;


    en4.core.request.send(scriptJquery.ajax({
      dataType:'html',
      url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
      data : {
      format : 'html',
      filter_type: filter_type,
      page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>,
      isAjax : 1
      },  

    beforeSend: function () {
      scriptJquery('#loader-<?php echo $widgetIdentity ?>').show();
    },    

    success: function(responseHTML) {

        scriptJquery('#hideResponse').html(responseHTML);
        scriptJquery('#loader-<?php echo $widgetIdentity ?>').hide();

        scriptJquery('#views-<?php echo $widgetIdentity ?>').find('.sitebooking_list').html(scriptJquery('#views-<?php echo $widgetIdentity ?>').find('.sitebooking_list').html() + scriptJquery('#hideResponse').find('.sitebooking_list').html());

        if(scriptJquery('#views-<?php echo $widgetIdentity ?>').html() == '') {

          scriptJquery('#views-<?php echo $widgetIdentity ?>').html(scriptJquery('#list-<?php echo $widgetIdentity ?>').html() + en4.core.language.translate("No Providers Found Related to this"));
        }

        scriptJquery('#views-<?php echo $widgetIdentity ?>').find('.sitebooking_grid').html(scriptJquery('#views-<?php echo $widgetIdentity ?>').find('.sitebooking_grid').html()+scriptJquery('#hideResponse').find('.sitebooking_grid').html());


        if(scriptJquery('#views-<?php echo $widgetIdentity ?>').html() == '') {

          scriptJquery('#views-<?php echo $widgetIdentity ?>').html(scriptJquery('#views-<?php echo $widgetIdentity ?>').html() + en4.core.language.translate("No Providers Found Related to this grid view"));
        }
        scriptJquery('#hideResponse').html('');
      }     
    }));
  };

  var cpage_<?php echo $widgetIdentity ?> = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
  
  var pages_<?php echo $widgetIdentity ?> = <?php echo $this->paginator->count() ?>;


  if(cpage_<?php echo $widgetIdentity ?> >= pages_<?php echo $widgetIdentity ?>) {
    scriptJquery("#view_more-<?php echo $widgetIdentity ?>").css('display','none');
  } else {
    scriptJquery("#view_more-<?php echo $widgetIdentity ?>").css('display','block');
  } 

</script>