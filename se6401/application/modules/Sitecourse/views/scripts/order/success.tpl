<div class="generic_layout_container layout_middle">
  <div class="sitecourse_alert_msg b_medium">
    <?php if (empty($this->state) || ($this->state == 'active')): ?>
      <p>
        <?php echo $this->success_message; ?>
      </p>
  
    <?php elseif ($this->state == 'pending'): ?>
      <h3>
        <?php echo $this->translate('Payment Pending') ?>
      </h3>
      <p>
        <?php echo $this->translate('Thank you for submitting your payment. Your payment is currently pending.') ?>
      </p>
  
    <?php else: ?>
      <h3>
        <?php echo $this->translate('Payment Failed') ?>
      </h3>
        <p>
      <?php echo $this->translate('There was an error processing your transaction for the order: %s.', $this->order_id) ?>
      <?php if (!empty($this->viewer_id)) : ?>
        <?php echo $this->translate('We suggest that you please try again after some time.') ?>
      <?php endif; ?>
      </p>
  
    <?php endif; ?>
  </div>
  
  <?php if (empty($this->state) || ($this->state == 'active') || ($this->state == 'pending')): ?>
    <?php if (!empty($this->viewer_id)) : ?>
        <div class="clr sitecourse_submit">
          <button class="mtop10 fright" onclick="viewYourOrder()">
            <?php echo $this->translate('Go to My Course') ?>
          </button>
        </div>
    <?php endif; ?>
  <?php else :?>
     <?php if (!empty($this->viewer_id)) : ?>
        <div class="clr">
          <button class="mtop10 fright" onclick="backToEvent()">
            <?php echo $this->translate('Browse Courses') ?>
          </button>
        </div>
    <?php endif; ?>
  <?php endif; ?>
</div>


<script type="text/javascript">
  function viewYourOrder()
  {
    window.location.href = '<?php echo $this->url(array('action' => 'index','course_id'=>$this->course_id), 'sitecourse_learning', true) ?>';
  }
  
  function backToEvent() {
      window.location.href = '<?php echo $this->url(array('action' => 'index'), 'sitecourse_general', true) ?>';
  }
</script>
