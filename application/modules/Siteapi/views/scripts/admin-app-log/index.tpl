<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteapi/externals/applog/script.js');
 ?>
<?php $this->headLink()
	->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteapi/externals/applog/style.css')
	->appendStylesheet('https://fonts.googleapis.com/css?family=Open+Sans:300,400');
 ?>
 	<div class="heading">App Log</div>
	<div class="container">
		<div class="filter">
			<div class="filter-div active" data-class="row"><a href="javascript:void(0);">All Logs</a></div>
			<div class="filter-div" data-class="row.api-issue"><a href="javascript:void(0);">API Issues</a></div>
			<div class="filter-div" data-class="row.app-issue"><a href="javascript:void(0);">APP Issues</a></div>
			<div class="clear-logs" data-url="admin/siteandroidapp/app-log/clear"><a href="javascript:void(0);">Clear Logs</a></div>
		</div>
		<div class="content"></div>
	</div>
<script type="text/javascript">
	window.addEvent('domready', function() {
		responseArray = <?php echo $this->content ? : '[]' ?>;
		container = scriptJquery('#global_content_wrapper').find('.content');;
		responseArray = responseArray.reverse();
		if (responseArray.length != 0)
			processJsonData(responseArray, container);
		container.removeClass('response-loading').addClass('response-loaded');		
	})
</script>