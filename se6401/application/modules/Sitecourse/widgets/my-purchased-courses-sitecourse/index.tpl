  <?php 
  $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecourse/externals/scripts/togglefavourite.js');
  ?>
  <?php 
  $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecourse/externals/scripts/courseslisting.js');
  ?>
  <ul class="courses_manage list_wrapper" id="purchased_courses">
  </ul>

  <div class="load-more-items" id="load-more-purchased-items" style="display: none;">
    <button><?php echo $this->translate("View More"); ?></button>
  </div>

  <div class="tip purchased-courses-tip">
    <span>
      <?php echo $this->translate('You have not purchased any courses yet.');
      ?>
    </span>
  </div>

  <script type="text/javascript">

    let loadMorePurchasedItemsElem = document.getElementById(
      'load-more-purchased-items'
      );

    var url = '<?php echo $this->url(array('action'=>'ajax-load'),'sitecourse_general',true); ?>'
    var item_count = '<?php echo $this->itemPerPage; ?>'
    var pagination_params = '<?php echo json_encode($this->pagination_params); ?>'
    var myPurchasedCoursesParams = {
      url : url,
      item_count : item_count,
      pagination_params : pagination_params,
      page_id : 0,
      course_container_id : 'purchased_courses',
      tip_container : '.purchased-courses-tip',
      load_more_item_id : 'load-more-purchased-items',
      type : 'purchased-courses',
      course_fields : <?php echo json_encode($this->course_info); ?>
    }
    loadMoreItems(myPurchasedCoursesParams)
    loadMorePurchasedItemsElem.addEventListener('click', function() {
      loadMoreItems(myPurchasedCoursesParams)
    });

  </script>
  <?php if( @$this->closeSmoothbox ): ?>
    <script type="text/javascript">
      TB_close();
    </script>
  <?php endif; ?>
