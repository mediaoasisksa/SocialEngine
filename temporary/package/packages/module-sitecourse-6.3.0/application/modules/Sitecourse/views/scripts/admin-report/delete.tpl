<form class="report-delete-modal-box global_form_box" method="post" id="submit-form">
    <input type="hidden" name="confirm" value="yes">
    <input type="hidden" name="ids" id="ids">
    <h3><?php echo $this->translate("Delete Report?"); ?></h3>
    <p><?php echo $this->translate(" Are you sure that you want to delete this report? It will not be recoverable after being deleted."); ?></p>
        <span>
            <button onclick="submitForm()">Delete</button>
           or 
           <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecourse', 'controller' => 'report', 'action' => 'index'), 
           $this->translate('Cancel'), array('onclick' => 'javascript:parent.Smoothbox.close();'));?>
        </span>
</form>

<script type="text/javascript">
    function submitForm(){
        document.getElementById('ids').value = '<?php echo $this->ids; ?>'
        document.getElementById('submit-form').submit();
    }
</script>
<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>
