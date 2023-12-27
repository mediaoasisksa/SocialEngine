
<!--Your information form-->
<div class="seao_buyer_payment_container">
  <div id="order_summary_block" class="seao_order_summary">
    <div class="seao_order_summary_block">
      <h3><?php echo $this->translate("Order Summary"); ?></b></h3>

      <ul class="seao_order_summary_list">
        <?php
        $courseDetail = $this->formValues;
        $totalOrderPrice = 0;
        $course_id = $courseDetail['course'];    
        $courseObj = Engine_Api::_()->getItem('sitecourse_course', $course_id);
        $getContentImages = Engine_Api::_()->sitecourse()->getContentImage($courseObj);
        $courseImg = $getContentImages['image_profile'];
        $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        ?>
        <li>           
          <div class="seao_order_summary_list_block">
            <div class="seao_order_summary_block_image">
              <img src="<?= $courseImg; ?>" alt="Course Image">
            </div>
            <div class="seao_order_summary_block_info">
              <h3 class="seao_order_summary_list_title">
                <?php echo $courseObj->title; ?>
              </h3>
              <div class="seao_order_summary_block_price">
                <div><?php if ($courseObj['price'] > 0): ?> 
                 <?= $currency; ?>
                  <?php echo $courseObj['price']; $totalOrderPrice+=$courseObj['price']; ?>
                <?php else: ?>
                 <?php echo $this->translate('Free') ?>
               <?php endif; ?>
               </div>
             </div>
           </div>
          
         </div>
       </li> 


       <li class="seao_order_summary_total bold">
        <div><?php echo $this->translate("Grand Total"); ?></div>
        <div><?= $currency; ?>
        <?php echo $totalOrderPrice; ?></div>
      </li>
    </ul>

  </div>
  </div>

  <div class="seao_order_summary_button">
      <form method="post" id ="buyer_details_form" action="<?php echo $this->url(array('action' => 'checkout','course_id'=>$this->course_id, 'buyer_details' => true), "sitecourse_order", true); ?>">            
        <button type="submit" >
          <?php echo $this->translate("Continue"); ?>
        </button>
      </form>
    </div>

</div>


<script type="text/javascript">
  window.addEventListener('scroll', function() {
    var el = scriptJquery('#order_summary_block'), topElement = el.parent();
    var elementPostionY = 0;
    if (typeof(topElement[0].offsetParent) != 'undefined') {
      elementPostionY = topElement[0].offsetTop;
    } else {
      elementPostionY = topElement[0].y;
    }

    if(elementPostionY < scriptJquery(window).scrollTop()){
      el.addClass("position_fixed");
    } else if (el.hasClass('position_fixed')){
     el.removeClass("position_fixed");
   }

 });

  document.getElementById(window).off('beforeunload');
</script>
