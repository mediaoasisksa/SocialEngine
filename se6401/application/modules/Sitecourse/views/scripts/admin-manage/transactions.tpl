<!-- render navigation menu -->
<h2 class="fleft"><?php echo $this->translate('Course Builder / Learning Management Plugin'); ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
</div>
<?php endif; ?>

<h3><?php echo $this->translate("Course Transactions");?></h3>
<p><?php echo $this->translate("Browse through the transactions made by users for courses. The search box below will search through the buyer names, courses name and the start and end date filter to search the transactions."); ?></p>
<br/>

<!-- search filter form -->
<form method="get" class="search__filter_form admin_search_form">
    <div class="form_wrapper">
        <div class="form_label">
            <label><?php echo $this->translate("User Name");?></label>
        </div>
        <div class="form_element">
            <input type="text" name="user_name">
        </div>
    </div>

    <div class="form_wrapper">
        <div class="form_label">
            <label><?php echo $this->translate("Course Title");?></label>
        </div>
        <div class="form_element">
            <input type="text" name="course_name">
        </div>
    </div>

    <div class="form_wrapper">
        <div class="form_label">
            <label><?php echo $this->translate("Start Date");?></label>
        </div>
        <div class="form_element">
            <input type="date" name="from">
        </div>
    </div>

    <div class="form_wrapper">
        <div class="form_label">
            <label><?php echo $this->translate("End Date");?></label>
        </div>
        <div class="form_element">
            <input type="date" name="to">
        </div>
    </div>

    <div class="form_wrapper">
        <div class="form_label">
            <label></label>
        </div>
        <div class="submit_element">
            <button type="submit" name="search"><?php echo $this->translate("Search");?></button>
        </div>
    </div>
    
</form>







<!-- render filter form -->
<?php echo $this->formFilter->render($this); ?>

<!-- transaction paginator -->
<?php if(count($this->paginator)): ?>
    <!-- transaction table -->
    <table class='admin_table' width="100%">

        <thead>
            <?php $class = ( $this->order == 'transaction_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
            <th align="left" class="<?php echo $class; ?>">
                <a href="javascript:void(0);" 
                onclick="javascript:changeOrder('transaction_id', 'DESC');" 
                title="<?php echo $this->translate('ID'); ?>" >
                <?php echo $this->translate('ID'); ?></a>
            </th>

            <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
            <th align="left" class="<?php echo $class; ?>">
                <a href="javascript:void(0);" 
                onclick="javascript:changeOrder('title', 'DESC');" 
                title="<?php echo $this->translate('Course Title'); ?>" >
                <?php echo $this->translate('Course Title'); ?></a>
            </th>

            <th align="left">
                <?php echo $this->translate('User Name'); ?></a>
            </th>

            <?php $class = ( $this->order == 'price' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
            <th align="left" class="<?php echo $class; ?>">
                <a href="javascript:void(0);" 
                onclick="javascript:changeOrder('price', 'DESC');" 
                title="<?php echo $this->translate('Amount'); ?>" >
                <?php echo $this->translate('Amount'); ?></a>
            </th>
            <th align="left">
                <?php echo $this->translate("Status");?>
            </th>

            <?php $class = ( $this->order == 'date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
            <th align="left" class="<?php echo $class; ?>">
                <a href="javascript:void(0);" 
                onclick="javascript:changeOrder('date', 'DESC');" 
                title="<?php echo $this->translate('Date'); ?>" >
                <?php echo $this->translate('Date'); ?></a>
            </th>
            <th align="left">
                <?php echo $this->translate("Options");?>
            </th>
        </thead>

        <?php foreach($this->paginator as $item): ?>

            <tbody>
                <td class='admin_table_bold admin-txt-normal'>
                    <?php echo $item->transaction_id; ?>
                </td>
                <td class='admin_table_bold admin-txt-normal'>
                    <?php $course = Engine_Api::_()->getItem('sitecourse_course',$item['course_id']); 
                    echo $this->htmlLink(Engine_Api::_()->sitecourse()->getHref($course['course_id'], $course['owner_id'],null, $course['title']),$course['title'],array('target' => '_blank')); ?>
                </td>
                <td class='admin_table_bold'> 
                    <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle(), array('target' => '_blank')); ?> 
                </td>
                <td>
                    <?php echo $item->currency . ' ' . $item->price; ?>
                </td>
                <td>
                    <?php echo $item->state; ?>
                </td>
                <td>
                    <?php echo date('d-M-Y',strtotime($item->date)); ?>
                </td>
                <td>
                    <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitecourse', 'controller' => 'admin-manage', 'action' => 'transaction-details', 'transaction_id' => $item['transaction_id']), $this->translate('Details'), array(
                      'class' => 'smoothbox',
                  )) ?>
              </td>
          </tbody>
      <?php endforeach; ?>
  </table>
<?php else: ?>
    <div class="tip">
        <span><?php echo $this->translate("No result were found.");?></span>
    </div>
<?php endif; ?>

<script type="text/javascript">

    function changeOrder(order,orderDirection) {
        let prevOrder = '<?php echo $this->order; ?>';
        let prevDirection = '<?php echo $this->order_direction; ?>';
        if(prevOrder == order) {
            document.getElementById('order_direction').value = (prevDirection == 'DESC') ? 'ASC':'DESC';
        }
        document.getElementById('order').value = order;
        document.getElementById('filter_form').submit();
    }
</script>
