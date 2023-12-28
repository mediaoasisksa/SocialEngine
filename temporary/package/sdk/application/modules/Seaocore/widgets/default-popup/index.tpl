
<?php if(!isset($_SESSION['defaultpopup']) ):?>
<script>
Smoothbox.open('<div><img src="http://www.tahrej.com/beta/public/admin/popup.jpg" style="width:100%" /><br /><br /><button onclick="Smoothbox.close();">Close</button></div>', {autoResize : false});
</script>
<?php $_SESSION['defaultpopup'] = 1; ?>
<?php endif;?>