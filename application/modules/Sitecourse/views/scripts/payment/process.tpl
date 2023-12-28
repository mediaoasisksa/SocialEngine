
<?php if($this->isSupported) { ?>
  <?php if($this->isAllowedAmount): ?>
<div>
  <div>
    <center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/loading.gif" /></center>
  </div>
  <div id="LoadingImage" style="text-align:center;margin-top:15px;font-size:17px;">  
    <?php echo $this->translate("Processing Request. Please wait .....") ?>
  </div>
</div>
<script type="text/javascript">
    function jsonToQueryString(json) {
      return '?' + 
        Object.keys(json).map(function(key) {
            return encodeURIComponent(key) + '=' +
                encodeURIComponent(json[key]);
        }).join('&');
    }

    scriptJquery( window ).load(function() {
      var url = '<?php echo $this->transactionUrl ?>';
      var data = <?php echo Zend_Json::encode($this->transactionData) ?>;

      window.location.href= url +jsonToQueryString(data);
    });
</script>
<!-- remove this if of no use -->
  <?php else: ?>
    <div class="tip"><span> <?php echo $this->translate("Minimum amount for this type of transaction is %s", 10); ?></span></div>
  <?php endif; ?>
<?php } else { ?>
<div class="tip"><span><?php echo $this->translate("This Payment gateway does not support this currency please choose another payment method for processing further.") ?> </span></div>
<?php } ?>
