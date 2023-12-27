
<?php echo $this->form->render($this) ?>

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
	 	// hide the submit btn
	 	document.getElementById('upload-wrapper').style.display = "none";
	 	// hide the url elem
	 	document.getElementById('file-wrapper').style = 'display:none';
	 	// change current video field
	 	currVideoField = value;
	 	// beacues video field is changed last match is not valid
	 	lastMatch = null;
	 	const urlElem = document.getElementById('url');
	 	// make the error field disappear
	 	document.getElementById('validation').style.display = "none";
	 	// show the appropriate msg and fields based on choose video type
	 	let file_element = document.getElementById("file-wrapper");
	 	let url_wrapper_element = document.getElementById("url-wrapper");
	 	url_wrapper_element.style = "display:block";
	 	if(value == 'youtube' || value == 'vimeo' || value == 'dailymotion' || value == 'iframely'){
	 		document.getElementById('code').value = "";
	 		urlElem.value = "";
	 		urlElem.style.display = 'block';
	 		urlElem.nextElementSibling.style.display = 'block';
	 		urlElem.nextElementSibling.textContent = `Paste the web address of the video here.`;
	 		return;
	 	}else if(value == 'embedcode'){
	 		document.getElementById('code').value = "";
	 		urlElem.style.display = 'block'
	 		urlElem.nextElementSibling.style.display = 'block';
	 		urlElem.nextElementSibling.textContent = 'Paste the iframe embed code of the video here.'
	 		urlElem.value = "";
	 		return;
	 	}
	 	else if (value == 'upload') {
	 		document.getElementById('url').value = "";
	 		document.getElementById('code').value = "";
	 		file_element.style.display = "block";
	 		url_wrapper_element.style.display = "none";
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
			}else{
				video.embed(url);
			}
		}else{
			document.getElementById('upload-wrapper').style.display = "none";
			document.getElementById('validation').style.display = "none";
		}

	}

	/**
	 * imediatley invoke the function for some initial setup 
	 * related to form
	 */

	 (function(){
	 	const textElem = document.createElement('p');
	 	textElem.id = 'validation';
	 	textElem.setAttribute('class','description');
	 	document.getElementById('url-element').appendChild(textElem);
	 	document.getElementById('file-wrapper').style = 'display:none';

	 	/**
	 	 * get the vido options
	 	 * make the selected field visible if any
	 	 * will visible in case where any field data is not valid
	 	 */
	 	 const radioOptions = document.getElementsByName('type');
	 	 const typesArray = ['youtube','vimeo','embed','dailymotion'];
	 	 for(let option of radioOptions){
	 	 	if(typesArray.includes(option.value) && option.checked){
	 	 		document.getElementById('url').style = 'display:block';
	 	 		document.getElementById('upload').style = 'display:block';
	 	 		currVideoField = option.value;
	 	 		break;
	 	 	}else if(option.checked){
	 	 		document.getElementById('file-wrapper').style = 'display:block';
	 	 		break;
	 	 	}
	 	 }

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
		    		'url': validationUrl,
		    		'data': {
		    			'format': 'html',
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
		    		    scriptJquery('#dump_data_storage_here').html(responseHTML)
		    			if (valid) {
		    			    
		    				document.getElementById('upload-wrapper').style.display = "block";
		    				document.getElementById('upload').style.display = "block";
		    				document.getElementById('validation').style.display = "none";
		    				document.getElementById('code').value = youtube_code;
		    				document.getElementById('title').value = informationVideoContent.title;
		    			} else {
		    				document.getElementById('upload-wrapper').style.display = "none";
		    				document.getElementById('validation').innerHTML = validationErrorMessage;
		    				document.getElementById('code').value = '';
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
					'url': validationUrl,
					'data': {
						'format': 'html',
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
							document.getElementById('code').value = vimeo_code;
							document.getElementById('title').value = informationVideoContent.title;
						} else {
							document.getElementById('upload-wrapper').style.display = "none";
							document.getElementById('validation').innerHTML = validationErrorMessage;
							document.getElementById('code').value = '';
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
					'url': validationUrl,
					'data': {
						'format': 'html',
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
							document.getElementById('code').value = dailymotion_code;
							document.getElementById('title').value = informationVideoContent.title;
						} else {
							document.getElementById('upload-wrapper').style.display = "none";
							document.getElementById('validation').innerHTML = validationErrorMessage;
							document.getElementById('code').value = '';
						}
					}
				}));
			}
		},
		iframely: function (url) {
			(scriptJquery.ajax({
				'url': validationUrl,
				'data': {
					'format': 'html',
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
						document.getElementById('title').value = informationVideoContent.title;
					} else {
						document.getElementById('upload-wrapper').style.display = "none";
						document.getElementById('validation').innerHTML = validationErrorMessage;
					}
				}
			}));
		},
		embed: function (embedCode) {
			document.getElementById('validation').style.display = "block";
			document.getElementById('validation').innerHTML = checkingUrlMessage;
			document.getElementById('upload-wrapper').style.display = "none";
			if( document.getElementById('thumbnail-wrapper') ) {
				document.getElementById('thumbnail-wrapper').style.display = "none";
			}
			document.getElementById('iframe').innerHTML = embedCode;
			iframeDiv = document.getElementById("iframe").children;
			iframeObj = null;
			bool = false;
			src = '';
			tp = "";
			if (scriptJquery("#iframe").find('IFRAME').length >= 1) {
				src = scriptJquery("#iframe").find('IFRAME')[0].src;
				tp = 'IFRAME';
			} else if (scriptJquery("#iframe").find('BLOCKQUOTE').length >= 1) {
				src = scriptJquery("#iframe").find('BLOCKQUOTE')[0].find('a').last().href;
				tp = 'BLOCKQUOTE';
			} else if (scriptJquery("#iframe").find('A').length >= 1) {
				src = scriptJquery("#iframe").find('A')[0].href;
				tp = 'A';
			}
			if (src != '')
			{
				src.match(/(http:|https:|)\/\/(player.|www.|in.)?(pinterest\.com|instagram\.com|twitter\.com|dailymotion\.com|vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com))\/(video\/|embed\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?/);
				var allowYoutube = '<?php echo !!($this->settings('sitecourse.youtube.apikey', $this->settings('video.youtube.apikey'))); ?>';
				if (RegExp.$3.indexOf('youtu') > -1 && allowYoutube) {
					video.youtube(src);
					document.getElementById('vtype').value = "youtube";
				} else if (RegExp.$3.indexOf('vimeo') > -1) {
					video.vimeo(src);
					document.getElementById('vtype').value = "vimeo";
				} else if (RegExp.$3.indexOf('dailymotion') > -1) {
					video.dailymotion(src);
					document.getElementById('vtype').value = "dailymotion"
				} else if (RegExp.$3.indexOf('pinterest') > -1) {
					document.getElementById('vtype').value = "pinterest";
					document.getElementById('upload-wrapper').style.display = "block";
					document.getElementById('upload').style.display = "block";
					document.getElementById('validation').style.display = "none";
					document.getElementById('code').value = src;
				} else if (tp != 'A') {
					document.getElementById('upload-wrapper').style.display = "block";
					document.getElementById('upload').style.display = "block";
					document.getElementById('validation').style.display = "none";
					document.getElementById('code').value = src;
				}
			}
		}
	}
	window.addEventListener('DOMContentLoaded', (event) => {
		let url = '<?=$this->url; ?>';
		updateUrlFields('<?=$this->vtype; ?>');
		document.getElementById('url').value = url;
		validateUrl(url);
    // document.getElementById('tbxFile_0').addEventListener('change',function(){
    // 	document.getElementById('title').value = document.querySelector('.file-name').textContent;
    // })
});

	

</script>
<div id="iframe" style="display:none;">
</div>

<div id="dump_data_storage_here" style="display:none;"></div>
