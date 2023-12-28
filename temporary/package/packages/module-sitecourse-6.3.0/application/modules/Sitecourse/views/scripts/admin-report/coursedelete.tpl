<h3><?php echo $this->translate("Delete Report?"); ?></h3>
<p><?php echo $this->translate(sprintf("Are you sure that you want to delete course?")) ?></p>
</br>

<form method="post" id="submit-form">

</form>

<p><button onclick="submitForm()">Delete</button>
   or 
   <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecourse', 'controller' => 'report', 'action' => 'index'), 
   $this->translate('Cancel'), array());?>
</p>

<script type="text/javascript">
    function submitForm(){
        document.getElementById('submit-form').submit();
    }
</script>
