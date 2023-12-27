  <?php 
  $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecourse/externals/scripts/togglefavourite.js');

  $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecourse/externals/scripts/courseslisting.js');
  ?>
  <ul class="courses_manage list_wrapper" id="my_courses">
  </ul>

  <div class="load-more-items" id="load-more-my-items" style="display: none;">
    <button><?php echo $this->translate("View More"); ?></button>
  </div>

  <div class="tip my-courses-tip">
    <span>
      <?php echo $this->translate('There are no courses created yet, why don’t you ');
      echo $this->htmlLink(
        array('route' => 'sitecourse_general', 'module' => 'sitecourse', 'controller' => 'index', 'action' => 'create',),
        $this->translate('Create One?'), array()); ?>
      </span>
    </div>


    <script type="text/javascript">
      let loadMoreMyItemsElem = document.getElementById('load-more-my-items')

      
        var url = '<?php echo $this->url(array('action'=>'ajax-load'),'sitecourse_general',true); ?>';
        var item_count = '<?php echo $this->itemPerPage; ?>';
        var pagination_params = '<?php echo json_encode($this->pagination_params); ?>';
        var myCoursesParams = {
          url : url,
          item_count : item_count,
          pagination_params : pagination_params,
          page_id : 0,
          course_container_id : 'my_courses',
          tip_container : '.my-courses-tip',
          load_more_item_id : 'load-more-my-items',
          type : 'my-courses',
          course_fields : <?php echo json_encode($this->course_info); ?>
        }
        loadMoreItems(myCoursesParams)
        loadMoreMyItemsElem.addEventListener('click', function() {
          loadMoreItems(myCoursesParams)
        })
      
    </script>
    <?php if( @$this->closeSmoothbox ): ?>
      <script type="text/javascript">
        TB_close();
      </script>
    <?php endif; ?>

