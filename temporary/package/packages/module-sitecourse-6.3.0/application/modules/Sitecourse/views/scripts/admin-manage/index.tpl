<h2 class="fleft"><?php echo $this->translate('Course Builder / Learning Management Plugin'); ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>


<?php if( count($this->subnavigation) ): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->subnavigation)->render(); ?>
  </div>
<?php endif; ?>
<h3><?php echo $this->translate('Manage Courses'); ?></h3>
<p><?php echo $this->translate('Manage_Courses_Description'); ?></p>

<br />

<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction){  

    if( order == currentOrder ) { 
      document.getElementById('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else { 
      document.getElementById('order').value = order;
      document.getElementById('order_direction').value = default_direction;
    }
    document.getElementById('filter_form').submit();
  }

  // function multiDelete()
  // {
  //   return confirm('<?php //echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected pages?")) ?>');
  // }

  // function selectAll()
  // {
  //   var i;
  //   var multidelete_form = document.getElementById('multidelete_form');
  //   var inputs = multidelete_form.elements;
  //   for (i = 1; i < inputs.length - 1; i++) {
  //     if (!inputs[i].disabled) {
  //       inputs[i].checked = inputs[0].checked;
  //     }
  //   }
  // }
</script>

<div class="admin_search sitecourse_admin_search">
  <div class="search">
    <form method="post" class="global_form_box" action="<?php echo $this->url(array('module' => 'sitecourse', 'controller' => 'manage', 'action' => 'index'),'admin_default', true) ?>">
      <div>
        <label>
          <?php echo  $this->translate("Title") ?>
        </label>
        <?php if( empty($this->title)):?>
          <input type="text" name="title" /> 
        <?php else: ?>
          <input type="text" name="title" value="<?php echo $this->translate($this->title)?>"/>
        <?php endif;?>
      </div>
      <div>
        <label>
          <?php echo  $this->translate("Owner") ?>
        </label>  
        <?php if( empty($this->owner)):?>
          <input type="text" name="owner" /> 
        <?php else: ?> 
          <input type="text" name="owner" value="<?php echo $this->translate($this->owner)?>" />
        <?php endif;?>
      </div>


      <div>
        <label>
          <?php echo  $this->translate("Approval Status") ?>
        </label>
        <select id="status" name="course_status">
          <option value="0"></option>
          <option value="3" <?php if( $this->course_status == 3) echo "selected";?> ><?php echo $this->translate("Dis-Approved") ?></option>
          <option value="2" <?php if( $this->course_status == 2) echo "selected";?> ><?php echo $this->translate("Approved") ?></option>
        </select>
      </div>


      <div>
        <label>
          <?php echo  $this->translate("Dificulty level") ?>
        </label>
        <select id="level" name="difficulty_level">
          <option value="0"></option>
          <option value="3" <?php if( $this->difficulty_level == 3) echo "selected";?> ><?php echo $this->translate("Expert") ?></option>
          <option value="2" <?php if( $this->difficulty_level == 2) echo "selected";?> ><?php echo $this->translate("Intermediate") ?></option>
          <option value="1" <?php if( $this->difficulty_level == 1) echo "selected";?> ><?php echo $this->translate("Beginner") ?></option>

        </select>
      </div>

        <?php  $categories = Engine_Api::_()->getDbTable('categories', 'sitecourse')->getCategoriesAssoc(); ?>
        <?php if(count($categories) > 0) :?>
          <div>
            <label>
              <?php echo  $this->translate("Category") ?>
            </label>
            <select id="category_id" name="category_id" onchange="changeSubCategory()";>
              <option value=""></option>
              <?php if (count($categories) != 0) : ?>
                <?php $categories_prepared[0] = "";
                foreach ($categories as $value) {
                  $categories_prepared[$value['category_id']] = $value['category_name'] ?>
                  <option value="<?php echo $value['category_id'];?>" <?php if( $this->category_id == $value['category_id']) echo "selected";?>><?php echo $value['category_name'] ?></option>
                <?php } ?>
              <?php endif ; ?>
            </select>
          </div>
          <div id="subcategory_id-label">
            <label>
              <?php echo  $this->translate("Subcategory") ?>  
            </label>

            <select name="subcategory_id" id="subcategory_id"></select>
          </div>

        <?php endif;?>
        <div>
          <label>
            <?php echo  $this->translate("Browse By") ?>  
          </label>
          <select id="" name="courseBrowse">
            <option value="0" ></option>
            <option value="1" <?php if( $this->courseBrowse == 1) echo "selected";?> ><?php echo $this->translate("Most Recent") ?></option>
            <option value="2" <?php if( $this->courseBrowse == 2) echo "selected";?> ><?php echo $this->translate("Most Rated") ?></option>
          </select>
        </div>      

        <div>
          <label>
            <?php echo  $this->translate("Start Date") ?>
          </label>
          <input type="date"  name="date_from">
        </div>
        <div>
          <label>
            <?php echo  $this->translate("End Date") ?>
          </label>
          <input type="date"  name="date_to">
        </div>
        <div>
          <label>
            <?php echo  $this->translate("Newest") ?>
          </label>
             <select name="newest">
              <option value="0" ></option>
              <option value="1" <?php if( $this->newest == 'on') echo "selected";?> ><?php echo $this->translate("Yes") ?></option>
            
          </select>
        </div>
        <div>
         <div class="sitecourse_search_button">
          <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>
        </div>
      </div>

      </form>
    </div>
  </div>
  <br />

  <div class='admin_search'>
    <?php echo $this->formFilter->render($this) ?>
  </div>

  <div class='admin_members_results'>
    <?php $counter=$this->paginator->getTotalItemCount(); if(!empty($counter)): ?>
    <div class="">
      <?php  echo $this->translate(array('%s course found.', '%s courses found.', $counter), $this->locale()->toNumber($counter)) ?>
    </div>
  <?php else:?>
    <div class="tip"><span>
      <?php  echo $this->translate("No results were found.") ?></span>
    </div>
  <?php endif; ?>
  <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
        //'query' => $this->formValues,
  ));
  ?>
</div>

<br />

<?php  if( $this->paginator->getTotalItemCount()>0):?>
  <!-- <form id='multidelete_form' method="post" action="<?php echo $this->url(array('action'=>'multi-delete'));?>" onSubmit="return multiDelete()"> -->
    <table class='admin_table' width="100%">
      <thead>
        <tr>
          <?php $class = ( $this->order == 'course_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('course_id', 'DESC');" title="<?php echo $this->translate('ID'); ?>" ><?php echo $this->translate('ID'); ?></a></th>
          <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');" title="<?php echo $this->translate('Course Title'); ?>" ><?php echo $this->translate('Title'); ?></a></th>
          <?php $class = ( $this->order == 'username' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th align="left"  class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');" title="<?php echo $this->translate('Owner Name'); ?>"><?php echo $this->translate('Owner');?></a></th>    
          <?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>               
          <th align="left" title="<?php echo $this->translate('Price'); ?>"><?php 
          echo $this->translate('Price');
          echo "(". $currency .")" ; ?></th>

          <th align="left" title="<?php echo $this->translate('Enrollment Count'); ?>"><?php echo $this->translate('Enrollment Count')  ?></th>

          <?php $class = ( $this->order == 'approved' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class='admin_table_centered <?php echo $class ?>'><a href="javascript:void(0);" onclick="javascript:changeOrder('approved', 'ASC');" title="<?php echo $this->translate('Approved'); ?>" ><?php echo $this->translate('A'); ?></a></th>

          <?php $class = ( $this->order == 'creation_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="admin_table_centered <?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');" title="<?php echo $this->translate('Creation Date'); ?>"><?php echo $this->translate('Creation Date'); ?></a></th>

          <th align="left" title="<?php echo $this->translate('Modification Date'); ?>"><?php echo $this->translate('Modification Date')  ?></th>

          <th align="left" title="<?php echo $this->translate('Options'); ?>"
           ><?php echo $this->translate('Options'); ?></th>
         </tr>
       </thead>

       <tbody>
        <?php if( count($this->paginator) ): ?>
          <?php foreach( $this->paginator as $item ): ?>
            <?php if($item->approved!=0):?>
              <tr id="course_<?php echo $item->course_id; ?>" class="course_info">

                <td ><?php echo $item->course_id ?></td>

                <td class='admin_table_bold admin-txt-normal'><?php echo $this->htmlLink(Engine_Api::_()->sitecourse()->getHref($item['course_id'], $item['owner_id'],null, $item['title']),$this->translate($item->getTitle()),array('target' => '_blank')); ?></td>

                <td class='admin_table_bold' title="<?php echo $item->getOwner()->getTitle() ?>"> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle(), array('target' => '_blank')); ?></td>          


                <td align="left"><?php echo $item->price; ?></td>

                <td align="left" class="enrollment_count admin_table_centered">0</td>
                <?php if($item->approved == 1): ?>
                  <td align="center" class="admin_table_centered"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitecourse/externals/images/sitecourse_approved1.gif');?>
                <?php else : ?>
                  <td align="center" class="admin_table_centered"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitecourse/externals/images/sitecourse_approved0.gif');?>
                <?php endif; ?>


                <td align="center" class="admin_table_centered"><?php echo $this->translate(gmdate('M d,Y',strtotime($item->creation_date))); ?>

                <td align="center" class="admin_table_centered"><?php echo $this->translate(gmdate('M d,Y',strtotime($item->modified_date))); ?>
                <td>
                 <?php echo $this->htmlLink(Engine_Api::_()->sitecourse()->getHref($item['course_id'], $item['owner_id'],null, $item['title']),$this->translate('View'),array('target' => '_blank')); ?>
                 |
                 <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitecourse', 'controller' => 'admin-manage', 'action' => 'course-details', 'course_id' => $item['course_id']), $this->translate('Details'), array('class' => 'smoothbox'));?>
                 |
                  <?php if($item->disable_enrollment == 0): ?>
                  <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitecourse', 'controller' => 'admin-manage', 'action' => 'disable-enrollment', 'course_id' => $item['course_id']), $this->translate('Disable-Enrollment'), array('class'=> 'smoothbox'));?>
                <?php else : ?>
                  <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitecourse', 'controller' => 'admin-manage', 'action' => 'enable-enrollment', 'course_id' => $item['course_id']), $this->translate('Enable-Enrollment'), array());?>
                <?php endif; ?>
               </td>
             </tr>
           <?php endif;?>
         <?php endforeach; ?>
       <?php endif; ?>
     </tbody>
   </table>
   <br />
    <!-- <div class='buttons'>
      <button type='submit'><?php //echo $this->translate('Delete Selected'); ?></button>
    </div> -->
  </form>
<?php endif;?>


<script type="text/javascript">


  function changeSubCategory(){
    let parentCategory = document.getElementById('category_id').value;
    if (parentCategory){
      let url = '<?php echo $this->url(array('action' => 'subcategory'), 'sitecourse_general', true);?>';
      let request = scriptJquery.ajax({
        url : url,
        data : {
          format : 'json',
          parent_category : parentCategory
        },
        success : function(responseJSON) {
          const subcats = responseJSON.subcats;
          let hasSubCat = 0;
          clear('subcategory_id');
          for(let key in subcats){
            if (subcats.hasOwnProperty(key)) {
              ++hasSubCat;
              value = subcats[key];
              addOption(document.getElementById('subcategory_id'),value, key);
            }
          }

          if(!hasSubCat){
            document.getElementById('subcategory_id').style.display = 'none';
            document.getElementById('subcategory_id-label').style.display = 'none';
          }
        }
      });
    }

  }

  function clear(ddName)
  { 
    for (var i = (document.getElementById(ddName).options.length-1); i >= 0; i--) 
    { 
      document.getElementById(ddName).options[ i ]=null; 
    } 
  }

  function addOption(selectbox,text,value )
  {
    var optn = document.createElement("OPTION");
    optn.text = text;
    optn.value = value;
    if(optn.text != '' && optn.value != '') {
      document.getElementById('subcategory_id').style.display = 'block';
      document.getElementById('subcategory_id-label').style.display = 'block';
      selectbox.options.add(optn);
    }
    else {
      document.getElementById('subcategory_id').style.display = 'none';
      document.getElementById('subcategory_id-label').style.display = 'none';
      selectbox.options.add(optn);
    }
  }

  if(scriptJquery('#subcategory_id').length)
    scriptJquery('#subcategory_id').css('display','none');
  if(scriptJquery('#subcategory_id-label').length)
    scriptJquery('#subcategory_id-label').css('display','none');
  
</script>




<script type="text/javascript">

  en4.core.runonce.add(function() {
    function fetchCourseIds() {
      const courseRowEl = document.querySelectorAll('.course_info');
      const  ids = [];

      courseRowEl.forEach(elem => {
        const id = elem.id;
        ids.push(id.substring(id.indexOf('_')+1));
      })
      return ids.map(id => parseInt(id) ? parseInt(id) : 0);
    }


    const courseIds = fetchCourseIds();
    let request = scriptJquery.ajax({
      url : '<?php echo $this->url(array('action' => 'enrollment-count')) ?>',
      data : {
        format : 'json',
        is_ajax : true,
        ids : courseIds
      },
      success : function(responseJSON) {
        const enrollments = responseJSON.enrollment
        if(!enrollments) return;
        for(let id in enrollments) {
          document.getElementById(`course_${id}`)
          .querySelector('.enrollment_count').textContent = enrollments[id] ?? 0;
        }
      }
    })
  })




</script>
