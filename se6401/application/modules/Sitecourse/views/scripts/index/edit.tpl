<?php 


$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecourse/externals/styles/style_sitecourse_dashboard.css');
?>


<div class="headline">
  <h2>
    <?php echo $this->translate('Courses');?>
  </h2>
  <?php if($this->navigation && count($this->navigation) > 0 ): ?>
    <div class="tabs">
      <?php
        // Render the menu
      echo $this->navigation()
      ->menu()
      ->setContainer($this->navigation)
      ->render();
      ?>
    </div>
  <?php endif; ?>
</div>

</br>


<div class="course_builder_dashboard">
  <div class="course_builder_dashboard_container">

    <?php $id=$this->course_id;
    $blockId = 1;
    $liId='edit'; ?>
    <?php include_once APPLICATION_PATH . '/application/modules/Sitecourse/views/scripts/dashboard/_menu.tpl'; ?>
    
    <div class="course_builder_dashboard_sections">
      <div class="course_builder_dashboard_sections_list">

        <div class="layout_middle">
          <div class="course_builder_dashboard_sections_header">
            <div class="course_builder_dashboard_sections_header_title">
              <img src="<?php echo $this->images['image_icon'];?>" alt="" />
              <h3><?php echo $this->translate('Course Dashboard'); ?></h3>
            </div>
            <?php include_once APPLICATION_PATH . '/application/modules/Sitecourse/views/scripts/dashboard/_dashboardNavigation.tpl'; ?>
          </div>
        </div>
        <div class="generic_layout_container">
          <div class="tip">
            <!-- request in under process -->
            <?php if($this->approval == 0 && !$this->draft): ?>
              <span> <?php echo $this->translate('Approval request is pending'); ?> </span>
              <!-- request is disapproved by admin -->
            <?php elseif($this->approval == 2): ?>
              <span> <?php echo $this->translate('Admin has disapproved the approval request ');?>
              <?php if($this->reason){
               echo $this->translate('and the reason stated by the admin is ('); 
               echo $this->reason;
                echo ')';
              } ?>
            </span>
              <?php if($this->approval_reminders): ?>
                <?php
                $count = $this->approval_reminders -$this->request_count;
                if($count <= 0) $count = 0;
                echo "Remaining Request count: ".$count;
                ?>
              <?php endif; ?>
            <?php endif; ?>


          </div>
          
          


          <?php echo $this->form->render($this) ?>
        </div>
      </div>

    </div>
  </div>

  <script type="text/javascript">
    function insertAfter(referenceNode, newNode) {
      referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
    }
    function addOption(selectbox,text,value )
    {
      var optn = document.createElement("OPTION");
      optn.text = text;
      optn.value = value;
      if('<?php echo $this->subcategory_id ;?>' == value){
        optn.selected = true;
      }
      if(optn.text != '' && optn.value != '') {
        scriptJquery('#subcategory_id').css('display','block')
        scriptJquery('#subcategory_id-label').css('display','block')
        selectbox.options.add(optn);
      }
      else {
        scriptJquery('#subcategory_id').css('display','none')
        scriptJquery('#subcategory_id-label').css('display','none')
        selectbox.options.add(optn);
      }
    }

    function clear(ddName)
    { 
      for (var i = (document.getElementById(ddName).options.length-1); i >= 0; i--) 
      { 
        document.getElementById(ddName).options[ i ]=null; 
      } 
    }



    function changeSubCategory(categoryId){
      console.log(categoryId);
      if(categoryId == 0){
        clear('subcategory_id');
        scriptJquery('#subcategory_id').css('display','none')
        scriptJquery('#subcategory_id-label').css('display','none')
        return;
      }
      let url = '<?php echo $this->url(array('action' => 'subcategory'), 'sitecourse_general', true);?>';
      scriptJquery.ajax({
        url : url,
        data : {
          format : 'json',
          parent_category : categoryId
        },
        success : function(responseJSON) {
          const subcats = responseJSON.subcats;
          let hasSubCat = 0;
          clear('subcategory_id');
          for(let key in subcats){
            if (subcats.hasOwnProperty(key)) {
              ++hasSubCat;
              value = subcats[key];
              addOption(scriptJquery('#subcategory_id')[0],value, key);
            }
          }

          if(!hasSubCat){
            scriptJquery('#subcategory_id').css('display','none')
            scriptJquery('#subcategory_id-label').css('display','none')
          }
        }
      });
    }


    const categoryElem = scriptJquery('#category_id');

    if(categoryElem){
      changeSubCategory(categoryElem.val());
    }




    const canEdit = <?php echo $this->canEditCat; ?>;
    console.log(canEdit);
    if(!canEdit){
      for (var i = (document.getElementById('category_id').options.length-1); i >= 0; i--) 
      { 
        document.getElementById('category_id').disabled = true;
      } 
      document.getElementById('submit').addEventListener('click',function(){
        document.getElementById('category_id').removeAttribute('disabled');
        
      })
    }


  </script>
















