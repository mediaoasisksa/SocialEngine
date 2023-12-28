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
<p><?php echo $this->translate('Manage_Courses_Request_Description'); ?></p>

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
</script>

<div class="admin_search sitecourse_admin_search">
  <div class="search">
    <form method="post" class="global_form_box" action="<?php echo $this->url(array('module' => 'sitecourse', 'controller' => 'manage', 'action' => 'request'),'admin_default', true) ?>">
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
          <th align="left"> <?php echo $this->translate('Approval Status'); ?> </th>          
          <th align="left" title="<?php echo $this->translate('Price'); ?>"><?php echo $this->translate('Price')  ?></th>


          <?php $class = ( $this->order == 'creation_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="admin_table_centered <?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');" title="<?php echo $this->translate('Creation Date'); ?>"><?php echo $this->translate('Creation Date'); ?></a></th>
          
          <th align="left" title="<?php echo $this->translate('Modification Date'); ?>"><?php echo $this->translate('Modification Date')  ?></th>

          <?php $class = ( $this->order == 'approved' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class='admin_table_centered <?php echo $class ?>'><a href="javascript:void(0);" onclick="javascript:changeOrder('approved', 'ASC');" title="<?php echo $this->translate('Approved'); ?>" ><?php echo $this->translate('Action'); ?></a></th>

        </tr>
      </thead>

      <tbody>
        <?php if( count($this->paginator) ): ?>
          <?php foreach( $this->paginator as $item ): ?>
           
              <tr>     
                <td ><?php echo $item->course_id ?></td>

               <td class='admin_table_bold admin-txt-normal'><?php echo $this->htmlLink(Engine_Api::_()->sitecourse()->getHref($item['course_id'], $item['owner_id'],null, $item['title']),$this->translate($item->getTitle()),array('target' => '_blank')); ?></td>

                <td class='admin_table_bold' title="<?php echo $item->getOwner()->getTitle() ?>"> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle(), array('target' => '_blank')); ?></td>   

                <?php if($item->approved == 0):?>
                  <td align="left"><?php echo $this->translate('Pending'); ?></td>
                <?php elseif($item->approved == 1): ?>
                 <td align="left"><?php echo $this->translate('Approved'); ?></td>
               <?php else: ?>
                 <td align="left"><?php echo $this->translate('Dis-Approved'); ?></td>
               <?php endif; ?>
               <td align="left"><?php echo $item->price; ?></td>

               <td align="center"><?php echo $this->translate(gmdate('M d,Y',strtotime($item->creation_date))); ?></td>

               <td align="center"><?php echo $this->translate(gmdate('M d,Y',strtotime($item->modified_date))); ?></td>

               <td align="center"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecourse', 'controller' => 'manage', 'action' => 'approved', 'id' => $item->course_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitecourse/externals/images/sitecourse_approved1.gif', '', array('title'=> $this->translate('Make Approved')))) ?>
               <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecourse', 'controller' => 'manage', 'action' => 'disapproved', 'id' => $item->course_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitecourse/externals/images/sitecourse_approved0.gif', '', array('title'=> $this->translate('Make Dis-Approved'))), array('class'=> 'smoothbox')) ?>
             </td>
           </tr>   
       <?php endforeach; ?>
     <?php endif; ?>
   </tbody>
 </table>
 <br />
  </form>
<?php endif;?>

<script type="text/javascript">

  function changeSubCategory(){
    let parentCategory = document.getElementById('category_id').value;
    if (parentCategory){
      let url = '<?php echo $this->url(array('action' => 'subcategory'), 'sitecourse_general', true);?>';
      let request = en4.core.request.send(scriptJquery.ajax({
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
            scriptJquery('#subcategory_id').css('display','none')
            scriptJquery('#subcategory_id-label').css('display','none')
          }
        }
      }));
      
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

  if(scriptJquery('#subcategory_id').length)
    scriptJquery('#subcategory_id').css('display','none')
  if(scriptJquery('#subcategory_id-label').length)
    scriptJquery('#subcategory_id-label').css('display','none')
  
</script>
