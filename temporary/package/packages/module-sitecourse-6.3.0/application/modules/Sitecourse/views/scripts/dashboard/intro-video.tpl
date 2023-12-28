<?php $liId='intro-video';
echo $this->form->render($this); ?>
<script type="text/javascript">
	function addOption(selectbox,text,value )
	{
		var optn = document.createElement("OPTION");
		optn.text = text;
		optn.value = value;
		if(optn.text != '' && optn.value != '') {
			selectbox.options.add(optn);
		}
		else {
			selectbox.options.add(optn);
		}
	}
	function clear(ddName)
	{ 
		for (var i = (document.getElementById(ddName).options.length-1); i >= 0; i--) 
		{ 
			if(document.getElementById(ddName).options[ i ].value != 0)
				document.getElementById(ddName).options[ i ]=null; 
		} 
	}

	function changeTopic(elem){
		const id = elem.value;
		let previewElem = document.getElementById('video_preview_wrapper');
		if(previewElem){
			previewElem.innerHTML = '';
			previewElem.style.display = 'none';
		}
		clear('lesson_id');
		if(id != 0){
			let url = '<?php echo $this->url(array('action' => 'getlessons','course_id'=>$this->course_id), 'sitecourse_dashboard', true);?>';
			let request = en4.core.request.send(scriptJquery.ajax({
				url : url,
				data : {
					format : 'json',
					topic_id : id
				},
				success : function(responseJSON) {
					const lessons = responseJSON.lessons;
					if(lessons.length){
						document.getElementById('lesson_id').options[0].text = '';
						document.getElementById("lesson_id").disabled = false;
						lessons.forEach((lesson,key,arr)=>{
							const value = lesson['lesson_id'];
							const text = lesson['title'];
							
							addOption(document.getElementById('lesson_id'),text,value);
						})
					}else{
						document.getElementById('lesson_id').options[0].text = 'No Video lesson Found.';
						document.getElementById("lesson_id").disabled  = true;
					}
				}
			}));
		}else{
			document.getElementById('lesson_id').options[0].text = 'Please select a topic';
			document.getElementById('lesson_id').disabled = true;
		}
	}

	function insertAfter(referenceNode, newNode) {
		referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
	}

	function createVideoDiv(){
		const div = document.createElement('div');
		div.id = 'video_preview_wrapper';
		insertAfter(document.getElementById('lesson_id-wrapper'),div);
	}
	
	function changeLesson(elem){
		const id = elem.value;
		if(id != 0){
			let url = '<?php echo $this->url(array('action' => 'getvideo','course_id'=>$this->course_id), 'sitecourse_dashboard', true);?>';
			let request = en4.core.request.send(scriptJquery.ajax({
				url : url,
				data : {
					format : 'json',
					lesson_id : id
				},
				success : function(responseJSON) {
					const video_url = responseJSON.video_url
					if(responseJSON.video.video_type == 'upload') {
						html = `<div class="video-course"><video id="video" width="100%" height="340px" controls autoplay>
						<source src="${video_url}" type="video/mp4" />
						Please try after some time
						</video></div>`;
					} else {
						html = `<div class="video-course">
						<iframe src="${video_url}" title="Video player" frameborder="0" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen autoplay></iframe>
						</div>`
					}
					let previewElem = document.getElementById('video_preview_wrapper');
					if(!previewElem){
						createVideoDiv();
						previewElem = document.getElementById('video_preview_wrapper');	
					}
					previewElem.innerHTML = html;
					previewElem.style.display = 'block';
				}
			}));
			
		}else{
			let previewElem = document.getElementById('video_preview_wrapper');
			if(previewElem){
				previewElem.style.display = 'none';
			}
		}
	}
	
	changeTopic(document.getElementById('topic_id'));

</script>
