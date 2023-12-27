<!-- list down courses -->
<ul class="courses_manage list_wrapper" id="courses_manage list_wrapper">
</ul>

<div class="load-more-items" id="load-more-items" style="display: none;">
  <button  id="loding_image_view" style="display:none;">
    <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' style='margin-right: 5px;' />
    <?php echo $this->translate("Loading ...") ?>
  </button>
  <button id="load_more_button"><?php echo $this->translate("View More");?></button>

</div>


<div class="tip browse-courses-tip">
  <span>
   <?php echo $this->translate('There are no courses created yet, why don’t you ');
   echo $this->htmlLink(
    array('route' => 'sitecourse_general', 'module' => 'sitecourse', 'controller' => 'index', 'action' => 'create',),
    $this->translate('Create One?'), array()); ?>
  </span>
</div>

<script type="text/javascript">


  let loadMoreItemsElem = document.getElementById(
    'load-more-items'
    )
  var url = '<?php echo $this->url(array('action'=>'ajax-load'),'sitecourse_general',true); ?>'
  var item_count = '<?php echo $this->itemPerPage; ?>';
  var textTrucationLimit = '<?php echo $this->textTrucationLimit; ?>';
  var pagination_params = '<?php echo json_encode($this->pagination_params); ?>'
  var coursesParams = {
    url : url,
    item_count : item_count,
    pagination_params : pagination_params,
    page_id : 0,
    textTrucationLimit : textTrucationLimit,
    course_container_id : 'courses_manage list_wrapper',
    tip_container : '.browse-courses-tip',
    load_more_item_id : 'load-more-items',
    type : 'browse-courses',
    course_fields : <?php echo json_encode($this->course_info); ?>
  }
  

    /**
     * The condition will check whether the page is adv search or not
     * if the current page is adv search then include the script as 
     * adv search not include scripts through ajax 
     * else just load the initial items
     */
     <?php if(isset($_POST['default_view'])): ?>
      var script = document.createElement('script');
      script.type = 'text/javascript';
      script.src = "<?php echo
      $this->layout()->staticBaseUrl . 'application/modules/Sitecourse/externals/scripts/courseslisting.js'?>";
    if(script.readyState) {  // only required for IE <9
      script.onreadystatechange = function() {
        if ( script.readyState === "loaded" || script.readyState === "complete" ) {
          script.onreadystatechange = null;
          loadMoreItems(coursesParams);
        }
      };
    } else {  //Others
      script.onload = function() {
        loadMoreItems(coursesParams);
      };
    }
    document.getElementsByTagName('head')[0].appendChild(script);
  <?php else: ?>
    loadMoreItems(coursesParams);
  <?php endif; ?>
  loadMoreItemsElem.addEventListener('click', function() {
    loadMoreItems(coursesParams)
  });


</script>






