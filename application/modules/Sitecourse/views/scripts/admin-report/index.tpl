<h2 class="fleft"><?php echo $this->translate('Course Builder / Learning Management Plugin'); ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>
<h3><?php echo $this->translate("Abuse Report");?></h3>
<p><?php echo $this->translate("Manage_Report_Description"); ?></p>


<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>
<div class='admin_members_results'>
  <?php $counter=$this->paginator->getTotalItemCount(); 
  if(!empty($counter)): ?>
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

  ));
  ?>
</div>
<br />


<?php  if( $this->paginator->getTotalItemCount()>0):?>
  <table class='admin_table' width="100%">
    <thead>
      <tr>
        <th width="30"><input onclick="selectAll(this.checked)" type='checkbox' class='checkbox'></th>
        <?php $class = ( $this->order == 'report_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
        <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('report_id', 'DESC');" title="<?php echo $this->translate('ID'); ?>" ><?php echo $this->translate('ID'); ?></a></th>
        <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
        <th align="left" class="<?php echo $class ?>"><?php echo $this->translate('Course Title'); ?></a></th>
        <?php $class = ( $this->order == 'reporter_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
        <th align="left"  class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('reporter_id', 'ASC');" title="<?php echo $this->translate('Reporter'); ?>"><?php echo $this->translate('Reporter');?></a></th>                   
        <?php $class = ( $this->order == 'creation_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
        <th class='admin_table_centered <?php echo $class ?>'><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'ASC');" title="<?php echo $this->translate('Date'); ?>" ><?php echo $this->translate('Date'); ?></a></th>
        <th align="left" title="<?php echo $this->translate('Reasons'); ?>"><?php echo $this->translate('Reasons')  ?></th>
        <th align="left" title="<?php echo $this->translate('Options'); ?>"
         ><?php echo $this->translate('Options'); ?></th>
       </tr>
     </thead>

     <tbody>
      <?php if( count($this->paginator) ): ?>
        <?php foreach( $this->paginator as $item ): ?>

          <tr>
            <td><input name='delete[]' type='checkbox' class='checkbox' value="<?php echo $item->report_id ?>"/></td>
            <td><?php echo $item['report_id']; ?></td>

            <td class='admin_table_bold admin-txt-normal' >
            <?php $course = Engine_Api::_()->getItem('sitecourse_course',$item['course_id']); 
            echo $this->htmlLink(Engine_Api::_()->sitecourse()->getHref($course['course_id'], $course['owner_id'],null, $course['title']),$course['title'],array('target' => '_blank')); ?>
          </td>

          <td>
            <?php $user = Engine_Api::_()->user()->getUser($item->reporter_id); 
            echo $this->htmlLink($user->getHref(), $user->getTitle(), array('target' => '_blank'));?>
          </td>

          <td><?php echo $item['creation_date']; ?></td>

          <td><?php echo $item['reason']; ?></td>

          <td>
            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecourse', 'controller' => 'report', 'action' => 'reportaction','report_id' => $item['report_id'],'course_id' => $item['course_id']), $this->translate('take action'), array(
              'class' => 'smoothbox'
            ));?>
            |
            <?php $course = Engine_Api::_()->getItem('sitecourse_course',$item['course_id']); ?>
            <?php if($course): ?>
              <?php
              echo $this->htmlLink(Engine_Api::_()->sitecourse()->getHref($course['course_id'], $course['owner_id'],null, $course['title']),'view content',array('target'=>'_blank'));
              ?>
            <?php endif; ?>
            |
            <a href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'sitecourse', 'controller' => 'report', 'action' => 'delete','id' => $item['report_id']), 'admin_default', array());?>')"
              style="cursor: pointer;"><?php echo $this->translate('dismiss'); ?>
            </a>



          </td>
        </tr>



      <?php endforeach; ?>

    <?php endif; ?>           


  </tbody>
</table>
<button onclick="deleteSelected()">
  Dismiss Selected
</button>
<?php endif; ?>

<?php     $front = Zend_Controller_Front::getInstance();
$baseUrl = $front->getBaseUrl();
$link =$baseUrl . '/' . "admin/sitecourse/report/deleteselected";
?>

<form id="delete_selected" method="post" action="<?php echo $link; ?>">
  <input type="hidden" name="ids" id="ids">
</form>

<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction){  
    if( order == currentOrder ) { 
      document.getElementById('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else { 
      document.getElementById('order_direction').value = default_direction;
    }
    document.getElementById('order').value = order;
    document.getElementById('filter_form').submit();
  }


  function selectAll(checked = true){
    const checkboxElem = document.querySelectorAll('.checkbox');

    checkboxElem.forEach(element => {
      element.checked = checked;
    }) 
  }

  function deleteSelected(){
    let checkedElements = [];

    const checkboxElem = document.querySelectorAll('.checkbox');
    checkboxElem.forEach(element => {
      if(!isNaN(element.value))
        if(element.checked) checkedElements.push(element.value);
    })
    if(checkedElements.length){
      document.getElementById('ids').value = checkedElements;
      document.getElementById('delete_selected').submit();
    }

  }
</script>

