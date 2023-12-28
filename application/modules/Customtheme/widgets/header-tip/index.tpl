<?php 

?>
<?php if($this->approved == 0):?>
<div class="custom_header_tip" id="CustomHeaderTip">
  <div class="custom_header_tip_txt"><?php echo $this->translate("Thank you for Joining us. Currently your account is under review and limited access. You will able to access all feature after review.");?></div>
  <span class="_close"><a href="javascript:void(0);" id="HeaderTipClose"><i class="fas fa-times"></i></a></span>
</div>


<?php endif;?>
<script>
  $('#HeaderTipClose').on('click', function(event){
    event.preventDefault();
    if($('#CustomHeaderTip').hasClass('_hide'))
      $('#CustomHeaderTip').removeClass('_hide');
    else
      $('#CustomHeaderTip').addClass('_hide');
    return false;
  });
</script>