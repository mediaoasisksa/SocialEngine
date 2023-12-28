<?php 


$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecourse/externals/styles/style_sitecourse_dashboard.css');
?>


<div class="course_builder_dashboard">
	<div class="course_builder_dashboard_container">

		<?php $id=$this->course_id;
		$blockId = 1;
		$liId='target'; ?>
		<?php include_once APPLICATION_PATH . '/application/modules/Sitecourse/views/scripts/dashboard/_menu.tpl'; ?>
		
		<div class="course_builder_dashboard_sections">
			<div class="course_builder_dashboard_sections_list">
				<div class="layout_middle">
					<div class="course_builder_dashboard_sections_header">
						<div class="course_builder_dashboard_sections_header_title">
							<img src="<?php echo $this->images['image_icon'];?>" alt="" />
							<h3><?php echo $this->translate('Course Dashboard'); ?></h3>
						</div>
						<?php include_once APPLICATION_PATH . '/application/modules/Sitecourse/views/scripts/dashboard/_dashboardNavigation.tpl'; ?>
					</div>          			

					<div class="generic_layout_container">
						<?php echo $this->form->render($this) ?>

					</div>
				</div>

			</div>
		</div>

	</div>
</div>




<script type="text/javascript">
	// curr selected video option
	let currVideoField = null;

	// last matched url
	let lastMatch = null;

	/**
	 * hide or show the repsective fields based on the choosen video option
	 */
	function updateUrlFields(value)
	{
	 	// current checked radio btn is same as previous
		if(currVideoField == value) return;
	 	// change current video field
		currVideoField = value;
	 	// beacues video field is changed last match is not valid
		lastMatch = null;
		const urlElem = document.getElementById('url');
	 	// make the error field disappear
		document.getElementById('validation').style.display = "none";
	 	// show the appropriate msg and fields based on choose video type
		if(value == 'youtube' || value == 'vimeo' || value == 'dailymotion'){
			urlElem.value = "";
			urlElem.style.display = 'block';
			urlElem.nextElementSibling.style.display = 'block';
			urlElem.nextElementSibling.textContent = `Paste the ${value} address of the video here.`;
			return;
		}else if(value == 'embedcode'){
			urlElem.style.display = 'block'
			urlElem.nextElementSibling.style.display = 'block';
			urlElem.nextElementSibling.textContent = 'Paste the iframe embed code of the video here.'
			urlElem.value = "";
			return;
		}
	}


	let validationUrl = '<?php echo $this->url(array("action" => "validation"), "sitecourse_video_general", true) ?>';
	let validationErrorMessage = "<?php echo $this->string()->escapeJavascript($this->translate("
		We could not find a video there - please check the URL and try again.If you are sure that the URL is valid, please click % s to continue.", " < a href = 'javascript:void(0);' onclick = 'javascript:ignoreValidation();' > " . $this->translate("here") . " < /a>")); ?>";
	let checkingUrlMessage = '<?php echo $this->string()->escapeJavascript($this->translate("Checking URL...")) ?>';

	/**
	 * validate the url based on the choosen option
	 */
	function validateUrl(url)
	{
		let m;
		// check for link
		if (currVideoField == 'embedcode') {
			m = url.match(/(https?:\/\/|\/\/)([-\w\.]+)+(:\d+)?(\/([-#:\w/_\.]*(\?\S+)?)?)?/);
		} else {
			m = url.match(/https?:\/\/([-\w\.]+)+(:\d+)?(\/([-#:\w/_\.]*(\?\S+)?)?)?/);
		}

		if ($type(m) && $type(m[0]) && lastMatch != m[0]) {
			lastMatch = url;
			if(currVideoField == 'youtube'){
				video.youtube(url);
			}
			else if(currVideoField == 'vimeo'){
				video.vimeo(url);
			}else if (currVideoField == 'dailymotion') {
				video.dailymotion(url);
			}else if (currVideoField == 'iframely') {
				video.iframely(url);
			}
		}

	}

	/**
	 * @todo 1.
	 * create this one with the help of zend
	 * this only for testing purpose remove and add into zend before the actual submission
	 */
	(function(){
		const textElem = document.createElement('p');
		textElem.id = 'validation';
		textElem.setAttribute('class','description');
		if(document.getElementById('url-element'))
			document.getElementById('url-element').appendChild(textElem);
	})();

	const video = {
		youtube: function (url) {
		    // extract v from url
			var myURI = new URL(url)
			var youtube_code = myURI.searchParams.get('v');
			if ( !youtube_code || youtube_code === undefined) {
				return
			}
			if (youtube_code) {
				(scriptJquery.ajax({
					'format': 'html',
					'url': validationUrl,
					'data': {
						'ajax': true,
						'code': youtube_code,
						'type': 'youtube'
					},
					'beforeSend': function () {
						document.getElementById('validation').style.display = "block";
						document.getElementById('validation').innerHTML = checkingUrlMessage;
						document.getElementById('upload-wrapper').style.display = "none";
					},
					'success': function (responseHTML) {
						if (valid) {
							document.getElementById('upload-wrapper').style.display = "block";
							document.getElementById('upload').style.display = "block";
							document.getElementById('validation').style.display = "none";
						} else {
							document.getElementById('upload-wrapper').style.display = "none";
							document.getElementById('validation').innerHTML = validationErrorMessage;
						}
					}
				}));
			}
		},
		vimeo: function (url) {

			var myURI = new URL(url)
			var vimeo_code = myURI.pathname.split('/')[1];
			if (vimeo_code.length > 0) 
			{
				(scriptJquery.ajax({
					'format': 'html',
					'url': validationUrl,
					'data': {
						'ajax': true,
						'code': vimeo_code,
						'type': 'vimeo'
					},
					'beforeSend': function () {
						document.getElementById('validation').style.display = "block";
						document.getElementById('validation').innerHTML = checkingUrlMessage;
						document.getElementById('upload-wrapper').style.display = "none";
					},
					'success': function (responseHTML) {
						scriptJquery('#dump_data_storage_here').html(responseHTML)
						if (valid) {
							document.getElementById('upload-wrapper').style.display = "block";
							document.getElementById('upload').style.display = "block";
							document.getElementById('validation').style.display = "none";
						} else {
							document.getElementById('upload-wrapper').style.display = "none";
							document.getElementById('validation').innerHTML = validationErrorMessage;
						}
					}
				}));
			}
		},
		dailymotion: function (url) {
			var myURI = new URL(url)
			var dailymotion_code = myURI.pathname.split('/')[2];

			if (dailymotion_code.length > 0)
			{
				(scriptJquery.ajax({
					'format': 'html',
					'url': validationUrl,
					'data': {
						'ajax': true,
						'code': dailymotion_code,
						'type': 'dailymotion'
					},
					'beforeSend': function () {
						document.getElementById('validation').style.display = "block";
						document.getElementById('validation').innerHTML = checkingUrlMessage;
						document.getElementById('upload-wrapper').style.display = "none";
					},
					'success': function (responseHTML) {
						scriptJquery('#dump_data_storage_here').html(responseHTML)
						if (valid) {
							document.getElementById('upload-wrapper').style.display = "block";
							document.getElementById('upload').style.display = "block";
							document.getElementById('validation').style.display = "none";
						} else {
							document.getElementById('upload-wrapper').style.display = "none";
							document.getElementById('validation').innerHTML = validationErrorMessage;
						}
					}
				}));
			}
		},
		iframely: function (url) {
			(scriptJquery.ajax({
				'url': validationUrl,
				'data': {
					'ajax': true,
					'code': url,
					'type': 'iframely'
				},
				beforeSend: function () {
					document.getElementById('validation').style.display = "block";
					document.getElementById('validation').innerHTML = checkingUrlMessage;
					document.getElementById('upload-wrapper').style.display = "none";
				},
				success: function (responseHTML) {
					if (valid) {
						document.getElementById('upload-wrapper').style.display = "block";
						document.getElementById('upload').style.display = "block";
						document.getElementById('validation').style.display = "none";
					} else {
						document.getElementById('upload-wrapper').style.display = "none";
						document.getElementById('validation').innerHTML = validationErrorMessage;
					}
				}
			}));
		}
	}
</script>

<div id="dump_data_storage_here" style="display:none;"></div>
