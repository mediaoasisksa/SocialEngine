<?php $details = $this->details; ?>
<h2 class="payment_transaction_detail_headline"><?php echo $this->translate('Transaction Details'); ?></h2>

<dl class="payment_transaction_details"> 
  <dd><?=$this->translate('Transaction ID');?> </dd>
  <dt><?php echo $details['transaction_id']; ?></dt>    
  <dd><?=$this->translate('User Name');?></dd>   
  <dt><?php $user = Engine_Api::_()->user()->getUser($details['user_id']); 
  echo $this->htmlLink($user->getHref(), $user->getTitle(), array('target' => '_blank'));?></dt> 
  <dd><?=$this->translate('Course Name');?></dd>
  <dt>  <?php $course = Engine_Api::_()->getItem('sitecourse_course',$details['course_id']); 
  echo $this->htmlLink(Engine_Api::_()->sitecourse()->getHref($course['course_id'], $course['owner_id'],null, $course['title']),$course['title'],array('target' => '_blank')); ?></dt>
  <dd> <?=$this->translate('Payment Amount');?>  </dd>
  <dt>  <?php echo $details['price']; ?> </dt>

  <dd>  <?=$this->translate('Gateway Transaction ID');?>   </dd>
  <dt> 
  <?php if( !empty($details['gateway_transaction_id']) ): ?>
    <?php echo $this->htmlLink(array(
      'route' => 'admin_default',
      'module' => 'sitecourse',
      'controller' => 'manage',
      'action' => 'detail-transaction',
      'transaction_id' => $details['transaction_id'],
    ), $details['gateway_transaction_id'], array(
      'target' => '_blank',
    )) ?>
  <?php else: ?>
    -
  <?php endif; ?>
  </dt>
  <dd>  <?=$this->translate('Date');?>  </dd>
  <dt>  <?php echo $details['date']; ?>  
</dt>
</dl>

<button onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('Cancel'); ?></button>

<?php if( @$this->closeSmoothbox ): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>
