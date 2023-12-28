<script type="text/javascript">
//<![CDATA[
  scriptJquery(document).ready(function() {
    scriptJquery('#sort').on('change', function(){
      scriptJquery(this).parents('form').eq(0).trigger("submit");
    });
  })
//]]>
</script>

<?php echo $this->searchForm->render($this) ?>
