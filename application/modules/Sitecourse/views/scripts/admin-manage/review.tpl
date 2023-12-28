<h2 class="fleft"><?php echo $this->translate('Course Builder / Learning Management Plugin'); ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>


<h3><?php echo $this->translate('Manage Review'); ?></h3>
<p><?php echo $this->translate('Manage_Review_Description'); ?></p>

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

  function multiDelete()
  {
    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected reviews?")) ?>');
  }

  function selectAll()
  {
    var i;
    var multidelete_form = document.getElementById('multireviewdelete_form');
    var inputs = multireviewdelete_form.elements;
    for (i = 1; i < inputs.length - 1; i++) {
      if (!inputs[i].disabled) {
        inputs[i].checked = inputs[0].checked;
      }
    }
  }
</script>

<div class="admin_search sitecourse_admin_search">
  <div class="search">
    <form method="post" class="global_form_box" action="<?php echo $this->url(array('module' => 'sitecourse', 'controller' => 'manage', 'action' => 'review'),'admin_default', true) ?>">
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
          <?php echo  $this->translate("Status") ?>
        </label>
        <select  name="review_status">
          <option value="0"></option>
          <option value="2" <?php if( $this->status == 2) echo "selected";?> ><?php echo $this->translate("Approved") ?></option>
          <option value="1" <?php if( $this->status == 1) echo "selected";?> ><?php echo $this->translate("Dis-Approved") ?></option>
        </select>
      </div>

       <div>
        <label>
          <?php echo  $this->translate("Rating") ?>
        </label>
        <select name="rating">

          <option value="0"></option>
          <option value="1" <?php if( $this->rating > 0 && $this->rating <= 1) echo "selected";?> ><?php echo $this->translate("0 to 1") ?></option>
          <option value="2" <?php if( $this->rating > 1 && $this->rating <= 2) echo "selected";?> ><?php echo $this->translate("1 to 2") ?></option>
          <option value="3" <?php if( $this->rating > 2 && $this->rating <= 3) echo "selected";?> ><?php echo $this->translate("2 to 3") ?></option>
          <option value="4" <?php if( $this->rating > 3 && $this->rating <= 4) echo "selected";?> ><?php echo $this->translate("3 to 4") ?></option>
          <option value="5" <?php if( $this->rating > 4 && $this->rating <= 5) echo "selected";?> ><?php echo $this->translate("4 to 5") ?></option>
        </select>
      </div>

       <div class="sitecourse_search_button">
        <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>
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
    <?php  echo $this->translate(array('%s review found.', '%s reviews found.', $counter), $this->locale()->toNumber($counter)) ?>
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
  <form id='multireviewdelete_form' method="post" action="<?php echo $this->url(array('action'=>'multi-review-delete'));?>" onSubmit="return multiDelete()"> 
    <table class='admin_table' width="100%">
      <thead>
        <tr>
          <th width="30" align="center"><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
          <?php $class = ( $this->order == 'review_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('review_id', 'DESC');" title="<?php echo $this->translate('ID'); ?>" ><?php echo $this->translate('ID'); ?></a></th>
          <?php $class = ( $this->order == 'review_title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('review_title', 'ASC');" title="<?php echo $this->translate('Review Title'); ?>" ><?php echo $this->translate('Title'); ?></a></th>
          <?php $class = ( $this->order == 'username' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th align="left"  class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');" title="<?php echo $this->translate('Owner Name'); ?>"><?php echo $this->translate('Owner');?></a></th>                  
         
          <th align="left"><?php echo $this->translate('Course Title'); ?></th>
          <?php $class = ( $this->order == 'rating' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('rating', 'ASC');" title="<?php echo $this->translate('Rating'); ?>" ><?php echo $this->translate('Rating'); ?></a></th>
        
          <th align="left" class="review"><?php echo $this->translate('Review'); ?></th>
          <?php $class = ( $this->order == 'status' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class='admin_table_centered <?php echo $class ?>'><a href="javascript:void(0);" onclick="javascript:changeOrder('status', 'ASC');" title="<?php echo $this->translate('Approved'); ?>" ><?php echo $this->translate('A'); ?></a></th>

          <?php $class = ( $this->order == 'creation_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="admin_table_centered <?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');" title="<?php echo $this->translate('Creation Date'); ?>"><?php echo $this->translate('Creation Date'); ?></a></th>          
          <th align="left" title="<?php echo $this->translate('Options'); ?>"
           ><?php echo $this->translate('Options'); ?></th>
         </tr>
       </thead>

       <tbody>
        <?php if( count($this->paginator) ): ?>
          <?php foreach( $this->paginator as $item ): ?>
           
            <tr>
              <td class="admin_table_centered"><input name='delete_<?php echo $item->review_id;?>' type='checkbox' class='checkbox' value="<?php echo $item->review_id ?>"/></td>

              <td ><?php echo $item->review_id ?></td>

              <td class='admin_table_bold admin-txt-normal'><?php echo $this->translate($item['review_title']); ?></td>

              <td class='admin_table_bold' title="<?php echo $item->getOwner()->getTitle() ?>"> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle(), array('target' => '_blank')); ?></td>      
               
                <td class='admin_table_bold admin-txt-normal'><?php $course = Engine_Api::_()->getItem('sitecourse_course',$item['course_id']); 
                  echo $this->htmlLink(Engine_Api::_()->sitecourse()->getHref($course['course_id'], $course['owner_id'],null, $course['title']),$course['title'],array('target' => '_blank'));; ?></td> 

                 <td class='admin_table_bold admin-txt-normal'><?php echo $this->translate($item['rating']); ?></td>

                  <td class='admin_table_bold admin-txt-normal'><?php echo $this->translate($item['review']); ?></td>   

          <?php if($item->status == 0): ?>
             <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitecourse', 'controller' => 'admin-manage', 'action' => 'review-approve', 'id' => $item['review_id'],'course_id'=>$item['course_id'],), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitecourse/externals/images/sitecourse_approved0.gif', '', array('title'=> $this->translate('Make Approved')))) ?>
                      </td>
          <?php else : ?>
            <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitecourse', 'controller' => 'admin-manage', 'action' => 'review-approve', 'id' => $item['review_id'],'course_id'=>$item['course_id'],), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitecourse/externals/images/sitecourse_approved1.gif', '', array('title'=> $this->translate('Make Dis-Approved')))) ?>
                      </td>
          <?php endif; ?>


          <td align="center" class="admin_table_centered"><?php echo $this->translate(gmdate('M d,Y',strtotime($item->creation_date))); ?>

         <td>
          <?php 
          $course = Engine_Api::_()->getItem('sitecourse_course',$item['course_id']);
          echo $this->htmlLink(Engine_Api::_()->sitecourse()->getHref($course['course_id'], $course['owner_id'],null, $course['title']),$this->translate('view'),array('target'=>'_blank')); ?>
          |
           <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitecourse', 'controller' => 'admin-manage', 'action' => 'delete-review', 'review_id' => $item['review_id']), $this->translate('delete'), array(
                  'class' => 'smoothbox',
                )) ?>
         </td>
        



        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>
<br />
    <div class='buttons'>
      <button type='submit'><?php echo $this->translate('Delete Selected'); ?></button>
    </div> 
  </form>
<?php endif;?>


